<?php
namespace larikmc\forms\assets;
use yii\web\AssetBundle;
class FormsAsset extends AssetBundle { public $sourcePath='@vendor/larikmc/yii2-forms/src/web'; public $css=['css/forms.css']; public $js=['js/forms.js']; public $depends=['yii\\web\\YiiAsset','yii\\bootstrap5\\BootstrapAsset']; }
