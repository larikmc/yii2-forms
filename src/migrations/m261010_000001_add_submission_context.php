<?php

use yii\db\Migration;

final class m261010_000001_add_submission_context extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('{{%forms_submission}}', 'context_json', $this->text()->null());
    }

    public function safeDown(): void
    {
        $this->dropColumn('{{%forms_submission}}', 'context_json');
    }
}
