<?php

use yii\db\Migration;

class m231004_123503_create_change_history_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%change_history}}', [
            'CHANGEID' => $this->bigInteger()->notNull()->comment('ID транзакции изменений'),
            'OBJECTID' => $this->bigInteger()->notNull()->comment('Уникальный ID объекта'),
            'ADROBJECTID' => $this->char(36)->notNull()->comment('GUID транзакции'),
            'OPERTYPEID' => $this->integer()->notNull()->comment('Тип операции (до 10 цифр)'),
            'NDOCID' => $this->bigInteger()->comment('ID документа (опционально)'),
            'CHANGEDATE' => $this->date()->notNull()->comment('Дата изменения'),
        ]);

        // Составной первичный ключ (CHANGEID + OBJECTID)
        $this->addPrimaryKey(
            'PK_change_history',
            '{{%change_history}}',
            ['CHANGEID', 'OBJECTID']
        );

//        // Индексы для часто используемых полей
//        $this->createIndex('idx_change_history_ADROBJECTID', '{{%change_history}}', 'ADROBJECTID');
//        $this->createIndex('idx_change_history_OPERTYPEID', '{{%change_history}}', 'OPERTYPEID');
    }

    public function safeDown()
    {
        $this->dropTable('{{%change_history}}');
    }
}