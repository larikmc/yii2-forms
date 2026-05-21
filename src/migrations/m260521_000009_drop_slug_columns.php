<?php

use yii\db\Migration;

class m260521_000009_drop_slug_columns extends Migration
{
    public function safeUp()
    {
        $formTable = $this->db->schema->getTableSchema('{{%forms_form}}', true);
        if ($formTable && isset($formTable->columns['slug'])) {
            $this->dropColumn('{{%forms_form}}', 'slug');
        }

        $fieldTable = $this->db->schema->getTableSchema('{{%forms_field}}', true);
        if ($fieldTable && isset($fieldTable->columns['slug'])) {
            $this->dropColumn('{{%forms_field}}', 'slug');
        }
    }

    public function safeDown()
    {
        $formTable = $this->db->schema->getTableSchema('{{%forms_form}}', true);
        if ($formTable && !isset($formTable->columns['slug'])) {
            $this->addColumn('{{%forms_form}}', 'slug', $this->string()->null());
        }

        $fieldTable = $this->db->schema->getTableSchema('{{%forms_field}}', true);
        if ($fieldTable && !isset($fieldTable->columns['slug'])) {
            $this->addColumn('{{%forms_field}}', 'slug', $this->string()->null());
        }
    }
}
