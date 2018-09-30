<?php

namespace skobka\dg;

interface Parser
{
    /**
     * @param string $file
     * @return void
     */
    public function parse(string $file);

    /**
     * @return string[]
     */
    public function getKeywords(): array;

    /**
     * @return string[]
     */
    public function getTitles(): array;

    /**
     * @return string[]
     */
    public function getTexts(): array;
}
