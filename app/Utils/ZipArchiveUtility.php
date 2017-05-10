<?php

namespace Utils;

class ZipArchiveUtility {

    public $timeLimit;
    public $memoryLimit;

    /**
     * 
     * @param type $max_execution_time
     * @param type $memory_limit
     */
    public function __construct($max_execution_time = 600, $memory_limit = '1024M') {
        $this->timeLimit = $max_execution_time;
        $this->memoryLimit = $memory_limit;
        $this->resetLimits();
    }

    public function resetLimits() {
        set_time_limit($this->timeLimit);
        ini_set('memory_limit', $this->memoryLimit);
    }

    /*
     * PHP: Recursively Backup Files & Folders to ZIP-File
     * (c) 2012-2014: Marvin Menzerath - http://menzerath.eu
     * Forked by toddsby
     * https://gist.github.com/toddsby/f98d82314259ec5483d8
     */

    public function zipData($source, $destination) {
        if (extension_loaded('zip')) {
            if (file_exists($source)) {
                $zip = new \ZipArchive();
                if ($zip->open($destination, \ZIPARCHIVE::OVERWRITE)) {
                    $source = realpath($source);
                    if (is_dir($source)) {
                        $iterator = new \RecursiveDirectoryIterator($source);
                        // skip dot files while iterating 
                        $iterator->setFlags(\RecursiveDirectoryIterator::SKIP_DOTS);
                        $files = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::SELF_FIRST);
                        foreach ($files as $file) {
                            $file = realpath($file);
                            if (is_dir($file)) {
                                $zip->addEmptyDir(str_replace($source . DIRECTORY_SEPARATOR, '', $file . DIRECTORY_SEPARATOR));
                            } else if (is_file($file)) {
                                $zip->addFromString(str_replace($source . DIRECTORY_SEPARATOR, '', $file), file_get_contents($file));
                            }
                        }
                    } else if (is_file($source)) {
                        $zip->addFromString(basename($source), file_get_contents($source));
                    }
                }
                return $zip->close();
            }
        }
        return false;
    }

    /**
     * Unzip files preserving dir structure
     * @param type $source
     * @param type $destination
     * @return boolean
     * @throws \Exception
     */
    public function unzip($source, $destination) {
        $zip = new \ZipArchive;
        $res = $zip->open($source);
        if ($res === TRUE) {
            $zip->extractTo($destination);
            $zip->close();
            unset($zip);
            return true;
        } else {
            throw new \Exception("ZipArchive $source extractTo $destination failed with code $res");
        }
    }

}
