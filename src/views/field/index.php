<?php
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
?>
<div class="sz-panel">
    <p><?= Html::a('Создать поле', ['create'], ['class' => 'btn btn-success']) ?></p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'name',
            'type',
            'mask',
            ['attribute' => 'is_active', 'format' => 'boolean'],
            [
                'class' => ActionColumn::class,
                'template' => '{update} {delete}',
                'contentOptions' => ['class' => 'action-column'],
                'buttons' => [
                    'update' => static function ($url) {
                        return Html::a('<span class="material-symbols-rounded">edit</span>', $url, [
                            'class' => 'sz-row-action',
                            'aria-label' => 'Редактировать',
                            'title' => 'Редактировать',
                        ]);
                    },
                    'delete' => static function ($url) {
                        return Html::a('<span class="material-symbols-rounded">delete</span>', $url, [
                            'class' => 'sz-row-action',
                            'aria-label' => 'Удалить',
                            'title' => 'Удалить',
                            'data-method' => 'post',
                            'data-confirm' => 'Удалить это поле?',
                        ]);
                    },
                ],
            ],
        ],
    ]) ?>
</div>
