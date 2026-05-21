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
        $rules = [
            [['name', 'submit_label'], 'required'],
            [['description', 'success_message'], 'string'],
            [['is_active', 'store_submissions'], 'boolean'],
            [['name', 'title', 'submit_label', 'button_class', 'submit_button_class', 'trigger_button_class'], 'string', 'max' => 255],
            [['name', 'title', 'description', 'submit_label', 'success_message', 'button_class', 'submit_button_class', 'trigger_button_class'], 'filter', 'filter' => 'trim'],
        ];

        if (self::hasNotificationEmailsColumn()) {
            $rules[] = [['notification_emails'], 'string'];
            $rules[] = [['notification_emails'], 'filter', 'filter' => 'trim'];
            $rules[] = [['notification_emails'], 'validateNotificationEmails'];
        }

        if (!self::hasButtonClassColumn()) {
            $rules = array_values(array_filter($rules, static function (array $rule): bool {
                return !in_array('button_class', (array) ($rule[0] ?? []), true);
            }));
        }
        if (!self::hasSubmitButtonClassColumn()) {
            $rules = array_values(array_filter($rules, static function (array $rule): bool {
                return !in_array('submit_button_class', (array) ($rule[0] ?? []), true);
            }));
        }
        if (!self::hasTriggerButtonClassColumn()) {
            $rules = array_values(array_filter($rules, static function (array $rule): bool {
                return !in_array('trigger_button_class', (array) ($rule[0] ?? []), true);
            }));
        }

        return $rules;
    }

    public function beforeValidate(): bool
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        $this->title = $this->normalizeTitleCase($this->title);
        $this->description = $this->normalizeTrimmed($this->description);
        $this->submit_label = $this->normalizeTitleCase($this->submit_label);
        $this->success_message = $this->normalizeTrimmed($this->success_message);
        $this->notification_emails = $this->normalizeTrimmed($this->notification_emails);
        $this->submit_button_class = $this->normalizeTrimmed($this->submit_button_class);
        $this->trigger_button_class = $this->normalizeTrimmed($this->trigger_button_class);

        $this->store_submissions = true;

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

    public function attributeLabels(): array
    {
        return ['name'=>'Название формы (видно только вам)','title'=>'Заголовок формы (на сайте)','description'=>'Описание','submit_label'=>'Текст кнопки','submit_button_class'=>'Класс кнопки отправки','trigger_button_class'=>'Класс кнопки открытия popup','button_class'=>'Класс кнопки (legacy)','success_message'=>'Сообщение после отправки','notification_emails'=>'Отправлять на e-mail','is_active'=>'Активна','store_submissions'=>'Сохранять заявки'];
    }

    public function getEffectiveSubmitButtonClass(): string
    {
        $module = \Yii::$app->getModule('forms');
        $moduleClass = $module instanceof \larikmc\forms\Module ? $module->getDefaultSubmitButtonClass() : '';
        $legacy = self::hasButtonClassColumn() ? trim((string) $this->getAttribute('button_class')) : '';
        $current = self::hasSubmitButtonClassColumn() ? trim((string) $this->getAttribute('submit_button_class')) : '';
        return trim($current !== '' ? $current : ($legacy !== '' ? $legacy : $moduleClass));
    }

    public function getEffectiveTriggerButtonClass(): string
    {
        $module = \Yii::$app->getModule('forms');
        $moduleClass = $module instanceof \larikmc\forms\Module ? $module->getDefaultTriggerButtonClass() : '';
        $legacy = self::hasButtonClassColumn() ? trim((string) $this->getAttribute('button_class')) : '';
        $current = self::hasTriggerButtonClassColumn() ? trim((string) $this->getAttribute('trigger_button_class')) : '';
        return trim($current !== '' ? $current : ($legacy !== '' ? $legacy : $moduleClass));
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

    public static function hasButtonClassColumn(): bool
    {
        static $result;
        if ($result !== null) {
            return $result;
        }

        try {
            $result = self::getTableSchema()->getColumn('button_class') !== null;
        } catch (\Throwable) {
            $result = false;
        }

        return $result;
    }

    public static function hasSubmitButtonClassColumn(): bool
    {
        static $result;
        if ($result !== null) {
            return $result;
        }
        try {
            $result = self::getTableSchema()->getColumn('submit_button_class') !== null;
        } catch (\Throwable) {
            $result = false;
        }
        return $result;
    }

    public static function hasTriggerButtonClassColumn(): bool
    {
        static $result;
        if ($result !== null) {
            return $result;
        }
        try {
            $result = self::getTableSchema()->getColumn('trigger_button_class') !== null;
        } catch (\Throwable) {
            $result = false;
        }
        return $result;
    }

    public function getFormFields() { return $this->hasMany(FormField::class, ['form_id' => 'id'])->orderBy(['sort_order'=>SORT_ASC,'id'=>SORT_ASC]); }
    public function getFields() { return $this->hasMany(Field::class, ['id' => 'field_id'])->via('formFields'); }
    public function getSubmissions() { return $this->hasMany(Submission::class, ['form_id' => 'id']); }
}
