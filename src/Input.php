<?php

namespace skobka\dg;

class Input
{
    /**
     * @var Input
     */
    private static $instance;

    /**
     * @var string
     */
    private $file;
    /**
     * @var string
     */
    private $output;

    /**
     * @return Input
     */
    public static function getInstance(): Input
    {
        if (!self::$instance) {
            self::$instance = new Input();
        }

        return self::$instance;
    }

    private function __construct()
    {
        if (PHP_SAPI !== 'cli') {
            throw new \Exception("Can be run only in CLI");
        }

        $argc = $_SERVER['argc'];
        $argv = $this->getArgv();

        if ($argc < 2) {
            throw new \Exception('Необходимо указать файл в качестве аргумента');
        }

        $this->file = $argv[1];
        $this->output = $argc > 2 ? $argv[2] : 'php://stdout';
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getOutput(): string
    {
        return $this->output;
    }

    public function hasSkipLong()
    {
        return in_array('--skip-long', $this->getArgv());
    }

    /**
     * Массив параметров, переданных скрипту
     * @return array
     */
    protected function getArgv()
    {
        return $_SERVER['argv'];
    }
}
