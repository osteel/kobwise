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

        $this->processHighlight($source, $target, $constants);

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
     * Parse and add the next highlight.
     *
     * @param  string $source
     * @param  string $target
     * @param  array  $constants
     * @return void
     */
    private function processHighlight(string $source, string $target, array $constants): void
    {
        if (is_null($highlight = $this->readLine($source))) {
            return;
        }

        if (empty($highlight)) {
            $this->processHighlight($source, $target, $constants);
            return;
        }

        $note = null;

        while (! empty($line = $this->readLine($source))) {
            if ($note !== null) {
                $note .= sprintf("\n%s", $line);
            } elseif (is_null($note = str_starts_with($line, 'Note: ') ? substr($line, 6) : null)) {
                $highlight .= sprintf("\n%s", $line);
            }
        }

        $this->clerk->writeCsv($target, array_merge([$highlight, $note], $constants));

        $this->processHighlight($source, $target, $constants);
    }
}
