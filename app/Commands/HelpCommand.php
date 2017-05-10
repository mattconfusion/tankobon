<?php

namespace Commands;

use Utils\RandomUtility;

class HelpCommand extends \ConsoleKit\HelpCommand
{
    public function execute(array $args, array $options = array())
    {
        if (empty($args)) {
            $formater = new \ConsoleKit\TextFormater(array('quote' => ' * '));
            $logo = "                             d8b                d8b                        
   d8P                       ?88                ?88                        
d888888P                      88b                88b     d888888P          
  ?88'   d888b8b    88bd88b   888  d88' d8888b   888888b  d8888b   88bd88b 
  88P   d8P' ?88    88P' ?8b  888bd8P' d8P' ?88  88P `?8bd8P' ?88  88P' ?8b
  88b   88b  ,88b  d88   88P d88888b   88b  d88 d88,  d8888b  d88 d88   88P
  `?8b  `?88P'`88bd88'   88bd88' `?88b,`?8888P'd88'`?88P'`?8888P'd88'   88b";
            $this->writeln("$logo\n", RandomUtility::generateRandomColorInt() | \ConsoleKit\Colors::BOLD);
            $this->writeln('Available commands:', \ConsoleKit\Colors::BLACK | \ConsoleKit\Colors::BOLD);
            foreach ($this->console->getCommands() as $name => $fqdn) {                
                if ($fqdn !== __CLASS__) {
                    $this->writeln($formater->format($name));
                }
            }
            $scriptName = basename($_SERVER['SCRIPT_FILENAME']);
            //$this->writeln("Use './$scriptName help command' for more info");
         }
        // else {
        //     $commandFQDN = '\\'.$this->console->getCommand($args[0]);
        //     $help = \ConsoleKit\Help::fromFQDN($commandFQDN, \ConsoleKit\Utils::get($args, 1));
        //     $this->writeln($help);
        // }
    }
}
