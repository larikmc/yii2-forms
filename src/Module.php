<?php

namespace larikmc\forms;

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

    public function init(): void
    {
        parent::init();
        $this->controllerNamespace = 'larikmc\\forms\\controllers';
    }

    public function getNotificationEmails(): array
    {
        return $this->normalizeEmails($this->notificationEmails);
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
