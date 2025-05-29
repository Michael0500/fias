<?php

use yii\db\Migration;

class m231004_123501_create_apartment_types_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%apartment_types}}', [
            'ID' => $this->bigInteger()->notNull()->unique()->comment('Уникальный идентификатор записи'),
            'NAME' => $this->string(50)->notNull()->comment('Наименование'),
            'SHORTNAME' => $this->string(50)->comment('Краткое наименование'),
            'DESC' => $this->string(250)->comment('Описание'),
            'UPDATEDATE' => $this->date()->notNull()->comment('Дата обновления'),
            'STARTDATE' => $this->date()->notNull()->comment('Начало действия'),
            'ENDDATE' => $this->date()->notNull()->comment('Окончание действия'),
            'ISACTIVE' => $this->boolean()->notNull()->comment('Активность (true/false)'),
        ]);

        // Первичный ключ
        $this->addPrimaryKey('PK_apartment_types_ID', '{{%apartment_types}}', 'ID');

//        // Индексы для часто используемых полей
//        $this->createIndex('idx_apartment_types_NAME', '{{%apartment_types}}', 'NAME');
//        $this->createIndex('idx_apartment_types_ISACTIVE', '{{%apartment_types}}', 'ISACTIVE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%apartment_types}}');
    }
}