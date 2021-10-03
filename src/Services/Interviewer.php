<?php

namespace Osteel\Kobwise\Services;

use Osteel\Kobwise\DataTransferObjects\ConversionData;
use Symfony\Component\Console\Input\InputInterface;

class Interviewer
{
    /**
     * @var Interpreter
     */
    private $interpreter;

    /**
     * Constructor.
     *
     * @param Interpreter $interpreter
     */
    public function __construct(Interpreter $interpreter)
    {
        $this->interpreter = $interpreter;
    }

    /**
     * Collect and return data necessary to conversion.
     *
     * @param  InputInterface $input
     * @return ConversionData
     * $throws Exception
     */
    public function collect(InputInterface $input): ConversionData
    {
        $data = new ConversionData();

        $data->setFile($input->getArgument('file'));

        $author = $this->interpreter->ask('Who is the author? (leave blank to skip)');
        $data->setAuthor($author);

        $url = $this->interpreter->ask('Would you like to add a URL? (leave blank to skip)');
        $data->setUrl($url);

        return $data;
    }
}
