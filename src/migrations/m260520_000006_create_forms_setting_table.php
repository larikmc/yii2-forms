<?php

use yii\db\Migration;

class m260520_000006_create_forms_setting_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%forms_setting}}', [
            'id' => $this->primaryKey(),
            'key' => $this->string(190)->notNull(),
            'value' => $this->text()->null(),
        ]);

        $this->createIndex('idx-forms_setting-key', '{{%forms_setting}}', 'key', true);
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%forms_setting}}');
    }
}
