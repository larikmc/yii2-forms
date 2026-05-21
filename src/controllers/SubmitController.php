<?php
namespace larikmc\forms\controllers;

use larikmc\forms\models\DynamicFormModel;
use larikmc\forms\models\Form;
use larikmc\forms\models\Submission;
use yii\web\Controller;

class SubmitController extends Controller
{
    public function actionIndex()
    {
        $request = \Yii::$app->request;
        if (!$request->isPost) { return $this->goHome(); }
        $formId = (int) $request->post('_form_id', 0);
        $honeypot = $request->post('forms_hp');

        $form = Form::find()->where(['is_active' => 1, 'id' => $formId])->one();
        if (!$form) { return $this->goBack(); }

        $formFields = $form->getFormFields()->andWhere(['is_active'=>1])->with('field')->all();
        $model = new DynamicFormModel($formFields);
        $model->load($request->post());
        $personalAgreement = (bool) $request->post('forms_personal_agreement');

        if (!empty($honeypot) || !$model->validate() || !$personalAgreement) {
            $errors = $model->getErrors();
            if (!$personalAgreement) {
                $errors['forms_personal_agreement'][] = 'Необходимо дать согласие на обработку персональных данных.';
            }
            \Yii::$app->session->setFlash('forms_error_'.$form->id, $errors);
            return $this->goBack();
        }

        $submission = new Submission();
        $submission->form_id = $form->id;
        $submission->status = Submission::STATUS_NEW;
        $submission->data_json = json_encode($model->getSubmissionData(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $submission->page_url = $request->referrer;
        $submission->referrer = $request->headers->get('referer');
        if (($module = $this->module) && $module->storeClientInfo) {
            $submission->ip = $request->userIP;
            $submission->user_agent = $request->userAgent;
        }
        $submission->save(false);

        $this->sendNotificationEmails($form, $model, $submission);

        \Yii::$app->session->setFlash('forms_success_'.$form->id, $form->success_message ?: 'Спасибо! Форма отправлена.');
        if (($module = $this->module) && $module->defaultSuccessRedirect) { return $this->redirect($module->defaultSuccessRedirect); }
        return $this->goBack();
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

        $subject = 'Новая заявка с формы "' . ($form->title ?: ('Форма #' . $form->id)) . '"';
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
