<?php

namespace Commands;

use Utils\ZipArchiveUtility;

class ExtractArchivesCommand extends CommandBaseClass {

    protected $zipUtility;

    public function execute(array $args, array $options = array()) {
        parent::execute($args,$options);
        $this->zipUtility = new ZipArchiveUtility();
        $this->extractArchivesFoundInFolder($this->sourceDir, $this->destDir, $this->zipUtility);
        $this->writeInABox("Extract archives to folders completed.",\ConsoleKit\Colors::WHITE, \ConsoleKit\Colors::GREEN);
    }

    /**
     * Write zip archives to dest directory from folders found at the first level of source directory
     * @param string $sourceDir
     * @param string $destDir
     * @param \Utils\ZipArchiveUtility $zipArchiveUtility
     */
    protected function extractArchivesFoundInFolder($sourceDir, $destDir, ZipArchiveUtility $zipArchiveUtility) {
        $this->writeln("Extract archives of folders found at $sourceDir", \ConsoleKit\Colors::CYAN);
        $foldersCount = 0;

        if (!is_dir($destDir)) {
            $this->writeln("..$destDir not found, creating it", \ConsoleKit\Colors::YELLOW);
            $mkdirResult = mkdir($destDir, '0777', true);
            if(!$mkdirResult){
                $this->writeErrorBox("Error while creating destination directory.");
                exit;
            }else{
                $this->writeHighlight("+ $destDir successfully created.", \ConsoleKit\Colors::GREEN);
            }
        }

        foreach (glob("{$sourceDir}" . DIRECTORY_SEPARATOR . '*.{zip,cbz}', GLOB_BRACE) as $file) {
            $this->writeln("..Found file $file", \ConsoleKit\Colors::WHITE);
            $folderToCreate = $destDir . DIRECTORY_SEPARATOR . pathinfo($file, PATHINFO_FILENAME);
            $this->writeln("..Creating $folderToCreate", \ConsoleKit\Colors::WHITE);
            $result = $zipArchiveUtility->unzip($file, $folderToCreate);
            if ($result) {
                $this->writeHighlight("+ Archive $file extracted to $folderToCreate .", \ConsoleKit\Colors::GREEN);
                ++$foldersCount;
                $zipArchiveUtility->resetLimits();
            } else {
                $this->writeerr("! Error creating $folderToCreate archive");
            }
            unset($folderToCreate,$result);
        }
        $this->writeSuccess("$foldersCount archives extracted.");
    }

}
