<?php
use yii\helpers\Html;
$buttonOptions = array_merge([
    'class' => 'btn btn-primary',
    'id' => 'forms-button-' . $uid,
    'type' => 'button',
    'data-forms-modal-open' => '#' . $modalId,
    'aria-controls' => $modalId,
    'aria-haspopup' => 'dialog',
], $buttonOptions);
?>
<?= Html::button(Html::encode($buttonLabel), $buttonOptions) ?>
<div class="forms-modal" id="<?= Html::encode($modalId) ?>" aria-hidden="true" data-forms-modal-slug="<?= Html::encode($form->slug) ?>">
  <div class="forms-modal__backdrop" data-forms-modal-close></div>
  <div class="forms-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="<?= Html::encode($modalId) ?>-title">
    <div class="forms-modal__card">
      <div class="forms-modal__header">
        <h3 class="forms-modal__title" id="<?= Html::encode($modalId) ?>-title"><?= Html::encode($form->title ?: $form->name) ?></h3>
        <button type="button" class="forms-modal__close" data-forms-modal-close aria-label="Закрыть">
          <span class="forms-modal__close-icon" aria-hidden="true"></span>
        </button>
      </div>
      <div class="forms-modal__body"><?= $formHtml ?></div>
    </div>
  </div>
</div>
