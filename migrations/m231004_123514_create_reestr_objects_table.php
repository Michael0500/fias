<?php

use yii\db\Migration;

class m231004_123514_create_reestr_objects_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%reestr_objects}}', [
            'OBJECTID' => $this->bigInteger()->notNull()->comment('Уникальный идентификатор объекта'),
            'CREATEDATE' => $this->date()->notNull()->comment('Дата создания'),
            'CHANGEID' => $this->bigInteger()->notNull()->comment('ID транзакции изменений'),
            'LEVELID' => $this->integer()->notNull()->comment('Уровень объекта'),
            'UPDATEDATE' => $this->date()->notNull()->comment('Дата обновления'),
            'OBJECTGUID' => $this->char(36)->notNull()->comment('GUID объекта'),
            'ISACTIVE' => $this->tinyInteger(1)->notNull()->comment('Активность (0/1)'),
        ]);

        // Первичный ключ на OBJECTID (уникальный идентификатор объекта)
        $this->addPrimaryKey('PK_reestr_objects_OBJECTID', '{{%reestr_objects}}', 'OBJECTID');

        // Индексы для часто используемых полей
        $this->createIndex('idx_reestr_objects_OBJECTGUID', '{{%reestr_objects}}', 'OBJECTGUID');
        $this->createIndex('idx_reestr_objects_LEVELID', '{{%reestr_objects}}', 'LEVELID');
        $this->createIndex('idx_reestr_objects_ISACTIVE', '{{%reestr_objects}}', 'ISACTIVE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%reestr_objects}}');
    }
}