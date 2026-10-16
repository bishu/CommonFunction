<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/src/Validation.php';

$cases = [
    ['HDFC0001234', 'HDFC0001234'],
    ['sbin0001234', 'SBIN0001234'],
    [" HDFC 0001234\t", 'HDFC0001234'],
    ['HDFC1001234', false],
    ['HDFC000123', false],
    ['HDFC00012345', false],
    ['12340001234', false],
    ['HDFC00012@4', false],
    ['', false],
];

foreach ($cases as [$input, $expected]) {
    $actual = ValidateIfsc($input);
    if ($actual !== $expected) {
        throw new RuntimeException(sprintf(
            'ValidateIfsc(%s): expected %s, got %s',
            var_export($input, true),
            var_export($expected, true),
            var_export($actual, true)
        ));
    }
}

printf("ValidateIfsc: %d checks passed.\n", count($cases));
