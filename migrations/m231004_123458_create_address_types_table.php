<?php

use yii\db\Migration;

class m231004_123458_create_address_types_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%address_types}}', [
            'ID' => $this->bigInteger()->notNull()->unique()->comment('Идентификатор записи'),
            'LEVEL' => $this->integer()->notNull()->comment('Уровень адресного объекта'),
            'SHORTNAME' => $this->string(50)->notNull()->comment('Краткое наименование'),
            'NAME' => $this->string(250)->notNull()->comment('Полное наименование'),
            'DESC' => $this->string(250)->comment('Описание'),
            'UPDATEDATE' => $this->date()->notNull()->comment('Дата обновления'),
            'STARTDATE' => $this->date()->notNull()->comment('Начало действия'),
            'ENDDATE' => $this->date()->notNull()->comment('Окончание действия'),
            'ISACTIVE' => $this->boolean()->notNull()->comment('Активность (true/false)'),
        ]);

        // Первичный ключ
        $this->addPrimaryKey('PK_address_types_ID', '{{%address_types}}', 'ID');

//        // Индексы для часто используемых полей
//        $this->createIndex('idx_address_types_LEVEL', '{{%address_types}}', 'LEVEL');
//        $this->createIndex('idx_address_types_SHORTNAME', '{{%address_types}}', 'SHORTNAME');
    }

    public function safeDown()
    {
        $this->dropTable('{{%address_types}}');
    }
}