<?php

namespace Utils;

class ZipArchiveUtility
{
    private $timeLimit;
    private $memoryLimit;
    private $ZIP_ERROR = [
        \ZipArchive::ER_EXISTS => 'File already exists.',
        \ZipArchive::ER_INCONS => 'Zip archive inconsistent.',
        \ZipArchive::ER_INVAL => 'Invalid argument.',
        \ZipArchive::ER_MEMORY => 'Malloc failure.',
        \ZipArchive::ER_NOENT => 'No such file.',
        \ZipArchive::ER_NOZIP => 'Not a zip archive.',
        \ZipArchive::ER_OPEN => "Can't open file.",
        \ZipArchive::ER_READ => 'Read error.',
        \ZipArchive::ER_SEEK => 'Seek error.',
    ];
    
    /**
    *
    * @param type $max_execution_time
    * @param type $memory_limit
    */
    public function __construct($max_execution_time = 600, $memory_limit = '1024M')
    {
        $this->timeLimit = $max_execution_time;
        $this->memoryLimit = $memory_limit;
        $this->resetLimits();
    }
    
    public function resetLimits()
    {
        set_time_limit($this->timeLimit);
        ini_set('memory_limit', $this->memoryLimit);
    }
    
    
    /**
     * Zip data recursively
     * see original https://gist.github.com/toddsby/f98d82314259ec5483d8
     *
     * @param string $source
     * @param string $destination
     * @return bool
     */
    public function zipData($source, $destination)
    {
        if (extension_loaded('zip')) {
            if (file_exists($source)) {
                $zip = new \ZipArchive();
                $zipOpenCode = $zip->open($destination, $this->getZipArchiveOpenMode($destination));

                if ($zipOpenCode !== true) {
                    throw new \Exception("ZipArchive open failed: {$this->getZipArchiveOpenErrorMessage($zipOpenCode)}");
                }
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
                        } elseif (is_file($file)) {
                            $zip->addFromString(str_replace($source . DIRECTORY_SEPARATOR, '', $file), file_get_contents($file));
                        }
                    }
                } elseif (is_file($source)) {
                    $zip->addFromString(basename($source), file_get_contents($source));
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
    public function unzip($source, $destination)
    {
        $zip = new \ZipArchive;
        $res = $zip->open($source);
        if ($res === true) {
            $zip->extractTo($destination);
            $zip->close();
            unset($zip);
            return true;
        } else {
            throw new \Exception("ZipArchive $source extractTo $destination failed with code $res");
        }
    }
    
    /**
    * Get the correct ZipArchive mode to use
    *
    * @param string $destination
    * @return string
    */
    private function getZipArchiveOpenMode($destination)
    {
        return $mode = file_exists($destination) ? \ZIPARCHIVE::OVERWRITE : \ZIPARCHIVE::CREATE;
    }
    
    /**
    * Return the error message for ZipArchive::open
    *
    * @param string $result_code
    * @return string
    */
    private function getZipArchiveOpenErrorMessage($result_code)
    {
        return isset($this->ZIP_ERROR[$result_code])? $this->ZIP_ERROR[$result_code] : 'Unknown error.';
    }
}
