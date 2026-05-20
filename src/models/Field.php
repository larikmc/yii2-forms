<?php
namespace larikmc\forms\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;

class Field extends ActiveRecord
{
    public const TYPE_TEXT='text'; public const TYPE_TEXTAREA='textarea'; public const TYPE_PHONE='phone'; public const TYPE_EMAIL='email'; public const TYPE_NUMBER='number'; public const TYPE_SELECT='select'; public const TYPE_CHECKBOX='checkbox'; public const TYPE_RADIO='radio'; public const TYPE_HIDDEN='hidden';
    public static function tableName(): string { return '{{%forms_field}}'; }
    public function behaviors(): array { return [TimestampBehavior::class]; }
    public static function types(): array { return [self::TYPE_TEXT,self::TYPE_TEXTAREA,self::TYPE_PHONE,self::TYPE_EMAIL,self::TYPE_NUMBER,self::TYPE_SELECT,self::TYPE_CHECKBOX,self::TYPE_RADIO,self::TYPE_HIDDEN]; }
    public function rules(): array { return [[['name','type'],'required'],[['options_json','validation_json','hint'],'string'],[['is_active'],'boolean'],[['name','slug','type','placeholder','mask'],'string','max'=>255],[['name','slug','placeholder','hint','mask','options_json','validation_json'],'filter','filter'=>'trim'],[['slug'],'filter','filter'=>static fn($value)=>is_string($value)?trim(mb_strtolower($value)):$value],[['slug'],'default','value'=>null],[['slug'],'match','pattern'=>'/^[a-zA-Z0-9_-]+$/'],[['slug'],'required'],[['slug'],'unique'],[['type'],'in','range'=>self::types()]]; }
    public function attributeLabels(): array { return ['name'=>'Название поля','slug'=>'Слаг','type'=>'Тип поля','placeholder'=>'Плейсхолдер по умолчанию','hint'=>'Подсказка по умолчанию','mask'=>'Маска','options_json'=>'Опции (JSON)','validation_json'=>'Валидация (JSON)','is_active'=>'Активно']; }
    public function getFormFields() { return $this->hasMany(FormField::class, ['field_id'=>'id']); }
    public function getOptions(): array { $d=json_decode((string)$this->options_json,true); return is_array($d)?$d:[]; }
    public function beforeValidate(): bool
    {
        if (!parent::beforeValidate()) {
            return false;
        }
        if (!$this->slug && $this->name) {
            $base = Inflector::slug((string)$this->name, '-');
            $base = $base !== '' ? $base : 'field';
            $slug = $base;
            $i = 2;
            while (self::find()->andWhere(['slug' => $slug])->andFilterWhere(['not', ['id' => $this->id]])->exists()) {
                $slug = $base . '-' . $i;
                $i++;
            }
            $this->slug = $slug;
        }
        return true;
    }
}
