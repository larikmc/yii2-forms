<?php
namespace larikmc\forms\assets;
use yii\web\AssetBundle;
class FormsAdminAsset extends AssetBundle
{
    public $sourcePath = '@vendor/larikmc/yii2-forms/src/web';
    public $css = ['css/forms-admin.css'];
    public $js = ['js/forms-admin.js'];
    public $depends = ['yii\\web\\YiiAsset'];

    public function init(): void
    {
        parent::init();

        // Ensure local development reflects module CSS changes without manual asset cleanup.
        if (YII_ENV_DEV) {
            $this->publishOptions['forceCopy'] = true;
        }
    }
}
