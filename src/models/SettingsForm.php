<?php

namespace larikmc\forms\models;

use larikmc\forms\Module;
use yii\base\Model;

class SettingsForm extends Model
{
    public ?string $notification_emails = null;

    public function rules(): array
    {
        return [
            [['notification_emails'], 'filter', 'filter' => 'trim'],
            [['notification_emails'], 'string'],
            [['notification_emails'], 'validateNotificationEmails'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'notification_emails' => 'E-mail по умолчанию для всех форм',
        ];
    }

    public function attributeHints(): array
    {
        return [
            'notification_emails' => 'Можно указать один или несколько e-mail через запятую. Эти адреса будут использоваться для всех форм, если у конкретной формы не задано своё значение.',
        ];
    }

    public function loadValues(): void
    {
        $module = \Yii::$app->getModule('forms');
        $fallback = $module instanceof Module && is_string($module->notificationEmails)
            ? $module->notificationEmails
            : null;

        $this->notification_emails = Setting::hasSettingsTable()
            ? Setting::getValue(Setting::KEY_NOTIFICATION_EMAILS, $fallback)
            : $fallback;
    }

    public function saveValues(): bool
    {
        if (!$this->validate() || !Setting::hasSettingsTable()) {
            return false;
        }

        Setting::setValue(Setting::KEY_NOTIFICATION_EMAILS, $this->notification_emails);
        return true;
    }

    public function validateNotificationEmails(string $attribute): void
    {
        $value = (string) $this->$attribute;
        if (trim($value) === '') {
            return;
        }

        $parts = preg_split('/[\s,;]+/', $value, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($parts as $email) {
            if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                $this->addError($attribute, 'Укажите корректные e-mail адреса через запятую.');
                return;
            }
        }
    }
}
