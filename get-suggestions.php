<?php
require __DIR__.'/vendor/autoload.php';

use \App\Suggester;
use \App\Dictionary;
use \App\WordProcessor;

$dictionary = new Dictionary();
$wordProcessor = new WordProcessor();

$game = new Suggester($dictionary, $wordProcessor);
$game->runCommandLine();



