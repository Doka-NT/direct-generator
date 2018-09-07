<?php

namespace skobka\dg\Factory;

use skobka\dg\Command\GenerateCommand;
use Symfony\Component\Console\Application;

/**
 * Фабрика приложения
 */
class AppFactory
{
    public const APP_NAME = 'direct-generator';
    public const APP_VERSION = '1.0.0';

    /**
     * @return Application
     */
    public static function create(): Application
    {
        $application = new Application(self::APP_NAME, self::APP_VERSION);
        $command = new GenerateCommand();

        $application->add($command);

        $application->setDefaultCommand($command->getName(), true);

        return $application;
    }
}
