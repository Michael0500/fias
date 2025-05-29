<?php

use yii\db\Migration;

class m231004_123512_create_params_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%params}}', [
            'id' => $this->bigInteger()->notNull()->unique()->comment('Идентификатор записи'),
            'objectid' => $this->bigInteger()->notNull()->comment('Глобальный идентификатор объекта'),
            'changeid' => $this->bigInteger()->comment('ID изменяющей транзакции (опционально)'),
            'changeidend' => $this->bigInteger()->notNull()->comment('ID завершающей транзакции'),
            'typeid' => $this->bigInteger()->notNull()->comment('Тип параметра (4 цифры)'),
            'value' => $this->text()->notNull()->comment('Значение параметра'),
            'updatedate' => $this->date()->notNull()->comment('Дата обновления'),
            'startdate' => $this->date()->notNull()->comment('Начало действия'),
            'enddate' => $this->date()->notNull()->comment('Окончание действия'),
        ]);

        // Первичный ключ
        //$this->addPrimaryKey('PK_params_ID', '{{%params}}', 'ID');

//        // Индексы для связей и поиска
//        $this->createIndex('idx_params_OBJECTID', '{{%params}}', 'OBJECTID');
//        $this->createIndex('idx_params_TYPEID', '{{%params}}', 'TYPEID');
//        $this->createIndex('idx_params_CHANGEIDEND', '{{%params}}', 'CHANGEIDEND');

        $sql = <<<SQL
CREATE OR REPLACE PROCEDURE import_params_from_xml(xml_path TEXT)
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
                 '/PARAMS/PARAM'
                 PASSING xml_data
                 COLUMNS
                   id          BIGINT   PATH '@ID',
                   objectid    BIGINT   PATH '@OBJECTID',
                   changeid    BIGINT   PATH '@CHANGEID',
                   changeidend BIGINT   PATH '@CHANGEIDEND',
                   typeid      INTEGER  PATH '@TYPEID',
                   value       TEXT     PATH '@VALUE',
                   updatedate  DATE     PATH '@UPDATEDATE',
                   startdate   DATE     PATH '@STARTDATE',
                   enddate     DATE     PATH '@ENDDATE'
             ) AS x
    )
    INSERT INTO params (
        id, objectid, changeid, changeidend, typeid, value, updatedate, startdate, enddate
    )
    SELECT id, objectid, changeid, changeidend, typeid, value, updatedate, startdate, enddate
    FROM extracted_data
    ON CONFLICT (id) DO UPDATE
    SET
      objectid = EXCLUDED.objectid,
      changeid = EXCLUDED.changeid,
      changeidend = EXCLUDED.changeidend,
      typeid = EXCLUDED.typeid,
      value = EXCLUDED.value,
      updatedate = EXCLUDED.updatedate,
      startdate = EXCLUDED.startdate,
      enddate = EXCLUDED.enddate;
END;
$$;
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable('{{%params}}');
        $this->execute('DROP PROCEDURE IF EXISTS import_params_from_xml(TEXT)');

    }
}