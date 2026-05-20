<?php

namespace larikmc\forms\helpers;

use yii\db\Connection;

class SchemaInstaller
{
    public static function ensureOptionalSchema(): void
    {
        try {
            $db = \Yii::$app->db;
            if (!$db instanceof Connection) {
                return;
            }

            $tablePrefix = $db->tablePrefix;
            $formsFormTable = self::realTableName($db, '{{%forms_form}}');
            $formsSettingTable = self::realTableName($db, '{{%forms_setting}}');

            if ($db->getTableSchema($formsFormTable, true) === null) {
                return;
            }

            self::ensureNotificationEmailsColumn($db, $formsFormTable);
            self::ensureButtonClassColumn($db, $formsFormTable);
            self::ensureSubmitButtonClassColumn($db, $formsFormTable);
            self::ensureTriggerButtonClassColumn($db, $formsFormTable);
            self::ensureSettingsTable($db, $formsSettingTable);
        } catch (\Throwable $e) {
            \Yii::warning($e->getMessage(), 'forms.schema');
        }
    }

    private static function ensureNotificationEmailsColumn(Connection $db, string $formsFormTable): void
    {
        $schema = $db->getTableSchema($formsFormTable, true);
        if ($schema === null || $schema->getColumn('notification_emails') !== null) {
            return;
        }

        $db->createCommand()->addColumn($formsFormTable, 'notification_emails', $db->schema->createColumnSchemaBuilder('text'))->execute();
        $db->schema->refreshTableSchema($formsFormTable);
    }

    private static function ensureSettingsTable(Connection $db, string $formsSettingTable): void
    {
        if ($db->getTableSchema($formsSettingTable, true) !== null) {
            return;
        }

        $db->createCommand()->createTable($formsSettingTable, [
            'id' => 'pk',
            'key' => 'varchar(190) NOT NULL',
            'value' => 'text NULL',
        ])->execute();

        $db->createCommand()->createIndex('idx-forms_setting-key', $formsSettingTable, 'key', true)->execute();
        $db->schema->refreshTableSchema($formsSettingTable);
    }

    private static function ensureButtonClassColumn(Connection $db, string $formsFormTable): void
    {
        $schema = $db->getTableSchema($formsFormTable, true);
        if ($schema === null || $schema->getColumn('button_class') !== null) {
            return;
        }

        $db->createCommand()->addColumn($formsFormTable, 'button_class', $db->schema->createColumnSchemaBuilder('string', 255))->execute();
        $db->schema->refreshTableSchema($formsFormTable);
    }

    private static function ensureSubmitButtonClassColumn(Connection $db, string $formsFormTable): void
    {
        $schema = $db->getTableSchema($formsFormTable, true);
        if ($schema === null || $schema->getColumn('submit_button_class') !== null) {
            return;
        }

        $db->createCommand()->addColumn($formsFormTable, 'submit_button_class', $db->schema->createColumnSchemaBuilder('string', 255))->execute();
        $db->schema->refreshTableSchema($formsFormTable);
    }

    private static function ensureTriggerButtonClassColumn(Connection $db, string $formsFormTable): void
    {
        $schema = $db->getTableSchema($formsFormTable, true);
        if ($schema === null || $schema->getColumn('trigger_button_class') !== null) {
            return;
        }

        $db->createCommand()->addColumn($formsFormTable, 'trigger_button_class', $db->schema->createColumnSchemaBuilder('string', 255))->execute();
        $db->schema->refreshTableSchema($formsFormTable);
    }

    private static function realTableName(Connection $db, string $name): string
    {
        return preg_replace('/{{%(.+?)}}/', $db->tablePrefix . '$1', $name);
    }
}
