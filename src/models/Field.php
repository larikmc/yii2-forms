<?php
namespace larikmc\forms\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Field extends ActiveRecord
{
    public const TYPE_TEXT='text'; public const TYPE_TEXTAREA='textarea'; public const TYPE_PHONE='phone'; public const TYPE_EMAIL='email'; public const TYPE_NUMBER='number'; public const TYPE_SELECT='select'; public const TYPE_CHECKBOX='checkbox'; public const TYPE_RADIO='radio'; public const TYPE_HIDDEN='hidden';
    public static function tableName(): string { return '{{%forms_field}}'; }
    public function behaviors(): array { return [TimestampBehavior::class]; }
    public static function types(): array { return [self::TYPE_TEXT,self::TYPE_TEXTAREA,self::TYPE_PHONE,self::TYPE_EMAIL,self::TYPE_NUMBER,self::TYPE_SELECT,self::TYPE_CHECKBOX,self::TYPE_RADIO,self::TYPE_HIDDEN]; }
    public function rules(): array { return [[['name','type'],'required'],[['options_json','validation_json','hint'],'string'],[['is_active'],'boolean'],[['name','type','placeholder','mask'],'string','max'=>255],[['name','placeholder','hint','mask','options_json','validation_json'],'filter','filter'=>'trim'],[['type'],'in','range'=>self::types()]]; }
    public function attributeLabels(): array { return ['name'=>'Название поля','type'=>'Тип поля','placeholder'=>'Плейсхолдер по умолчанию','hint'=>'Подсказка по умолчанию','mask'=>'Маска','options_json'=>'Опции (JSON)','validation_json'=>'Валидация (JSON)','is_active'=>'Активно']; }
    public function getFormFields() { return $this->hasMany(FormField::class, ['field_id'=>'id']); }
    public function getOptions(): array { $d=json_decode((string)$this->options_json,true); return is_array($d)?$d:[]; }
    public function beforeValidate(): bool
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        $this->name = $this->normalizeTitleCase($this->name);
        $this->placeholder = $this->normalizeTrimmed($this->placeholder);
        $this->hint = $this->normalizeTrimmed($this->hint);
        $this->mask = $this->normalizeTrimmed($this->mask);
        $this->options_json = $this->normalizeTrimmed($this->options_json);
        $this->validation_json = $this->normalizeTrimmed($this->validation_json);

        return true;
    }

    private function normalizeTrimmed(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);
        return $value === '' ? null : $value;
    }

    private function normalizeTitleCase(?string $value): ?string
    {
        $value = $this->normalizeTrimmed($value);
        if ($value === null) {
            return null;
        }

        $first = mb_substr($value, 0, 1);
        $rest = mb_substr($value, 1);

        return mb_strtoupper($first) . $rest;
    }
}
