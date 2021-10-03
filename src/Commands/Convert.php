<?php

namespace Osteel\Kobwise\Commands;

use Osteel\Kobwise\Exceptions\Exception;
use Osteel\Kobwise\Services\Clerk;
use Osteel\Kobwise\Services\Converter;
use Osteel\Kobwise\Services\Interviewer;
use Osteel\Kobwise\Services\Interpreter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Convert extends Command
{
    /**
     * The name of the command (the part after "bin/console").
     *
     * @var string
     */
    protected static $defaultName = 'convert';

    /**
     * The command description shown when running "php bin/console list".
     *
     * @var string
     */
    protected static $defaultDescription = 'Converts Kobo annotations to Readwise highlights.';

    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'The annotation file');
    }

    /**
     * Execute the command
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int 0 if everything went fine, or an exit code.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $interpreter = new Interpreter($input, $output);
        $interviewer = new Interviewer($interpreter);
        $clerk       = new Clerk();
        $converter   = new Converter($clerk, $interpreter);

        try {
            $file = $converter->convert($interviewer->collect($input));
        } catch (Exception $exception) {
            $interpreter->error($exception->getMessage());
            return $exception->getCode();
        }

        $interpreter->success(sprintf('The book annotations were successfully converted! See file "%s".', $file));

        $interpreter->comment('I recommend you review your highlights before import. Here is a preview:');

        $interpreter->horizontalTable($clerk->readCsv($file, 100), [$clerk->readCsv($file, 100)]);
        $clerk->close($file);

        return Command::SUCCESS;
    }
}
