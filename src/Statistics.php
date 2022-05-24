<?php
namespace App;
use App\Dictionary;
use App\WordProcessor;
use App\Game;

class Statistics {

  /** @var Dictionary $dictionary */
  protected $dictionary;

  /** @var WordProcessor $wordProcessor*/
  protected $wordProcessor;

  /** @var Game $game*/
  protected $game;

  public function setSuggester(Suggester $suggester)
  {
    $this->suggester = $suggester;
  }

  public function setDictionary(Dictionary $dictionary)
  {
    $this->dictionary = $dictionary;
  }

  /**
   * @param \App\WordProcessor $wordProcessor
   * @return void
   */
  public function setWordProcessor(WordProcessor $wordProcessor)
  {
    $this->wordProcessor = $wordProcessor;
  }

  /**
   * @param \App\Game $game
   * @return void
   */
  public function setGame(Game $game)
  {
    $this->game = $game;
  }

  /**
   * @return void
   * @throws \Exception
   */
  public function run() {
    $startWords = [
      "adieu",
      "arise",
      "roast"
    ];

    $startWord = "arise";

    $words = $this->getTopWords(1000);
    $numGuesses = [];
    $failures = [];
    foreach($words as $wordData) {
      $word = $wordData["word"];

      $wordle = new Wordle($word, $this->dictionary, $this->wordProcessor);
      $suggester = new Suggester($this->dictionary, $this->wordProcessor);
      $lastFeedBack = $wordle->processGuess($startWord);
      $hasWon = $wordle->hasWon($lastFeedBack);
      if ($hasWon) {
        echo "First guess!\n";
        $numGuesses[$word] = 1;
        continue;
      }
      $attempts = 1;
      $lastGuess = $startWord;
//      echo "$word attempt $attempts: $lastGuess\n";
      echo ".";
      while(!$hasWon && $attempts < 6) {
        $attempts++;
        $suggestions = $suggester->getSuggestions($lastGuess, $lastFeedBack);
        if(count($suggestions) < 1) {
          echo "NO SUGGESTIONS for $word\n";
          continue;
        }
        $lastGuess = current($suggestions);
//        echo "$word attempt $attempts: $lastGuess\n";
        $lastFeedBack = $wordle->processGuess($lastGuess);
        $hasWon = $wordle->hasWon($lastFeedBack);
        if ($hasWon) {
          $numGuesses[$word] = $attempts;
        }
      }
      if (!$hasWon) {
        $failures[] = $word;
      }
    }
    echo "\n";
    echo "failures: ";
    var_dump($failures);
    echo "Guesses: ";
    $sum = 0;
    foreach($numGuesses as $attp) {
      $sum += $attp;
    }
    echo "average successful: " . round($sum/count($numGuesses), 2) . "\n";

  }


  /**
   * @param $threshold
   * @return array
   * @throws \Exception
   */
  public function getTopWords($threshold)
  {
    $data = $this->dictionary->getDictionary();
    return array_slice($data, 0, $threshold);
  }
}
