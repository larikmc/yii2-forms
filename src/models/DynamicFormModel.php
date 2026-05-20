<?php
namespace larikmc\forms\models;

use yii\base\DynamicModel;

class DynamicFormModel extends DynamicModel
{
    /** @var FormField[] */
    private array $formFields = [];
    private array $labels = [];
    private array $hints = [];

    public function __construct(array $formFields, $config = [])
    {
        $this->formFields = $formFields;
        $attributes = [];
        foreach ($formFields as $formField) {
            $slug = $formField->field->slug;
            $attributes[$slug] = null;
            $this->labels[$slug] = $formField->label_override ?: $formField->field->name;
            $this->hints[$slug] = $formField->getEffectiveHint() ?: '';
        }
        parent::__construct($attributes, $config);
    }

    public function rules(): array
    {
        $rules = [];
        foreach ($this->formFields as $formField) {
            $field = $formField->field;
            $slug = $field->slug;
            if ($formField->is_required) { $rules[] = [[$slug], 'required']; }
            $baseRule = match ($field->type) {
                Field::TYPE_EMAIL => [[$slug], 'email'],
                Field::TYPE_NUMBER => [[$slug], 'number'],
                Field::TYPE_CHECKBOX => [[$slug], 'boolean'],
                Field::TYPE_SELECT, Field::TYPE_RADIO => [[$slug], 'in', 'range' => array_values(array_filter(array_map(fn($o) => $o['value'] ?? null, $field->getOptions())))],
                default => [[$slug], 'string'],
            };
            $rules[] = $baseRule;

            if ($field->type === Field::TYPE_PHONE) {
                $mask = $field->mask ?: '+7 (999) 999-99-99';
                $rules[] = [[$slug], function (string $attribute) use ($mask) {
                    $value = (string) $this->$attribute;
                    $value = trim($value);

                    if ($value === '') {
                        return;
                    }

                    $expectedDigits = substr_count($mask, '9') + preg_match_all('/(?<!9)\d/', preg_replace('/9/', '', $mask), $matches);
                    $actualDigits = preg_match_all('/\d/', $value, $matches);

                    if (str_contains($value, '_') || $actualDigits < $expectedDigits) {
                        $this->addError($attribute, 'Введите корректный телефон.');
                    }
                }];
            }
        }
        return $rules;
    }

    public function attributeLabels(): array { return $this->labels; }
    public function attributeHints(): array { return $this->hints; }

    public function getSubmissionData(): array
    {
        $data = [];
        foreach ($this->getAttributes() as $attribute => $value) {
            $label = $this->labels[$attribute] ?? $attribute;
            $data[$label] = $value;
        }
        return $data;
    }

    public function getDynamicAttributes(): array { return array_keys($this->getAttributes()); }
}
