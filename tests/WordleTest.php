<?php

namespace App;

use PHPUnit\Framework\TestCase;

class WordleTest extends TestCase
{
    public function processGuessDataProvider() {
      return [
        [
          "word" => "price",
          "guess" => "arise",
          "expected" => [
            Suggester::WRONG,
            Suggester::CORRECT,
            Suggester::CORRECT,
            Suggester::WRONG,
            Suggester::CORRECT
          ]
        ],
        [
        "word" => "pitch",
        "guess" => "start",
        "expected" => [
          Suggester::WRONG,
          Suggester::WRONG_POSITION,
          Suggester::WRONG,
          Suggester::WRONG,
          Suggester::WRONG
        ]
      ]
      ];
    }

  /**
   * @dataProvider processGuessDataProvider
   * @param $word
   * @param $guess
   * @param $expected
   * @return void
   * @throws \Exception
   */
    public function testProcessGuess($word, $guess, $expected) {
      $wordl = new Wordle($word, new Dictionary(), new WordProcessor());
      $feedBack = $wordl->processGuess($guess);
      $this->assertEquals($expected, $feedBack);
    }

    public function hasWonDataProvider() {
      return [
        [
          [
            Suggester::WRONG,
            Suggester::WRONG,
            Suggester::WRONG,
            Suggester::WRONG,
            Suggester::WRONG
          ],
          false
        ],
        [
          [
            Suggester::CORRECT,
            Suggester::WRONG_POSITION,
            Suggester::CORRECT,
            Suggester::WRONG_POSITION,
            Suggester::CORRECT
          ],
          false
        ],
        [
          [
            Suggester::CORRECT,
            Suggester::CORRECT,
            Suggester::CORRECT,
            Suggester::CORRECT,
            Suggester::CORRECT
          ],
          true
        ],
      ];
    }


  /**
   * @dataProvider hasWonDataProvider
   * @param $feedback
   * @param $expecedIsWinner
   * @return void
   */
  public function testHasWon($feedback, $expecedIsWinner) {
      $word = "aaaaa";
      $wordl = new Wordle($word, new Dictionary(), new WordProcessor());
      $hasWon = $wordl->hasWon($feedback);
      $message = $expecedIsWinner ? "should have won" : "should have lost";
      $message .= " but they " . ($hasWon ? "won" : "lost");
      $this->assertEquals($expecedIsWinner, $hasWon, $message);
    }
}
