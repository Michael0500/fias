<?php

use yii\db\Migration;

class m231004_123502_create_car_places_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%car_places}}', [
            'id' => $this->bigInteger()->notNull()->unique()->comment('Уникальный идентификатор записи'),
            'objectid' => $this->bigInteger()->notNull()->comment('Глобальный идентификатор объекта'),
            'objectguid' => $this->char(36)->notNull()->comment('UUID объекта'),
            'changeid' => $this->bigInteger()->notNull()->comment('ID транзакции изменений'),
            'number' => $this->string(50)->notNull()->comment('Номер машиноместа'),
            'opertypeid' => $this->integer()->notNull()->comment('Статус действия (1-2 цифры)'),
            'previd' => $this->bigInteger()->comment('Связь с предыдущей записью'),
            'nextid' => $this->bigInteger()->comment('Связь с последующей записью'),
            'updatedate' => $this->date()->notNull()->comment('Дата обновления'),
            'startdate' => $this->date()->notNull()->comment('Начало действия'),
            'enddate' => $this->date()->notNull()->comment('Окончание действия'),
            'isactual' => $this->tinyInteger(1)->notNull()->comment('Актуальность (0/1)'),
            'isactive' => $this->tinyInteger(1)->notNull()->comment('Активность (0/1)'),
        ]);

        // Первичный ключ
        //$this->addPrimaryKey('PK_car_places_ID', '{{%car_places}}', 'ID');

//        // Индексы для часто используемых полей
//        $this->createIndex('idx_car_places_OBJECTID', '{{%car_places}}', 'OBJECTID');
//        $this->createIndex('idx_car_places_OBJECTGUID', '{{%car_places}}', 'OBJECTGUID');
//        $this->createIndex('idx_car_places_CHANGEID', '{{%car_places}}', 'CHANGEID');
        $sql = <<<SQL
CREATE OR REPLACE PROCEDURE import_car_places_from_xml(xml_path TEXT)
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
                 '/CARPLACES/CARPLACE'
                 PASSING xml_data
                 COLUMNS
                     id          BIGINT   PATH '@ID',
                     objectid    BIGINT   PATH '@OBJECTID',
                     objectguid  UUID     PATH '@OBJECTGUID',
                     changeid    BIGINT   PATH '@CHANGEID',
                     number      TEXT     PATH '@NUMBER',
                     opertypeid  INTEGER  PATH '@OPERTYPEID',
                     previd      BIGINT   PATH '@PREVID',
                     nextid      BIGINT   PATH '@NEXTID',
                     updatedate  DATE     PATH '@UPDATEDATE',
                     startdate   DATE     PATH '@STARTDATE',
                     enddate     DATE     PATH '@ENDDATE',
                     isactual    INTEGER  PATH '@ISACTUAL',
                     isactive    INTEGER  PATH '@ISACTIVE'
             ) AS x
    )
    INSERT INTO car_places (
        id, objectid, objectguid, changeid, number, opertypeid,
        previd, nextid, updatedate, startdate, enddate, isactual, isactive
    )
    SELECT
        id, objectid, objectguid, changeid, number, opertypeid,
        previd, nextid, updatedate, startdate, enddate, isactual, isactive
    FROM extracted_data
    ON CONFLICT (id) DO UPDATE
        SET
            objectid   = EXCLUDED.objectid,
            objectguid = EXCLUDED.objectguid,
            changeid   = EXCLUDED.changeid,
            number     = EXCLUDED.number,
            opertypeid = EXCLUDED.opertypeid,
            previd     = EXCLUDED.previd,
            nextid     = EXCLUDED.nextid,
            updatedate = EXCLUDED.updatedate,
            startdate  = EXCLUDED.startdate,
            enddate    = EXCLUDED.enddate,
            isactual   = EXCLUDED.isactual,
            isactive   = EXCLUDED.isactive;
END;
$$;
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable('{{%car_places}}');
        $this->execute('DROP PROCEDURE IF EXISTS import_car_places_from_xml(TEXT)');

    }
}