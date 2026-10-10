<?php

use larikmc\forms\models\Setting;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
?>

<div class="sz-panel">
    <?php if (!Setting::hasSettingsTable()): ?>
        <div class="alert alert-warning">
            Для сохранения настроек примените миграции расширения.
        </div>
    <?php endif; ?>

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'notification_emails')->textarea(['rows' => 4])->hint($model->getAttributeHint('notification_emails')) ?>
    <?= $form->field($model, 'default_submit_button_class')->textInput()->hint($model->getAttributeHint('default_submit_button_class')) ?>
    <?= $form->field($model, 'default_trigger_button_class')->textInput()->hint($model->getAttributeHint('default_trigger_button_class')) ?>
    <?= $form->field($model, 'consent_text_html')->textarea(['rows' => 5])->hint($model->getAttributeHint('consent_text_html')) ?>
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end(); ?>
</div>
