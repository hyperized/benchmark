<?php


namespace Hyperized\Benchmark\Generic;

/**
 * Class Table
 *
 * Source from: https://gist.github.com/RamadhanAmizudin/ca87f7be83c6237bb070
 *
 * @package Hyperized\Benchmark\Generic
 */
class Table
{
    /**
     * @var int
     */
    private static $spacingX = 1;
    /**
     * @var int
     */
    private static $spacingY = 0;
    /**
     * @var string
     */
    private static $jointCharacter = '+';
    /**
     * @var string
     */
    private static $lineXCharacter = '-';
    /**
     * @var string
     */
    private static $lineYCharacter = '|';
    /**
     * @var string
     */
    private static $newLine = "\n";

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
     * @param $table
     *
     * @return array
     */
    private function columnHeaders($table): array
    {
        return \array_keys(\reset($table));
    }

    /**
     * @param $table
     * @param $columnHeaders
     *
     * @return array
     */
    private function columnLengths($table, $columnHeaders): array
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

            if (($max % 2) !== ($header_length % 2)) {
                ++$max;
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
    private function rowSeparator($columnLengths): string
    {
        $row = '';
        foreach ($columnLengths as $columnLength) {
            $row .= self::$jointCharacter . \str_repeat(self::$lineXCharacter,
                    (self::$spacingX * 2) + $columnLength);
        }
        $row .= self::$jointCharacter;

        return $row;
    }

    /**
     * @param $columnLengths
     *
     * @return string
     */
    private function rowSpacer($columnLengths): string
    {
        $row = '';
        foreach ($columnLengths as $columnLength) {
            $row .= self::$lineYCharacter . \str_repeat(' ', (self::$spacingX * 2) + $columnLength);
        }
        $row .= self::$lineYCharacter;

        return $row;
    }

    /**
     * @param $columnHeaders
     * @param $columnLengths
     *
     * @return string
     */
    private function rowHeaders($columnHeaders, $columnLengths): string
    {
        $row = '';
        foreach ($columnHeaders as $header) {
            $row .= self::$lineYCharacter . \str_pad($header, (self::$spacingX * 2) + $columnLengths[$header], ' ',
                    STR_PAD_BOTH);
        }
        $row .= self::$lineYCharacter;

        return $row;
    }

    /**
     *
     */
    private function render(): void
    {
        echo $this->rowSeparator . self::$newLine;
        echo \str_repeat($this->rowSpacer . self::$newLine, self::$spacingY);
        echo $this->rowHeaders . self::$newLine;
        echo \str_repeat($this->rowSpacer . self::$newLine, self::$spacingY);
        echo $this->rowSeparator . self::$newLine;
        echo \str_repeat($this->rowSpacer . self::$newLine, self::$spacingY);
        foreach ($this->tableArray as $rowCells) {
            $rowCells = $this->rowCells($rowCells, $this->columnHeaders, $this->columnLength);
            echo $rowCells . self::$newLine;
            echo \str_repeat($this->rowSpacer . self::$newLine, self::$spacingY);
        }
        echo $this->rowSeparator . self::$newLine;
    }

    /**
     * @param $rowCells
     * @param $columnHeaders
     * @param $columnLengths
     *
     * @return string
     */
    private function rowCells($rowCells, $columnHeaders, $columnLengths): string
    {
        $row = '';
        foreach ($columnHeaders as $header) {
            $row .= self::$lineYCharacter . \str_repeat(' ', self::$spacingX) . \str_pad($rowCells[$header],
                    self::$spacingX + $columnLengths[$header], ' ', STR_PAD_RIGHT);
        }
        $row .= self::$lineYCharacter;

        return $row;
    }

}