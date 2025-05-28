<?php

use yii\db\Migration;

class m231004_123502_create_car_places_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%car_places}}', [
            'ID' => $this->bigInteger()->notNull()->unique()->comment('Уникальный идентификатор записи'),
            'OBJECTID' => $this->bigInteger()->notNull()->comment('Глобальный идентификатор объекта'),
            'OBJECTGUID' => $this->char(36)->notNull()->comment('UUID объекта'),
            'CHANGEID' => $this->bigInteger()->notNull()->comment('ID транзакции изменений'),
            'NUMBER' => $this->string(50)->notNull()->comment('Номер машиноместа'),
            'OPERTYPEID' => $this->integer()->notNull()->comment('Статус действия (1-2 цифры)'),
            'PREVID' => $this->bigInteger()->comment('Связь с предыдущей записью'),
            'NEXTID' => $this->bigInteger()->comment('Связь с последующей записью'),
            'UPDATEDATE' => $this->date()->notNull()->comment('Дата обновления'),
            'STARTDATE' => $this->date()->notNull()->comment('Начало действия'),
            'ENDDATE' => $this->date()->notNull()->comment('Окончание действия'),
            'ISACTUAL' => $this->tinyInteger(1)->notNull()->comment('Актуальность (0/1)'),
            'ISACTIVE' => $this->tinyInteger(1)->notNull()->comment('Активность (0/1)'),
        ]);

        // Первичный ключ
        $this->addPrimaryKey('PK_car_places_ID', '{{%car_places}}', 'ID');

//        // Индексы для часто используемых полей
//        $this->createIndex('idx_car_places_OBJECTID', '{{%car_places}}', 'OBJECTID');
//        $this->createIndex('idx_car_places_OBJECTGUID', '{{%car_places}}', 'OBJECTGUID');
//        $this->createIndex('idx_car_places_CHANGEID', '{{%car_places}}', 'CHANGEID');
    }

    public function safeDown()
    {
        $this->dropTable('{{%car_places}}');
    }
}