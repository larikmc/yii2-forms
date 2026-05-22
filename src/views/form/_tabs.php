<?php
use yii\helpers\Html;
/** @var \larikmc\forms\models\Form $model */
$items = [
    'main' => ['label' => 'Основное', 'url' => ['update', 'id' => $model->id]],
    'fields' => ['label' => 'Поля', 'url' => ['fields', 'id' => $model->id]],
    'code' => ['label' => 'Код вставки', 'url' => ['code', 'id' => $model->id]],
];
?>
<div class="sz-page mb-3">
    <ul class="nav nav-tabs">
    <?php foreach ($items as $key => $item): ?>
        <li class="nav-item">
            <?= Html::a($item['label'], $item['url'], ['class' => 'nav-link' . (($active ?? '') === $key ? ' active' : '')]) ?>
        </li>
    <?php endforeach; ?>
    </ul>
</div>
