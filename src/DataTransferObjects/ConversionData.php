<?php

namespace Osteel\Kobwise\DataTransferObjects;

use Osteel\Kobwise\Exceptions\Exception;
use Symfony\Component\Console\Command\Command;

class ConversionData
{
    /**
     * @var string
     */
    private ?string $author;

    /**
     * @var string
     */
    private string $file;

    /**
     * @var string
     */
    private ?string $url;

    /**
     * @return string|null
     */
    public function getAuthor(): ?string
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param  string|null $author
     * @return ConversionData
     */
    public function setAuthor(?string $author = null): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @param  string $file
     * @return ConversionData
     * @throws Exception
     */
    public function setFile(string $file): self
    {
        if (! is_file($file)) {
            throw new Exception('I coulnd\'t find this file... Are you sure the path is correct?', Command::INVALID);
        }

        $resource = fopen($file, 'r');

        if (empty(fgets($resource))) {
            throw new Exception('This file appears to be empty', Command::INVALID);
        }

        fclose($resource);

        $this->file = $file;

        return $this;
    }

    /**
     * @param  string|null $url
     * @return ConversionData
     */
    public function setUrl(?string $url = null): self
    {
        $this->url = $url;

        return $this;
    }
}
