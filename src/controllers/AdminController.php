<?php
namespace larikmc\forms\controllers;

use larikmc\forms\assets\FormsAdminAsset;
use larikmc\forms\Module;
use yii\filters\AccessControl;
use yii\web\Controller;

abstract class AdminController extends Controller
{
    public function render($view, $params = [])
    {
        FormsAdminAsset::register($this->getView());
        $navigation = $this->getView()->renderFile(
            $this->module->getViewPath() . DIRECTORY_SEPARATOR . '_navigation.php',
            ['activeController' => $this->id],
            $this,
        );

        return $this->renderContent($navigation . $this->renderPartial($view, $params));
    }

    public function behaviors(): array
    {
        $module = $this->module;
        return ['access'=>['class'=>AccessControl::class,'rules'=>[['allow'=>true,'roles'=>[$module instanceof Module ? $module->adminPermission : 'admin']]]]];
    }
}
