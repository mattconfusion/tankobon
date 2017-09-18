<?php

namespace Commands;

use Utils\ZipArchiveUtility;
use Utils\FileSystemUtility;
use Config\Defs;

class MakeArchivesCommand extends CommandBaseClass {

    protected $zipUtility;
    protected $archivePrefix;
    protected $archiveSuffix;
    protected $cleanupAfter = false;

    public function execute(array $args, array $options = array()) {
        parent::execute($args,$options);
        
        if(isset($options[Defs::CLI_OPTIONS_ARCHIVE_PREFIX])){
            $this->archivePrefix = $options[Defs::CLI_OPTIONS_ARCHIVE_PREFIX];
        }

        if(isset($options[Defs::CLI_OPTIONS_ARCHIVE_SUFFIX])){
            $this->archiveSuffix = $options[Defs::CLI_OPTIONS_ARCHIVE_SUFFIX];
        }

        if(isset($options[Defs::CLI_OPTIONS_CLEANUP])){
            $this->cleanupAfter = true;
        }

        if(isset($options[Defs::CLI_OPTIONS_SANITIZE])){
            $sanitizeCmd = new SanitizeCommand($this->console);
            $sanitizeCmd->execute(array($this->sourceDir),$options);
        }

        $this->zipUtility = new ZipArchiveUtility();
        $this->makeArchivesFromFolders($this->sourceDir, $this->destDir, $this->zipUtility);
        $this->writeInABox("Make archives from folders completed.",\ConsoleKit\Colors::WHITE, \ConsoleKit\Colors::GREEN);
    }

    protected function getCbzName($folderName,$prefix='',$suffix='_tankobon') {
        return "{$prefix}{$folderName}{$suffix}.cbz";
    }

    /**
     * Write zip archives to dest directory from folders found at the first level of source directory
     * @param string $sourceDir
     * @param string $destDir
     * @param \Utils\ZipArchiveUtility $zipArchiveUtility
     */
    protected function makeArchivesFromFolders($sourceDir, $destDir, ZipArchiveUtility $zipArchiveUtility) {
        $this->writeln("Make archives of folders found at $sourceDir", \ConsoleKit\Colors::CYAN);
        $archivesCount = 0;
        foreach (new \DirectoryIterator($sourceDir) as $fileInfo) {
            if ($fileInfo->isDir() && !$fileInfo->isDot()) {    
                $folderToZip = $fileInfo->getPath() . DIRECTORY_SEPARATOR . $fileInfo->current() . DIRECTORY_SEPARATOR;
                $this->writeln("..Found path $folderToZip", \ConsoleKit\Colors::WHITE);
                $zipToCreate = $destDir . DIRECTORY_SEPARATOR . $this->getCbzName($fileInfo->getFilename(),$this->archivePrefix,$this->archiveSuffix);
                $this->writeln("..Creating $zipToCreate", \ConsoleKit\Colors::WHITE);
                $result = $zipArchiveUtility->zipData($folderToZip, $zipToCreate);
                if($result){
                    $this->writeHighlight("+ Archive $zipToCreate created.",\ConsoleKit\Colors::GREEN);
                    ++$archivesCount;
                    $zipArchiveUtility->resetLimits();
                    //if cleanup is true, remove all the folders leaving just the archives
                    if(true == $this->cleanupAfter){
                        $dirremoved = FileSystemUtility::removeDirectory($folderToZip);
                        if($dirremoved){
                            $this->writeln("..Removed $folderToZip", \ConsoleKit\Colors::YELLOW);
                        }else{
                            $this->writeerr("! Unable to remove $folderToZip");
                        }
                    }

                }else{
                    $this->writeerr("! Error creating $zipToCreate archive");
                }
                
                unset($folderToZip,$zipToCreate,$result);
            }
        }
        $this->writeln("$archivesCount archives created from $sourceDir to $destDir.", \ConsoleKit\Colors::GREEN | \ConsoleKit\Colors::BOLD);
    }

}
