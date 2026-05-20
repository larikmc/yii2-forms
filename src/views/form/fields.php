<?php
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;
/** @var \larikmc\forms\models\Form $model */
/** @var \larikmc\forms\models\FormField $linkModel */
/** @var \larikmc\forms\models\Field[] $availableFields */
/** @var \yii\data\ActiveDataProvider $dataProvider */
?>
<?= $this->render('_tabs', ['model' => $model, 'active' => 'fields']) ?>

<div class="sz-panel mb-3">
    <h5>Добавить поле в форму</h5>
    <?php $f = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-6"><?= $f->field($linkModel, 'field_id')->dropDownList(ArrayHelper::map($availableFields, 'id', 'name'), ['prompt' => 'Выберите поле']) ?></div>
        <div class="col-md-3"><?= $f->field($linkModel, 'sort_order')->input('number') ?></div>
        <div class="col-md-3"><?= $f->field($linkModel, 'is_required')->checkbox() ?></div>
    </div>
    <?= Html::submitButton('Добавить поле', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end(); ?>
</div>

<div class="sz-panel">
    <h5>Поля формы</h5>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Поле</th><th>Тип</th><th>Порядок</th><th>Обязательное</th><th>Плейсхолдер</th><th>Подсказка</th><th>Активно</th><th>Действие</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($dataProvider->getModels() as $formField): ?>
            <?php $rowForm = ActiveForm::begin(['action' => ['update-field', 'id' => $model->id, 'formFieldId' => $formField->id], 'options' => ['class' => 'm-0']]); ?>
            <tr>
                <td><?= Html::encode($formField->field?->name) ?></td>
                <td><?= Html::encode($formField->field?->type) ?></td>
                <td><?= $rowForm->field($formField, 'sort_order')->input('number')->label(false) ?></td>
                <td><?= $rowForm->field($formField, 'is_required')->checkbox(['label' => false]) ?></td>
                <td><?= $rowForm->field($formField, 'placeholder_override')->textInput()->label(false) ?></td>
                <td><?= $rowForm->field($formField, 'hint_override')->textInput()->label(false) ?></td>
                <td><?= $rowForm->field($formField, 'is_active')->checkbox(['label' => false])->hint(false) ?></td>
                <td>
                    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-sm btn-success']) ?>
                    <?= Html::a('Удалить', ['delete-field', 'id' => $model->id, 'formFieldId' => $formField->id], ['class' => 'btn btn-sm btn-outline-danger', 'data-method' => 'post', 'data-confirm' => 'Удалить поле из формы?']) ?>
                </td>
            </tr>
            <?php ActiveForm::end(); ?>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
