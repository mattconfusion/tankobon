<?php

namespace Commands;

use Config\Defs;
use Utils\RandomUtility;

class CommandBaseClass extends \ConsoleKit\Command {
	
	protected $jsonConfig = null;
	protected $sourceDir;
	protected $destDir;

	public function execute(array $args, array $options = array()) {
		$this->jsonConfig = $this->getJsonConfigFromOptions($options);
		$this->getSourceAndDestinationFromArgs($args);
	}


	/**
	 * Set CommandBaseClass::$sourceDir and CommandBaseClass::$destDir from passed args array
	 * @param  array  $args arguments array passed by the cli
	 * @return 
	 */
	protected function getSourceAndDestinationFromArgs(array $args){

		if (!isset($args[0])) {
            $this->writeErrorBox('Missing argument 1, source directory needed');
            exit;
        }

        if (!isset($args[1])) {
            $args[1] = $args[0];
            $this->writeWarning('Missing argument 2, destination directory will be '.$args[1]);
        }
            
        $this->sourceDir = $args[0];
        $this->destDir = $args[1];
        return;
	}

	/**
	 * Use this method if the config json file is required for the command
	 * @return [type] [description]
	 */
	protected function requireJsonConfig(){
		if(!isset($this->jsonConfig)){
			$this->writeErrorBox("A config json file is needed for this command");
			exit;
		}
	}

	/**
	 * Get the json config file from the path indicated in the options
	 * @param  array $options the array of options passed in console
	 * @return \Std Object
	 */
	private function getJsonConfigFromOptions(array $options){
		if(isset($options[Defs::CLI_OPTIONS_CONFIG])){
			if(!is_file($options[Defs::CLI_OPTIONS_CONFIG])){
				$this->writeErrorBox("Json config file not found at ".$options['config']);
				exit;
			}
			$jsonString = file_get_contents($options[Defs::CLI_OPTIONS_CONFIG]);
			$jsonConfig = json_decode($jsonString);
			if($jsonConfig == false){
				$this->writeErrorBox("Unable to decode json config file ".$options['config']);
				exit;
			}
			unset($jsonString);
			return $jsonConfig;
		}else{
			$this->writeWarning('No config json file specified in --config option.');
			return null;
		}
	}

	/**
	 * Write a new line of warning (yellow and bold) to the console
	 * @param  string $message the message of the warning
	 * @return 
	 */
	public function writeWarning($message){
		$this->writeln($message, \ConsoleKit\Colors::YELLOW | \ConsoleKit\Colors::BOLD);
	}

	/**
	 * Write a new line for successful operation (green and bold) to the console
	 * @param  string $message the message to write 
	 * @return 
	 */
	public function writeSuccess($message){
		$this->writeln($message, \ConsoleKit\Colors::GREEN | \ConsoleKit\Colors::BOLD);
	}

	/**
	 * Write and highlight a message with reverse foreground background color
	 * @param  string $message the message of the warning
	 * @param  string $color   should be a \ConsoleKit\Colors constant
	 * @return 
	 */
	public function writeHighlight($message, $color = \ConsoleKit\Colors::WHITE){
		$this->writeln($message, $color | \ConsoleKit\Colors::REVERSE);
	}

	/**
	 * Write error using \ConsoleKit\Command writeerr command adding "\n"
	 * @param  string $message the error message
	 * @return 
	 */
	public function writeError($message){
		$this->writeerr($message."\n");
	}

	/**
	 * Write error in a box
	 * @param  string $message the error message
	 * @return 
	 */
	public function writeErrorBox($text){
		$this->writeInABox($text,\ConsoleKit\Colors::WHITE,\ConsoleKit\Colors::RED);
	}

	/**
	 * Write Text in a Box
	 * @param  string $text 
	 * @param  \ConsoleKit\Color constant  $textColor       [description]
	 * @param  \ConsoleKit\Color constant $backgroundColor [description]
	 * @return 
	 */
	public function writeInABox($text, $textColor = \ConsoleKit\Colors::WHITE, $backgroundColor = \ConsoleKit\Colors::CYAN){
		$box = new \ConsoleKit\Widgets\Box($this->console, "\n".$text."\n", '');
        $out = \ConsoleKit\Colors::colorizeLines($box, $textColor, $backgroundColor);
        $out = \ConsoleKit\TextFormater::apply($out, array('indent' => 5));
        $this->writeln($out);
        unset($box,$out,$text);
	}


	public function writeLogo($textColor = null, $backgroundColor = null){
		if($textColor == null){
			$textColor = RandomUtility::generateRandomColorInt();
		}

		$text = "                             d8b                d8b                        
   d8P                       ?88                ?88                        
d888888P                      88b                88b     d888888P          
  ?88'   d888b8b    88bd88b   888  d88' d8888b   888888b  d8888b   88bd88b 
  88P   d8P' ?88    88P' ?8b  888bd8P' d8P' ?88  88P `?8bd8P' ?88  88P' ?8b
  88b   88b  ,88b  d88   88P d88888b   88b  d88 d88,  d8888b  d88 d88   88P
  `?8b  `?88P'`88bd88'   88bd88' `?88b,`?8888P'd88'`?88P'`?8888P'd88'   88b";
        $out = \ConsoleKit\Colors::colorizeLines("\n".$text."\n", $textColor | \ConsoleKit\Colors::BOLD, $backgroundColor);
        $out = \ConsoleKit\TextFormater::apply($out);
        $this->writeln($out);
        unset($out,$text);
	}

	//JsonConfig Library

	protected function getGroupingMode(){
		return $this->jsonConfig->grouping_mode;
	}

	protected function getArchivePrefix(){
		return $this->jsonConfig->archive_prefix;
	}

	protected function getArchiveSuffix(){
		return $this->jsonConfig->archive_suffix;
	}

	protected function getArchiveExtension(){
		return $this->jsonConfig->archive_extension;
	}

	protected function getVolumesFromChapterMode(){
		return $this->jsonConfig->chapter_mode->volumes;
	}

	protected function getRenameFilesCounterMode(){
		if(isset($this->jsonConfig->rename_files_counter)){
			return $this->jsonConfig->rename_files_counter;
		}
		return Defs::CONFIG_RENAME_FILES_COUNTER_UNIQUE;	
	}

	protected function getVolumeNameFromSubstring($folderName){
		try{
			$volumeNumberString = substr($folderName, 
			       $this->jsonConfig->volume_mode->volume_number->string_start_index,
			       $this->jsonConfig->volume_mode->volume_number->string_end_index);
			if(!$volumeNumberString || $volumeNumberString == ''){
				$this->writeerr("Volume number not found in folder name $folderName, volume_number object in the config json file.");
			}
			return $volumeNumberString;
		}catch(\Exception $e){
			throw new \Exception('Check volume_number object in the config json file.');
		}
	}
}
