<?php
namespace App;

use Exception;

class Wordle {

  protected $dictionary;
  protected $wordProcessor;
  protected $word;

  /**
   * @param $word
   * @param Dictionary $dictionary
   * @param WordProcessor $wordProcessor
   */
  public function __construct($word, Dictionary $dictionary, WordProcessor $wordProcessor) {
    $this->word = strtolower($word);
    $this->dictionary = $dictionary;
    $this->wordProcessor = $wordProcessor;
  }

  /**
   * @param $guess
   * @return array
   * @throws Exception
   */
  public function processGuess($guess): array {
    if (strlen($guess) != 5) {
      throw new Exception("Guess must be 5 letters");
    }
    if (!$this->dictionary->isWordValid($guess)) {
      throw new Exception("$guess is not found in our dictionary");
    }
    return $this->getFeedback($this->word, $guess);
  }

  /**
   * @param $word
   * @param $guess
   * @return array
   */
  public function getFeedback($word, $guess): array {

    $feedback = [
      Suggester::WRONG,
      Suggester::WRONG,
      Suggester::WRONG,
      Suggester::WRONG,
      Suggester::WRONG
    ];
    for ($i = 0; $i < 5; $i++) {
        $guessLetter = $guess[$i];
        if ($word[$i] === $guessLetter) {
          $feedback[$i] = Suggester::CORRECT;
        } elseif (strpos($word, $guessLetter) !== false) {
          $feedback[$i] = Suggester::WRONG_POSITION;
        }
    }

    return $feedback;
  }

  /**
   * @param $feedback
   * @return bool
   */
  public function hasWon($feedback) {

    $winningFeedback = [
      Suggester::CORRECT,
      Suggester::CORRECT,
      Suggester::CORRECT,
      Suggester::CORRECT,
      Suggester::CORRECT
    ];
    return $winningFeedback === $feedback;
  }
}
