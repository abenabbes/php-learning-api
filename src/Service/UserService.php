<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Exception\InvalidUserDataException;
use App\Exception\UserNotFoundException;
use App\Repository\UserRepositoryInterface;


// ** Ancien façon de faire les imports avant PHP-7 **
//require_once __DIR__ . '/../Entity/User.php';
//require_once __DIR__ . '/../Repository/UserRepository.php';

class UserService
{
    // Instanciation de repository
    private UserRepositoryInterface $userRepository; 

    public function __construct(UserRepositoryInterface $userRepository){
        $this->userRepository = $userRepository;
    }
    
    private function getUsers(): array{
        $user1 = new User(1,"Nom-10", 10);
        $user2 = new User(2,"Nom-15", 15);
        $user3 = new User(3,"Nom-20", 20);
        $user4 = new User(4,"Nom-25", 25);
        $user5 = new User(5,"Nom-30", 30);
        $user6 = new User(6,"Nom-35", 35);
        $user7 = new User(7,"Nom-40", 40);
        
        $users = [$user1, $user2, $user3, $user4, $user5, $user6, $user7];
        
        return $users;
        
    }
    
    public function findUsersOlderThan(int $age): array
    {
        // Tableau ou liste vide
        $filteredUsers = [];
        
        foreach ($this->getUsers() as $user) {
            if ($user->getAge() >= $age) {
                // Ajout de User dans le tableau
                $filteredUsers[] = $user;
            }
        }
        
        return $filteredUsers;
    }
      
    // *************************************************
    public function findUserByName(string $name): User{
        //Récupérer l'utilisateur de la base de données
        $user = $this->userRepository->findUserByName($name);
        //Si l'utilisateur n'existe pas, lancer une exception
        if ($user === null) {
            throw UserNotFoundException::withName($name);
        }
        return $user;
    }    

     public function findUserById(int $id): User{
        $user = $this->userRepository->findUserById($id);
        if ($user === null) {
            throw UserNotFoundException::withId($id);
        }
        return $user;
    }
    
    public function findAllUsers(): array{
        return $this->userRepository->findAllUsers();
    }

    public function createUser(User $user): User{
        // Si l'age de l'utilisateur est négatif, lancer une exception
        if ($user->getAge() < 0) {
            throw InvalidUserDataException::invalidAge();
        }
        return $this->userRepository->createUser($user);
    }
}

