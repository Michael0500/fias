<?php

use yii\db\Migration;

class m231004_123503_create_change_history_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%change_history}}', [
            'changeid' => $this->bigInteger()->notNull()->comment('ID транзакции изменений'),
            'objectid' => $this->bigInteger()->notNull()->comment('Уникальный ID объекта'),
            'adrobjectid' => $this->char(36)->unique()->notNull()->comment('GUID транзакции'),
            'opertypeid' => $this->integer()->notNull()->comment('Тип операции (до 10 цифр)'),
            'ndocid' => $this->bigInteger()->comment('ID документа (опционально)'),
            'changedate' => $this->date()->notNull()->comment('Дата изменения'),
        ]);

        // Составной первичный ключ (CHANGEID + OBJECTID)
        /*$this->addPrimaryKey(
            'PK_change_history',
            '{{%change_history}}',
            ['CHANGEID', 'OBJECTID']
        );*/

//        // Индексы для часто используемых полей
//        $this->createIndex('idx_change_history_ADROBJECTID', '{{%change_history}}', 'ADROBJECTID');
//        $this->createIndex('idx_change_history_OPERTYPEID', '{{%change_history}}', 'OPERTYPEID');

        $sql = <<<SQL
CREATE OR REPLACE PROCEDURE import_change_history_from_xml(xml_path TEXT)
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
                     changeid   BIGINT   PATH '@CHANGEID',
                     objectid   BIGINT   PATH '@OBJECTID',
                     adrobjectid UUID    PATH '@ADROBJECTID',
                     opertypeid INTEGER PATH '@OPERTYPEID',
                     ndocid     BIGINT   PATH '@NDOCID',
                     changedate DATE     PATH '@CHANGEDATE'
             ) AS x
    )
    INSERT INTO change_history (
        changeid, objectid, adrobjectid, opertypeid, ndocid, changedate
    )
    SELECT
        changeid, objectid, adrobjectid, opertypeid, ndocid, changedate
    FROM extracted_data
    ON CONFLICT (adrobjectid) DO UPDATE
        SET
            objectid   = EXCLUDED.objectid,
            changeid= EXCLUDED.changeid,
            opertypeid = EXCLUDED.opertypeid,
            ndocid     = EXCLUDED.ndocid,
            changedate = EXCLUDED.changedate;
END;
$$;
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable('{{%change_history}}');
        $this->execute('DROP PROCEDURE IF EXISTS import_change_history_from_xml(TEXT)');

    }
}