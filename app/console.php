<?php

$console = new ConsoleKit\Console();
$console->addCommandsFromDir(__DIR__ . DIRECTORY_SEPARATOR . 'Commands', 'Commands');
$console->run();

