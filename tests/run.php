<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/QuantityPatchValidator.php';

use PhpEmptyZeroValidationLab\QuantityPatchValidator;

function assertSameValue(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        throw new RuntimeException($message . "\nExpected: " . var_export($expected, true) . "\nActual:   " . var_export($actual, true));
    }
}

function assertThrows(callable $operation, string $exceptionClass, string $message): void
{
    try {
        $operation();
    } catch (Throwable $error) {
        if ($error instanceof $exceptionClass) {
            return;
        }
        throw new RuntimeException($message . "\nExpected exception: {$exceptionClass}\nActual exception: " . $error::class);
    }

    throw new RuntimeException($message . "\nExpected exception: {$exceptionClass}\nActual exception: none");
}

$validator = new QuantityPatchValidator();
$tests = [
    '空文字の数量は必須入力エラーになる' => static function () use ($validator): void {
        assertThrows(
            static fn () => $validator->parseRequiredQuantity(''),
            \DomainException::class,
            '空文字は数量として受け付けてはならない'
        );
    },
    '文字列の0は数量ゼロとして受け付けられる' => static function () use ($validator): void {
        assertSameValue(
            0,
            $validator->parseRequiredQuantity('0'),
            '在庫をゼロへ更新する要求を未入力として拒否してはならない'
        );
    },
    '数字以外の値は数量として受け付けられない' => static function () use ($validator): void {
        assertThrows(
            static fn () => $validator->parseRequiredQuantity('zero'),
            \DomainException::class,
            '数字以外の値を数量として解釈してはならない'
        );
    },
];

$failures = [];
foreach ($tests as $name => $test) {
    try {
        $test();
        fwrite(STDOUT, "PASS: {$name}\n");
    } catch (Throwable $error) {
        $failures[] = $name;
        fwrite(STDERR, "FAIL: {$name}\n{$error->getMessage()}\n");
    }
}

if ($failures !== []) {
    fwrite(STDERR, sprintf("%d test(s) failed.\n", count($failures)));
    exit(1);
}

fwrite(STDOUT, sprintf("%d test(s) passed.\n", count($tests)));
