<?php

namespace larikmc\forms\models;

use larikmc\forms\Module;
use yii\base\Model;

class SettingsForm extends Model
{
    public ?string $notification_emails = null;
    public ?string $default_submit_button_class = null;
    public ?string $default_trigger_button_class = null;
    public ?string $consent_text_html = null;

    public function rules(): array
    {
        return [
            [['notification_emails'], 'filter', 'filter' => 'trim'],
            [['default_submit_button_class', 'default_trigger_button_class'], 'filter', 'filter' => 'trim'],
            [['notification_emails', 'consent_text_html'], 'string'],
            [['default_submit_button_class', 'default_trigger_button_class'], 'string', 'max' => 255],
            [['notification_emails'], 'validateNotificationEmails'],
            [['consent_text_html'], 'required'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'notification_emails' => 'E-mail по умолчанию для всех форм',
            'default_submit_button_class' => 'Класс кнопки отправки (по умолчанию)',
            'default_trigger_button_class' => 'Класс кнопки открытия popup (по умолчанию)',
            'consent_text_html' => 'Текст согласия (HTML)',
        ];
    }

    public function attributeHints(): array
    {
        return [
            'notification_emails' => 'Можно указать один или несколько e-mail через запятую. Эти адреса будут использоваться для всех форм, если у конкретной формы не задано своё значение.',
            'default_submit_button_class' => 'Применяется к кнопке отправки формы, если в конкретной форме класс не задан.',
            'default_trigger_button_class' => 'Применяется к кнопке открытия popup, если в конкретной форме класс не задан.',
            'consent_text_html' => 'Можно использовать HTML, например ссылку на политику: <a href="/privacy">Политикой обработки персональных данных</a>.',
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
        $this->default_submit_button_class = Setting::hasSettingsTable()
            ? Setting::getValue(Setting::KEY_DEFAULT_SUBMIT_BUTTON_CLASS, $module instanceof Module ? $module->defaultSubmitButtonClass : '')
            : ($module instanceof Module ? $module->defaultSubmitButtonClass : '');
        $this->default_trigger_button_class = Setting::hasSettingsTable()
            ? Setting::getValue(Setting::KEY_DEFAULT_TRIGGER_BUTTON_CLASS, $module instanceof Module ? $module->defaultTriggerButtonClass : '')
            : ($module instanceof Module ? $module->defaultTriggerButtonClass : '');
        $this->consent_text_html = Setting::hasSettingsTable()
            ? Setting::getValue(Setting::KEY_CONSENT_TEXT_HTML, $module instanceof Module ? $module->defaultConsentTextHtml : '')
            : ($module instanceof Module ? $module->defaultConsentTextHtml : '');
    }

    public function saveValues(): bool
    {
        if (!$this->validate() || !Setting::hasSettingsTable()) {
            return false;
        }

        Setting::setValue(Setting::KEY_NOTIFICATION_EMAILS, $this->notification_emails);
        Setting::setValue(Setting::KEY_DEFAULT_SUBMIT_BUTTON_CLASS, $this->default_submit_button_class);
        Setting::setValue(Setting::KEY_DEFAULT_TRIGGER_BUTTON_CLASS, $this->default_trigger_button_class);
        Setting::setValue(Setting::KEY_CONSENT_TEXT_HTML, $this->consent_text_html);
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
