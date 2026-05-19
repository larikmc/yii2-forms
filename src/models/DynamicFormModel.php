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
            $this->labels[$slug] = $formField->getEffectiveLabel() ?: $formField->field->name;
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
            $rules[] = match ($field->type) {
                Field::TYPE_EMAIL => [[$slug], 'email'],
                Field::TYPE_NUMBER => [[$slug], 'number'],
                Field::TYPE_CHECKBOX => [[$slug], 'boolean'],
                Field::TYPE_SELECT, Field::TYPE_RADIO => [[$slug], 'in', 'range' => array_values(array_filter(array_map(fn($o) => $o['value'] ?? null, $field->getOptions())))],
                default => [[$slug], 'string'],
            };
        }
        return $rules;
    }

    public function attributeLabels(): array { return $this->labels; }
    public function attributeHints(): array { return $this->hints; }
    public function getSubmissionData(): array { return $this->getAttributes(); }
    public function getDynamicAttributes(): array { return array_keys($this->getAttributes()); }
}
