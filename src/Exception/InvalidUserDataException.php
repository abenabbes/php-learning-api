<?php
declare(strict_types=1);

namespace App\Exception;

use InvalidArgumentException;

class InvalidUserDataException extends InvalidArgumentException
{
    public static function missingFields(): self
    {
        return new self("Les champs 'name' et 'age' sont obligatoires.");
    }

    public static function invalidAge(): self
    {
        return new self("L'âge doit être un nombre positif.");
    }
}