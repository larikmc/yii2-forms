<?php
namespace larikmc\forms\widgets;

use larikmc\forms\assets\FormsModalAsset;
use larikmc\forms\helpers\TemplateHelper;
use larikmc\forms\models\Form;
use larikmc\forms\Module;
use yii\base\Widget;

class FormModalWidget extends Widget
{
    public string $slug;
    public string $buttonLabel = 'Оставить заявку';
    public ?string $formTemplate = null;
    public ?string $modalTemplate = null;
    public ?string $formView = null;
    public ?string $modalView = null;
    public array $buttonOptions = [];
    public array $modalOptions = [];
    public array $formOptions = [];

    public function run(): string
    {
        $module = \Yii::$app->getModule('forms');
        if (!$module instanceof Module) { return ''; }
        $form = Form::find()->where(['slug'=>$this->slug, 'is_active'=>1])->one();
        if (!$form) { return YII_DEBUG ? "<!-- Form \"{$this->slug}\" not found or inactive -->" : ''; }

        FormsModalAsset::register($this->view);
        $uid = $this->getId() . '-' . $form->slug . '-' . substr(md5(uniqid('', true)), 0, 8);
        $modalId = 'forms-modal-' . $form->slug . '-' . substr(md5(uniqid('', true)), 0, 8);
        $formHtml = FormWidget::widget(['slug'=>$this->slug,'template'=>$this->formTemplate,'view'=>$this->formView,'formOptions'=>$this->formOptions]);

        return $this->render(TemplateHelper::resolve('modal', $this->modalTemplate, $this->modalView, $module), compact('form','uid','modalId','formHtml') + [
            'buttonLabel'=>$this->buttonLabel,'buttonOptions'=>$this->buttonOptions,'modalOptions'=>$this->modalOptions,'widget'=>$this,
        ]);
    }
}
