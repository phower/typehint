<?php

require __DIR__ . '/../vendor/autoload.php';

use Phower\TypeHint\ErrorHandler;

ErrorHandler::initialize();

function strongTyped(string $string)
{
    return $string . PHP_EOL;
}

function weakTyped($string)
{
    if (!is_string($string)) {
        throw new \InvalidArgumentException('Argument must be a string.');
    }
    return $string . PHP_EOL;
}

$runs = $argc > 1 && $argv[1] > 0 ? $argv[1] : 1000;

$sum = 0;

for ($i = 0; $i < $runs; $i++) {
    $start = microtime(true);
    strongTyped('string' . $i);
    $duration = microtime(true) - $start;
    $sum += $duration;
}

$strong = $sum / $runs;

$sum = 0;

for ($i = 0; $i < $runs; $i++) {
    $start = microtime(true);
    weakTyped('string' . $i);
    $duration = microtime(true) - $start;
    $sum += $duration;
}

$weak = $sum / $runs;

echo 'Strong Typed: ' . number_format($strong, 8) . ' secs' . PHP_EOL;
echo 'Weak Typed  : ' . number_format($weak, 8) . ' secs' . PHP_EOL;
