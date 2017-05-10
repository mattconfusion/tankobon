<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'SplClassLoader.php';

/**
 * Autoload classes using PSR-0 from source directory
 * @param  string $sourceDir the source directory
 * @return void
 */
function autoloadClasses($sourceDir){
	$classes = array();

	foreach (new \DirectoryIterator($sourceDir) as $fileInfo) {
	    if ($fileInfo->isDir() && !$fileInfo->isDot()) { 
	    	$classes[] = $fileInfo->getFilename();
	    }
	}

	//cycle through classes and load em
	foreach ($classes as $class) {
	    $classLoader = new SplClassLoader($class, $sourceDir . DIRECTORY_SEPARATOR);
	    $classLoader->register();
	    //var_dump($classLoader);
	    unset($classLoader);
	}

	unset($classes);
}

