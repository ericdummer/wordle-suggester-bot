<?php

namespace App;

use PHPUnit\Framework\TestCase;

class CsvReaderTest extends TestCase
{
  public function testGetInstance() {
    $instance = CsvReader::getInstance();
    $this->assertInstanceOf(CsvReader::class, $instance);
  }

  public function testSetInstance() {
    $test = new CsvReader();
    CsvReader::setInstance($test);
    $instance = CsvReader::getInstance($test);
    $this->assertInstanceOf(CsvReader::class, $instance);
  }
}
