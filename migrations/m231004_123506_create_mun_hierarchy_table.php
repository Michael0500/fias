<?php

use yii\db\Migration;

class m231004_123506_create_mun_hierarchy_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%mun_hierarchy}}', [
            'id' => $this->bigInteger()->notNull()->unique()->comment('Уникальный идентификатор записи'),
            'objectid' => $this->bigInteger()->notNull()->comment('Глобальный идентификатор объекта'),
            'parentobjid' => $this->bigInteger()->comment('Идентификатор родительского объекта'),
            'changeid' => $this->bigInteger()->notNull()->comment('ID транзакции изменений'),
            'oktmo' => $this->string(11)->comment('Код ОКТМО (8-11 цифр)'),
            'previd' => $this->bigInteger()->comment('Предыдущая запись'),
            'nextid' => $this->bigInteger()->comment('Следующая запись'),
            'updatedate' => $this->date()->notNull()->comment('Дата обновления'),
            'startdate' => $this->date()->notNull()->comment('Начало действия'),
            'enddate' => $this->date()->notNull()->comment('Окончание действия'),
            'isactive' => $this->tinyInteger(1)->notNull()->comment('Активность (0/1)'),
            'path' => $this->text()->notNull()->comment('Полный иерархический путь'),
        ]);

        // Первичный ключ
        /*$this->addPrimaryKey('PK_mun_hierarchy_ID', '{{%mun_hierarchy}}', 'ID');

        // Индексы для связей и поиска
        $this->createIndex('idx_mun_hierarchy_OBJECTID', '{{%mun_hierarchy}}', 'OBJECTID');
        $this->createIndex('idx_mun_hierarchy_PARENTOBJID', '{{%mun_hierarchy}}', 'PARENTOBJID');
        $this->createIndex('idx_mun_hierarchy_CHANGEID', '{{%mun_hierarchy}}', 'CHANGEID');
        $this->createIndex('idx_mun_hierarchy_OKTMO', '{{%mun_hierarchy}}', 'OKTMO');*/


        $sql = <<<SQL
CREATE OR REPLACE PROCEDURE import_mun_hierarchy_from_xml(xml_path TEXT)
LANGUAGE plpgsql
AS
$$
BEGIN
WITH raw_xml AS (
    SELECT pg_read_file(xml_path) AS xml_content
),
     parsed_xml AS (
         SELECT xmlparse(content xml_content) AS xml_data
         FROM raw_xml
     ),
     extracted_data AS (
         SELECT *
         FROM parsed_xml,
              xmltable(
                      '/ITEMS/ITEM'
                          PASSING xml_data
                 COLUMNS
                     id           BIGINT  PATH '@ID',
                      objectid     BIGINT  PATH '@OBJECTID',
                      parentobjid  BIGINT  PATH '@PARENTOBJID',
                      changeid     BIGINT  PATH '@CHANGEID',
                      oktmo        VARCHAR(11) PATH '@OKTMO',
                      previd       BIGINT  PATH '@PREVID',
                      nextid       BIGINT  PATH '@NEXTID',
                      updatedate   DATE    PATH '@UPDATEDATE',
                      startdate    DATE    PATH '@STARTDATE',
                      enddate      DATE    PATH '@ENDDATE',
                      isactive     INTEGER PATH '@ISACTIVE',
                      path         TEXT    PATH '@PATH'
              ) AS x
     )
INSERT INTO mun_hierarchy (
        id, objectid, parentobjid, changeid,
        oktmo, previd, nextid, updatedate,
        startdate, enddate, isactive, path
    )
SELECT
    id, objectid, parentobjid, changeid,
    oktmo, previd, nextid, updatedate,
    startdate, enddate, isactive, path
FROM extracted_data
    ON CONFLICT (id) DO UPDATE
                            SET
                                objectid     = EXCLUDED.objectid,
                            parentobjid  = EXCLUDED.parentobjid,
                            changeid     = EXCLUDED.changeid,
                            oktmo        = EXCLUDED.oktmo,
                            previd       = EXCLUDED.previd,
                            nextid       = EXCLUDED.nextid,
                            updatedate   = EXCLUDED.updatedate,
                            startdate    = EXCLUDED.startdate,
                            enddate      = EXCLUDED.enddate,
                            isactive     = EXCLUDED.isactive,
                            path         = EXCLUDED.path;
END;
$$;
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable('{{%mun_hierarchy}}');
        $this->execute('DROP PROCEDURE IF EXISTS import_mun_hierarchy_from_xml(TEXT)');
    }
}