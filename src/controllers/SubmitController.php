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
        $slug = $request->post('_form_slug');
        $honeypot = $request->post('forms_hp');

        $form = Form::find()->where(['slug'=>$slug,'is_active'=>1])->one();
        if (!$form) { return $this->goBack(); }

        $formFields = $form->getFormFields()->andWhere(['is_active'=>1])->with('field')->all();
        $model = new DynamicFormModel($formFields);
        $model->load($request->post());

        if (!empty($honeypot) || !$model->validate()) {
            \Yii::$app->session->setFlash('forms_error_'.$slug, $model->getErrors());
            return $this->goBack();
        }

        if ($form->store_submissions) {
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
        }

        \Yii::$app->session->setFlash('forms_success_'.$slug, $form->success_message ?: 'Спасибо! Форма отправлена.');
        if (($module = $this->module) && $module->defaultSuccessRedirect) { return $this->redirect($module->defaultSuccessRedirect); }
        return $this->goBack();
    }
}
