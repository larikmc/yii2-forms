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

    public function init(): void
    {
        parent::init();
        $this->controllerNamespace = 'larikmc\\forms\\controllers';
    }
}
