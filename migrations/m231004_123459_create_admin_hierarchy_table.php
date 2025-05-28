<?php

use yii\db\Migration;

class m231004_123459_create_admin_hierarchy_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%admin_hierarchy}}', [
            'ID' => $this->bigInteger()->notNull()->unique()->comment('Уникальный идентификатор записи'),
            'OBJECTID' => $this->bigInteger()->notNull()->comment('Глобальный идентификатор объекта'),
            'PARENTOBJID' => $this->bigInteger()->comment('Идентификатор родительского объекта'),
            'CHANGEID' => $this->bigInteger()->notNull()->comment('ID транзакции изменений'),
            'REGIONCODE' => $this->string(4)->comment('Код региона (1-4 цифры)'),
            'AREACODE' => $this->string(4)->comment('Код района (1-4 цифры)'),
            'CITYCODE' => $this->string(4)->comment('Код города (1-4 цифры)'),
            'PLACECODE' => $this->string(4)->comment('Код населенного пункта'),
            'PLANCODE' => $this->string(4)->comment('Код ЭПС'),
            'STREETCODE' => $this->string(4)->comment('Код улицы'),
            'PREVID' => $this->bigInteger()->comment('Связь с предыдущей записью'),
            'NEXTID' => $this->bigInteger()->comment('Связь с последующей записью'),
            'UPDATEDATE' => $this->date()->notNull()->comment('Дата обновления'),
            'STARTDATE' => $this->date()->notNull()->comment('Начало действия'),
            'ENDDATE' => $this->date()->notNull()->comment('Окончание действия'),
            'ISACTIVE' => $this->tinyInteger(1)->notNull()->comment('Активность (0/1)'),
            'PATH' => $this->text()->notNull()->comment('Иерархический путь'),
        ]);

        // Первичный ключ
        $this->addPrimaryKey('PK_admin_hierarchy_ID', '{{%admin_hierarchy}}', 'ID');

//        // Индексы для ключевых полей
//        $this->createIndex('idx_admin_hierarchy_OBJECTID', '{{%admin_hierarchy}}', 'OBJECTID');
//        $this->createIndex('idx_admin_hierarchy_PARENTOBJID', '{{%admin_hierarchy}}', 'PARENTOBJID');
//        $this->createIndex('idx_admin_hierarchy_CHANGEID', '{{%admin_hierarchy}}', 'CHANGEID');
//        $this->createIndex('idx_admin_hierarchy_ISACTIVE', '{{%admin_hierarchy}}', 'ISACTIVE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%admin_hierarchy}}');
    }
}