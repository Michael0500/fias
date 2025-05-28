<?php

use yii\db\Migration;

class m231004_123515_create_rooms_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%rooms}}', [
            'ID' => $this->bigInteger()->notNull()->unique()->comment('Уникальный идентификатор записи'),
            'OBJECTID' => $this->bigInteger()->notNull()->comment('Глобальный идентификатор объекта'),
            'OBJECTGUID' => $this->char(36)->notNull()->comment('UUID объекта'),
            'CHANGEID' => $this->bigInteger()->notNull()->comment('ID транзакции изменений'),
            'NUMBER' => $this->string(50)->notNull()->comment('Номер комнаты/офиса'),
            'ROOMTYPE' => $this->tinyInteger(1)->notNull()->comment('Тип комнаты (1 цифра)'),
            'OPERTYPEID' => $this->tinyInteger(2)->notNull()->comment('Статус действия (1-2 цифры)'),
            'PREVID' => $this->bigInteger()->comment('Предыдущая запись'),
            'NEXTID' => $this->bigInteger()->comment('Следующая запись'),
            'UPDATEDATE' => $this->date()->notNull()->comment('Дата обновления'),
            'STARTDATE' => $this->date()->notNull()->comment('Начало действия'),
            'ENDDATE' => $this->date()->notNull()->comment('Окончание действия'),
            'ISACTUAL' => $this->tinyInteger(1)->notNull()->comment('Актуальность (0/1)'),
            'ISACTIVE' => $this->tinyInteger(1)->notNull()->comment('Активность (0/1)'),
        ]);

        // Первичный ключ
        $this->addPrimaryKey('PK_rooms_ID', '{{%rooms}}', 'ID');

//        // Индексы для часто используемых полей
//        $this->createIndex('idx_rooms_OBJECTID', '{{%rooms}}', 'OBJECTID');
//        $this->createIndex('idx_rooms_OBJECTGUID', '{{%rooms}}', 'OBJECTGUID');
//        $this->createIndex('idx_rooms_ROOMTYPE', '{{%rooms}}', 'ROOMTYPE');
//        $this->createIndex('idx_rooms_ISACTUAL', '{{%rooms}}', 'ISACTUAL');
//        $this->createIndex('idx_rooms_ISACTIVE', '{{%rooms}}', 'ISACTIVE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%rooms}}');
    }
}