<?php
namespace larikmc\forms\controllers;

use larikmc\forms\helpers\SubmissionContextHelper;
use larikmc\forms\models\DynamicFormModel;
use larikmc\forms\models\Form;
use larikmc\forms\models\Submission;
use yii\web\Response;
use yii\web\Controller;

class SubmitController extends Controller
{
    public function actionIndex()
    {
        $request = \Yii::$app->request;
        if (!$request->isPost) { return $this->goHome(); }
        $isAjax = $request->isAjax;
        $formId = (int) $request->post('_form_id', 0);
        $honeypot = $request->post('forms_hp');

        $form = Form::find()->where(['is_active' => 1, 'id' => $formId])->one();
        if (!$form) {
            return $isAjax
                ? $this->asJsonError(['_form' => ['Форма не найдена или отключена.']], Response::HTTP_NOT_FOUND)
                : $this->goBack();
        }

        $formFields = $form->getFormFields()->andWhere(['is_active'=>1])->with('field')->all();
        $model = new DynamicFormModel($formFields);
        $model->load($request->post());
        $personalAgreement = (bool) $request->post('forms_personal_agreement');

        if (!empty($honeypot) || !$model->validate() || !$personalAgreement) {
            $errors = $model->getErrors();
            if (!$personalAgreement) {
                $errors['forms_personal_agreement'][] = 'Необходимо дать согласие на обработку персональных данных.';
            }
            if ($isAjax) {
                return $this->asJsonError($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            \Yii::$app->session->setFlash('forms_error_'.$form->id, $errors);
            return $this->goBack();
        }

        $submission = new Submission();
        $submission->form_id = $form->id;
        $submission->status = Submission::STATUS_NEW;
        $submission->data_json = json_encode($model->getSubmissionData(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $contextToken = $request->post('_forms_context', '');
        $context = ($this->module instanceof \larikmc\forms\Module)
            ? SubmissionContextHelper::verify(is_string($contextToken) ? $contextToken : '', $form->id, $this->module)
            : [];
        $submission->context_json = $context ? json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null;
        $submission->page_url = $context['page_url'] ?? $request->referrer;
        $submission->referrer = $request->headers->get('referer');
        if (($module = $this->module) && $module->storeClientInfo) {
            $submission->ip = $request->userIP;
            $submission->user_agent = $request->userAgent;
        }
        if (!$submission->save(false)) {
            \Yii::error('Cannot save forms submission.', 'forms.submit');
            if ($isAjax) {
                return $this->asJsonError(['_form' => ['Не удалось сохранить заявку. Попробуйте позже.']], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            \Yii::$app->session->setFlash('forms_error_'.$form->id, ['_form' => ['Не удалось сохранить заявку. Попробуйте позже.']]);
            return $this->goBack();
        }

        $this->sendNotificationEmails($form, $model, $submission);

        $successMessage = $form->success_message ?: 'Спасибо! Форма отправлена.';
        if ($isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true,
                'message' => $successMessage,
            ];
        }
        \Yii::$app->session->setFlash('forms_success_'.$form->id, $successMessage);
        if (($module = $this->module) && $module->defaultSuccessRedirect) { return $this->redirect($module->defaultSuccessRedirect); }
        return $this->goBack();
    }

    private function asJsonError(array $errors, int $statusCode = 422): array
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        \Yii::$app->response->statusCode = $statusCode;

        return [
            'success' => false,
            'errors' => $errors,
        ];
    }

    private function sendNotificationEmails(Form $form, DynamicFormModel $model, Submission $submission): void
    {
        $emails = $form->getNotificationEmailsList();
        if ($emails === [] || !\Yii::$app->has('mailer')) {
            return;
        }

        $mailer = \Yii::$app->mailer;
        $data = $model->getSubmissionData();
        $rows = [];
        foreach ($data as $label => $value) {
            $rows[] = $label . ': ' . (is_scalar($value) ? (string) $value : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }
        $contextRows = [];
        foreach (SubmissionContextHelper::displayRows($submission->getContext()) as $label => $value) {
            $contextRows[] = $label . ': ' . $value;
        }

        $formDisplayName = $form->name ?: ($form->title ?: ('Форма #' . $form->id));
        $subject = 'Новая заявка с формы "' . $formDisplayName . '"';
        $body = implode(PHP_EOL, [
            $subject,
            '',
            'Дата: ' . date('d.m.Y H:i:s'),
            'Страница: ' . ($submission->page_url ?: '-'),
            'Referrer: ' . ($submission->referrer ?: '-'),
            'IP: ' . ($submission->ip ?: '-'),
            '',
            'Данные формы:',
            implode(PHP_EOL, $rows),
            '',
            'Контекст страницы и объекта:',
            $contextRows ? implode(PHP_EOL, $contextRows) : 'Не указан',
        ]);

        try {
            $mailer->compose()
                ->setTo($emails)
                ->setSubject($subject)
                ->setTextBody($body)
                ->send();
        } catch (\Throwable $e) {
            \Yii::warning($e->getMessage(), 'forms.mail');
        }
    }
}
