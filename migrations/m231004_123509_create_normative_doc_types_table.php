<?php

use yii\db\Migration;

class m231004_123509_create_normative_doc_types_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%normative_doc_types}}', [
            'ID' => $this->integer()->notNull()->unique()->comment('Идентификатор типа документа'),
            'NAME' => $this->string(500)->notNull()->comment('Наименование типа документа'),
            'STARTDATE' => $this->date()->notNull()->comment('Дата начала действия'),
            'ENDDATE' => $this->date()->notNull()->comment('Дата окончания действия'),
        ]);

        // Первичный ключ
        $this->addPrimaryKey('PK_normative_doc_types_ID', '{{%normative_doc_types}}', 'ID');
    }

    public function safeDown()
    {
        $this->dropTable('{{%normative_doc_types}}');
    }
}