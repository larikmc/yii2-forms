<?php
namespace larikmc\forms\helpers;

use larikmc\forms\models\Field;
use larikmc\forms\models\FormField;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class FieldRenderHelper
{
    public static function render($model, FormField $formField): string
    {
        $field = $formField->field;
        $slug = method_exists($model, 'attributeByFieldId')
            ? $model->attributeByFieldId((int) $field->id)
            : ('field_' . $field->id);
        $placeholder = $formField->getEffectivePlaceholder();
        $hint = $formField->getEffectiveHint();
        $label = $formField->label_override ?: $field->name;

        if ($field->type === Field::TYPE_HIDDEN) {
            return Html::activeHiddenInput($model, $slug);
        }

        $input = match ($field->type) {
            Field::TYPE_TEXTAREA => Html::activeTextarea($model, $slug, self::inputOptions($model, $slug, $label, $placeholder, $formField, 'textarea', ['rows' => 4, 'class' => 'forms-control forms-control--textarea'])),
            Field::TYPE_PHONE => Html::activeInput('tel', $model, $slug, self::inputOptions($model, $slug, $label, $placeholder ?: ($field->mask ?: '+7 (999) 999-99-99'), $formField, 'phone', ['class' => 'forms-control', 'inputmode' => 'tel'])),
            Field::TYPE_EMAIL => Html::activeInput('email', $model, $slug, self::inputOptions($model, $slug, $label, $placeholder, $formField, 'email', ['class' => 'forms-control'])),
            Field::TYPE_NUMBER => Html::activeInput('number', $model, $slug, self::inputOptions($model, $slug, $label, $placeholder, $formField, 'number', ['class' => 'forms-control'])),
            Field::TYPE_SELECT => Html::activeDropDownList($model, $slug, self::options($field), self::inputOptions($model, $slug, $label, null, $formField, 'select', ['prompt' => 'Выберите', 'class' => 'forms-control forms-select'])),
            Field::TYPE_CHECKBOX => self::renderCheckbox($model, $slug, $label, $hint, $formField),
            Field::TYPE_RADIO => self::renderRadioList($model, $slug, $label, $hint, $formField),
            default => Html::activeTextInput($model, $slug, self::inputOptions($model, $slug, $label, $placeholder, $formField, 'text', ['class' => 'forms-control'])),
        };

        if ($field->type === Field::TYPE_CHECKBOX || $field->type === Field::TYPE_RADIO) {
            return $input;
        }

        $labelHtml = Html::tag('label', Html::encode($label), ['class' => 'forms-label', 'for' => Html::getInputId($model, $slug)]);
        $hintHtml = $hint ? Html::tag('div', Html::encode($hint), ['class' => 'forms-hint']) : '';
        $errorHtml = Html::tag('div', '', [
            'class' => 'forms-error',
            'data-forms-error' => $slug,
            'id' => 'forms-error-' . $slug,
        ]);
        return Html::tag('div', $labelHtml . $input . $hintHtml . $errorHtml, [
            'class' => 'forms-field',
            'data-forms-field-wrap' => $slug,
            'data-forms-field-type' => $field->type,
            'data-forms-required' => $formField->is_required ? 1 : 0,
        ]);
    }

    private static function options(Field $field): array
    {
        return ArrayHelper::map($field->getOptions(), 'value', 'label');
    }

    private static function inputOptions($model, string $slug, string $label, ?string $placeholder, FormField $formField, string $type, array $extra = []): array
    {
        $field = $formField->field;

        return array_merge([
            'id' => Html::getInputId($model, $slug),
            'placeholder' => $placeholder,
            'data-forms-field' => $slug,
            'data-forms-label' => $label,
            'data-forms-type' => $type,
            'data-forms-required' => $formField->is_required ? 1 : 0,
            'data-forms-mask' => $field->type === Field::TYPE_PHONE ? ($field->mask ?: '+7 (999) 999-99-99') : null,
            'aria-describedby' => 'forms-error-' . $slug,
        ], $extra);
    }

    private static function renderCheckbox($model, string $slug, string $label, ?string $hint, FormField $formField): string
    {
        $input = Html::activeCheckbox($model, $slug, [
            'class' => 'forms-checkbox',
            'label' => Html::tag('span', Html::encode($label), ['class' => 'forms-checkbox__text']),
            'labelOptions' => ['class' => 'forms-checkbox-wrap'],
            'uncheck' => 0,
            'data-forms-field' => $slug,
            'data-forms-label' => $label,
            'data-forms-type' => 'checkbox',
            'data-forms-required' => $formField->is_required ? 1 : 0,
        ]);

        $hintHtml = $hint ? Html::tag('div', Html::encode($hint), ['class' => 'forms-hint']) : '';
        $errorHtml = Html::tag('div', '', ['class' => 'forms-error', 'data-forms-error' => $slug, 'id' => 'forms-error-' . $slug]);

        return Html::tag('div', $input . $hintHtml . $errorHtml, [
            'class' => 'forms-field forms-field--checkbox',
            'data-forms-field-wrap' => $slug,
            'data-forms-field-type' => 'checkbox',
            'data-forms-required' => $formField->is_required ? 1 : 0,
        ]);
    }

    private static function renderRadioList($model, string $slug, string $label, ?string $hint, FormField $formField): string
    {
        $items = Html::activeRadioList($model, $slug, self::options($formField->field), [
            'class' => 'forms-radio-list',
            'item' => static function ($index, $itemLabel, $name, $checked, $value) use ($slug, $label, $formField) {
                $input = Html::radio($name, $checked, [
                    'value' => $value,
                    'class' => 'forms-radio',
                    'data-forms-field' => $slug,
                    'data-forms-label' => $label,
                    'data-forms-type' => 'radio',
                    'data-forms-required' => $formField->is_required ? 1 : 0,
                ]);

                return Html::tag('label', $input . Html::tag('span', Html::encode($itemLabel), ['class' => 'forms-radio__text']), [
                    'class' => 'forms-radio-wrap',
                ]);
            },
            'unselect' => null,
        ]);

        $labelHtml = Html::tag('div', Html::encode($label), ['class' => 'forms-label']);
        $hintHtml = $hint ? Html::tag('div', Html::encode($hint), ['class' => 'forms-hint']) : '';
        $errorHtml = Html::tag('div', '', ['class' => 'forms-error', 'data-forms-error' => $slug, 'id' => 'forms-error-' . $slug]);

        return Html::tag('div', $labelHtml . $items . $hintHtml . $errorHtml, [
            'class' => 'forms-field forms-field--radio',
            'data-forms-field-wrap' => $slug,
            'data-forms-field-type' => 'radio',
            'data-forms-required' => $formField->is_required ? 1 : 0,
        ]);
    }
}
