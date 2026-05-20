<?php

namespace larikmc\forms\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;

class Form extends ActiveRecord
{
    public static function tableName(): string { return '{{%forms_form}}'; }

    public function behaviors(): array { return [TimestampBehavior::class]; }

    public function rules(): array
    {
        $rules = [
            [['title', 'submit_label'], 'required'],
            [['description', 'success_message'], 'string'],
            [['is_active', 'store_submissions'], 'boolean'],
            [['name', 'slug', 'title', 'submit_label'], 'string', 'max' => 255],
            [['name', 'slug', 'title', 'description', 'submit_label', 'success_message'], 'filter', 'filter' => 'trim'],
            [['slug'], 'filter', 'filter' => static fn($value) => is_string($value) ? trim(mb_strtolower($value)) : $value],
            [['slug'], 'default', 'value' => null],
            [['slug'], 'match', 'pattern' => '/^[a-zA-Z0-9_-]+$/'],
            [['slug'], 'required'],
            [['slug'], 'unique'],
        ];

        if (self::hasNotificationEmailsColumn()) {
            $rules[] = [['notification_emails'], 'string'];
            $rules[] = [['notification_emails'], 'filter', 'filter' => 'trim'];
            $rules[] = [['notification_emails'], 'validateNotificationEmails'];
        }

        return $rules;
    }

    public function beforeValidate(): bool
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        if ($this->title) {
            $this->name = $this->title;
        }

        $this->store_submissions = true;

        if (!$this->slug && $this->name) {
            $base = Inflector::slug((string)$this->name, '-');
            $base = $base !== '' ? $base : 'form';
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

    public function attributeLabels(): array
    {
        return ['name'=>'Служебное название','slug'=>'Слаг','title'=>'Заголовок формы','description'=>'Описание','submit_label'=>'Текст кнопки','success_message'=>'Сообщение после отправки','notification_emails'=>'E-mail для уведомлений','is_active'=>'Активна','store_submissions'=>'Сохранять заявки'];
    }

    public function getNotificationEmailsList(): array
    {
        $module = \Yii::$app->getModule('forms');
        if (!$module instanceof \larikmc\forms\Module) {
            return [];
        }

        if (self::hasNotificationEmailsColumn() && trim((string) $this->getAttribute('notification_emails')) !== '') {
            return $module->normalizeEmails($this->getAttribute('notification_emails'));
        }

        return $module->getNotificationEmails();
    }

    public function validateNotificationEmails(string $attribute): void
    {
        if (!self::hasNotificationEmailsColumn()) {
            return;
        }

        $value = (string) $this->$attribute;
        if (trim($value) === '') {
            return;
        }

        $parts = preg_split('/[\s,;]+/', $value, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($parts as $email) {
            $email = trim($email);
            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->addError($attribute, 'Укажите корректные e-mail адреса через запятую.');
                return;
            }
        }
    }

    public static function hasNotificationEmailsColumn(): bool
    {
        static $result;
        if ($result !== null) {
            return $result;
        }

        try {
            $result = self::getTableSchema()->getColumn('notification_emails') !== null;
        } catch (\Throwable) {
            $result = false;
        }

        return $result;
    }

    public function getFormFields() { return $this->hasMany(FormField::class, ['form_id' => 'id'])->orderBy(['sort_order'=>SORT_ASC,'id'=>SORT_ASC]); }
    public function getFields() { return $this->hasMany(Field::class, ['id' => 'field_id'])->via('formFields'); }
    public function getSubmissions() { return $this->hasMany(Submission::class, ['form_id' => 'id']); }
}
