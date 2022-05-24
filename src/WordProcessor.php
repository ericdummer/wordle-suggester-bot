<?php
namespace App;

class WordProcessor {

  /**
   * @param $data
   * @return array
   */
  public function indexData($data): array {
    $processedData = [];
    foreach ($data as $row) {
      $word = $row['word'];
      foreach(str_split($word) as $index => $char) {
        $processedData[$index.$char][]  = $word;
        if (!(isset($processedData[$char]) && is_array($processedData[$char]) && in_array($word, $processedData[$char]))) {
          $processedData[$char][] = $word;
        }
      }
    }
    return $processedData;
  }

  /**
   * @param array $data
   * @return array
   */
  public function getWordsWithFrequency(array $data): array
  {
    $processedData = [];
    foreach ($data as $row) {
      $word = $row['word'];
      $frequency = $row['frequency'];
      $processedData[$word] = $frequency;
    }
    return $processedData;

  }

  /**
   * @param array $suggestions
   * @return array
   */
  public function getCharacterPosCountsFromSuggestions(array $suggestions): array
  {
    $data = [];
    foreach($suggestions as $s) {
      $data[] = ["word" => $s];
    }
    $indexedData = $this->indexData($data);
    $counts = array_map(function ($words) { return count($words); }, $indexedData);
    arsort($counts);
    return $counts;
  }

  /**
   * @param array $suggestions
   * @param array $wordFrequencies
   * @return array
   */
  public function getFiveBestWordFromSuggestions(array $suggestions, array $wordFrequencies): array {
    $charCounts = $this->getCharacterPosCountsFromSuggestions($suggestions);

    $wordScore = [];
    foreach($suggestions as $suggestion) {
      $wordFrequency = $wordFrequencies[$suggestion] ?? 0;
      $charScore = 0;

      $distinctChar = array_unique(str_split($suggestion));
      foreach($distinctChar as $char) {
        $charScore += $charCounts[$char] ?? 0;
      }
      $wordScore[$suggestion] = [
        "word" => $suggestion,
        "score" => floor($wordFrequency/1000) + $charScore,
        "word-frequency" => $wordFrequency,
        "char-score" => $charScore,
      ];
    }
    array_multisort (array_column($wordScore, 'score'), SORT_DESC, $wordScore);

//    $temp = array_slice($wordScore, 0, 10);
//    foreach($temp as $word => $data) {
//      $wordFrequency = $data["word-frequency"];
//      $charScore = $data["char-score"];
//      $score = $data["score"];
//      echo "$word - score($score) WF($wordFrequency) CS($charScore)\n";
//    }

    return array_slice(array_column($wordScore, "word"), 0, 30);
  }
}
