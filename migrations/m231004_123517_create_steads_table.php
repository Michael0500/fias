<?php

use yii\db\Migration;

class m231004_123517_create_steads_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%steads}}', [
            'ID' => $this->bigInteger()->notNull()->comment('Уникальный идентификатор записи'),
            'OBJECTID' => $this->bigInteger()->notNull()->comment('Глобальный уникальный идентификатор объекта'),
            'OBJECTGUID' => $this->char(36)->notNull()->comment('UUID объекта'),
            'CHANGEID' => $this->bigInteger()->notNull()->comment('ID транзакции'),
            'NUMBER' => $this->string(250)->notNull()->comment('Номер участка'),
            'OPERTYPEID' => $this->string(2)->notNull()->comment('Тип операции'),
            'PREVID' => $this->bigInteger()->comment('Предыдущая запись'),
            'NEXTID' => $this->bigInteger()->comment('Следующая запись'),
            'UPDATEDATE' => $this->date()->notNull()->comment('Дата обновления'),
            'STARTDATE' => $this->date()->notNull()->comment('Начало действия'),
            'ENDDATE' => $this->date()->notNull()->comment('Окончание действия'),
            'ISACTUAL' => $this->tinyInteger(1)->notNull()->comment('Актуальность (0/1)'),
            'ISACTIVE' => $this->tinyInteger(1)->notNull()->comment('Активность (0/1)'),
        ]);

        // Первичный ключ
        $this->addPrimaryKey('PK_steads_ID', '{{%steads}}', 'ID');

//        // Индексы для часто используемых полей
//        $this->createIndex('idx_steads_OBJECTID', '{{%steads}}', 'OBJECTID');
//        $this->createIndex('idx_steads_OBJECTGUID', '{{%steads}}', 'OBJECTGUID');
//        $this->createIndex('idx_steads_CHANGEID', '{{%steads}}', 'CHANGEID');
//        $this->createIndex('idx_steads_OPERTYPEID', '{{%steads}}', 'OPERTYPEID');
//        $this->createIndex('idx_steads_PREVID', '{{%steads}}', 'PREVID');
//        $this->createIndex('idx_steads_NEXTID', '{{%steads}}', 'NEXTID');
//        $this->createIndex('idx_steads_ISACTUAL', '{{%steads}}', 'ISACTUAL');
//        $this->createIndex('idx_steads_ISACTIVE', '{{%steads}}', 'ISACTIVE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%steads}}');
    }
}