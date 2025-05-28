<?php

use yii\db\Migration;

class m231004_123504_create_houses_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%houses}}', [
            'ID' => $this->bigInteger()->notNull()->unique()->comment('Уникальный идентификатор записи'),
            'OBJECTID' => $this->bigInteger()->notNull()->comment('Глобальный идентификатор объекта'),
            'OBJECTGUID' => $this->char(36)->notNull()->comment('UUID объекта'),
            'CHANGEID' => $this->bigInteger()->notNull()->comment('ID транзакции изменений'),
            'HOUSENUM' => $this->string(50)->comment('Основной номер дома'),
            'ADDNUM1' => $this->string(50)->comment('Дополнительный номер 1'),
            'ADDNUM2' => $this->string(50)->comment('Дополнительный номер 2'),
            'HOUSETYPE' => $this->integer()->comment('Тип дома'),
            'ADDTYPE1' => $this->integer()->comment('Доп. тип 1'),
            'ADDTYPE2' => $this->integer()->comment('Доп. тип 2'),
            'OPERTYPEID' => $this->integer()->notNull()->comment('Статус действия'),
            'PREVID' => $this->bigInteger()->comment('Предыдущая запись'),
            'NEXTID' => $this->bigInteger()->comment('Следующая запись'),
            'UPDATEDATE' => $this->date()->notNull()->comment('Дата обновления'),
            'STARTDATE' => $this->date()->notNull()->comment('Начало действия'),
            'ENDDATE' => $this->date()->notNull()->comment('Окончание действия'),
            'ISACTUAL' => $this->tinyInteger(1)->notNull()->comment('Актуальность (0/1)'),
            'ISACTIVE' => $this->tinyInteger(1)->notNull()->comment('Активность (0/1)'),
        ]);

        // Первичный ключ
        $this->addPrimaryKey('PK_houses_ID', '{{%houses}}', 'ID');

//        // Индексы для часто используемых полей
//        $this->createIndex('idx_houses_OBJECTID', '{{%houses}}', 'OBJECTID');
//        $this->createIndex('idx_houses_OBJECTGUID', '{{%houses}}', 'OBJECTGUID');
//        $this->createIndex('idx_houses_CHANGEID', '{{%houses}}', 'CHANGEID');
    }

    public function safeDown()
    {
        $this->dropTable('{{%houses}}');
    }
}