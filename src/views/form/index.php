<?php
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
?>
<div class="sz-panel">
    <p><?= Html::a('Создать форму', ['create'], ['class' => 'btn btn-success']) ?></p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['attribute' => 'title', 'label' => 'Форма', 'value' => static fn($model) => $model->title ?: $model->name],
            'slug',
            [
                'attribute' => 'is_active',
                'format' => 'raw',
                'label' => 'Активна',
                'value' => static function ($model) {
                    if ((bool) $model->is_active) {
                        return Html::tag('span', 'Активна', ['class' => 'badge text-bg-success']);
                    }

                    return Html::tag('span', 'Выключена', ['class' => 'badge text-bg-secondary']);
                },
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{update} {fields} {code} {subs} {delete}',
                'contentOptions' => ['class' => 'action-column'],
                'buttons' => [
                    'update' => static function ($url) {
                        return Html::a('<span class="material-symbols-rounded">edit</span>', $url, [
                            'class' => 'sz-row-action',
                            'aria-label' => 'Редактировать',
                            'title' => 'Редактировать',
                        ]);
                    },
                    'fields' => static function ($url, $model) {
                        return Html::a('<span class="material-symbols-rounded">view_list</span>', ['fields', 'id' => $model->id], [
                            'class' => 'sz-row-action',
                            'aria-label' => 'Поля',
                            'title' => 'Поля',
                        ]);
                    },
                    'code' => static function ($url, $model) {
                        return Html::a('<span class="material-symbols-rounded">code</span>', ['code', 'id' => $model->id], [
                            'class' => 'sz-row-action',
                            'aria-label' => 'Код вставки',
                            'title' => 'Код вставки',
                        ]);
                    },
                    'subs' => static function ($url, $model) {
                        return Html::a('<span class="material-symbols-rounded">mail</span>', ['submissions', 'id' => $model->id], [
                            'class' => 'sz-row-action',
                            'aria-label' => 'Заявки',
                            'title' => 'Заявки',
                        ]);
                    },
                    'delete' => static function ($url) {
                        return Html::a('<span class="material-symbols-rounded">delete</span>', $url, [
                            'class' => 'sz-row-action',
                            'aria-label' => 'Удалить',
                            'title' => 'Удалить',
                            'data-method' => 'post',
                            'data-confirm' => 'Удалить эту форму?',
                        ]);
                    },
                ],
            ],
        ],
    ]) ?>
</div>
