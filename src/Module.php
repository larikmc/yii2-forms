<?php

namespace larikmc\forms;

use larikmc\forms\helpers\SchemaInstaller;
use larikmc\forms\models\Setting;
use yii\base\Module as BaseModule;

class Module extends BaseModule
{
    public string $adminPermission = 'admin';
    public bool $storeClientInfo = true;
    public string $defaultFormTemplate = 'default';
    public string $defaultModalTemplate = 'default';
    public ?string $customTemplatesPath = '@app/views/forms/templates';
    public array $submitRoute = ['/forms/submit/index'];
    public ?string $defaultSuccessRedirect = null;
    public array|string|null $notificationEmails = null;
    public string $defaultSubmitButtonClass = '';
    public string $defaultTriggerButtonClass = '';
    public string $defaultConsentTextHtml = 'Даю согласие на обработку персональных данных для обработки моего обращения и обратной связи со мной. Ознакомлен(а) с Политикой обработки персональных данных.';

    public function init(): void
    {
        parent::init();
        $this->controllerNamespace = 'larikmc\\forms\\controllers';
        SchemaInstaller::ensureOptionalSchema();
    }

    public function getNotificationEmails(): array
    {
        $stored = null;
        if (Setting::hasSettingsTable()) {
            $stored = Setting::getValue(Setting::KEY_NOTIFICATION_EMAILS);
        }

        return $this->normalizeEmails($stored ?: $this->notificationEmails);
    }

    public function getDefaultSubmitButtonClass(): string
    {
        if (Setting::hasSettingsTable()) {
            $stored = trim((string) Setting::getValue(Setting::KEY_DEFAULT_SUBMIT_BUTTON_CLASS, ''));
            if ($stored !== '') {
                return $stored;
            }
        }

        return trim($this->defaultSubmitButtonClass);
    }

    public function getDefaultTriggerButtonClass(): string
    {
        if (Setting::hasSettingsTable()) {
            $stored = trim((string) Setting::getValue(Setting::KEY_DEFAULT_TRIGGER_BUTTON_CLASS, ''));
            if ($stored !== '') {
                return $stored;
            }
        }

        return trim($this->defaultTriggerButtonClass);
    }

    public function getConsentTextHtml(): string
    {
        if (Setting::hasSettingsTable()) {
            $stored = trim((string) Setting::getValue(Setting::KEY_CONSENT_TEXT_HTML, ''));
            if ($stored !== '') {
                return $stored;
            }
        }

        return $this->defaultConsentTextHtml;
    }

    public function normalizeEmails(array|string|null $emails): array
    {
        if (is_array($emails)) {
            $emails = implode(',', $emails);
        }

        if (!is_string($emails) || trim($emails) === '') {
            return [];
        }

        $result = [];
        foreach (preg_split('/[\s,;]+/', $emails, -1, PREG_SPLIT_NO_EMPTY) as $email) {
            $email = trim(mb_strtolower($email));
            if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $result[$email] = $email;
            }
        }

        return array_values($result);
    }
}
