<?php

use yii\db\Migration;

class m260520_000005_add_notification_emails_to_forms_form_table extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('{{%forms_form}}', 'notification_emails', $this->text()->null()->after('success_message'));
    }

    public function safeDown(): void
    {
        $this->dropColumn('{{%forms_form}}', 'notification_emails');
    }
}
