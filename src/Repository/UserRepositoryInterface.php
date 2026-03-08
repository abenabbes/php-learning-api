<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;

interface UserRepositoryInterface
{
    public function findAllUsers(): array;
    public function findUserById(int $id): ?User;
    public function findUserByName(string $name): ?User;
    public function createUser(User $user): User;
}