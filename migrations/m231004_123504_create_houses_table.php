<?php

use yii\db\Migration;

class m231004_123504_create_houses_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%houses}}', [
            'id' => $this->bigInteger()->notNull()->unique()->comment('Уникальный идентификатор записи'),
            'objectid' => $this->bigInteger()->notNull()->comment('Глобальный идентификатор объекта'),
            'objectguid' => $this->char(36)->notNull()->comment('UUID объекта'),
            'changeid' => $this->bigInteger()->notNull()->comment('ID транзакции изменений'),
            'housenum' => $this->string(50)->comment('Основной номер дома'),
            'addnum1' => $this->string(50)->comment('Дополнительный номер 1'),
            'addnum2' => $this->string(50)->comment('Дополнительный номер 2'),
            'housetype' => $this->bigInteger()->comment('Тип дома'),
            'addtype1' => $this->bigInteger()->comment('Доп. тип 1'),
            'addtype2' => $this->bigInteger()->comment('Доп. тип 2'),
            'opertypeid' => $this->bigInteger()->notNull()->comment('Статус действия'),
            'previd' => $this->bigInteger()->comment('Предыдущая запись'),
            'nextid' => $this->bigInteger()->comment('Следующая запись'),
            'updatedate' => $this->date()->notNull()->comment('Дата обновления'),
            'startdate' => $this->date()->notNull()->comment('Начало действия'),
            'enddate' => $this->date()->notNull()->comment('Окончание действия'),
            'isactual' => $this->tinyInteger(1)->notNull()->comment('Актуальность (0/1)'),
            'isactive' => $this->tinyInteger(1)->notNull()->comment('Активность (0/1)'),
        ]);

        // Первичный ключ
        //$this->addPrimaryKey('PK_houses_ID', '{{%houses}}', 'ID');

//        // Индексы для часто используемых полей
//        $this->createIndex('idx_houses_OBJECTID', '{{%houses}}', 'OBJECTID');
//        $this->createIndex('idx_houses_OBJECTGUID', '{{%houses}}', 'OBJECTGUID');
//        $this->createIndex('idx_houses_CHANGEID', '{{%houses}}', 'CHANGEID');

        $sql = <<<SQL
CREATE OR REPLACE PROCEDURE import_houses_from_xml(xml_path TEXT)
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
                 '/HOUSES/HOUSE'
                 PASSING xml_data
                 COLUMNS
                     id          BIGINT      PATH '@ID',
                     objectid    BIGINT      PATH '@OBJECTID',
                     objectguid  UUID        PATH '@OBJECTGUID',
                     changeid    BIGINT      PATH '@CHANGEID',
                     housenum    TEXT        PATH '@HOUSENUM',
                     addnum1     TEXT        PATH '@ADDNUM1',
                     addnum2     TEXT        PATH '@ADDNUM2',
                     housetype   INTEGER     PATH '@HOUSETYPE',
                     addtype1    INTEGER     PATH '@ADDTYPE1',
                     addtype2    INTEGER     PATH '@ADDTYPE2',
                     opertypeid  INTEGER     PATH '@OPERTYPEID',
                     previd      BIGINT      PATH '@PREVID',
                     nextid      BIGINT      PATH '@NEXTID',
                     updatedate  DATE        PATH '@UPDATEDATE',
                     startdate   DATE        PATH '@STARTDATE',
                     enddate     DATE        PATH '@ENDDATE',
                     isactual    INTEGER     PATH '@ISACTUAL',
                     isactive    INTEGER     PATH '@ISACTIVE'
             ) AS x
    )
    INSERT INTO houses (
        id, objectid, objectguid, changeid, housenum, addnum1, addnum2,
        housetype, addtype1, addtype2, opertypeid, previd, nextid,
        updatedate, startdate, enddate, isactual, isactive
    )
    SELECT
        id, objectid, objectguid, changeid, housenum, addnum1, addnum2,
        housetype, addtype1, addtype2, opertypeid, previd, nextid,
        updatedate, startdate, enddate, isactual, isactive
    FROM extracted_data
    ON CONFLICT (id) DO UPDATE
        SET
            objectid    = EXCLUDED.objectid,
            objectguid  = EXCLUDED.objectguid,
            changeid    = EXCLUDED.changeid,
            housenum    = EXCLUDED.housenum,
            addnum1     = EXCLUDED.addnum1,
            addnum2     = EXCLUDED.addnum2,
            housetype   = EXCLUDED.housetype,
            addtype1    = EXCLUDED.addtype1,
            addtype2    = EXCLUDED.addtype2,
            opertypeid  = EXCLUDED.opertypeid,
            previd      = EXCLUDED.previd,
            nextid      = EXCLUDED.nextid,
            updatedate  = EXCLUDED.updatedate,
            startdate   = EXCLUDED.startdate,
            enddate     = EXCLUDED.enddate,
            isactual    = EXCLUDED.isactual,
            isactive    = EXCLUDED.isactive;
END;
$$;
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable('{{%houses}}');
        $this->execute('DROP PROCEDURE IF EXISTS import_houses_from_xml(TEXT)');

    }
}