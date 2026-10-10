<?php

use yii\helpers\Html;

/** @var string $activeController */

$items = [
    ['id' => 'form', 'label' => 'Формы', 'url' => ['/forms/form/index']],
    ['id' => 'field', 'label' => 'Поля форм', 'url' => ['/forms/field/index']],
    ['id' => 'submission', 'label' => 'Заявки', 'url' => ['/forms/submission/index']],
    ['id' => 'settings', 'label' => 'Настройки', 'url' => ['/forms/settings/index']],
];
?>
<nav class="forms-admin-navigation" aria-label="Разделы модуля форм">
    <?php foreach ($items as $item): ?>
        <?= Html::a(
            $item['label'],
            $item['url'],
            ['class' => 'forms-admin-navigation__link' . ($item['id'] === $activeController ? ' is-active' : '')],
        ) ?>
    <?php endforeach; ?>
</nav>
