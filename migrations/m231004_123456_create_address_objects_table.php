<?php

use yii\db\Migration;

class m231004_123456_create_address_objects_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%address_objects}}', [
            'ID' => $this->bigInteger()->notNull()->unique()->comment('Уникальный идентификатор записи'),
            'OBJECTID' => $this->bigInteger()->notNull()->comment('Глобальный уникальный идентификатор'),
            'OBJECTGUID' => $this->char(36)->notNull()->comment('Глобальный уникальный идентификатор'),
            'CHANGEID' => $this->bigInteger()->notNull()->comment('ID изменившей транзакции'),
            'NAME' => $this->string(250)->notNull()->comment('Наименование'),
            'TYPENAME' => $this->string(50)->notNull()->comment('Тип объекта'),
            'LEVEL' => $this->string(10)->notNull()->comment('Уровень адресного объекта'),
            'OPERTYPEID' => $this->integer()->notNull()->comment('Статус действия над записью'),
            'PREVID' => $this->bigInteger()->comment('Связь с предыдущей записью'),
            'NEXTID' => $this->bigInteger()->comment('Связь с последующей записью'),
            'UPDATEDATE' => $this->date()->notNull()->comment('Дата обновления'),
            'STARTDATE' => $this->date()->notNull()->comment('Начало действия'),
            'ENDDATE' => $this->date()->notNull()->comment('Окончание действия'),
            'ISACTUAL' => $this->tinyInteger(1)->notNull()->comment('Актуальность (0/1)'),
            'ISACTIVE' => $this->tinyInteger(1)->notNull()->comment('Активность (0/1)'),
        ]);

        // Первичный ключ
        $this->addPrimaryKey('PK_address_objects_ID', '{{%address_objects}}', 'ID');

//        // Добавляем индексы для часто используемых полей
//        $this->createIndex('idx_address_objects_OBJECTID', '{{%address_objects}}', 'OBJECTID');
//        $this->createIndex('idx_address_objects_OBJECTGUID', '{{%address_objects}}', 'OBJECTGUID');
//        $this->createIndex('idx_address_objects_LEVEL', '{{%address_objects}}', 'LEVEL');
    }

    public function safeDown()
    {
        $this->dropTable('{{%address_objects}}');
    }
}