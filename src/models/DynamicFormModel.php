<?php
namespace larikmc\forms\models;

use yii\base\DynamicModel;

class DynamicFormModel extends DynamicModel
{
    /** @var FormField[] */
    private array $formFields = [];
    private array $labels = [];
    private array $hints = [];
    private array $fieldAttributeMap = [];

    public function __construct(array $formFields, $config = [])
    {
        $this->formFields = $formFields;
        $attributes = [];
        foreach ($formFields as $formField) {
            $attribute = self::attributeNameByFieldId((int) $formField->field_id);
            $this->fieldAttributeMap[(int) $formField->field_id] = $attribute;
            $attributes[$attribute] = null;
            $this->labels[$attribute] = $formField->label_override ?: $formField->field->name;
            $this->hints[$attribute] = $formField->getEffectiveHint() ?: '';
        }
        parent::__construct($attributes, $config);
    }

    public function rules(): array
    {
        $rules = [];
        foreach ($this->formFields as $formField) {
            $field = $formField->field;
            $attribute = $this->attributeByFieldId((int) $formField->field_id);
            if ($formField->is_required) { $rules[] = [[$attribute], 'required']; }
            $baseRule = match ($field->type) {
                Field::TYPE_EMAIL => [[$attribute], 'email'],
                Field::TYPE_NUMBER => [[$attribute], 'number'],
                Field::TYPE_CHECKBOX => [[$attribute], 'boolean'],
                Field::TYPE_SELECT, Field::TYPE_RADIO => [[$attribute], 'in', 'range' => array_values(array_filter(array_map(fn($o) => $o['value'] ?? null, $field->getOptions())))],
                default => [[$attribute], 'string'],
            };
            $rules[] = $baseRule;

            if ($field->type === Field::TYPE_PHONE) {
                $mask = $field->mask ?: '+7 (999) 999-99-99';
                $rules[] = [[$attribute], function (string $attribute) use ($mask) {
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

    public function attributeByFieldId(int $fieldId): string
    {
        return $this->fieldAttributeMap[$fieldId] ?? self::attributeNameByFieldId($fieldId);
    }

    public static function attributeNameByFieldId(int $fieldId): string
    {
        return 'field_' . $fieldId;
    }
}
