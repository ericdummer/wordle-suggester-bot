<?php

namespace App;

use PHPUnit\Framework\TestCase;

class WordProcessorTest extends TestCase
{
  public function testIndexData() {
    $sut = new WordProcessor();
    $expected = [
      "0a"=> ["adieu"],
      "a"=> ["adieu"],
      "1d"=> ["adieu"],
      "d" => ["adieu"],
      "2i"=> ["adieu", "heist"],
      "i" => ["adieu", "heist"],
      "3e"=> ["adieu"],
      "e" => ["adieu", "heist"],
      "4u"=> ["adieu"],
      "u" => ["adieu"],
      "0h"=> ["heist"],
      "h" => ["heist"],
      "1e"=>["heist"],
      "3s"=> ["heist"],
      "4t"=> ["heist"],
      "s" => ["heist"],
      "t" => ["heist"]
    ];

    $dataToIndex = [
      [
        "word" => "adieu",
        "frequency" => 999
      ],
      [
        "word" => "heist",
        "frequency" => 998
      ]
    ];
    $indexed = $sut->indexData($dataToIndex);
    $this->assertEquals($expected, $indexed);
  }

  public function testGetWordsWithFrequency(){
    $sut = new WordProcessor();

    $expected = [
      "adieu" => 999,
      "heist" => 998
    ];

    $data = [
      [
        "word" => "adieu",
        "frequency" => 999
      ],
      [
        "word" => "heist",
        "frequency" => 998
      ]
    ];
    $wordsWithFrequency = $sut->getWordsWithFrequency($data);
    $this->assertEquals($expected, $wordsWithFrequency);

  }

  public function testGetCharacterPosCounts() {
    $sut = new WordProcessor();
    $dictionaryData = ['which', 'their', 'would'];

    $counts = $sut->getCharacterPosCountsFromSuggestions($dictionaryData);
    $expected = [
      '0w' => 2,
      '1h' => 2,
      'h' => 2,
      'i' => 2,
      'w' => 2,
      'r' => 1,
      '4d' => 1,
      'l' => 1,
      '3l' => 1,
      'u' => 1,
      '2u' => 1,
      'o' => 1,
      '1o' => 1,
      'e' => 1,
      '4r' => 1,
      '3i' => 1,
      '2e' => 1,
      't' => 1,
      '0t' => 1,
      '4h' => 1,
      'c' => 1,
      '3c' => 1,
      '2i' => 1,
      'd' => 1
    ];
    $this->assertEquals($expected, $counts);
  }
  public function testGetBestWordFromSuggestions(){
    $sut = new WordProcessor();
    $dictionary = new Dictionary();
//    $dictData = $dictionary->getDictionary();
    $dictData = [
      [
        "word" => "arise",
        "frequency" => 12
      ],
      [
        "word" => "their",
        "frequency" => 247596
      ],
      [
        "word" => "arose",
        "frequency" => 6
      ],
      [
        "word" => "which",
        "frequency" => 349120
      ],

    ];

    $wordFrequencies = $sut->getWordsWithFrequency($dictData);
    $allWords = array_column($dictData, "word");
    $bestWord = $sut->getFiveBestWordFromSuggestions($allWords, $wordFrequencies);
    $expected = ['which', 'their', 'arise', 'arose'];
    $this->assertEquals($expected, $bestWord);
  }

}
