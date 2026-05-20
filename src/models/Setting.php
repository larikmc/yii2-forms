<?php

namespace larikmc\forms\models;

use yii\db\ActiveRecord;

class Setting extends ActiveRecord
{
    public const KEY_NOTIFICATION_EMAILS = 'notification_emails';
    public const KEY_DEFAULT_SUBMIT_BUTTON_CLASS = 'default_submit_button_class';
    public const KEY_DEFAULT_TRIGGER_BUTTON_CLASS = 'default_trigger_button_class';
    public const KEY_CONSENT_TEXT_HTML = 'consent_text_html';

    public static function tableName(): string
    {
        return '{{%forms_setting}}';
    }

    public function rules(): array
    {
        return [
            [['key'], 'required'],
            [['value'], 'string'],
            [['key'], 'string', 'max' => 190],
            [['key'], 'unique'],
        ];
    }

    public static function getValue(string $key, ?string $default = null): ?string
    {
        $model = static::find()->where(['key' => $key])->one();
        return $model?->value ?? $default;
    }

    public static function setValue(string $key, ?string $value): void
    {
        $model = static::find()->where(['key' => $key])->one();
        if (!$model) {
            $model = new static();
            $model->key = $key;
        }

        $model->value = $value;
        $model->save(false);
    }

    public static function hasSettingsTable(): bool
    {
        static $result;
        if ($result !== null) {
            return $result;
        }

        try {
            $result = static::getDb()->getTableSchema(static::tableName(), true) !== null;
        } catch (\Throwable) {
            $result = false;
        }

        return $result;
    }
}
