<?php

namespace App\Services\Configuration;

use Exception;

class ConfigurationFactory
{
    /**
     * @param string $filePath
     *
     * @return null|Configuration
     */
    public static function buildFromFilesystem(string $filePath): ?Configuration
    {
        $data = static::retrieveDataFromFilesystem($filePath);
        if (is_array($data)) {
            return new Configuration($data);
        }
        return null;
    }

    /**
     * @param string $filePath
     *
     * @return mixed
     * @throws Exception
     */
    private static function retrieveDataFromFilesystem(string $filePath)
    {
        $data = null;
        if (is_dir($filePath)) {
            $data = self::retrieveDataFromDirectory($filePath);
        } elseif (is_file($filePath)) {
            $data = self::retrieveDataFromFile($filePath);
        }
        return $data;
    }

    /**
     * @param string $directoryPath
     *
     * @return null|array
     * @throws Exception
     */
    private static function retrieveDataFromDirectory(string $directoryPath): ?array
    {
        if (!($directory = opendir($directoryPath))) {
            throw new Exception('Unable to open directory');
        }

        $data = null;
        while (($childFileName = readdir($directory)) !== false) {
            // Filter . and ..
            if (trim($childFileName, '.') != '') {
                $childFilePath = $directoryPath . DIRECTORY_SEPARATOR . $childFileName;
                $childFilePathInfo = pathinfo($childFilePath);

                try {
                    $retrievedData = static::retrieveDataFromFilesystem($childFilePath);
                    if ($retrievedData !== null) {
                        if ($data === null) {
                            $data = [];
                        }
                        $data[$childFilePathInfo['filename']] = $retrievedData;
                    }
                } catch (Exception $e) {
                    continue;
                }
            }
        }
        closedir($directory);

        return $data;
    }

    /**
     * @param string $filePath
     *
     * @return mixed
     * @throws Exception
     */
    private static function retrieveDataFromFile(string $filePath)
    {
        $filePathInfo = pathinfo($filePath);
        if ($filePathInfo['extension'] != 'php') {
            throw new Exception('Not a PHP file');
        }

        return require $filePath;
    }
}
