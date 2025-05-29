<?php

use yii\db\Migration;

class m231004_123510_create_object_levels_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%object_levels}}', [
            'level' => $this->smallInteger()->notNull()->unique()->comment('Уровень объекта (первичный ключ)'),
            'name' => $this->string(250)->notNull()->comment('Наименование уровня'),
            'shortname' => $this->string(50)->comment('Краткое наименование'),
            'updatedate' => $this->date()->notNull()->comment('Дата обновления'),
            'startdate' => $this->date()->notNull()->comment('Начало действия'),
            'enddate' => $this->date()->notNull()->comment('Окончание действия'),
            'isactive' => $this->boolean()->notNull()->comment('Активность (true/false)'),
        ]);

        // Первичный ключ на поле LEVEL
        //$this->addPrimaryKey('PK_object_levels_LEVEL', '{{%object_levels}}', 'LEVEL');

//        // Индекс для активных записей
//        $this->createIndex('idx_object_levels_ISACTIVE', '{{%object_levels}}', 'ISACTIVE');
        $sql = <<<SQL
CREATE OR REPLACE PROCEDURE import_object_levels_from_xml(xml_path TEXT)
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
                 '/OBJECTLEVELS/OBJECTLEVEL'
                 PASSING xml_data
                 COLUMNS
                   level       INTEGER PATH '@LEVEL',
                   name        TEXT    PATH '@NAME',
                   shortname   TEXT    PATH '@SHORTNAME',
                   updatedate  DATE    PATH '@UPDATEDATE',
                   startdate   DATE    PATH '@STARTDATE',
                   enddate     DATE    PATH '@ENDDATE',
                   isactive    BOOLEAN PATH '@ISACTIVE'
             ) AS x
    )
    INSERT INTO object_levels (
        level, name, shortname, updatedate, startdate, enddate, isactive
    )
    SELECT level, name, shortname, updatedate, startdate, enddate, isactive
    FROM extracted_data
    ON CONFLICT (level) DO UPDATE
    SET
      name = EXCLUDED.name,
      shortname = EXCLUDED.shortname,
      updatedate = EXCLUDED.updatedate,
      startdate = EXCLUDED.startdate,
      enddate = EXCLUDED.enddate,
      isactive = EXCLUDED.isactive;
END;
$$;
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable('{{%object_levels}}');
        $this->execute('DROP PROCEDURE IF EXISTS import_object_levels_from_xml(TEXT)');
    }
}