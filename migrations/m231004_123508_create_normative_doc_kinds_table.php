<?php

use yii\db\Migration;

class m231004_123508_create_normative_doc_kinds_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%normative_doc_kinds}}', [
            'id' => $this->integer()->notNull()->unique()->comment('Идентификатор вида документа'),
            'name' => $this->string(500)->notNull()->comment('Наименование вида документа'),
        ]);

        // Первичный ключ
        //$this->addPrimaryKey('PK_normative_doc_kinds_ID', '{{%normative_doc_kinds}}', 'ID');

        $sql = <<<SQL
CREATE OR REPLACE PROCEDURE import_normative_doc_kinds_from_xml(xml_path TEXT)
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
                 '/NDOCKINDS/NDOCKIND'
                 PASSING xml_data
                 COLUMNS
                     id    INTEGER PATH '@ID',
                     name  TEXT    PATH '@NAME'
             ) AS x
    )
    INSERT INTO normative_doc_kinds (id, name)
    SELECT id, name
    FROM extracted_data
    ON CONFLICT (id) DO UPDATE
        SET name = EXCLUDED.name;
END;
$$;
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable('{{%normative_doc_kinds}}');
        $this->execute('DROP PROCEDURE IF EXISTS import_normative_doc_kinds_from_xml(TEXT)');
    }
}