<?php

use yii\db\Migration;

class m231004_123516_create_room_types_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%room_types}}', [
            'ID' => $this->integer()->notNull()->unique()->comment('Идентификатор типа (ключ)'),
            'NAME' => $this->string(100)->notNull()->comment('Наименование'),
            'SHORTNAME' => $this->string(50)->comment('Краткое наименование'),
            'DESC' => $this->string(250)->comment('Описание'),
            'UPDATEDATE' => $this->date()->notNull()->comment('Дата обновления'),
            'STARTDATE' => $this->date()->notNull()->comment('Начало действия'),
            'ENDDATE' => $this->date()->notNull()->comment('Окончание действия'),
            'ISACTIVE' => $this->tinyInteger(1)->notNull()->comment('Активность (0/1)'),
        ]);

        // Первичный ключ
        $this->addPrimaryKey('PK_room_types_ID', '{{%room_types}}', 'ID');

//        // Индексы для часто используемых полей
//        $this->createIndex('idx_room_types_ISACTIVE', '{{%room_types}}', 'ISACTIVE');
//        $this->createIndex('idx_room_types_NAME', '{{%room_types}}', 'NAME');
    }

    public function safeDown()
    {
        $this->dropTable('{{%room_types}}');
    }
}