<?php
namespace larikmc\forms\helpers;

use larikmc\forms\models\Field;
use larikmc\forms\models\FormField;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

class FieldRenderHelper
{
    public static function render(ActiveForm $form, $model, FormField $formField): string
    {
        $field = $formField->field;
        $slug = $field->slug;
        $placeholder = $formField->getEffectivePlaceholder();
        $hint = $formField->getEffectiveHint();
        $label = $formField->getEffectiveLabel();

        if ($field->type === Field::TYPE_HIDDEN) { return Html::activeHiddenInput($model, $slug); }

        $activeField = match ($field->type) {
            Field::TYPE_TEXTAREA => $form->field($model, $slug)->textarea(['rows'=>4, 'placeholder'=>$placeholder]),
            Field::TYPE_PHONE => $form->field($model, $slug)->widget(MaskedInput::class,['mask'=>$field->mask ?: '+7 (999) 999-99-99','options'=>['placeholder'=>$placeholder]]),
            Field::TYPE_EMAIL => $form->field($model, $slug)->input('email',['placeholder'=>$placeholder]),
            Field::TYPE_NUMBER => $form->field($model, $slug)->input('number',['placeholder'=>$placeholder]),
            Field::TYPE_SELECT => $form->field($model, $slug)->dropDownList(self::options($field),['prompt'=>'Выберите']),
            Field::TYPE_CHECKBOX => $form->field($model, $slug)->checkbox(),
            Field::TYPE_RADIO => $form->field($model, $slug)->radioList(self::options($field)),
            default => $form->field($model, $slug)->textInput(['placeholder'=>$placeholder]),
        };

        return $activeField->label($label)->hint($hint);
    }

    private static function options(Field $field): array
    {
        return ArrayHelper::map($field->getOptions(), 'value', 'label');
    }
}
