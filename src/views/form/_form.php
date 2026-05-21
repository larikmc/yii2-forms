<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

?>
<div class="sz-panel">
    <?php $f = ActiveForm::begin(); ?>

    <?= $f->field($model, 'name')->hint('Только для админки. На сайте не выводится.') ?>
    <?= $f->field($model, 'title')->hint('Можно оставить пустым, если заголовок в форме не нужен.') ?>
    <?= $f->field($model, 'description')->textarea() ?>
    <?= $f->field($model, 'submit_label') ?>

    <?php if (\larikmc\forms\models\Form::hasSubmitButtonClassColumn()): ?>
        <?= $f->field($model, 'submit_button_class')->hint('Класс кнопки отправки внутри формы. Если пусто, берется дефолт из модуля.') ?>
    <?php endif; ?>

    <?php if (\larikmc\forms\models\Form::hasTriggerButtonClassColumn()): ?>
        <?= $f->field($model, 'trigger_button_class')->hint('Класс кнопки открытия popup. Если пусто, берется дефолт из модуля.') ?>
    <?php endif; ?>

    <?= $f->field($model, 'success_message')->textarea() ?>

    <?php if (\larikmc\forms\models\Form::hasNotificationEmailsColumn()): ?>
        <?= $f->field($model, 'notification_emails')->textarea(['rows' => 3])->hint('Если оставить пустым, форма будет отправлять уведомления на e-mail по умолчанию из раздела "Настройки". Можно указать несколько адресов через запятую.') ?>
    <?php endif; ?>

    <?= $f->field($model, 'is_active')->checkbox() ?>

    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>

    <?php ActiveForm::end(); ?>
</div>
