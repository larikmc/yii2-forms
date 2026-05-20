<?php
namespace larikmc\forms\assets;
use yii\web\AssetBundle;
class FormsModalAsset extends AssetBundle
{
    public $sourcePath = '@vendor/larikmc/yii2-forms/src/web';
    public $css = ['css/forms.css'];
    public $js = ['js/forms-modal.js'];
    public $depends = [];
    public $publishOptions = ['forceCopy' => YII_DEBUG];
}
