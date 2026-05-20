<?php

namespace larikmc\forms\controllers;

use larikmc\forms\models\SettingsForm;

class SettingsController extends AdminController
{
    public function actionIndex()
    {
        $model = new SettingsForm();
        $model->loadValues();

        if ($model->load(\Yii::$app->request->post()) && $model->saveValues()) {
            \Yii::$app->session->setFlash('success', 'Настройки форм сохранены.');
            return $this->refresh();
        }

        return $this->render('index', compact('model'));
    }
}
