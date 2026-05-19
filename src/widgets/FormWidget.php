<?php
namespace larikmc\forms\widgets;

use larikmc\forms\assets\FormsAsset;
use larikmc\forms\helpers\TemplateHelper;
use larikmc\forms\models\DynamicFormModel;
use larikmc\forms\models\Form;
use larikmc\forms\Module;
use yii\base\Widget;

class FormWidget extends Widget
{
    public string $slug;
    public ?string $template = null;
    public ?string $view = null;
    public array $options = [];
    public array $formOptions = [];
    public bool $ajax = false;

    public function run(): string
    {
        $module = \Yii::$app->getModule('forms');
        if (!$module instanceof Module) { return ''; }

        $form = Form::find()->where(['slug'=>$this->slug, 'is_active'=>1])->one();
        if (!$form) {
            return YII_DEBUG ? "<!-- Form \"{$this->slug}\" not found or inactive -->" : '';
        }

        FormsAsset::register($this->view);
        $uid = $this->getId() . '-' . $form->slug . '-' . substr(md5(uniqid('', true)), 0, 8);
        $formFields = $form->getFormFields()->andWhere(['is_active'=>1])->with('field')->all();
        $model = new DynamicFormModel($formFields);

        return $this->render(TemplateHelper::resolve('form', $this->template, $this->view, $module), [
            'model'=>$model,'form'=>$form,'formFields'=>$formFields,'uid'=>$uid,'widget'=>$this,
        ]);
    }
}
