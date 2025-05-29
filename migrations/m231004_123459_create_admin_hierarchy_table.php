<?php

use yii\db\Migration;

class m231004_123459_create_admin_hierarchy_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%admin_hierarchy}}', [
            'id' => $this->bigInteger()->notNull()->unique()->comment('Уникальный идентификатор записи'),
            'objectid' => $this->bigInteger()->notNull()->comment('Глобальный идентификатор объекта'),
            'parentobjid' => $this->bigInteger()->comment('Идентификатор родительского объекта'),
            'changeid' => $this->bigInteger()->notNull()->comment('ID транзакции изменений'),
            'regioncode' => $this->string(4)->comment('Код региона (1-4 цифры)'),
            'areacode' => $this->string(4)->comment('Код района (1-4 цифры)'),
            'citycode' => $this->string(4)->comment('Код города (1-4 цифры)'),
            'placecode' => $this->string(4)->comment('Код населенного пункта'),
            'plancode' => $this->string(4)->comment('Код ЭПС'),
            'streetcode' => $this->string(4)->comment('Код улицы'),
            'previd' => $this->bigInteger()->comment('Связь с предыдущей записью'),
            'nextid' => $this->bigInteger()->comment('Связь с последующей записью'),
            'updatedate' => $this->date()->notNull()->comment('Дата обновления'),
            'startdate' => $this->date()->notNull()->comment('Начало действия'),
            'enddate' => $this->date()->notNull()->comment('Окончание действия'),
            'isactive' => $this->tinyInteger(1)->notNull()->comment('Активность (0/1)'),
            'path' => $this->text()->notNull()->comment('Иерархический путь'),
        ]);

        // Первичный ключ
        //$this->addPrimaryKey('PK_admin_hierarchy_ID', '{{%admin_hierarchy}}', 'ID');

//        // Индексы для ключевых полей
//        $this->createIndex('idx_admin_hierarchy_OBJECTID', '{{%admin_hierarchy}}', 'OBJECTID');
//        $this->createIndex('idx_admin_hierarchy_PARENTOBJID', '{{%admin_hierarchy}}', 'PARENTOBJID');
//        $this->createIndex('idx_admin_hierarchy_CHANGEID', '{{%admin_hierarchy}}', 'CHANGEID');
//        $this->createIndex('idx_admin_hierarchy_ISACTIVE', '{{%admin_hierarchy}}', 'ISACTIVE');

        $sql = <<<SQL
CREATE OR REPLACE PROCEDURE import_admin_hierarchy_from_xml(xml_path TEXT)
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
                     id           BIGINT  PATH '@ID',
                     objectid     BIGINT  PATH '@OBJECTID',
                     parentobjid  BIGINT  PATH '@PARENTOBJID',
                     changeid     BIGINT  PATH '@CHANGEID',
                     regioncode   TEXT    PATH '@REGIONCODE',
                     areacode     TEXT    PATH '@AREACODE',
                     citycode     TEXT    PATH '@CITYCODE',
                     placecode    TEXT    PATH '@PLACECODE',
                     plancode     TEXT    PATH '@PLANCODE',
                     streetcode   TEXT    PATH '@STREETCODE',
                     previd       BIGINT  PATH '@PREVID',
                     nextid       BIGINT  PATH '@NEXTID',
                     updatedate   DATE    PATH '@UPDATEDATE',
                     startdate    DATE    PATH '@STARTDATE',
                     enddate      DATE    PATH '@ENDDATE',
                     isactive     INTEGER PATH '@ISACTIVE',
                     path         TEXT    PATH '@PATH'
             ) AS x
    )
    INSERT INTO admin_hierarchy (
        id, objectid, parentobjid, changeid,
        regioncode, areacode, citycode, placecode, plancode, streetcode,
        previd, nextid,
        updatedate, startdate, enddate,
        isactive, path
    )
    SELECT
        id, objectid, parentobjid, changeid,
        regioncode, areacode, citycode, placecode, plancode, streetcode,
        previd, nextid,
        updatedate, startdate, enddate,
        isactive, path
    FROM extracted_data
    ON CONFLICT (id) DO UPDATE
        SET
            objectid    = EXCLUDED.objectid,
            parentobjid = EXCLUDED.parentobjid,
            changeid    = EXCLUDED.changeid,
            regioncode  = EXCLUDED.regioncode,
            areacode    = EXCLUDED.areacode,
            citycode    = EXCLUDED.citycode,
            placecode   = EXCLUDED.placecode,
            plancode    = EXCLUDED.plancode,
            streetcode  = EXCLUDED.streetcode,
            previd      = EXCLUDED.previd,
            nextid      = EXCLUDED.nextid,
            updatedate  = EXCLUDED.updatedate,
            startdate   = EXCLUDED.startdate,
            enddate     = EXCLUDED.enddate,
            isactive    = EXCLUDED.isactive,
            path        = EXCLUDED.path;
END;
$$;
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable('{{%admin_hierarchy}}');
        $this->execute('DROP PROCEDURE IF EXISTS import_admin_hierarchy_from_xml(TEXT)');

    }
}