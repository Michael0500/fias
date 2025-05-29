<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use yii\db\Exception;

class XmlImportController extends Controller
{
    private $totalBytesProcessed = 0;
    private $startTime;

    /**
     * Обрабатывает все XML-файлы в папке, вызывает хранимые процедуры в транзакции
     *
     * @param string $dir Путь к папке с XML-файлами
     */
    public function actionProcess()
    {
        $this->startTime = microtime(true);
        $this->totalBytesProcessed = 0;

        $dir = Yii::getAlias('@app/web/docs');
        $this->parse($dir);

        $dir = Yii::getAlias('@app/web/docs/04');
        $this->parse($dir);

        $executionTime = microtime(true) - $this->startTime;

        $totalSec = round($executionTime, 2);
        $totalMin = round( $totalSec / 60, 2);
        $totalH = round($totalMin / 60, 2);
        $totalMB = round($this->totalBytesProcessed / (1024 * 1024), 2);
        $totalGB = round($this->totalBytesProcessed / (1024 * 1024 * 1024), 3);

        $this->stdout("\nИтог:\n", Console::FG_PURPLE);
        $this->stdout("Общее количество времени: {$totalSec} секунд ({$totalMin} мин.) ({$totalH} часов.)\n", Console::FG_PURPLE);
        $this->stdout("Объем обработанных файлов: {$totalMB} MB ({$totalGB} GB)\n", Console::FG_PURPLE);
    }

    private function parse(string $dir){
        if (!is_dir($dir)) {
            $this->stderr("Directory not found: $dir\n", Console::FG_RED);
            return 1;
        }

        $files = glob($dir . DIRECTORY_SEPARATOR . '*.XML');

        if (empty($files)) {
            $this->stdout("No XML files found in directory: $dir\n", Console::FG_YELLOW);
            return 1;
        }

        foreach ($files as $filePath) {
            $fileName = basename($filePath);
            $fileSize = filesize($filePath);
            $this->totalBytesProcessed += $fileSize;
            $fileSizeMB = round($fileSize / (1024 * 1024), 2);

            $this->stdout("Processing file: {$fileName} (Size: {$fileSizeMB} MB)\n", Console::FG_GREEN);

            // Удаляем BOM, если он есть
            $this->removeBom($filePath);

            // Определяем нужную процедуру
            $procedure = $this->getProcedureName($fileName);
            if (!$procedure) {
                $this->stderr("Unknown file type: $fileName, skipping...\n", Console::FG_RED);
                continue;
            }

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $fileStartTime = microtime(true);
                $this->stdout("Start: {$fileName} imported.\n", Console::FG_YELLOW);

                Yii::$app->db->createCommand("CALL {$procedure}(:file_path)")
                    ->bindValue(':file_path', $filePath)
                    ->execute();

                $transaction->commit();

                // Удаляем файл только при успехе
                unlink($filePath);
                $fileTime = round(microtime(true) - $fileStartTime, 2);
                $this->stdout("Success: {$fileName} imported and deleted. Time: {$fileTime} sec\n", Console::FG_BLUE);
            } catch (\Throwable $e) {
                $transaction->rollBack();
                $this->stderr("Failed to process {$fileName}: {$e->getMessage()}\n", Console::FG_RED);
            }
        }

        return 1;
    }

    /**
     * Определяет название процедуры по имени файла
     *
     * @param string $fileName
     * @return string|null
     */
    private function getProcedureName($fileName)
    {
        $map = [
            '/^AS_REESTR_OBJECTS_/'     => 'import_reestr_objects_from_xml',
            '/^AS_MUN_HIERARCHY_/'      => 'import_mun_hierarchy_from_xml',
            '/^AS_NORMATIVE_DOCS_([0-9]{2}).*$/i'     => 'import_normative_docs_from_xml',
            '/^AS_NORMATIVE_DOCS_KINDS_/'     => 'import_normative_doc_kinds_from_xml',
            '/^AS_NORMATIVE_DOCS_TYPES_/'     => 'import_normative_doc_types_from_xml',
            '/^AS_ADDR_OBJ_([0-9]{2}).*$/i' => 'import_address_objects_from_xml',
            '/^AS_ADDR_OBJ_DIVISION_/' => 'import_address_objects_division_from_xml',
            '/^AS_ADDR_OBJ_TYPES_/' => 'import_address_object_types_from_xml',
            '/^AS_ADM_HIERARCHY_/' => 'import_admin_hierarchy_from_xml',
            '/^AS_APARTMENTS_([0-9]{2}).*$/i' => 'import_apartments_from_xml',
            '/^AS_APARTMENT_TYPES_/' => 'import_apartment_types_from_xml',
            '/^AS_CARPLACES_([0-9]{2}).*$/i' => 'import_car_places_from_xml',
            '/^AS_HOUSES_([0-9]{2}).*$/i' => 'import_houses_from_xml',
            '/^AS_CHANGE_HISTORY_/' => 'import_change_history_from_xml',
            '/^AS_HOUSE_TYPES_/' => 'import_housetypes_from_xml',
            '/^AS_ADDHOUSE_TYPES_/' => 'import_housetypes_from_xml',
            '/^AS_OBJECT_LEVELS_/' => 'import_object_levels_from_xml',
            '/^AS_OPERATION_TYPES_/' => 'import_operation_types_from_xml',
            '/^AS_STEADS_PARAMS_([0-9]{2}).*$/i' => 'import_params_from_xml',
            '/^AS_ROOMS_([0-9]{2}).*$/i' => 'import_rooms_from_xml',
            '/^AS_PARAM_TYPES_/' => 'import_param_types_from_xml',
            '/^AS_ROOM_TYPES_/' => 'import_room_types_from_xml',
            '/^AS_STEADS_([0-9]{2}).*$/i' => 'import_steads_from_xml',
            '/^AS_STEADS_PARAMS_/' => 'import_params_from_xml',
            '/^AS_HOUSES_PARAMS_/' => 'import_params_from_xml',
            '/^AS_ADDR_OBJ_PARAMS_/' => 'import_params_from_xml',
            '/^AS_APARTMENTS_PARAMS_/' => 'import_params_from_xml',
            '/^AS_CARPLACES_PARAMS_/' => 'import_params_from_xml',
            '/^AS_ROOMS_PARAMS_/' => 'import_params_from_xml',
        ];

        foreach ($map as $pattern => $procedure) {
            if (preg_match($pattern, $fileName)) {
                return $procedure;
            }
        }

        return null;
    }

    /**
     * Удаляет BOM из файла, если он есть
     *
     * @param string $filePath
     */
    private function removeBom($filePath)
    {
        $content = file_get_contents($filePath);
        $bom = "\xEF\xBB\xBF";

        if (strncmp($content, $bom, 3) === 0) {
            $content = substr($content, 3);
            file_put_contents($filePath, $content);
            $this->stdout("BOM removed from: " . basename($filePath) . "\n", Console::FG_CYAN);
        }
    }
}