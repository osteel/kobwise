<?php

namespace Osteel\Kobwise\Services;

use Symfony\Component\Console\Style\SymfonyStyle;

class Interpreter extends SymfonyStyle
{
    /**
     * {@inheritdoc}
     *
     * @param  string        $question
     * @param  string|null   $default
     * @param  callable|null $validator
     * @return string
     */
    public function ask(string $question, string $default = null, callable $validator = null)
    {
        return parent::ask(sprintf(' ✍️  %s', $question), $default, $validator);
    }

    /**
     * {@inheritdoc}
     *
     * @param  string $message
     * @return void
     */
    public function comment($message)
    {
        $this->block(sprintf(' ℹ️   %s', $message), null, 'fg=white;bg=blue', ' ', true);
    }

    /**
     * {@inheritdoc}
     *
     * @param  string $message
     * @return void
     */
    public function error($message)
    {
        $this->block(sprintf(' 🚨  %s', $message), null, 'fg=white;bg=red', ' ', true);
    }

    /**
     * {@inheritdoc}
     *
     * @param  string $message
     * @return void
     */
    public function success($message)
    {
        $this->block(sprintf(' 🎉  %s', $message), null, 'fg=white;bg=green', ' ', true);
    }
}
