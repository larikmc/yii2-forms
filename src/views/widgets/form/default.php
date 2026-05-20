<?php
use larikmc\forms\helpers\FieldRenderHelper;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
/** @var $model \larikmc\forms\models\DynamicFormModel */
/** @var $form \larikmc\forms\models\Form */
$successMessage = Yii::$app->session->getFlash('forms_success_' . $form->slug);
$errorMessages = Yii::$app->session->getFlash('forms_error_' . $form->slug);
$consentTextHtml = '';
$module = Yii::$app->getModule('forms');
if ($module instanceof \larikmc\forms\Module) {
    $consentTextHtml = HtmlPurifier::process($module->getConsentTextHtml(), [
        'HTML.Allowed' => 'a[href|target|rel],b,strong,i,em,span,br',
        'AutoFormat.RemoveEmpty' => true,
    ]);
}
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
<?= Html::beginForm(['/forms/submit/index'], 'post', array_merge([
    'id' => 'forms-form-' . $uid,
    'class' => 'forms-widget__form',
    'novalidate' => true,
], $widget->formOptions)) ?>
<?= Html::hiddenInput('_form_slug', $form->slug) ?>
<div class="forms-widget-hp"><?= Html::textInput('forms_hp', '', ['autocomplete' => 'off', 'tabindex' => -1]) ?></div>
<?php foreach ($formFields as $formField): ?><?= FieldRenderHelper::render($model, $formField) ?><?php endforeach; ?>
<div class="forms-field forms-field--checkbox" data-forms-field-wrap="forms_personal_agreement" data-forms-field-type="checkbox" data-forms-required="1">
    <label class="forms-checkbox-wrap">
        <?= Html::checkbox('forms_personal_agreement', false, [
            'class' => 'forms-checkbox',
            'value' => 1,
            'data-forms-field' => 'forms_personal_agreement',
            'data-forms-label' => 'Согласие на обработку персональных данных',
            'data-forms-type' => 'checkbox',
            'data-forms-required' => 1,
        ]) ?>
        <span class="forms-checkbox-ui" aria-hidden="true"></span>
        <span class="forms-checkbox__text">
            <?= $consentTextHtml !== '' ? $consentTextHtml : Html::encode('Даю согласие на обработку персональных данных для обработки моего обращения и обратной связи со мной. Ознакомлен(а) с Политикой обработки персональных данных.') ?>
        </span>
    </label>
    <div class="forms-error" data-forms-error="forms_personal_agreement" id="forms-error-forms_personal_agreement"></div>
</div>
<?= Html::submitButton(
    Html::tag('span', Html::encode($form->submit_label), ['class' => 'forms-widget__submit-text'])
    . Html::tag('span', '', ['class' => 'forms-widget__submit-spinner', 'aria-hidden' => 'true']),
    ['class' => $form->getEffectiveSubmitButtonClass(), 'type' => 'submit']
) ?>
<?= Html::endForm() ?>
<?php endif; ?>
</div>
