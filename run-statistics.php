<?php
require __DIR__.'/vendor/autoload.php';

use \App\Statistics;
use \App\Dictionary;
use \App\Game;
use \App\WordProcessor;

$statistics = new Statistics();
$statistics->setDictionary(new Dictionary());
$statistics->setWordProcessor(new WordProcessor());
$statistics->setGame(new Game());
$statistics->run();



