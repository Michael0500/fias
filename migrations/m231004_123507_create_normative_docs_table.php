<?php

use yii\db\Migration;

class m231004_123507_create_normative_docs_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%normative_docs}}', [
            'id' => $this->bigInteger()->notNull()->unique()->comment('Уникальный идентификатор документа'),
            //'name' => $this->text()->notNull()->comment('Наименование документа'),
            'name' => $this->text()->comment('Наименование документа'),
            'date' => $this->date()->notNull()->comment('Дата документа'),
            'number' => $this->string(150)->notNull()->comment('Номер документа'),
            'type' => $this->bigInteger()->notNull()->comment('Тип документа (до 10 цифр)'),
            'kind' => $this->bigInteger()->notNull()->comment('Вид документа (до 10 цифр)'),
            'updatedate' => $this->date()->notNull()->comment('Дата обновления'),
            'orgname' => $this->string(255)->comment('Орган, создавший документ'),
            'regnum' => $this->string(100)->comment('Номер госрегистрации'),
            'regdate' => $this->date()->comment('Дата госрегистрации'),
            'accdate' => $this->date()->comment('Дата вступления в силу'),
            'comment' => $this->text()->comment('Комментарий'),
        ]);

        // Первичный ключ
        //$this->addPrimaryKey('PK_normative_docs_ID', '{{%normative_docs}}', 'ID');

//        // Индексы для часто используемых полей
//        $this->createIndex('idx_normative_docs_TYPE', '{{%normative_docs}}', 'TYPE');
//        $this->createIndex('idx_normative_docs_KIND', '{{%normative_docs}}', 'KIND');
//        $this->createIndex('idx_normative_docs_DATE', '{{%normative_docs}}', 'DATE');

        $sql = <<<SQL
CREATE OR REPLACE PROCEDURE import_normative_docs_from_xml(xml_path TEXT)
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
            '/NORMDOCS/NORMDOC'
            PASSING xml_data
            COLUMNS
                id INTEGER PATH '@ID',
                name TEXT PATH '@NAME',
                date DATE PATH '@DATE',
                number TEXT PATH '@NUMBER',
                type INTEGER PATH '@TYPE',
                kind INTEGER PATH '@KIND',
                updatedate DATE PATH '@UPDATEDATE',
                orgname TEXT PATH '@ORGNAME',
                regnum TEXT PATH '@REGNUM',
                regdate DATE PATH '@REGDATE',
                accdate DATE PATH '@ACCDATE',
                comment TEXT PATH '@COMMENT'
        ) AS x
    )
    INSERT INTO normative_docs (
        id, name, date, number, type, kind, updatedate, 
        orgname, regnum, regdate, accdate, comment
    )
    SELECT
        id, name, date, number, type, kind, updatedate, 
        orgname, regnum, regdate, accdate, comment
    FROM extracted_data
    ON CONFLICT (id) DO UPDATE
    SET
        date = EXCLUDED.date,
        name = EXCLUDED.name,
        number = EXCLUDED.number,
        type = EXCLUDED.type,
        kind = EXCLUDED.kind,
        updatedate = EXCLUDED.updatedate,
        orgname = EXCLUDED.orgname,
        regnum = EXCLUDED.regnum,
        regdate = EXCLUDED.regdate,
        accdate = EXCLUDED.accdate,
        comment = EXCLUDED.comment;
END;
$$;
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable('{{%normative_docs}}');
        $this->execute('DROP PROCEDURE IF EXISTS import_normative_docs_from_xml(TEXT)');
    }
}