<?php

use yii\db\Migration;

class m260520_000007_add_button_class_to_forms_form_table extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('{{%forms_form}}', 'button_class', $this->string(255)->null()->after('submit_label'));
    }

    public function safeDown(): void
    {
        $this->dropColumn('{{%forms_form}}', 'button_class');
    }
}
