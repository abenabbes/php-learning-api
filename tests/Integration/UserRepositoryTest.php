<?php
declare(strict_types=1);

namespace App\Tests\Integration;

use App\Database\Database;
use App\Entity\User;
use App\Repository\UserRepository;
use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;

class UserRepositoryTest extends TestCase
{
    private UserRepository $repository;

    protected function setUp(): void
    {
        // Chargement du .env
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $this->repository = new UserRepository();

        // Nettoyage de la table avant chaque test
        $pdo = Database::getConnection();
        $pdo->exec('DELETE FROM users');
        $pdo->exec('ALTER TABLE users AUTO_INCREMENT = 1');
    }

    // ✅ Test 1 — findAllUsers retourne tableau vide si BDD vide
    public function testFindAllUsersReturnsEmptyArray(): void
    {
        $result = $this->repository->findAllUsers();
        $this->assertSame([], $result);
    }

    // ✅ Test 2 — createUser insère en BDD et retourne le user avec ID
    public function testCreateUserInsertsAndReturnsUserWithId(): void
    {
        $user   = new User(0, 'Alice', 30);
        $result = $this->repository->createUser($user);

        $this->assertSame(1, $result->getId());
        $this->assertSame('Alice', $result->getName());
        $this->assertSame(30, $result->getAge());
    }

    // ✅ Test 3 — findUserById retourne le bon user
    public function testFindUserByIdReturnsCorrectUser(): void
    {
        $user    = new User(0, 'Alice', 30);
        $created = $this->repository->createUser($user);

        $found = $this->repository->findUserById($created->getId());

        $this->assertNotNull($found);
        $this->assertSame('Alice', $found->getName());
    }

    // ✅ Test 4 — findUserById retourne null si non trouvé
    public function testFindUserByIdReturnsNullWhenNotFound(): void
    {
        $result = $this->repository->findUserById(999);
        $this->assertNull($result);
    }

    // ✅ Test 5 — findAllUsers retourne tous les users insérés
    public function testFindAllUsersReturnsAllInsertedUsers(): void
    {
        $this->repository->createUser(new User(0, 'Alice', 30));
        $this->repository->createUser(new User(0, 'Bob', 25));

        $result = $this->repository->findAllUsers();

        $this->assertCount(2, $result);
    }
}