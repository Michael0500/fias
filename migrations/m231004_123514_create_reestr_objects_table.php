<?php

use yii\db\Migration;

class m231004_123514_create_reestr_objects_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%reestr_objects}}', [
            'objectid' => $this->bigInteger()->notNull()->comment('Уникальный идентификатор объекта'),
            'createdate' => $this->date()->notNull()->comment('Дата создания'),
            'changeid' => $this->bigInteger()->notNull()->comment('ID транзакции изменений'),
            'levelid' => $this->bigInteger()->notNull()->comment('Уровень объекта'),
            'updatedate' => $this->date()->notNull()->comment('Дата обновления'),
            'objectguid' => $this->char(36)->unique()->notNull()->comment('GUID объекта'),
            'isactive' => $this->tinyInteger(1)->notNull()->comment('Активность (0/1)'),
        ]);

        // Первичный ключ на OBJECTID (уникальный идентификатор объекта)
        /*$this->addPrimaryKey('PK_reestr_objects_OBJECTID', '{{%reestr_objects}}', 'OBJECTID');

        // Индексы для часто используемых полей
        $this->createIndex('idx_reestr_objects_OBJECTGUID', '{{%reestr_objects}}', 'OBJECTGUID');
        $this->createIndex('idx_reestr_objects_LEVELID', '{{%reestr_objects}}', 'LEVELID');
        $this->createIndex('idx_reestr_objects_ISACTIVE', '{{%reestr_objects}}', 'ISACTIVE');*/

        $sql = <<<SQL
CREATE OR REPLACE PROCEDURE import_reestr_objects_from_xml(xml_path TEXT)
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
                          '/REESTR_OBJECTS/OBJECT'
                          PASSING xml_data
                          COLUMNS
                              objectid     BIGINT  PATH '@OBJECTID',
                              createdate   DATE    PATH '@CREATEDATE',
                              changeid     BIGINT  PATH '@CHANGEID',
                              levelid      INTEGER PATH '@LEVELID',
                              updatedate   DATE    PATH '@UPDATEDATE',
                              objectguid   UUID    PATH '@OBJECTGUID',
                              isactive     INTEGER PATH '@ISACTIVE'
                  ) AS x
         )
    INSERT INTO reestr_objects (
        objectid, createdate, changeid, levelid,
        updatedate, objectguid, isactive
    )
    SELECT
        objectid, createdate, changeid, levelid,
        updatedate, objectguid, isactive
    FROM extracted_data
    ON CONFLICT (objectguid) DO UPDATE
        SET
            createdate = EXCLUDED.createdate,
            changeid = EXCLUDED.changeid,
            levelid = EXCLUDED.levelid,
            updatedate = EXCLUDED.updatedate,
            objectid = EXCLUDED.objectid,
            isactive = EXCLUDED.isactive;
END;
$$;
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable('{{%reestr_objects}}');
        $this->execute('DROP PROCEDURE IF EXISTS import_reestr_objects_from_xml(TEXT)');
    }
}