<?php

declare(strict_types=1);

namespace PhpEmptyZeroValidationLab;

use DomainException;

final class QuantityPatchValidator
{
    public function parseRequiredQuantity(string $quantity): int
    {
        if (empty($quantity)) {
            throw new DomainException('quantity is required');
        }

        if (!ctype_digit($quantity)) {
            throw new DomainException('quantity must contain only digits');
        }

        return (int) $quantity;
    }
}
