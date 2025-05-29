<?php

use yii\db\Migration;

class m231004_123517_create_steads_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%steads}}', [
            'id' => $this->bigInteger()->unique()->notNull()->comment('Уникальный идентификатор записи'),
            'objectid' => $this->bigInteger()->notNull()->comment('Глобальный уникальный идентификатор объекта'),
            'objectguid' => $this->char(36)->notNull()->comment('UUID объекта'),
            'changeid' => $this->bigInteger()->notNull()->comment('ID транзакции'),
            'number' => $this->string(250)->notNull()->comment('Номер участка'),
            'opertypeid' => $this->string(2)->notNull()->comment('Тип операции'),
            'previd' => $this->bigInteger()->comment('Предыдущая запись'),
            'nextid' => $this->bigInteger()->comment('Следующая запись'),
            'updatedate' => $this->date()->notNull()->comment('Дата обновления'),
            'startdate' => $this->date()->notNull()->comment('Начало действия'),
            'enddate' => $this->date()->notNull()->comment('Окончание действия'),
            'isactual' => $this->tinyInteger(1)->notNull()->comment('Актуальность (0/1)'),
            'isactive' => $this->tinyInteger(1)->notNull()->comment('Активность (0/1)'),
        ]);

        // Первичный ключ
        //$this->addPrimaryKey('PK_steads_ID', '{{%steads}}', 'ID');

//        // Индексы для часто используемых полей
//        $this->createIndex('idx_steads_OBJECTID', '{{%steads}}', 'OBJECTID');
//        $this->createIndex('idx_steads_OBJECTGUID', '{{%steads}}', 'OBJECTGUID');
//        $this->createIndex('idx_steads_CHANGEID', '{{%steads}}', 'CHANGEID');
//        $this->createIndex('idx_steads_OPERTYPEID', '{{%steads}}', 'OPERTYPEID');
//        $this->createIndex('idx_steads_PREVID', '{{%steads}}', 'PREVID');
//        $this->createIndex('idx_steads_NEXTID', '{{%steads}}', 'NEXTID');
//        $this->createIndex('idx_steads_ISACTUAL', '{{%steads}}', 'ISACTUAL');
//        $this->createIndex('idx_steads_ISACTIVE', '{{%steads}}', 'ISACTIVE');
        $sql = <<<SQL
CREATE OR REPLACE PROCEDURE import_steads_from_xml(xml_path TEXT)
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
                 '/STEADS/STEAD'
                 PASSING xml_data
                 COLUMNS
                   id          BIGINT   PATH '@ID',
                   objectid    BIGINT   PATH '@OBJECTID',
                   objectguid  UUID     PATH '@OBJECTGUID',
                   changeid    BIGINT   PATH '@CHANGEID',
                   number      TEXT     PATH '@NUMBER',
                   opertypeid  TEXT     PATH '@OPERTYPEID',
                   previd      BIGINT   PATH '@PREVID',
                   nextid      BIGINT   PATH '@NEXTID',
                   updatedate  DATE     PATH '@UPDATEDATE',
                   startdate   DATE     PATH '@STARTDATE',
                   enddate     DATE     PATH '@ENDDATE',
                   isactual    INTEGER  PATH '@ISACTUAL',
                   isactive    INTEGER  PATH '@ISACTIVE'
             ) AS x
    )
    INSERT INTO steads (
        id, objectid, objectguid, changeid, number, opertypeid,
        previd, nextid, updatedate, startdate, enddate, isactual, isactive
    )
    SELECT 
        id, objectid, objectguid, changeid, number, opertypeid,
        previd, nextid, updatedate, startdate, enddate, isactual, isactive
    FROM extracted_data
    ON CONFLICT (id) DO UPDATE
    SET
      objectid = EXCLUDED.objectid,
      objectguid = EXCLUDED.objectguid,
      changeid = EXCLUDED.changeid,
      number = EXCLUDED.number,
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
        $this->dropTable('{{%steads}}');
        $this->execute('DROP PROCEDURE IF EXISTS import_steads_from_xml(TEXT)');

    }
}