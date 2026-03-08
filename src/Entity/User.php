<?php
declare(strict_types=1);

namespace App\Entity;

class User
{
    public function __construct(
        private int    $id,
        private string $name,
        private int    $age
    ) {}

    public function toArray(): array
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
            'age'  => $this->age,
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setAge(int $age): void
    {
        $this->age = $age;
    }
}