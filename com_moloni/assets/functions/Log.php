<?php

namespace Moloni\Functions;

class Log
{
    private static $fileName = false;

    public static function write($message)
    {
        if(!is_dir(__DIR__ . '/../../logs')) {
            if (!mkdir($concurrentDirectory = __DIR__ . '/../../logs') && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }
        }

        $fileName = (defined('COMPANY_ID') ? COMPANY_ID : '000')
            . (self::$fileName ? self::$fileName . '.log' : date('Ymd'))
            . '.log';

        $logFile = fopen(__DIR__ . '/../../logs/' . $fileName, 'ab');
        fwrite($logFile, '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL);

    }

    public static function getFileUrl()
    {
        $fileName = (defined('COMPANY_ID') ? COMPANY_ID : '000')
            . (self::$fileName ? self::$fileName . '.log' : date('Ymd'))
            . '.log';

        return  __DIR__ . '/../../logs/' . $fileName;
    }

    public static function removeLogs()
    {
        $logFiles = glob(__DIR__ . '/../../logs/*.log');
        if (!empty($logFiles) && is_array($logFiles)) {
            $deleteSince = strtotime(date('Y-m-d'));
            foreach ($logFiles as $file) {
                if (filemtime($file) < $deleteSince) {
                    unlink($file);
                }
            }
        }

    }

    public static function setFileName($name)
    {
        if (!empty($name)) {
            self::$fileName = $name;
        }
    }

}