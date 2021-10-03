<?php

namespace Osteel\Kobwise\Services;

class Clerk
{
    /**
     * Resource map.
     *
     * @var array
     */
    private array $resources;

    /**
     * Close the provided file's resource.
     *
     * @param  string $file
     * @return void
     */
    public function close(string $file): void
    {
        if (empty($this->resources[$file])) {
            return;
        }

        fclose($this->resources[$file]);
        unset($this->resources[$file]);
    }

    /**
     * Return the number of lines of the provided file.
     *
     * @param  string $file
     * @return string
     */
    public function count(string $file): int
    {
        $count    = 0;
        $resource = $this->getResource($file, 'r');

        while (! feof($resource)) {
            $line  = fgets($resource, 4096);
            $count = $count + substr_count($line, PHP_EOL);
        }

        rewind($resource);

        return $count;
    }

    /**
     * Read and return the next line of the provided file.
     *
     * @param  string $file
     * @param  int|null $maxChar
     * @return string|null
     */
    public function read(string $file, ?int $maxChar = null): ?string
    {
        $resource = $this->getResource($file, 'r');

        if (feof($resource)) {
            return null;
        }

        $content = fgets($resource);

        return $content !== false ? $this->trimValues([$content], $maxChar)[0] : null;
    }

    /**
     * Read and return the next line of the provided csv file.
     *
     * @param  string   $file
     * @param  int|null $maxChar
     * @return array|null
     */
    public function readCsv(string $file, ?int $maxChar = null): ?array
    {
        $resource = $this->getResource($file, 'r');

        if (feof($resource)) {
            return null;
        }

        $content = fgetcsv($resource);

        return $content !== false ? $this->trimValues($content, $maxChar) : null;
    }

    /**
     * Add some fields to the provided csv file.
     *
     * @param  string $file
     * @param  array  $fields
     * @return void
     */
    public function writeCsv(string $file, array $fields): void
    {
        fputcsv($this->getResource($file, 'w'), $fields);
    }

    /**
     * Return a new or existing resource for the provided file.
     *
     * @param  string $file
     * @param  string $mode
     * @return resource
     */
    private function getResource(string $file, string $mode)
    {
        return $this->resources[$file] ?? $this->resources[$file] = fopen($file, $mode);
    }

    /**
     * Make sure each value of the provided array is no longer than the provided number of characters.
     *
     * @param  array    $values
     * @param  int|null $maxChar
     * @return array
     */
    private function trimValues(array $values, ?int $maxChar = null): array
    {
        if ($maxChar === null) {
            return $values;
        }

        foreach ($values as $key => $value) {
            $values[$key] = strlen($value) <= $maxChar ? $value : sprintf('%s...', substr($value, 0, $maxChar));
        }

        return $values;
    }
}
