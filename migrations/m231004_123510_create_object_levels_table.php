<?php

use yii\db\Migration;

class m231004_123510_create_object_levels_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%object_levels}}', [
            'LEVEL' => $this->smallInteger()->notNull()->unique()->comment('Уровень объекта (первичный ключ)'),
            'NAME' => $this->string(250)->notNull()->comment('Наименование уровня'),
            'SHORTNAME' => $this->string(50)->comment('Краткое наименование'),
            'UPDATEDATE' => $this->date()->notNull()->comment('Дата обновления'),
            'STARTDATE' => $this->date()->notNull()->comment('Начало действия'),
            'ENDDATE' => $this->date()->notNull()->comment('Окончание действия'),
            'ISACTIVE' => $this->boolean()->notNull()->comment('Активность (true/false)'),
        ]);

        // Первичный ключ на поле LEVEL
        $this->addPrimaryKey('PK_object_levels_LEVEL', '{{%object_levels}}', 'LEVEL');

//        // Индекс для активных записей
//        $this->createIndex('idx_object_levels_ISACTIVE', '{{%object_levels}}', 'ISACTIVE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%object_levels}}');
    }
}