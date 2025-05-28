<?php

use yii\db\Migration;

class m231004_123511_create_operation_types_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%operation_types}}', [
            'ID' => $this->integer()->notNull()->unique()->comment('Идентификатор статуса (ключ)'),
            'NAME' => $this->string(100)->notNull()->comment('Наименование'),
            'SHORTNAME' => $this->string(100)->comment('Краткое наименование'),
            'DESC' => $this->string(250)->comment('Описание'),
            'UPDATEDATE' => $this->date()->notNull()->comment('Дата обновления'),
            'STARTDATE' => $this->date()->notNull()->comment('Начало действия'),
            'ENDDATE' => $this->date()->notNull()->comment('Окончание действия'),
            'ISACTIVE' => $this->boolean()->notNull()->comment('Активность (true/false)'),
        ]);

        // Первичный ключ
        $this->addPrimaryKey('PK_operation_types_ID', '{{%operation_types}}', 'ID');

//        // Индексы для часто используемых полей
//        $this->createIndex('idx_operation_types_ISACTIVE', '{{%operation_types}}', 'ISACTIVE');
//        $this->createIndex('idx_operation_types_NAME', '{{%operation_types}}', 'NAME');
    }

    public function safeDown()
    {
        $this->dropTable('{{%operation_types}}');
    }
}