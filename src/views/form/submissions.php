<?= $this->render('_tabs', ['model' => $model, 'active' => 'submissions']) ?>
<div class="sz-panel"><?= yii\helpers\Html::a("К заявкам", ["/forms/submission/index"], ["class"=>"btn btn-primary"]) ?></div>
