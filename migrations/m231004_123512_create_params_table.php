<?php

use yii\db\Migration;

class m231004_123512_create_params_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%params}}', [
            'ID' => $this->bigInteger()->notNull()->unique()->comment('Идентификатор записи'),
            'OBJECTID' => $this->bigInteger()->notNull()->comment('Глобальный идентификатор объекта'),
            'CHANGEID' => $this->bigInteger()->comment('ID изменяющей транзакции (опционально)'),
            'CHANGEIDEND' => $this->bigInteger()->notNull()->comment('ID завершающей транзакции'),
            'TYPEID' => $this->integer()->notNull()->comment('Тип параметра (4 цифры)'),
            'VALUE' => $this->text()->notNull()->comment('Значение параметра'),
            'UPDATEDATE' => $this->date()->notNull()->comment('Дата обновления'),
            'STARTDATE' => $this->date()->notNull()->comment('Начало действия'),
            'ENDDATE' => $this->date()->notNull()->comment('Окончание действия'),
        ]);

        // Первичный ключ
        $this->addPrimaryKey('PK_params_ID', '{{%params}}', 'ID');

//        // Индексы для связей и поиска
//        $this->createIndex('idx_params_OBJECTID', '{{%params}}', 'OBJECTID');
//        $this->createIndex('idx_params_TYPEID', '{{%params}}', 'TYPEID');
//        $this->createIndex('idx_params_CHANGEIDEND', '{{%params}}', 'CHANGEIDEND');
    }

    public function safeDown()
    {
        $this->dropTable('{{%params}}');
    }
}