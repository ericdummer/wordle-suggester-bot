<?php
namespace App;

use Exception;

class Suggester {

  const WRONG = 'wrong';
  const CORRECT = 'correct';
  const WRONG_POSITION = 'wrong_position';

  const WINNING_FEEDBACK = [
    self::CORRECT,
    self::CORRECT,
    self::CORRECT,
    self::CORRECT,
    self::CORRECT
  ];

  protected $dictionary;
  protected $wordProcessor;
  protected $indexedWords = [];
  protected $wordFrequencies = [];

  protected $runningExcludes = [];

  /**
   * @param Dictionary $dictionary
   * @param WordProcessor $wordProcessor
   */
  public function __construct(Dictionary $dictionary, WordProcessor $wordProcessor) {
    $this->dictionary = $dictionary;
    $this->wordProcessor = $wordProcessor;
  }

  /**
   * @param string $char
   * @param int $position
   * @param string $status
   * @return array
   */
  public function getIncludeConfigByChar(string $char, int $position, string $status): array {
    $includes = [];
    switch ($status) {
      case self::CORRECT:
          $includes[] = "$position$char";
          break;
      case self::WRONG_POSITION:
          $includes[] = $char;
          break;
      case self::WRONG:
      default:
        //do nothing
    }
    return $includes;
  }

  /**
   * @param string $char
   * @param int $position
   * @param string $status
   * @return array
   */
  public function getExcludeConfigByChar(string $char, int $position, string $status): array {
    $excludes = [];
    switch ($status) {
      case self::CORRECT:
        //do nothing
        break;
      case self::WRONG_POSITION:
        $excludes[] = "$position$char";
        break;
      case self::WRONG:
        $excludes[] = "$char";
        break;
      default:
        //do nothing
    }
    return $excludes;
  }

  /**
   * @param string $guess
   * @param array $feedback
   * @return array
   * @throws Exception
   */
  public function getSuggestions(string $guess, array $feedback): array {
    $chars = str_split($guess);
    $includeKeys = [];

    foreach ($feedback as $pos => $status) {
      $c = $chars[$pos];
      $newIncludeKeys = $this->getIncludeConfigByChar($c, $pos, $status);
      $includeKeys = array_merge($includeKeys, $newIncludeKeys);

      $newExcludeKeys = $this->getExcludeConfigByChar($c, $pos, $status);
      $this->runningExcludes = array_merge($this->runningExcludes, $newExcludeKeys);
    }

    $suggestions = [];

    if(count($includeKeys) < 1) {
      //get all the words
      $indexedWords = $this->getIndexedWords();
      foreach($indexedWords as $wordsInIndex) {
        $suggestions = array_merge($suggestions, $wordsInIndex);
      }
    } else {
      foreach($includeKeys as $key) {
        $newWords = $this->getWordsByKey($key);
        if (count($suggestions) < 1) {
          $suggestions = $newWords;
        } else {
          $suggestions = array_intersect($suggestions, $newWords);
        }
      }
    }

    foreach($this->runningExcludes as $key) {
      $excludeWords = $this->getWordsByKey($key);
      if (count($excludeWords) > 0) {
        $suggestions = array_diff($suggestions, $excludeWords);
      }
    }

    $dictData = $this->dictionary->getDictionary();
    $wordFrequencies = $this->wordProcessor->getWordsWithFrequency($dictData);
    return $this->wordProcessor->getFiveBestWordFromSuggestions($suggestions, $wordFrequencies);
  }

  /**
   * @param $key
   * @return array
   */
  protected function getWordsByKey($key): array {
    $indexedWords = $this->getIndexedWords();
    return $indexedWords[$key] ?? [];
  }

  /**
   * @return array
   * @throws Exception
   */
  protected function getIndexedWords(): array
  {
    if (count($this->indexedWords) < 1) {
      $data = $this->dictionary->getDictionary();
      $this->indexedWords = $this->wordProcessor->indexData($data);
    }
    return $this->indexedWords;
  }

  /**
   * @return void
   * @throws Exception
   */
  public function runCommandLine() {

    echo "WORLE SUGGESTER!!!!\n";
    echo "To leave type: exit\n";
    echo "To start over, type: restart\n";
    echo "...\n";
    echo "What was your first guess?\n";
    $exit = false;
    $feedback = [
      self::WRONG,
      self::WRONG,
      self::WRONG,
      self::WRONG,
      self::WRONG
    ];
    while( !$exit) {
      $guess = $this->getNextGuess();
      foreach(str_split($guess) as $pos => $char) {
        $feedback[$pos] = $this->getGuessFeedback($char, $pos);
      }
      $exit = self::WINNING_FEEDBACK == $feedback;
      if($exit) {
        echo "YOU GOT IT!\nThanks for playing!\n";
      }

      $suggestions = $this->getSuggestions($guess, $feedback);
      echo "Top 5 Suggestions:\n";
      echo "-----------------\n";
      foreach($suggestions as $suggestion) {
        echo "$suggestion\n";
      }
      echo "What was your next guess?\n";
    }
  }

  /**
   * @return string|void
   * @throws Exception
   */
  public function getNextGuess() {
    $isValidWord = false;
    $guess = "";
    $fileHandler = fopen( 'php://stdin', 'r' );
    while( !$isValidWord  && $line = fgets( $fileHandler ) ) {
      $guess = strtolower(trim($line));// line is processed on "enter" which add a carriage return to $line

      if ($guess === "exit") {
        exit("Exiting. Thanks for playing!");
      }
      if ($guess === "restart") {
        $this->runningExcludes = [];
        echo "...\n";
        echo "clearing data\n";
        echo "...\n";
        echo "What was your first guess?\n";
        $this->getNextGuess();
        return "";
      }
      $isValidWord = $this->dictionary->isWordValid($guess);
      if (!$isValidWord) {
        echo "$guess is not in our dictionary of 5 letter words.\nPlease enter a better guess!\n";
      }
    }
    return $guess;
  }

  /**
   * @param $char
   * @param $pos
   * @return string|void
   */
  public function getGuessFeedback($char, $pos) {
      $feedback = self::WRONG;
      $humanPositions = $pos + 1;
      echo "What color was $char? position $humanPositions:\n";
      echo "1) Black - not in the word\n";
      echo "2) Yellow - in the wrong position\n";
      echo "3) Green - correct\n";
      echo ":";
     $isValidEntry = false;

    $fileHandler = fopen( 'php://stdin', 'r' );
    while( !$isValidEntry  && $line = fgets( $fileHandler ) ) {
      $entry = trim($line);// line is processed on "enter" which add a carriage return to $line
      if (strtolower($entry) === "exit") {
        exit("Exiting. Thanks for playing!\n");
      }
      switch($entry) {
        case 1:
          $feedback = self::WRONG;
          $isValidEntry = true;
          break;
        case 2:
          $feedback = self::WRONG_POSITION;
          $isValidEntry = true;
          break;
        case 3:
          $feedback = self::CORRECT;
          $isValidEntry = true;
          break;
        default:
          $isValidEntry = false;
      }

    }
    return $feedback;
  }
}
