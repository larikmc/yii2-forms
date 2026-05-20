<?php

use yii\db\Migration;

class m260520_000008_add_separate_button_classes_to_forms_form_table extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('{{%forms_form}}', 'submit_button_class', $this->string(255)->null()->after('button_class'));
        $this->addColumn('{{%forms_form}}', 'trigger_button_class', $this->string(255)->null()->after('submit_button_class'));
    }

    public function safeDown(): void
    {
        $this->dropColumn('{{%forms_form}}', 'trigger_button_class');
        $this->dropColumn('{{%forms_form}}', 'submit_button_class');
    }
}
