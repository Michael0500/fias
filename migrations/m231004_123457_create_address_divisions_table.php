<?php

use yii\db\Migration;

class m231004_123457_create_address_divisions_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%address_divisions}}', [
            'id' => $this->bigInteger()->notNull()->unique()->comment('Уникальный идентификатор записи (ключевое поле)'),
            'parentid' => $this->bigInteger()->notNull()->comment('Идентификатор родительского элемента'),
            'childid' => $this->bigInteger()->notNull()->comment('Идентификатор дочернего элемента'),
            'changeid' => $this->bigInteger()->notNull()->comment('ID изменившей транзакции'),
        ]);

        // Первичный ключ
        //$this->addPrimaryKey('PK_address_divisions_ID', '{{%address_divisions}}', 'ID');

//        // Индексы для связей
//        $this->createIndex('idx_address_divisions_PARENTID', '{{%address_divisions}}', 'PARENTID');
//        $this->createIndex('idx_address_divisions_CHILDID', '{{%address_divisions}}', 'CHILDID');
//        $this->createIndex('idx_address_divisions_CHANGEID', '{{%address_divisions}}', 'CHANGEID');



$sql = <<<SQL
CREATE OR REPLACE PROCEDURE import_address_objects_division_from_xml(xml_path TEXT)
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
                     id       BIGINT PATH '@ID',
                     parentid BIGINT PATH '@PARENTID',
                     childid  BIGINT PATH '@CHILDID',
                     changeid BIGINT PATH '@CHANGEID'
             ) AS x
    )
    INSERT INTO address_divisions (
        id, parentid, childid, changeid
    )
    SELECT
        id, parentid, childid, changeid
    FROM extracted_data
    ON CONFLICT (id) DO UPDATE
        SET
            parentid = EXCLUDED.parentid,
            childid = EXCLUDED.childid,
            changeid = EXCLUDED.changeid;
END;
$$;
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable('{{%address_divisions}}');
        $this->execute('DROP PROCEDURE IF EXISTS import_address_objects_division_from_xml(TEXT)');

    }
}