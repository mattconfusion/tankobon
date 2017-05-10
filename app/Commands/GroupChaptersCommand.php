<?php

namespace Commands;

use Config\Defs;
use Utils\FileSystemUtility;

class GroupChaptersCommand extends CommandBaseClass {

	public function execute(array $args, array $options = array()) {
		parent::execute($args,$options);
		if(Defs::CONFIG_GROUPING_MODE_VOLUME == $this->getGroupingMode()){
			$this->groupFoldersVolumeMode($this->sourceDir,$this->destDir);
		}else{
			$this->groupFoldersChapterMode($this->sourceDir,$this->destDir);
		}
		$this->writeInABox("Group chapter folders completed.",\ConsoleKit\Colors::WHITE, \ConsoleKit\Colors::GREEN);
	}

	/**
	 * [groupFoldersVolumeMode description]
	 * @param  [type] $sourceDir [description]
	 * @param  [type] $destDir   [description]
	 * @return [type]            [description]
	 */
	protected function groupFoldersVolumeMode($sourceDir, $destDir){
		$this->writeln("Group chapter folders found at $sourceDir in VOLUME MODE", \ConsoleKit\Colors::CYAN);
		$foldersCount = 0;
		$sourceFoldersCount = 0;
        foreach (new \DirectoryIterator($sourceDir) as $fileInfo) {
        	if ($fileInfo->isDir() && !$fileInfo->isDot()) {   
        		++$sourceFoldersCount; 
                $volumeName = $this->getVolumeNameFromSubstring($fileInfo->getFilename());
                $this->writeln("..Extracted volume name $volumeName from ".$fileInfo->getFilename(), \ConsoleKit\Colors::WHITE);
                $folderToCreate = $destDir.DIRECTORY_SEPARATOR.$volumeName;
                $sourceFolder = $fileInfo->getPath() . DIRECTORY_SEPARATOR . $fileInfo->current() . DIRECTORY_SEPARATOR;
                if(!is_dir($folderToCreate)){
                	 $this->writeln("..Creating $folderToCreate", \ConsoleKit\Colors::WHITE);
                	 $mkdirResult = mkdir($folderToCreate, '0777', true);
                	 if($mkdirResult){
                	 	$this->writeHighlight("+ Folder $folderToCreate created.",\ConsoleKit\Colors::GREEN);
                	 	++$foldersCount;
                	 }else{
                	 	$this->writeErrorBox("Error while creating $folderToCreate");
                	 	exit;
                	 }
                	 unset($mkdirResult);
                }else{
                	$this->writeln("= $folderToCreate already exists.");
                }

                $this->writeln("..Recursive copy from $sourceFolder to $folderToCreate", \ConsoleKit\Colors::WHITE);
                FileSystemUtility::recursiveCopy($sourceFolder,$folderToCreate.DIRECTORY_SEPARATOR.$fileInfo->getFilename());
                unset($volumeName,$folderToCreate,$sourceFolder);
            }
        }
        $this->writeSuccess("Created $foldersCount folders. Copied $sourceFoldersCount chapters.");
	}

	protected function groupFoldersChapterMode($sourceDir, $destDir){
		$this->writeln("Group chapter folders found at $sourceDir in CHAPTER MODE", \ConsoleKit\Colors::CYAN);
        $foldersCount = 0;
        $sourceFoldersCount = 0;
        $chapterFolders = array();
        
        foreach (new \DirectoryIterator($sourceDir) as $fileInfo) {
            if ($fileInfo->isDir() && !$fileInfo->isDot()) {   
                $this->writeln("..Found chapter folder ".$fileInfo->getFilename(), \ConsoleKit\Colors::WHITE);
                $chapterFolders[] = $fileInfo->getPath(). DIRECTORY_SEPARATOR . $fileInfo->current() . DIRECTORY_SEPARATOR;
            }
        }

        $lastChapterIndex = 0;
        foreach (get_object_vars($this->getVolumesFromChapterMode()) as $volumeName => $numberOfChapters) {
            $this->writeln("..Creating volume $volumeName");
            $folderToCreate = $destDir.DIRECTORY_SEPARATOR.$volumeName;
            if(!is_dir($folderToCreate)){
                $mkdirResult = mkdir($folderToCreate, '0777', true);
                
                if($mkdirResult){
                   $this->writeHighlight("+ Folder $folderToCreate created.",\ConsoleKit\Colors::GREEN);
                   ++$foldersCount;
                }else{
                   $this->writeErrorBox("Error while creating $folderToCreate");
                   exit;
                }
            }else{
                    $this->writeln("= $folderToCreate already exists.");
                }

            for ($i=$lastChapterIndex; $i < $lastChapterIndex+$numberOfChapters; $i++) { 
                $chapterToCopy = $folderToCreate.DIRECTORY_SEPARATOR.pathinfo($chapterFolders[$i], PATHINFO_BASENAME);
                $this->writeln("..Copying chapter folder from {$chapterFolders[$i]} to ".
                               $chapterToCopy, \ConsoleKit\Colors::WHITE);
                FileSystemUtility::recursiveCopy($chapterFolders[$i],$chapterToCopy);
            }
            $lastChapterIndex = $i;
        }

         $this->writeSuccess("Created $foldersCount folders. Copied $sourceFoldersCount chapters.");

        
	}



}
