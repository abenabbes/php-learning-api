<?php
declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

class UserNotFoundException extends RuntimeException
{
    public static function withId(int $id): self
    {
        return new self("Utilisateur avec l'ID $id introuvable.");
    }

    public static function withName(string $name): self
    {
        return new self("Utilisateur avec le nom '$name' introuvable.");
    }
}