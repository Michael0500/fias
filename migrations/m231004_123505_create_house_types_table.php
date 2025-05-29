<?php

use yii\db\Migration;

class m231004_123505_create_house_types_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%house_types}}', [
            'ID' => $this->bigInteger()->notNull()->unique()->comment('Уникальный идентификатор записи'),
            'NAME' => $this->string(50)->notNull()->comment('Наименование типа'),
            'SHORTNAME' => $this->string(50)->comment('Краткое наименование'),
            'DESC' => $this->string(250)->comment('Описание типа'),
            'UPDATEDATE' => $this->date()->notNull()->comment('Дата обновления'),
            'STARTDATE' => $this->date()->notNull()->comment('Начало действия'),
            'ENDDATE' => $this->date()->notNull()->comment('Окончание действия'),
            'ISACTIVE' => $this->boolean()->notNull()->comment('Активность (true/false)'),
        ]);

        // Первичный ключ
        $this->addPrimaryKey('PK_house_types_ID', '{{%house_types}}', 'ID');

//        // Индексы для часто используемых полей
//        $this->createIndex('idx_house_types_NAME', '{{%house_types}}', 'NAME');
//        $this->createIndex('idx_house_types_ISACTIVE', '{{%house_types}}', 'ISACTIVE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%house_types}}');
    }
}