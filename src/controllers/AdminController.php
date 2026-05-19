<?php
namespace larikmc\forms\controllers;

use larikmc\forms\Module;
use yii\filters\AccessControl;
use yii\web\Controller;

abstract class AdminController extends Controller
{
    public function behaviors(): array
    {
        $module = $this->module;
        return ['access'=>['class'=>AccessControl::class,'rules'=>[['allow'=>true,'roles'=>[$module instanceof Module ? $module->adminPermission : 'admin']]]]];
    }
}
