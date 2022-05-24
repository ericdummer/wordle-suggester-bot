<?php namespace App;

class CsvReader {
  static $instance;

  /**
   * @param CsvReader $instance
   * @return void
   */
  static public function setInstance(CsvReader $instance) {
    self::$instance = $instance;
  }

  /**
   * @return CsvReader
   */
  static public function getInstance(): CsvReader
  {
    return self::$instance ?: new self();
  }

  /**
   * @param $filePath
   * @param bool $hasHeader
   * @return array
   */
  public function loadFile($filePath, bool $hasHeader = true): array
  {
    $fileData = [];
    $columnHeaders = [];
    $row = 1;
    if (($handle = fopen($filePath, "r")) !== FALSE) {
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if ($hasHeader && $row == 1) {
          $columnHeaders = $data;
        } else {
          $rowData = [];
          foreach($data as $column => $cell) {
            $key = $columnHeaders[$column] ?: $column;
            $rowData[$key] = $cell;
          }
          $fileData[] = $rowData;
        }
        $row++;
      }
      fclose($handle);
    }
    return $fileData;
  }
}
