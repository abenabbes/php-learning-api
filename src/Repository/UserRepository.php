<?php
declare(strict_types=1); 

namespace App\Repository;

use App\Database\Database;
use App\Entity\User;
use PDO;

// ** Ancien façon de faire les imports avant PHP-7 **
//require_once __DIR__ . '/../Entity/User.php';
//require_once __DIR__ . '/../Database/Database.php';

//************************************************************************
// TEST des URLS de l'API :
// URI : http://localhost/php-learning-api/public/index.php// 
// 
//GET  /users                    → liste tous les users de la BDD
//GET  /users/1                  → user avec ID 1
//GET  /users/name/Alice         → user avec le nom Alice
//POST /users  {"name":"Bob","age":30}  → crée un user, retourne 201
// ************************************************************************

class UserRepository implements UserRepositoryInterface
{
    //attributs
    private PDO $pdo ;

    //constructeur
    public function __construct(){
        $this->pdo = Database::getConnection();
    }

    public function findAllUsers(): array{
        // Requête SQL pour récupérer tous les utilisateurs
        $sql = "SELECT * FROM users";
        // Exécution de la requête
        $stmt = $this->pdo->query($sql);
        // Récupération des résultats et création d'instances de User
        $users = [];

        while($row = $stmt->fetch()){
            $users[] = new User(
                $row['id'],
                $row['name'],
                $row['age']
            );
        }

        return $users;
    }


    public function findUserById(int $id): ?User{
            
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }
        
        return new User(
            $row['id'],
            $row['name'],
            $row['age']
        
        );
    }

    public function findUserByName(string $name): ?User{
    
        $sql = "SELECT * FROM users WHERE name = :name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['name' => $name]);
        $row = $stmt->fetch();

        if ($row === false) {
        return null;
        }

        return new User(
            $row['id'], 
            $row['name'], 
            $row['age']
        );
    }

    public function createUser(User $user): User{
    
        $sql = "INSERT INTO users (name, age) VALUES (:name, :age)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':name' => $user->getName(),
            ':age' => $user->getAge()
        ]);

        // Récupérer l'ID généré pour le nouvel utilis ateur
        $id = (int)$this->pdo->lastInsertId();
        // Retourner l'utilisateur créé avec son ID
    return new User($id, 
                    $user->getName(), 
                    $user->getAge());
    }
}
