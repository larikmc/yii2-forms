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
    public function rules(): array { return [[['name','slug','type'],'required'],[['options_json','validation_json','hint'],'string'],[['is_active'],'boolean'],[['name','slug','type','label','placeholder','mask'],'string','max'=>255],[['slug'],'match','pattern'=>'/^[a-zA-Z0-9_-]+$/'],[['slug'],'unique'],[['type'],'in','range'=>self::types()]]; }
    public function getFormFields() { return $this->hasMany(FormField::class, ['field_id'=>'id']); }
    public function getOptions(): array { $d=json_decode((string)$this->options_json,true); return is_array($d)?$d:[]; }
}
