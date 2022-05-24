<?php namespace App;
use \Exception;

class Dictionary {
  const DEFAULT_FILE = __DIR__ . '/files/dictionary.txt';
  const WIKIPEDIA_FREQUENCY = __DIR__ . '/files/enwiki-20210820-words-frequency.txt';
  const CORPUS_BY_FREQUENCY = __DIR__ . '/files/corpus-num.txt';

  protected $filePath;
  protected $fileData = [];

  /**
   * @param string|null $filePath
   */
  public function __construct(string $filePath = null)
  {
    $this->filePath = !is_null($filePath) ?: self::DEFAULT_FILE;
  }

  /**
   * @return array
   * @throws Exception
   */
  public function getDictionary(): array
  {
    if (count($this->fileData) < 1) {
      if (is_readable($this->filePath)) {
        $this->fileData = CsvReader::getInstance()->loadFile($this->filePath);
      } else {
        //log (file is not readable)
        throw new Exception("File is not readable: {$this->filePath}");
      }
    }
    return $this->fileData;
  }

  /**
   * @param $frequencyThreshold
   * @param $dictionaryData
   * @return mixed
   */
  public function getRandomWord($frequencyThreshold = 1000, $dictionaryData) {
    array_multisort (array_column($dictionaryData, 'frequency'), SORT_DESC, $dictionaryData);
    $filtered = array_slice($dictionaryData, 0, $frequencyThreshold);
    $rand = mt_rand(0,count($filtered) - 1);
    $word = $filtered[$rand];
    return $word["word"];
  }

  /**
   * @param $word
   * @return bool
   * @throws Exception
   */
  public function isWordValid($word) {
    if(count($this->fileData) < 1) {
      $this->getDictionary();
    }
    return in_array($word, array_column($this->fileData, "word"));
  }



}
