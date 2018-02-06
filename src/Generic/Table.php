<?php


namespace Hyperized\Benchmark\Generic;

// Source from: https://gist.github.com/RamadhanAmizudin/ca87f7be83c6237bb070

/**
 * Class Table
 * @package Hyperized\Benchmark\Generic
 */
class Table
{
    /**
     * @var int
     */
    static $spacingX = 1;
    /**
     * @var int
     */
    static $spacingY = 0;
    /**
     * @var string
     */
    static $jointCharacter = '+';
    /**
     * @var string
     */
    static $lineXCharacter = '-';
    /**
     * @var string
     */
    static $lineYCharacter = '|';
    /**
     * @var string
     */
    static $newLine = "\n";

    /**
     * @var
     */
    private $tableArray;
    /**
     * @var
     */
    private $columnHeaders;
    /**
     * @var
     */
    private $columnLength;
    /**
     * @var
     */
    private $rowSeparator;
    /**
     * @var
     */
    private $rowSpacer;
    /**
     * @var
     */
    private $rowHeaders;

    /**
     * @param array $tableArray
     */
    public function __construct(array $tableArray)
    {
        $this->tableArray = $tableArray;
        $this->columnHeaders = $this->columnHeaders($this->tableArray);
        $this->columnLength = $this->columnLengths($this->tableArray, $this->columnHeaders);
        $this->rowSeparator = $this->rowSeparator($this->columnLength);
        $this->rowSpacer = $this->rowSpacer($this->columnLength);
        $this->rowHeaders = $this->rowHeaders($this->columnHeaders, $this->columnLength);

        $this->render();
    }

    /**
     *
     */
    private function render()
    {
        echo $this->rowSeparator . static::$newLine;
        echo \str_repeat($this->rowSpacer . static::$newLine, static::$spacingY);
        echo $this->rowHeaders . static::$newLine;
        echo \str_repeat($this->rowSpacer . static::$newLine, static::$spacingY);
        echo $this->rowSeparator . static::$newLine;
        echo \str_repeat($this->rowSpacer . static::$newLine, static::$spacingY);
        foreach ($this->tableArray as $rowCells) {
            $rowCells = $this->rowCells($rowCells, $this->columnHeaders, $this->columnLength);
            echo $rowCells . static::$newLine;
            echo \str_repeat($this->rowSpacer . static::$newLine, static::$spacingY);
        }
        echo $this->rowSeparator . static::$newLine;
    }

    /**
     * @param $table
     *
     * @return array
     */
    private function columnHeaders($table)
    {
        return \array_keys(\reset($table));
    }

    /**
     * @param $table
     * @param $columnHeaders
     *
     * @return array
     */
    private function columnLengths($table, $columnHeaders)
    {
        $lengths = [];
        foreach ($columnHeaders as $header) {
            $header_length = \strlen($header);
            $max = $header_length;
            foreach ($table as $row) {
                $length = \strlen($row[$header]);
                if ($length > $max) {
                    $max = $length;
                }
            }

            if (($max % 2) != ($header_length % 2)) {
                $max += 1;
            }

            $lengths[$header] = $max;
        }

        return $lengths;
    }

    /**
     * @param $columnLengths
     *
     * @return string
     */
    private function rowSeparator($columnLengths)
    {
        $row = '';
        foreach ($columnLengths as $columnLength) {
            $row .= static::$jointCharacter . \str_repeat(static::$lineXCharacter,
                    (static::$spacingX * 2) + $columnLength);
        }
        $row .= static::$jointCharacter;

        return $row;
    }

    /**
     * @param $columnLengths
     *
     * @return string
     */
    private function rowSpacer($columnLengths)
    {
        $row = '';
        foreach ($columnLengths as $columnLength) {
            $row .= static::$lineYCharacter . \str_repeat(' ', (static::$spacingX * 2) + $columnLength);
        }
        $row .= static::$lineYCharacter;

        return $row;
    }

    /**
     * @param $columnHeaders
     * @param $columnLengths
     *
     * @return string
     */
    private function rowHeaders($columnHeaders, $columnLengths)
    {
        $row = '';
        foreach ($columnHeaders as $header) {
            $row .= static::$lineYCharacter . \str_pad($header, (static::$spacingX * 2) + $columnLengths[$header], ' ',
                    STR_PAD_BOTH);
        }
        $row .= static::$lineYCharacter;

        return $row;
    }

    /**
     * @param $rowCells
     * @param $columnHeaders
     * @param $columnLengths
     *
     * @return string
     */
    private function rowCells($rowCells, $columnHeaders, $columnLengths)
    {
        $row = '';
        foreach ($columnHeaders as $header) {
            $row .= static::$lineYCharacter . \str_repeat(' ', static::$spacingX) . \str_pad($rowCells[$header],
                    static::$spacingX + $columnLengths[$header], ' ', STR_PAD_RIGHT);
        }
        $row .= static::$lineYCharacter;

        return $row;
    }

}