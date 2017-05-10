<?php

namespace Utils;

class FileSystemUtility {

    /**
     * Sanitize name of a file or a dir through iconv
     * @param string $name the name of the file
     * @param string $outputCharset output charset, see iconv documentation
     * @param string $inputCharset input charset
     * @param string $locale the locale used in the translit operation in iconv
     * @return string
     * @throws \Exception if iconv returns false
     */
    public static function sanitizeNameIconv($name, $outputCharset = 'ASCII//TRANSLIT', $inputCharset = 'UTF-8', $locale = 'en_GB') {
        setlocale(LC_ALL, $locale);
        $result = iconv($inputCharset, $outputCharset, $name);
        if ($result) {
            return $result;
        } else {
            throw new \Exception("Iconv conversion of $name returned false.");
        }
    }

    /**
     * Sanitize name of file or directory using regex replace and deleteing the chars
     * @param string $name
     * @param string $regexPattern
     * @return string
     */
    public static function sanitizeNameRegex($name, $regexPattern = '/[^\x00-\x7F]/') {
        return preg_replace($regexPattern, '', $name);
    }

    /**
     * Recursive copy of files and folders
     * @param  string $source Source from which copy
     * @param  string $dest   Destination of the copy
     * @return 
     */
    public static function recursiveCopy($source,$dest){
        $dir = opendir($source); 
        @mkdir($dest); 
        while(false !== ( $file = readdir($dir)) ) { 
            if (( $file != '.' ) && ( $file != '..' )) { 
                if ( is_dir($source . DIRECTORY_SEPARATOR . $file) ) { 
                    self::recursiveCopy($source . DIRECTORY_SEPARATOR . $file,$dest . DIRECTORY_SEPARATOR . $file); 
                } 
                else { 
                    copy($source . DIRECTORY_SEPARATOR . $file,$dest . DIRECTORY_SEPARATOR . $file); 
                } 
            } 
        } 
        closedir($dir); 
    }


    public static function getNumberOfPaddingChars($elementsCount){
        $floatNumber = (float)"0.$elementsCount";
        $range = (int) $elementsCount / $floatNumber;
        $pads = substr_count((string)$range, '0')+1;
        return $pads;
    }

}
