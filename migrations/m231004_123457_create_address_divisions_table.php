<?php

use yii\db\Migration;

class m231004_123457_create_address_divisions_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%address_divisions}}', [
            'ID' => $this->bigInteger()->notNull()->unique()->comment('Уникальный идентификатор записи'),
            'PARENTID' => $this->bigInteger()->notNull()->comment('Идентификатор родительского элемента'),
            'CHILDID' => $this->bigInteger()->notNull()->comment('Идентификатор дочернего элемента'),
            'CHANGEID' => $this->bigInteger()->notNull()->comment('ID изменившей транзакции'),
        ]);

        // Первичный ключ
        $this->addPrimaryKey('PK_address_divisions_ID', '{{%address_divisions}}', 'ID');

//        // Индексы для связей
//        $this->createIndex('idx_address_divisions_PARENTID', '{{%address_divisions}}', 'PARENTID');
//        $this->createIndex('idx_address_divisions_CHILDID', '{{%address_divisions}}', 'CHILDID');
//        $this->createIndex('idx_address_divisions_CHANGEID', '{{%address_divisions}}', 'CHANGEID');
    }

    public function safeDown()
    {
        $this->dropTable('{{%address_divisions}}');
    }
}