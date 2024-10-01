<?php

use Illuminate\Foundation\Console\QueuedCommand;

function getQueuedCommandProtectedDataProperty(QueuedCommand $command): mixed
{
    $class = new ReflectionClass($command);
    $reflection = $class->getProperty('data');

    return $reflection->getValue($command);
}
