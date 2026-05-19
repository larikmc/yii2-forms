<?php
namespace larikmc\forms\helpers;

use larikmc\forms\Module;
use yii\base\InvalidConfigException;

class TemplateHelper
{
    public static function resolve(string $type, ?string $template, ?string $viewAlias, Module $module): string
    {
        if ($viewAlias) { return $viewAlias; }
        $template = $template ?: ($type === 'modal' ? $module->defaultModalTemplate : $module->defaultFormTemplate);
        if ($module->customTemplatesPath) {
            $custom = $module->customTemplatesPath . '/' . $type . '/' . $template . '.php';
            if (is_file(\Yii::getAlias($custom))) { return $custom; }
        }
        $fallback = '@vendor/larikmc/yii2-forms/src/views/widgets/' . $type . '/' . $template . '.php';
        if (is_file(\Yii::getAlias($fallback))) { return $fallback; }
        throw new InvalidConfigException("Template not found: {$type}/{$template}");
    }
}
