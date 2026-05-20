<?php
use larikmc\forms\helpers\FieldRenderHelper;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
/** @var $model \larikmc\forms\models\DynamicFormModel */
/** @var $form \larikmc\forms\models\Form */
$successMessage = Yii::$app->session->getFlash('forms_success_' . $form->slug);
$errorMessages = Yii::$app->session->getFlash('forms_error_' . $form->slug);
?>
<div class="forms-widget" id="forms-<?= Html::encode($uid) ?>">
<?php if ($widget->showHeading && $form->title): ?><h3><?= Html::encode($form->title) ?></h3><?php endif; ?>
<?php if ($widget->showDescription && $form->description): ?><p><?= Html::encode($form->description) ?></p><?php endif; ?>
<?php if ($successMessage): ?>
    <div class="forms-widget__alert forms-widget__alert--success"><?= Html::encode($successMessage) ?></div>
<?php else: ?>
<?php if ($errorMessages): ?>
    <div class="forms-widget__alert forms-widget__alert--error">
        <?= Html::encode(implode(' ', array_map(static fn($messages) => implode(' ', (array) $messages), (array) $errorMessages))) ?>
    </div>
<?php endif; ?>
<?php $af = ActiveForm::begin(['action'=>['/forms/submit/index'],'options'=>array_merge(['id'=>'forms-form-'.$uid], $widget->formOptions)]); ?>
<?= Html::hiddenInput('_form_slug', $form->slug) ?>
<div class="forms-widget-hp"><?= Html::textInput('forms_hp','',['autocomplete'=>'off','tabindex'=>-1]) ?></div>
<?php foreach ($formFields as $formField): ?><?= FieldRenderHelper::render($af, $model, $formField) ?><?php endforeach; ?>
<?= Html::submitButton(Html::encode($form->submit_label), ['class'=>'btn btn-primary']) ?>
<?php ActiveForm::end(); ?>
<?php endif; ?>
</div>
