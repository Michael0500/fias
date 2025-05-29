<?php

use yii\db\Migration;

class m231004_123513_create_param_types_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%param_types}}', [
            'id' => $this->smallInteger()->notNull()->unique()->comment('Идентификатор типа параметра (первичный ключ)'),
            'name' => $this->string(50)->notNull()->comment('Наименование'),
            'code' => $this->string(50)->notNull()->comment('Краткое наименование (код)'),
            'desc' => $this->string(120)->comment('Описание'),
            'updatedate' => $this->date()->notNull()->comment('Дата обновления'),
            'startdate' => $this->date()->notNull()->comment('Начало действия'),
            'enddate' => $this->date()->notNull()->comment('Окончание действия'),
            'isactive' => $this->boolean()->notNull()->comment('Активность (true/false)'),
        ]);

        // Первичный ключ на поле ID
        //$this->addPrimaryKey('PK_param_types_ID', '{{%param_types}}', 'ID');

//        // Индексы для часто используемых полей
//        $this->createIndex('idx_param_types_CODE', '{{%param_types}}', 'CODE');
//        $this->createIndex('idx_param_types_ISACTIVE', '{{%param_types}}', 'ISACTIVE');
        $sql = <<<SQL
CREATE OR REPLACE PROCEDURE import_param_types_from_xml(xml_path TEXT)
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
                 '/PARAMTYPES/PARAMTYPE'
                 PASSING xml_data
                 COLUMNS
                   id         INTEGER  PATH '@ID',
                   name       TEXT     PATH '@NAME',
                   code       TEXT     PATH '@CODE',
                   "desc"       TEXT     PATH '@DESC',
                   updatedate DATE     PATH '@UPDATEDATE',
                   startdate  DATE     PATH '@STARTDATE',
                   enddate    DATE     PATH '@ENDDATE',
                   isactive   BOOLEAN  PATH '@ISACTIVE'
             ) AS x
    )
    INSERT INTO param_types (
        id, name, code, "desc", updatedate, startdate, enddate, isactive
    )
    SELECT id, name, code, "desc", updatedate, startdate, enddate, isactive
    FROM extracted_data
    ON CONFLICT (id) DO UPDATE
    SET
      name = EXCLUDED.name,
      code = EXCLUDED.code,
      "desc" = EXCLUDED.desc,
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
        $this->dropTable('{{%param_types}}');
        $this->execute('DROP PROCEDURE IF EXISTS import_param_types_from_xml(TEXT)');

    }
}