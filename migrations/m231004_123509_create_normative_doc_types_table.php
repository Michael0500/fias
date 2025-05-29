<?php

use yii\db\Migration;

class m231004_123509_create_normative_doc_types_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%normative_doc_types}}', [
            'id' => $this->integer()->notNull()->unique()->comment('Идентификатор типа документа'),
            'name' => $this->string(500)->notNull()->comment('Наименование типа документа'),
            'startdate' => $this->date()->notNull()->comment('Дата начала действия'),
            'enddate' => $this->date()->notNull()->comment('Дата окончания действия'),
        ]);

        // Первичный ключ
        //$this->addPrimaryKey('PK_normative_doc_types_ID', '{{%normative_doc_types}}', 'ID');

        $sql = <<<SQL
CREATE OR REPLACE PROCEDURE import_normative_doc_types_from_xml(xml_path TEXT)
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
                 '/NDOCTYPES/NDOCTYPE'
                 PASSING xml_data
                 COLUMNS
                     id         INTEGER   PATH '@ID',
                     name       TEXT      PATH '@NAME',
                     startdate  DATE      PATH '@STARTDATE',
                     enddate    DATE      PATH '@ENDDATE'
             ) AS x
    )
    INSERT INTO normative_doc_types (id, name, startdate, enddate)
    SELECT id, name, startdate, enddate
    FROM extracted_data
    ON CONFLICT (id) DO UPDATE
        SET 
          name = EXCLUDED.name,
          startdate = EXCLUDED.startdate,
          enddate = EXCLUDED.enddate;
END;
$$;
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable('{{%normative_doc_types}}');
        $this->execute('DROP PROCEDURE IF EXISTS import_normative_doc_types_from_xml(TEXT)');

    }
}