<?php

namespace Commands;

use Utils\FileSystemUtility;
use Config\Defs;

class RenameFilesCommand extends CommandBaseClass {

	public function execute(array $args, array $options = array()) {
        parent::execute($args,$options);

        $counterMode = $this->getRenameFilesCounterMode();

        switch($counterMode){
            case Defs::CONFIG_RENAME_FILES_COUNTER_FOLDER:
            $this->recursiveRenameFilesFolderCounter($this->sourceDir);
            break;
            case Defs::CONFIG_RENAME_FILES_COUNTER_UNIQUE:
            $this->recursiveRenameFilesUniqueCounter($this->sourceDir);
            break;
            default:
            $this->recursiveRenameFilesUniqueCounter($this->sourceDir);
            break;
        }

        $this->writeInABox("Rename files completed.",\ConsoleKit\Colors::WHITE, \ConsoleKit\Colors::GREEN);
    }


    protected function recursiveRenameFilesUniqueCounter($directory){
    	$this->writeln("Recursive rename of files in $directory", \ConsoleKit\Colors::CYAN);
        $path = realpath($directory);
        $di = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS), 
                                             \RecursiveIteratorIterator::SELF_FIRST);
        $padlength = FileSystemUtility::getNumberOfPaddingChars(iterator_count($di));
        $count = 1;

        $folderPrefix = null;
        foreach ($di as $name => $fio) {
            if(true == $fio->isFile()){
                //$this->writeln($fio->getFilename().' having path '.basename($fio->getPath()));
                $pageNumber = str_pad($count,$padlength, '0', STR_PAD_LEFT);
                //$this->writeln($pageNumber.'.'.pathinfo($fio->getFilename(),PATHINFO_EXTENSION));
                rename($name, $fio->getPath() . DIRECTORY_SEPARATOR .$pageNumber.'.'.pathinfo($fio->getFilename(),PATHINFO_EXTENSION));
                ++$count;
                unset($pageNumber);
            }
        }

        $this->writeSuccess("$count pages renamed.");
        
    }

    
    protected function recursiveRenameFilesFolderCounter($directory){
        /*
            - fare un directory iterator non ricorsivo (si presume che i capitoli siano già raccolti in cartelle-volumi)
            - su ogni path trovato lanciare recursiveRenameFilesUniqueCounter($pathTrovato)
         */
            $folderCount = 0;
            foreach (new \DirectoryIterator($directory) as $fileInfo) {
                if ($fileInfo->isDir() && !$fileInfo->isDot()) { 
                    $this->writeln('..Now on folder '.$fileInfo->getFilename());
                    $this->recursiveRenameFilesUniqueCounter(realpath($fileInfo->getPath().DIRECTORY_SEPARATOR.$fileInfo->getFilename()));
                    ++$folderCount;
                }
            }
            $this->writeSuccess("File renaming called on $folderCount volume folders.");

        /*
        if(true == $fio->isDir()){
                    $this->writeln('..Now on folder '.$fio->getFilename());
                    $this->writeln('..Prefix for images will be '.$fio->getFilename());
                    $folderPrefix = $fio->getFilename();
                    $count = 1;
                }

                if(true == $fio->isFile()){
                    if($folderPrefix == basename($fio->getPath())){
                        $this->writeln($fio->getFilename().' having path '.basename($fio->getPath()));
                        $pageNumber = str_pad($count,$padlength, '0', STR_PAD_LEFT);
                        $this->writeln($folderPrefix." - ".$pageNumber.'.'.pathinfo($fio->getFilename(),PATHINFO_EXTENSION));
                        ++$count;
                        unset($pageNumber);
                    }else{
                        $this->writeError("Skipping {$fio->getFilename()} assumed prefix and folder path are different");
                    }
                }  
         */
            }


        }