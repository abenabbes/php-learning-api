<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\User;
use App\Exception\InvalidUserDataException;
use App\Exception\UserNotFoundException;
use App\Repository\UserRepositoryInterface;
use App\Service\UserService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    /** @var MockObject&UserRepositoryInterface */
    private $repositoryMock;
    private UserService $userService;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(UserRepositoryInterface::class);
        $this->userService    = new UserService($this->repositoryMock);
    }

    public function testFindUserByIdReturnsUser(): void
    {
        $expectedUser = new User(1, 'Alice', 30);

        $this->repositoryMock
            ->expects($this->once())
            ->method('findUserById')
            ->with(1)
            ->willReturn($expectedUser);

        $result = $this->userService->findUserById(1);

        $this->assertSame($expectedUser, $result);
    }

    public function testFindUserByIdThrowsExceptionWhenNotFound(): void
    {
        $this->repositoryMock
            ->expects($this->once())
            ->method('findUserById')
            ->with(999)
            ->willReturn(null);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("Utilisateur avec l'ID 999 introuvable.");

        $this->userService->findUserById(999);
    }

    public function testCreateUserThrowsExceptionWhenAgeIsInvalid(): void
    {
        // Stub — le repository ne sera jamais appelé
        $stub = $this->createStub(UserRepositoryInterface::class);
        $service = new UserService($stub);

        $user = new User(0, 'Bob', -5);

        $this->expectException(InvalidUserDataException::class);

        $service->createUser($user);
    }

    public function testCreateUserReturnsCreatedUser(): void
    {
        $user        = new User(0, 'Bob', 25);
        $createdUser = new User(1, 'Bob', 25);

        $this->repositoryMock
            ->expects($this->once())
            ->method('createUser')
            ->with($user)
            ->willReturn($createdUser);

        $result = $this->userService->createUser($user);

        $this->assertSame(1, $result->getId());
        $this->assertSame('Bob', $result->getName());
    }
}