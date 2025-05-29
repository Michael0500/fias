<?php

use yii\db\Migration;

class m231004_123456_create_address_objects_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%address_objects}}', [
            'id' => $this->bigInteger()->notNull()->unique()->comment('Уникальный идентификатор записи (ключевое поле)'),
            'objectid' => $this->bigInteger()->notNull()->comment('Глобальный уникальный идентификатор (INTEGER)'),
            'objectguid' => $this->char(36)->notNull()->comment('Глобальный уникальный идентификатор (UUID)'),
            'changeid' => $this->bigInteger()->notNull()->comment('ID изменившей транзакции'),
            'name' => $this->string(250)->notNull()->comment('Наименование'),
            'typename' => $this->string(50)->notNull()->comment('Тип объекта'),
            'level' => $this->string(10)->notNull()->comment('Уровень адресного объекта'),
            'opertypeid' => $this->integer()->notNull()->comment('Статус действия над записью'),
            'previd' => $this->bigInteger()->comment('Связь с предыдущей записью'),
            'nextid' => $this->bigInteger()->comment('Связь с последующей записью'),
            'updatedate' => $this->date()->notNull()->comment('Дата обновления'),
            'startdate' => $this->date()->notNull()->comment('Начало действия'),
            'enddate' => $this->date()->notNull()->comment('Окончание действия'),
            'isactual' => $this->tinyInteger(1)->notNull()->comment('Актуальность (0/1)'),
            'isactive' => $this->tinyInteger(1)->notNull()->comment('Активность (0/1)'),
        ]);

        // Первичный ключ
        //$this->addPrimaryKey('PK_address_objects_ID', '{{%address_objects}}', 'ID');

//        // Добавляем индексы для часто используемых полей
//        $this->createIndex('idx_address_objects_OBJECTID', '{{%address_objects}}', 'OBJECTID');
//        $this->createIndex('idx_address_objects_OBJECTGUID', '{{%address_objects}}', 'OBJECTGUID');
//        $this->createIndex('idx_address_objects_LEVEL', '{{%address_objects}}', 'LEVEL');
        $sql = <<<SQL
CREATE OR REPLACE PROCEDURE import_address_objects_from_xml(xml_path TEXT)
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
                 '/ADDRESSOBJECTS/OBJECT'
                 PASSING xml_data
                 COLUMNS
                     id BIGINT PATH '@ID',
                     objectid BIGINT PATH '@OBJECTID',
                     objectguid UUID PATH '@OBJECTGUID',
                     changeid BIGINT PATH '@CHANGEID',
                     name TEXT PATH '@NAME',
                     typename TEXT PATH '@TYPENAME',
                     level TEXT PATH '@LEVEL',
                     opertypeid INTEGER PATH '@OPERTYPEID',
                     previd BIGINT PATH '@PREVID',
                     nextid BIGINT PATH '@NEXTID',
                     updatedate DATE PATH '@UPDATEDATE',
                     startdate DATE PATH '@STARTDATE',
                     enddate DATE PATH '@ENDDATE',
                     isactual INTEGER PATH '@ISACTUAL',
                     isactive INTEGER PATH '@ISACTIVE'
             ) AS x
    )
    INSERT INTO address_objects (
        id, objectid, objectguid, changeid,
        name, typename, level, opertypeid,
        previd, nextid, updatedate, startdate,
        enddate, isactual, isactive
    )
    SELECT
        id, objectid, objectguid, changeid,
        name, typename, level, opertypeid,
        previd, nextid, updatedate, startdate,
        enddate, isactual, isactive
    FROM extracted_data
    ON CONFLICT (id) DO UPDATE
        SET objectid = EXCLUDED.objectid,
            objectguid = EXCLUDED.objectguid,
            changeid = EXCLUDED.changeid,
            name = EXCLUDED.name,
            typename = EXCLUDED.typename,
            level = EXCLUDED.level,
            opertypeid = EXCLUDED.opertypeid,
            previd = EXCLUDED.previd,
            nextid = EXCLUDED.nextid,
            updatedate = EXCLUDED.updatedate,
            startdate = EXCLUDED.startdate,
            enddate = EXCLUDED.enddate,
            isactual = EXCLUDED.isactual,
            isactive = EXCLUDED.isactive;
END;
$$;
SQL;

        $this->execute($sql);

    }

    public function safeDown()
    {
        $this->dropTable('{{%address_objects}}');
        $this->execute('DROP PROCEDURE IF EXISTS import_address_objects_from_xml(TEXT)');

    }
}