<?php namespace App;

class CsvWriter {
  static $instance;

  /**
   * @return CsvWriter
   */
  static public function getInstance(): CsvWriter
  {
    return self::$instance ?: new self();
  }

  /**
   * @param $data
   * @param $filePath
   * @return void
   */
  public function writeFile($data, $filePath)
  {
    $fp = fopen($filePath, 'w');
    foreach ($data as $fields) {
      fputcsv($fp, $fields);
    }
    fclose($fp);
  }
}
