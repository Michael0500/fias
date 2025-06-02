<?php

/**
 * @var $argc int
 * @var $argv array
 */

namespace app\commands;

use yii\console\Controller;
use yii\helpers\Console;

class DownloadController extends Controller
{
    public function actionUrl()
    {
        if ($argc < 2) {
            echo "Использование: php download.php <URL> [выходной_файл]\n";
            exit(1);
        }

        $url = $argv[1];
        $filename = $argc > 2 ? $argv[2] : basename(parse_url($url, PHP_URL_PATH));

        if (empty($filename)) {
            $filename = 'downloaded_file_' . time();
        }

        $resumePosition = 0;
        $isResume = false;

        // Проверяем существование файла для докачки
        if (file_exists($filename)) {
            $resumePosition = filesize($filename);
            $isResume = true;
        }

        $fp = @fopen($filename, $isResume ? 'a+b' : 'w+b');
        if (!$fp) {
            echo "Ошибка: Не удалось открыть файл '$filename'\n";
            exit(1);
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_FILE => $fp,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => 'ResumeDownloader/1.0',
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RANGE => $isResume ? "$resumePosition-" : '',
            CURLOPT_NOPROGRESS => false,
            CURLOPT_PROGRESSFUNCTION => function ($ch, $dTotal, $dNow, $uTotal, $uNow) use ($resumePosition) {
                static $lastPrint = 0;
                $now = microtime(true);

                if ($dTotal > 0 && ($now - $lastPrint > 0.5 || $dNow == $dTotal)) {
                    $progress = $resumePosition + $dNow;
                    $total = $resumePosition + $dTotal;
                    $percent = $total > 0 ? round(($progress / $total) * 100) : 0;

                    $progressHuman = $this->formatBytes($progress);
                    $totalHuman = $this->formatBytes($total);

                    echo "\rПрогресс: $percent% [$progressHuman / $totalHuman]";
                    $lastPrint = $now;
                }

                return 0;
            }
        ]);

        echo "Начало загрузки: $url\n";
        if ($isResume) {
            echo "Продолжение с позиции: " . $this->formatBytes($resumePosition) . "\n";
        }

        if (!curl_exec($ch)) {
            echo "\nОшибка: " . curl_error($ch) . "\n";
            fclose($fp);
            curl_close($ch);
            exit(1);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $downloadedBytes = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);
        $totalSize = $resumePosition + $downloadedBytes;

        curl_close($ch);
        fclose($fp);

        echo "\n";

        if (($httpCode == 206 && $isResume) || $httpCode == 200) {
            echo "Файл успешно " . ($isResume ? "докачан" : "скачан") . "!\n";
            echo "Итоговый размер: " . $this->formatBytes($totalSize) . "\n";
            echo "Сохранено как: $filename\n";
            exit(0);
        }

        echo "Ошибка: Сервер вернул код $httpCode\n";
        if ($httpCode == 416) {
            echo "Запрашиваемый диапазон недоступен (файл уже полностью скачан?)\n";
            exit(0);
        }

        exit(1);
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}