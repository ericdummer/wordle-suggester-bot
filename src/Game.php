<?php namespace App;

class Game {

  /**
   * @return void
   * @throws \Exception
   */
  public function run() {
    //choose a new word
    $dictionary = new Dictionary();
    $wordProcessor = new WordProcessor();

    $word = $dictionary->getRandomWord(1000, $dictionary->getDictionary());
    $wordle = new Wordle($word, $dictionary, $wordProcessor);
    $attempts = 0;
    $hasWon = false;
    $allGuesses = [];
    $allFeedback = [];
    echo "I have chose a five letter word. Try and guess it!\n";
    echo "You have 6 attempts.\n";
    echo "I will show you how close you are fore each letter.\n";
    echo "Black background: that letter is not in the word.\n";
    echo "\e[0;43mYellow\e[0m background: that letter is in the word, but in the wrong place\n";
    echo "\e[0;42mGreen\e[0m background: that letter is correct.\n";
    echo "What is your guess:\n";

    $fileHandler = fopen( 'php://stdin', 'r' );
    while( $attempts < 6 && !$hasWon  && $line = fgets( $fileHandler ) ) {
      $guess = trim($line);
      if(strlen($guess) !== 5) {
        echo "Please enter a 5 letter word. $guess is a " . strlen($guess) . " letter word.\n";
      } else {
        if (!$dictionary->isWordValid($guess)) {
          echo "$guess is not in our dictionary. Please try again\n";
        } else {
          $feedback = $wordle->processGuess($guess);
          $hasWon = $wordle->hasWon($feedback);
          $allGuesses[] = $guess;
          $allFeedback[] = $feedback;
          echo "-----\n";
          foreach($allFeedback as $num => $f) {
            $this->printFeedback($allGuesses[$num], $f);
          }
          if(!$hasWon) {
            echo "Try again:\n";
          }

          $attempts++;
        }

      }
    }
    fclose( $fileHandler );
    if ($hasWon) {
      echo "You Win! Attempts: $attempts";
    } else {
      echo "Sorry, you lose!";
    }
    echo " The word was: $word\n";

  }

  /**
   * @param $guess
   * @param $feedback
   * @return void
   */
  public function printFeedback($guess, $feedback) {
    foreach($feedback as $key => $f) {
      $char = $guess[$key];
      switch($f) {
        case Suggester::WRONG_POSITION:
          $this->printYellow($char);
          break;
        case Suggester::CORRECT:
          $this->printGreen($char);
          break;
        default:
          $this->printBlack($char);
          break;
      }
    }
    echo "\n";
  }

  /**
   * @param $char
   * @return void
   */
  public function printBlack($char) {
    echo "\e[0;37m$char\e[0m";
  }

  /**
   * @param $char
   * @return void
   */
  public function printYellow($char) {
    echo "\e[0;43m$char\e[0m";
  }

  /**
   * @param $char
   * @return void
   */
  public function printGreen($char) {
    echo "\e[0;42m$char\e[0m";
  }

}
