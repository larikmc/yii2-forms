<?php
namespace larikmc\forms\models;

use yii\db\ActiveRecord;

class FormField extends ActiveRecord
{
    public static function tableName(): string { return '{{%forms_form_field}}'; }
    public function rules(): array { return [[['form_id','field_id'],'required'],[['form_id','field_id','sort_order'],'integer'],[['is_required','is_active'],'boolean'],[['label_override','placeholder_override','hint_override'],'string','max'=>255],[['settings_json'],'string'],[['form_id','field_id'],'unique','targetAttribute'=>['form_id','field_id'],'message'=>'Это поле уже добавлено в форму.']]; }
    public function attributeLabels(): array { return ['form_id'=>'Форма','field_id'=>'Поле','sort_order'=>'Порядок','is_required'=>'Обязательное','label_override'=>'Подпись (переопределение)','placeholder_override'=>'Плейсхолдер (переопределение)','hint_override'=>'Подсказка (переопределение)','settings_json'=>'Настройки (JSON)','is_active'=>'Активно']; }
    public function getForm() { return $this->hasOne(Form::class,['id'=>'form_id']); }
    public function getField() { return $this->hasOne(Field::class,['id'=>'field_id']); }
    public function getEffectiveLabel(): ?string { return $this->label_override ?: $this->field?->name; }
    public function getEffectivePlaceholder(): ?string { return $this->placeholder_override ?: $this->field?->placeholder; }
    public function getEffectiveHint(): ?string { return $this->hint_override ?: $this->field?->hint; }
}
