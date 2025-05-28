<?php

use yii\db\Migration;

class m231004_123500_create_apartments_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%apartments}}', [
            'ID' => $this->bigInteger()->notNull()->unique()->comment('Уникальный идентификатор записи'),
            'OBJECTID' => $this->bigInteger()->notNull()->comment('Глобальный идентификатор объекта'),
            'OBJECTGUID' => $this->char(36)->notNull()->comment('UUID объекта'),
            'CHANGEID' => $this->bigInteger()->notNull()->comment('ID транзакции изменений'),
            'NUMBER' => $this->string(50)->notNull()->comment('Номер комнаты'),
            'APARTTYPE' => $this->integer()->notNull()->comment('Тип комнаты (2 цифры)'),
            'OPERTYPEID' => $this->integer()->notNull()->comment('Статус действия'),
            'PREVID' => $this->bigInteger()->comment('Связь с предыдущей записью'),
            'NEXTID' => $this->bigInteger()->comment('Связь с последующей записью'),
            'UPDATEDATE' => $this->date()->notNull()->comment('Дата обновления'),
            'STARTDATE' => $this->date()->notNull()->comment('Начало действия'),
            'ENDDATE' => $this->date()->notNull()->comment('Окончание действия'),
            'ISACTUAL' => $this->tinyInteger(1)->notNull()->comment('Актуальность (0/1)'),
            'ISACTIVE' => $this->tinyInteger(1)->notNull()->comment('Активность (0/1)'),
        ]);

        // Первичный ключ
        $this->addPrimaryKey('PK_apartments_ID', '{{%apartments}}', 'ID');

//        // Индексы для часто используемых полей
//        $this->createIndex('idx_apartments_OBJECTID', '{{%apartments}}', 'OBJECTID');
//        $this->createIndex('idx_apartments_OBJECTGUID', '{{%apartments}}', 'OBJECTGUID');
//        $this->createIndex('idx_apartments_CHANGEID', '{{%apartments}}', 'CHANGEID');
    }

    public function safeDown()
    {
        $this->dropTable('{{%apartments}}');
    }
}