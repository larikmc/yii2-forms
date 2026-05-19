<?php

namespace larikmc\forms\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Form extends ActiveRecord
{
    public static function tableName(): string { return '{{%forms_form}}'; }

    public function behaviors(): array { return [TimestampBehavior::class]; }

    public function rules(): array
    {
        return [
            [['name', 'slug', 'submit_label'], 'required'],
            [['description', 'success_message'], 'string'],
            [['is_active', 'store_submissions'], 'boolean'],
            [['name', 'slug', 'title', 'submit_label'], 'string', 'max' => 255],
            [['slug'], 'match', 'pattern' => '/^[a-zA-Z0-9_-]+$/'],
            [['slug'], 'unique'],
        ];
    }

    public function attributeLabels(): array
    {
        return ['name'=>'Название','slug'=>'Slug','title'=>'Заголовок','description'=>'Описание','submit_label'=>'Текст кнопки','success_message'=>'Сообщение успеха','is_active'=>'Активна','store_submissions'=>'Сохранять заявки'];
    }

    public function getFormFields() { return $this->hasMany(FormField::class, ['form_id' => 'id'])->orderBy(['sort_order'=>SORT_ASC,'id'=>SORT_ASC]); }
    public function getFields() { return $this->hasMany(Field::class, ['id' => 'field_id'])->via('formFields'); }
    public function getSubmissions() { return $this->hasMany(Submission::class, ['form_id' => 'id']); }
}
