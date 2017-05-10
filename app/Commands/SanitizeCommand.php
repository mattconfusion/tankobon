<?php

namespace Commands;

use Utils\FileSystemUtility;

class SanitizeCommand extends CommandBaseClass {

    /**
     * Execute the sanitize command.
     * 
     * @usage Sanitize the filenames and directory names by deleteing every character outside a-z and 0-9
     * @arg  source argument 1: path of the directory to be parsed recursevely
     * @param  array  $args    [description]
     * @param  array  $options [description]
     * @return [type]          [description]
     */
    public function execute(array $args, array $options = array()) {
        parent::execute($args,$options);
        $this->recursiveDirAndFileNamesSanitize($this->sourceDir);
        $this->writeInABox("Sanitize filenames completed.",\ConsoleKit\Colors::WHITE, \ConsoleKit\Colors::GREEN);
    }

    /**
     * Sanitize all the file names and directory names starting recursevely from a specified path
     * @param string $directory the root path
     */
    protected function recursiveDirAndFileNamesSanitize($directory) {
        $this->writeln("Recursive sanitize of $directory", \ConsoleKit\Colors::CYAN);
        $countChanges = 0;
        $path = realpath($directory);
        $di = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($di as $name => $fio) {
            $newname = FileSystemUtility::sanitizeNameRegex($fio->getFilename());
            if ($newname !== $fio->getFilename()) {
                rename($name, $fio->getPath() . DIRECTORY_SEPARATOR . $newname);
                $this->writeln("..Found non-standard name \"{$fio->getFilename()}\"");
                $this->writeHighlight("> Renamed to \"$newname\"", \ConsoleKit\Colors::GREEN);
                unset($newname);
                ++$countChanges;
            }
        }
        unset($di, $path);
        $this->writeSuccess("$countChanges changes applied in recursive sanitize of $directory.", \ConsoleKit\Colors::GREEN);
    }

}
