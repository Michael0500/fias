<?php

use yii\db\Migration;

class m231004_123458_create_address_types_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%address_types}}', [
            'id' => $this->bigInteger()->notNull()->unique()->comment('Идентификатор записи'),
            'level' => $this->integer()->notNull()->comment('Уровень адресного объекта'),
            'shortname' => $this->string(50)->notNull()->comment('Краткое наименование'),
            'name' => $this->string(250)->notNull()->comment('Полное наименование'),
            'desc' => $this->string(250)->comment('Описание'),
            'updatedate' => $this->date()->notNull()->comment('Дата обновления'),
            'startdate' => $this->date()->notNull()->comment('Начало действия'),
            'enddate' => $this->date()->notNull()->comment('Окончание действия'),
            'isactive' => $this->boolean()->notNull()->comment('Активность (true/false)'),
        ]);

        // Первичный ключ
        //$this->addPrimaryKey('PK_address_types_ID', '{{%address_types}}', 'ID');

//        // Индексы для часто используемых полей
//        $this->createIndex('idx_address_types_LEVEL', '{{%address_types}}', 'LEVEL');
//        $this->createIndex('idx_address_types_SHORTNAME', '{{%address_types}}', 'SHORTNAME');



 $sql = <<<SQL
CREATE OR REPLACE PROCEDURE import_address_object_types_from_xml(xml_path TEXT)
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
                 '/ADDRESSOBJECTTYPES/ADDRESSOBJECTTYPE'
                 PASSING xml_data
                 COLUMNS
                     id         INTEGER PATH '@ID',
                     level      INTEGER PATH '@LEVEL',
                     shortname  TEXT    PATH '@SHORTNAME',
                     name       TEXT    PATH '@NAME',
                     "desc"       TEXT    PATH '@DESC',
                     updatedate DATE    PATH '@UPDATEDATE',
                     startdate  DATE    PATH '@STARTDATE',
                     enddate    DATE    PATH '@ENDDATE',
                     isactive   BOOLEAN PATH '@ISACTIVE'
             ) AS x
    )
    INSERT INTO address_types (
        id, level, shortname, name, "desc", updatedate, startdate, enddate, isactive
    )
    SELECT
        id, level, shortname, name, "desc", updatedate, startdate, enddate, isactive
    FROM extracted_data
    ON CONFLICT (id) DO UPDATE
        SET
            level      = EXCLUDED.level,
            shortname  = EXCLUDED.shortname,
            name       = EXCLUDED.name,
            "desc"       = EXCLUDED.desc,
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
        $this->dropTable('{{%address_types}}');
        $this->execute('DROP PROCEDURE IF EXISTS import_address_object_types_from_xml(TEXT)');

    }
}