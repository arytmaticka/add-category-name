<?php

namespace App\Traits;

use InvalidArgumentException;
use Throwable;
use function json_decode;
use function json_encode;


trait JsonArrayTrait
{
    /**
     * @var array Json Data stored as array
     */
    private array $data = [];

    /**
     * @var string|null Key for associative array. If it's null number of row is stored as key
     */

    /**
     * Reads input and parse it to an array
     *
     * @param string $input Input string
     * @param int $maxDepth Maximum depth of returned array
     * @return void
     * @throws InvalidArgumentException
     */
    public function read(string $input, int $maxDepth = 512): void
    {
        try {
            $this->data = json_decode($input, true, $maxDepth);
        } catch (Throwable $ex){
            throw new InvalidArgumentException(json_last_error_msg());
        }
        if (property_exists($this, 'key') && isset($this->key)) {
            $newData = [];
            foreach ($this->data as $num => $row) {
                if (!array_key_exists($this->key, $row)) {
                    throw new InvalidArgumentException("Unable to find {$this->key} in $num row.");
                }
                $newData[$row[$this->key]] = $row;
            }
            $this->data = $newData;
        }

    }

    /**
     * Reads json from file
     *
     * @param string $fileName
     * @param int $maxDepth Maximum depth of returned array
     * @return void
     * @throws InvalidArgumentException
     */
    public function readFromFile(string $fileName, int $maxDepth = 512): void
    {
        $contents = file_get_contents($fileName);
        if ($contents == false) {
            throw new InvalidArgumentException("Problem with reading json file: " . $fileName);
        }

        $this->read($contents, $maxDepth);
    }

    public function asArray(): array
    {
        return $this->data;
    }

    /**
     * @return string JSON encoded string
     */
    public function asString(): string
    {
        return json_encode($this->asArray());
    }
}