<?php

namespace Commands;

use Config\Defs;

class BatchProcessCommand extends CommandBaseClass
{
    public function execute(array $args, array $options = array())
    {
        parent::execute($args, $options);
        $this->writeLogo();
        $this->requireJsonConfig();
        $groupCmd = new GroupChaptersCommand($this->console);
        $groupCmd->execute(array($this->sourceDir,$this->destDir), $options);
        //launch split pages command according to flag --split
        if (isset($options[Defs::CLI_OPTIONS_SPLIT])) {
            $splitCmd = new SplitPagesCommand($this->console);
            $splitCmd->execute(array($this->destDir), $options);
            unset($splitCmd);
        }
        //launch rename files command according to flag --rename
        if (isset($options[Defs::CLI_OPTIONS_RENAME])) {
            $renameCmd = new RenameFilesCommand($this->console);
            $renameCmd->execute(array($this->destDir), $options);
            unset($renameCmd);
        }
        $sanitizeCmd = new SanitizeCommand($this->console);
        $sanitizeCmd->execute(array($this->destDir,$this->destDir), $options);
        $makeArchivesCmd = new MakeArchivesCommand($this->console);
        $makeArchivesCmd->execute(
            array($this->destDir,$this->destDir),
            array(Defs::CLI_OPTIONS_ARCHIVE_PREFIX => $this->jsonConfig->archive_prefix,
                                        Defs::CLI_OPTIONS_ARCHIVE_SUFFIX => $this->jsonConfig->archive_suffix,
                                        Defs::CLI_OPTIONS_CLEANUP => true)
        );
        unset($groupCmd,$sanitizeCmd,$makeArchivesCmd);
    }

    /**
     * Override of the standard method: if destination is unknown a default subfolder is used.
     * @param  array  $args [description]
     * @return [type]       [description]
     */
    protected function getSourceAndDestinationFromArgs(array $args)
    {
        if (!isset($args[0])) {
            $this->writeErrorBox('Missing argument 1, source directory needed');
            exit;
        }

        if (!isset($args[1])) {
            $args[1] = $args[0].DIRECTORY_SEPARATOR.Defs::DEFAULT_TANKOBON_EXPORT_SUBFOLDER;
            $this->writeWarning('Missing argument 2, destination directory will be '.$args[1]);
        }

        if (file_exists($args[1])) {
            $result = mkdir($args[1]);
            if (!result) {
                throw new Exception("Unable to create directory {$args[1]}");
            } else {
                $this->writeSuccess('Created destination directory '.$args[1]);
            }
        }

        $this->sourceDir = $args[0];
        $this->destDir = $args[1];
        return;
    }
}
