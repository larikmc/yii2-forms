<?php

use yii\helpers\Html;

/** @var string $activeController */

$items = [
    ['id' => 'form', 'label' => 'Формы', 'url' => ['/forms/form/index']],
    ['id' => 'field', 'label' => 'Поля форм', 'url' => ['/forms/field/index']],
    ['id' => 'submission', 'label' => 'Заявки', 'url' => ['/forms/submission/index']],
    ['id' => 'settings', 'label' => 'Настройки', 'url' => ['/forms/settings/index']],
];

$tabs = array_map(
    static fn (array $item): array => [
        'label' => $item['label'],
        'url' => $item['url'],
        'active' => $item['id'] === $activeController,
    ],
    $items,
);
?>
<nav class="sz-page" aria-label="Разделы модуля форм">
    <?php if (class_exists(\larikmc\admin\widgets\AdminTabs::class)): ?>
        <?= \larikmc\admin\widgets\AdminTabs::widget(['items' => $tabs]) ?>
    <?php else: ?>
        <ul class="nav nav-tabs mb-3 sz-tabs__nav">
            <?php foreach ($tabs as $tab): ?>
                <li class="nav-item">
                    <?= Html::a($tab['label'], $tab['url'], [
                        'class' => 'nav-link' . ($tab['active'] ? ' active' : ''),
                        'aria-current' => $tab['active'] ? 'page' : null,
                    ]) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</nav>
