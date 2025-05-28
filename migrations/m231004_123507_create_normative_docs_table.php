<?php

use yii\db\Migration;

class m231004_123507_create_normative_docs_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%normative_docs}}', [
            'ID' => $this->bigInteger()->notNull()->unique()->comment('Уникальный идентификатор документа'),
            'NAME' => $this->text()->notNull()->comment('Наименование документа'),
            'DATE' => $this->date()->notNull()->comment('Дата документа'),
            'NUMBER' => $this->string(150)->notNull()->comment('Номер документа'),
            'TYPE' => $this->integer()->notNull()->comment('Тип документа (до 10 цифр)'),
            'KIND' => $this->integer()->notNull()->comment('Вид документа (до 10 цифр)'),
            'UPDATEDATE' => $this->date()->notNull()->comment('Дата обновления'),
            'ORGNAME' => $this->string(255)->comment('Орган, создавший документ'),
            'REGNUM' => $this->string(100)->comment('Номер госрегистрации'),
            'REGDATE' => $this->date()->comment('Дата госрегистрации'),
            'ACCDATE' => $this->date()->comment('Дата вступления в силу'),
            'COMMENT' => $this->text()->comment('Комментарий'),
        ]);

        // Первичный ключ
        $this->addPrimaryKey('PK_normative_docs_ID', '{{%normative_docs}}', 'ID');

//        // Индексы для часто используемых полей
//        $this->createIndex('idx_normative_docs_TYPE', '{{%normative_docs}}', 'TYPE');
//        $this->createIndex('idx_normative_docs_KIND', '{{%normative_docs}}', 'KIND');
//        $this->createIndex('idx_normative_docs_DATE', '{{%normative_docs}}', 'DATE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%normative_docs}}');
    }
}