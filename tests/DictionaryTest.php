<?php
namespace App;

use PHPUnit\Framework\TestCase;

class DictionaryTest extends TestCase
{

  public function testGetDictionary() {
      $dictionary = new Dictionary();
      $data = $dictionary->getDictionary();
      $randomWord = $dictionary->getRandomWord(1000, $data);
      var_dump($randomWord);
  }
}
