<?php
use yii\bootstrap5\Html;
$buttonOptions = array_merge(['class'=>'btn btn-primary','id'=>'forms-button-'.$uid,'data-bs-toggle'=>'modal','data-bs-target'=>'#'.$modalId], $buttonOptions);
?>
<?= Html::button(Html::encode($buttonLabel), $buttonOptions) ?>
<div class="modal fade" id="<?= Html::encode($modalId) ?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title"><?= Html::encode($form->name) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><?= $formHtml ?></div></div></div>
</div>
