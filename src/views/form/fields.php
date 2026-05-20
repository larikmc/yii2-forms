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
    <?php $bulkForm = ActiveForm::begin(['action' => ['save-fields', 'id' => $model->id], 'options' => ['class' => 'm-0']]); ?>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Поле</th><th>Тип</th><th>Порядок</th><th>Обязательное</th><th>Плейсхолдер</th><th>Подсказка</th><th>Активно</th><th>Действие</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($dataProvider->getModels() as $formField): ?>
            <tr>
                <td><?= Html::encode($formField->field?->name) ?></td>
                <td><?= Html::encode($formField->field?->type) ?></td>
                <td><?= Html::activeInput('number', $formField, "[{$formField->id}]sort_order", ['class' => 'form-control']) ?></td>
                <td><?= Html::activeCheckbox($formField, "[{$formField->id}]is_required", ['label' => false, 'class' => 'form-check-input']) ?></td>
                <td><?= Html::activeTextInput($formField, "[{$formField->id}]placeholder_override", ['class' => 'form-control']) ?></td>
                <td><?= Html::activeTextInput($formField, "[{$formField->id}]hint_override", ['class' => 'form-control']) ?></td>
                <td><?= Html::activeCheckbox($formField, "[{$formField->id}]is_active", ['label' => false, 'class' => 'form-check-input']) ?></td>
                <td>
                    <?= Html::a('Удалить', ['delete-field', 'id' => $model->id, 'formFieldId' => $formField->id], ['class' => 'btn btn-sm btn-outline-danger', 'data-method' => 'post', 'data-confirm' => 'Удалить поле из формы?']) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    <?php ActiveForm::end(); ?>
</div>
