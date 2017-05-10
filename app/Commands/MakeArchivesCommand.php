<?php

namespace Commands;

use Utils\ZipArchiveUtility;

class MakeArchivesCommand extends CommandBaseClass {

    protected $zipUtility;

    public function execute(array $args, array $options = array()) {
        parent::execute($args,$options);
        $this->zipUtility = new ZipArchiveUtility();
        $this->makeArchivesFromFolders($this->sourceDir, $this->destDir, $this->zipUtility);
        $this->writeInABox("Make archives from folders completed.",\ConsoleKit\Colors::WHITE, \ConsoleKit\Colors::GREEN);
    }

    protected function getCbzName($folderName) {
        return "{$folderName}_tankobon.cbz";
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
                $zipToCreate = $destDir . DIRECTORY_SEPARATOR . $this->getCbzName($fileInfo->getFilename());
                $this->writeln("..Creating $zipToCreate", \ConsoleKit\Colors::WHITE);
                $result = $zipArchiveUtility->zipData($folderToZip, $zipToCreate);
                if($result){
                    $this->writeHighlight("+ Archive $zipToCreate created.",\ConsoleKit\Colors::GREEN);
                    ++$archivesCount;
                    $zipArchiveUtility->resetLimits();
                }else{
                    $this->writeerr("! Error creating $zipToCreate archive");
                }
                
                unset($folderToZip,$zipToCreate,$result);
            }
        }
        $this->writeln("$archivesCount archives created from $sourceDir to $destDir.", \ConsoleKit\Colors::GREEN | \ConsoleKit\Colors::BOLD);
    }

}
