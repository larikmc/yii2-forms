<?php
use larikmc\forms\models\Field;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$typeItems = [
    Field::TYPE_TEXT => 'Текст (одна строка)',
    Field::TYPE_TEXTAREA => 'Текст (несколько строк)',
    Field::TYPE_PHONE => 'Телефон',
    Field::TYPE_EMAIL => 'E-mail',
    Field::TYPE_NUMBER => 'Число',
    Field::TYPE_SELECT => 'Список (выбор из списка)',
    Field::TYPE_CHECKBOX => 'Флажок (да/нет)',
    Field::TYPE_RADIO => 'Переключатель (один вариант)',
    Field::TYPE_HIDDEN => 'Скрытое поле',
];
?>
<div class="sz-panel">
<?php
$f = ActiveForm::begin();
echo $f->field($model, 'name');
echo $f->field($model, 'type')->dropDownList($typeItems);
echo $f->field($model, 'placeholder')->hint('Серый текст-подсказка внутри поля до ввода. Пример: "Введите телефон".');
echo $f->field($model, 'hint')->hint('Маленькая подсказка под полем. Пример: "Мы не передаём данные третьим лицам".');
echo $f->field($model, 'mask')->hint('Шаблон ввода (обычно для телефона). Пример: +7 (999) 999-99-99. Если не нужно, оставьте пустым.');
echo $f->field($model, 'options_json')->textarea()->hint('Нужно для типов "Список", "Флажок", "Переключатель".');
echo $f->field($model, 'validation_json')->textarea();
echo $f->field($model, 'is_active')->checkbox();
echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);
ActiveForm::end();
?>
</div>
