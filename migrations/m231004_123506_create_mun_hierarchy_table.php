<?php

use yii\db\Migration;

class m231004_123506_create_mun_hierarchy_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%mun_hierarchy}}', [
            'ID' => $this->bigInteger()->notNull()->unique()->comment('Уникальный идентификатор записи'),
            'OBJECTID' => $this->bigInteger()->notNull()->comment('Глобальный идентификатор объекта'),
            'PARENTOBJID' => $this->bigInteger()->comment('Идентификатор родительского объекта'),
            'CHANGEID' => $this->bigInteger()->notNull()->comment('ID транзакции изменений'),
            'OKTMO' => $this->string(11)->comment('Код ОКТМО (8-11 цифр)'),
            'PREVID' => $this->bigInteger()->comment('Предыдущая запись'),
            'NEXTID' => $this->bigInteger()->comment('Следующая запись'),
            'UPDATEDATE' => $this->date()->notNull()->comment('Дата обновления'),
            'STARTDATE' => $this->date()->notNull()->comment('Начало действия'),
            'ENDDATE' => $this->date()->notNull()->comment('Окончание действия'),
            'ISACTIVE' => $this->tinyInteger(1)->notNull()->comment('Активность (0/1)'),
            'PATH' => $this->text()->notNull()->comment('Полный иерархический путь'),
        ]);

        // Первичный ключ
        $this->addPrimaryKey('PK_mun_hierarchy_ID', '{{%mun_hierarchy}}', 'ID');

        // Индексы для связей и поиска
        $this->createIndex('idx_mun_hierarchy_OBJECTID', '{{%mun_hierarchy}}', 'OBJECTID');
        $this->createIndex('idx_mun_hierarchy_PARENTOBJID', '{{%mun_hierarchy}}', 'PARENTOBJID');
        $this->createIndex('idx_mun_hierarchy_CHANGEID', '{{%mun_hierarchy}}', 'CHANGEID');
        $this->createIndex('idx_mun_hierarchy_OKTMO', '{{%mun_hierarchy}}', 'OKTMO');
    }

    public function safeDown()
    {
        $this->dropTable('{{%mun_hierarchy}}');
    }
}