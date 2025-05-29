<?php

use yii\db\Migration;

class m231004_123501_create_apartment_types_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%apartment_types}}', [
            'id' => $this->bigInteger()->notNull()->unique()->comment('Идентификатор типа (ключ)'),
            'name' => $this->string(50)->notNull()->comment('Наименование'),
            'shortname' => $this->string(50)->comment('Краткое наименование'),
            'desc' => $this->string(250)->comment('Описание'),
            'updatedate' => $this->date()->notNull()->comment('Дата обновления'),
            'startdate' => $this->date()->notNull()->comment('Начало действия'),
            'enddate' => $this->date()->notNull()->comment('Окончание действия'),
            'isactive' => $this->boolean()->notNull()->comment('Активность (true/false)'),
        ]);

        // Первичный ключ
        //$this->addPrimaryKey('PK_apartment_types_ID', '{{%apartment_types}}', 'ID');

//        // Индексы для часто используемых полей
//        $this->createIndex('idx_apartment_types_NAME', '{{%apartment_types}}', 'NAME');
//        $this->createIndex('idx_apartment_types_ISACTIVE', '{{%apartment_types}}', 'ISACTIVE');

        $sql = <<<SQL
CREATE OR REPLACE PROCEDURE import_apartment_types_from_xml(xml_path TEXT)
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
                 '/APARTMENTTYPES/APARTMENTTYPE'
                 PASSING xml_data
                 COLUMNS
                     id         INTEGER  PATH '@ID',
                     name       TEXT     PATH '@NAME',
                     shortname  TEXT     PATH '@SHORTNAME',
                     "desc"     TEXT     PATH '@DESC',
                     updatedate DATE     PATH '@UPDATEDATE',
                     startdate  DATE     PATH '@STARTDATE',
                     enddate    DATE     PATH '@ENDDATE',
                     isactive   BOOLEAN  PATH '@ISACTIVE'
             ) AS x
    )
    INSERT INTO apartment_types (
        id, name, shortname, "desc", updatedate, startdate, enddate, isactive
    )
    SELECT
        id, name, shortname, "desc", updatedate, startdate, enddate, isactive
    FROM extracted_data
    ON CONFLICT (id) DO UPDATE
        SET
            name       = EXCLUDED.name,
            shortname  = EXCLUDED.shortname,
            "desc"     = EXCLUDED."desc",
            updatedate = EXCLUDED.updatedate,
            startdate  = EXCLUDED.startdate,
            enddate    = EXCLUDED.enddate,
            isactive   = EXCLUDED.isactive;
END;
$$;
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable('{{%apartment_types}}');
        $this->execute('DROP PROCEDURE IF EXISTS import_apartment_types_from_xml(TEXT)');

    }
}