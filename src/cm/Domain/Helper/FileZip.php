<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 16.08.18
 * Time: 14:58
 */

namespace GenBM\Domain\Helper;

use GenBM\Exception as Ex;

class FileZip
{
    public static function CreateArchive($source, $destination,$rootDir='')
    {
        if(strlen($rootDir)>0)
            $rootDirSep="/";
        else
            $rootDirSep="";
        if (!extension_loaded('zip'))
            throw new Ex\GenerationException("No extension found for zip");

        $zip = new \ZipArchive();
        if (!$zip->open($destination, \ZIPARCHIVE::CREATE))
            throw new Ex\GenerationException("No create zip file");

        $source = str_replace('\\', DIRECTORY_SEPARATOR, realpath($source));
        $source = str_replace('/', DIRECTORY_SEPARATOR, $source);

        if (is_dir($source) === true) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source),
                \RecursiveIteratorIterator::SELF_FIRST);
            $zip->addEmptyDir($rootDir);
            foreach ($files as $file) {
                $file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
                $file = str_replace('/', DIRECTORY_SEPARATOR, $file);

                if(substr($file, strrpos($file, DIRECTORY_SEPARATOR) + 1)=="delfile.json")
                    continue;

                if ($file == '.' || $file == '..' || empty($file) || $file == DIRECTORY_SEPARATOR) {
                    continue;
                }
                // Ignore "." and ".." folders
                if (in_array(substr($file, strrpos($file, DIRECTORY_SEPARATOR) + 1), array('.', '..'))) {
                    continue;
                }

                $file = realpath($file);
                $file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
                $file = str_replace('/', DIRECTORY_SEPARATOR, $file);

                if (is_dir($file) === true) {
                    $d = str_replace($source . DIRECTORY_SEPARATOR, '', $file);
                    if (empty($d)) {
                        continue;
                    }
                    $zip->addEmptyDir($rootDir.$rootDirSep.$d);
                } elseif (is_file($file) === true) {
                    $zip->addFromString($rootDir.$rootDirSep.str_replace($source . DIRECTORY_SEPARATOR, '', $file),
                        file_get_contents($file));
                } else {
                    // do nothing
                }
            }
        } elseif (is_file($source) === true) {
            $zip->addFromString(basename($source), file_get_contents($source));
        }
        return $zip->close();
    }
}