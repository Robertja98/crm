<?php
// tests/CsvHandlerTest.php
// PHPUnit test for csv_handler.php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../csv_handler.php';

class CsvHandlerTest extends TestCase {
    private $testFile = __DIR__ . '/test_contacts.csv';
    private $schema = ['id', 'name', 'email'];

    protected function setUp(): void {
        // Create a test CSV file
        $data = [
            ['id' => '1', 'name' => 'Alice', 'email' => 'alice@example.com'],
            ['id' => '2', 'name' => 'Bob', 'email' => 'bob@example.com'],
        ];
        writeCSV($this->testFile, $data, $this->schema);
    }

    protected function tearDown(): void {
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
    }

    public function testReadCSV() {
        $rows = readCSV($this->testFile, $this->schema);
        $this->assertCount(2, $rows);
        $this->assertEquals('Alice', $rows[0]['name']);
        $this->assertEquals('bob@example.com', $rows[1]['email']);
    }

    public function testWriteCSV() {
        $data = [
            ['id' => '3', 'name' => 'Charlie', 'email' => 'charlie@example.com']
        ];
        writeCSV($this->testFile, $data, $this->schema);
        $rows = readCSV($this->testFile, $this->schema);
        $this->assertCount(1, $rows);
        $this->assertEquals('Charlie', $rows[0]['name']);
    }

    public function testAppendCSV() {
        $row = ['3', 'Charlie', 'charlie@example.com'];
        appendCSV($this->testFile, $row);
        $rows = readCSV($this->testFile, $this->schema);
        $this->assertCount(3, $rows);
        $this->assertEquals('Charlie', $rows[2]['name']);
    }
}
