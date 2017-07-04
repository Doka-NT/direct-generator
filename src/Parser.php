<?php

namespace skobka\dg;

interface Parser
{
    /**
     * @return void
     */
    public function parse();

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
