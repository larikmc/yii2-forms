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
    public int $formId = 0;
    public ?string $template = null;
    public ?string $view = null;
    public array $options = [];
    public array $formOptions = [];
    public bool $ajax = false;
    public bool $showHeading = true;
    public bool $showDescription = true;

    public function run(): string
    {
        $module = \Yii::$app->getModule('forms');
        if (!$module instanceof Module) { return ''; }

        if ($this->formId <= 0) {
            return YII_DEBUG ? '<!-- Form id is required -->' : '';
        }
        $form = Form::find()->where(['is_active' => 1, 'id' => $this->formId])->one();
        if (!$form) {
            return YII_DEBUG ? "<!-- Form (id={$this->formId}) not found or inactive -->" : '';
        }

        FormsAsset::register($this->getView());
        $uid = $this->getId() . '-form-' . $form->id . '-' . substr(md5(uniqid('', true)), 0, 8);
        $formFields = $form->getFormFields()->andWhere(['is_active'=>1])->with('field')->all();
        $model = new DynamicFormModel($formFields);

        return $this->render(TemplateHelper::resolve('form', $this->template, $this->view, $module), [
            'model'=>$model,'form'=>$form,'formFields'=>$formFields,'uid'=>$uid,'widget'=>$this,
        ]);
    }
}
