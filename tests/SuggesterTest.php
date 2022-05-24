<?php

namespace App;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SuggesterTest extends TestCase
{

  public function getIncludeDataProvider() {
    return [
      [
        "char" => "s",
        "position" => 0,
        "status" => Suggester::CORRECT,
        "expected" => ["0s"]
      ]
    ];
  }

  /**
   * @dataProvider getIncludeDataProvider
   * @param $char
   * @param $position
   * @param $status
   * @param $expected
   * @return void
   */
  public function testGetIncludesIndexByChar($char, $position, $status, $expected) {
    /** @var Dictionary|MockObject $mockDictionary */
    $mockDictionary = $this->createMock(Dictionary::class);
    /** @var WordProcessor|MockObject $mockWordProcessor */
    $mockWordProcessor = $this->createMock(WordProcessor::class);
    $suggester = new Suggester($mockDictionary, $mockWordProcessor);

    $includes = $suggester->getIncludeConfigByChar($char, $position, $status);
    $this->assertEquals($expected, $includes);
  }

  public function getExcludeDataProvider() {
    return [
      [
        "char" => "s",
        "position" => 0,
        "status" => Suggester::CORRECT,
        "expected" => []
      ],
      [
        "char" => "s",
        "position" => 0,
        "status" => Suggester::WRONG_POSITION,
        "expected" => ['0s']
      ],
      [
        "char" => "s",
        "position" => 0,
        "status" => Suggester::WRONG,
        "expected" => ['s']
      ]
    ];
  }

  /**
   * @dataProvider getExcludeDataProvider
   * @param $char
   * @param $position
   * @param $status
   * @param $expected
   * @return void
   */
  public function testGetExcludeConfigByChar($char, $position, $status, $expected) {
    /** @var Dictionary|MockObject $mockDictionary */
    $mockDictionary = $this->createMock(Dictionary::class);
    /** @var WordProcessor|MockObject $mockWordProcessor */
    $mockWordProcessor = $this->createMock(WordProcessor::class);
    $suggester = new Suggester($mockDictionary, $mockWordProcessor);

    $includes = $suggester->getExcludeConfigByChar($char, $position, $status);
    $this->assertEquals($expected, $includes);
  }

  public function testGetSuggestions() {
    $expected = ['something'];

    // actual: "reins"
    // guess  "heist"
    // status [ wrong, correct, correct, wrong_position, wrong]

    /** @var Dictionary|MockObject $mockDictionary */
    $mockDictionary = $this->createMock(Dictionary::class)
//      ->method("getWordsByKey")
//      ->withConsecutive( ["h", "e", "i", "s", "t"] )
//      ->willReturnOnConsecutiveCalls(
//        ["holly", "cheap", "cheap"],
//        [],
//        [],
//        []
//      )
  ;

    /** @var WordProcessor|MockObject $mockWordProcessor */
    $mockWordProcessor = $this->createMock(WordProcessor::class);

//    $suggester = new Suggester($mockDictionary, $mockWordProcessor);

    $guess = "arise";
    $statuses = [
      Suggester::WRONG,
      Suggester::WRONG,
      Suggester::WRONG,
      Suggester::WRONG,
      Suggester::WRONG
    ];

    $suggester = new Suggester(new Dictionary(), new WordProcessor());

    $suggestions = $suggester->getSuggestions($guess, $statuses);
    $this->assertEquals($expected, $suggestions);
    $this->assertTrue();

  }

}
