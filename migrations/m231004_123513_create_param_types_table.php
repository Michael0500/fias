<?php

use yii\db\Migration;

class m231004_123513_create_param_types_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%param_types}}', [
            'ID' => $this->bigInteger()->notNull()->unique()->comment('Уникальный идентификатор записи'),
            'NAME' => $this->string(50)->notNull()->comment('Наименование'),
            'CODE' => $this->string(50)->notNull()->comment('Краткое наименование (код)'),
            'DESC' => $this->string(120)->comment('Описание'),
            'UPDATEDATE' => $this->date()->notNull()->comment('Дата обновления'),
            'STARTDATE' => $this->date()->notNull()->comment('Начало действия'),
            'ENDDATE' => $this->date()->notNull()->comment('Окончание действия'),
            'ISACTIVE' => $this->boolean()->notNull()->comment('Активность (true/false)'),
        ]);

        // Первичный ключ на поле ID
        $this->addPrimaryKey('PK_param_types_ID', '{{%param_types}}', 'ID');

//        // Индексы для часто используемых полей
//        $this->createIndex('idx_param_types_CODE', '{{%param_types}}', 'CODE');
//        $this->createIndex('idx_param_types_ISACTIVE', '{{%param_types}}', 'ISACTIVE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%param_types}}');
    }
}