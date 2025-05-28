<?php

use yii\db\Migration;

class m231004_123508_create_normative_doc_kinds_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%normative_doc_kinds}}', [
            'ID' => $this->integer()->notNull()->unique()->comment('Идентификатор вида документа'),
            'NAME' => $this->string(500)->notNull()->comment('Наименование вида документа'),
        ]);

        // Первичный ключ
        $this->addPrimaryKey('PK_normative_doc_kinds_ID', '{{%normative_doc_kinds}}', 'ID');
    }

    public function safeDown()
    {
        $this->dropTable('{{%normative_doc_kinds}}');
    }
}