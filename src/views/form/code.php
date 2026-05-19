<?php use yii\helpers\Html; \larikmc\forms\assets\FormsAdminAsset::register($this); ?>
<div class="sz-panel"><p>Форма одна и та же. Отличается только способ вывода.</p>
<pre id="code1"><?= Html::encode("<?= \\larikmc\\forms\\widgets\\FormWidget::widget(['slug' => '{$model->slug}']) ?>") ?></pre><button class="btn btn-outline-secondary forms-copy-btn" data-forms-copy="#code1">Скопировать</button>
<pre id="code2"><?= Html::encode("<?= \\larikmc\\forms\\widgets\\FormWidget::widget(['slug' => '{$model->slug}','template'=>'default']) ?>") ?></pre><button class="btn btn-outline-secondary forms-copy-btn" data-forms-copy="#code2">Скопировать</button>
<pre id="code3"><?= Html::encode("<?= \\larikmc\\forms\\widgets\\FormModalWidget::widget(['slug' => '{$model->slug}','buttonLabel'=>'Оставить заявку']) ?>") ?></pre><button class="btn btn-outline-secondary forms-copy-btn" data-forms-copy="#code3">Скопировать</button></div>
