<?php

namespace Osteel\Kobwise\Services;

use Osteel\Kobwise\DataTransferObjects\ConversionData;

class Converter
{
    private const HEADERS = ['Highlight', 'Note', 'Title', 'Author', 'URL'];

    /**
     * @var Clerk
     */
    private $clerk;

    /**
     * @var Interpreter
     */
    private $interpreter;

    /**
     * Constructor.
     *
     * @param Clerk       $clerk
     * @param Interpreter $interpreter
     */
    public function __construct(Clerk $clerk, Interpreter $interpreter)
    {
        $this->clerk       = $clerk;
        $this->interpreter = $interpreter;
    }

    /**
     * Generate the csv file based on collected data.
     *
     * @param  ConversionData $data
     * @return string
     */
    public function convert(ConversionData $data): string
    {
        $source = $data->getFile();
        $target = sprintf('%s.csv', pathinfo($source)['filename']);

        $this->clerk->writeCsv($target, self::HEADERS);

        $this->interpreter->progressStart($this->clerk->count($source));

        // The file's first line should be the title.
        $constants = [$this->readLine($source), $data->getAuthor(), $data->getUrl()];

        $this->addRow($source, $target, $constants);

        $this->clerk->close($source);
        $this->clerk->close($target);

        $this->interpreter->progressFinish();

        return $target;
    }

    /**
     * Read and return the next line of the file.
     *
     * @param  string $source
     * @return string|null
     */
    private function readLine(string $source): ?string
    {
        $this->interpreter->progressAdvance();

        if (is_null($line = $this->clerk->read($source))) {
            return null;
        }

        return trim(preg_replace('/[ \t]+/', ' ', preg_replace('/\s*$^\s*/m', "\n", $line)));
    }

    /**
     * Process the next line of the file.
     *
     * @param  string      $source
     * @param  string      $target
     * @param  array       $constants
     * @param  string|null $current
     * @param  string|null $next
     * @return void
     */
    private function addRow(
        string $source,
        string $target,
        array $constants,
        ?string $current = null,
        ?string $next = null
    ): void {
        $current = $next;

        if (is_null($next = $this->readLine($source))) {
            return;
        }

        if (empty($current)) {
            $this->addRow($source, $target, $constants, $current, $next);
            return;
        }

        $note = str_starts_with($next, 'Note: ') ? substr($next, 6) : null;

        $this->clerk->writeCsv($target, array_merge([$current, $note], $constants));

        if (! empty($note)) {
            $next = $this->readLine($source);
        }

        $this->addRow($source, $target, $constants, $current, $next);
    }
}
