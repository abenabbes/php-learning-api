<?php
declare(strict_types=1); 

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use App\Exception\InvalidUserDataException;
use App\Exception\UserNotFoundException;
use App\Repository\UserRepository;

// ** Ancien façon de faire les imports avant PHP-7 **
//require_once __DIR__ . '/../Entity/User.php';     
//require_once __DIR__ . '/../Service/UserService.php';

class UserController{
    
    // Instanciation de service
    private UserService $userService ;  

    public function __construct(){
        // On injecte le Repository dans le Service
        $this->userService = new UserService(new UserRepository());
    }
    
    
    // Méthode permet de trouver un user par son nom.
    public function findUserGetByName(string $name) : void{
        
        //Type de header dans les cas d'un GET
        header('Content-Type: application/json');
                
       try {
            $user = $this->userService->findUserByName($name);
            echo json_encode($user->toArray());
        } catch (UserNotFoundException $e) {
            http_response_code(404);
            echo json_encode(['error' => $e->getMessage()]);
        }       
    }

    // Méthode permet de trouver un user par son ID.
    public function findUserGetById(int $id) : void{
        
        //Type de header dans les cas d'un GET
        header('Content-Type: application/json');
        
        try {
            $user = $this->userService->findUserById($id);
            echo json_encode($user->toArray());
        } catch (UserNotFoundException $e) {
            http_response_code(404);
            echo json_encode(['error' => $e->getMessage()]);
        }
        
    }

    // Méthode permet de créer un user à partir des données envoyées dans le corps de la requête POST.
    public function createUserPost() : void{
         header('Content-Type: application/json');
    
        // Recupération des paramétres de l'URL dans le cas d'un POSTE : dans ce cas name et age
        // Exemple de URL = http://localhost/projet-from-scratch/users&name=toto&age=25
       $data = json_decode(file_get_contents("php://input"), true);
       // Si le data ne contient pas ni name OU ni age --> Alors error
       //if(!isset($data['name']) || !isset($data['age'])){
         if (!isset($data['name'], $data['age'])) {
         http_response_code(400);
         echo json_encode(["error" => "Les données sont absents ou invalides"]);
         return ;
       } 
       
        // Création de user
          try {
            $user    = new User(0, (string) $data['name'], (int) $data['age']);
            $created = $this->userService->createUser($user);
            http_response_code(201);
            echo json_encode($created->toArray());
        } catch (InvalidUserDataException $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // Méthode permet de trouver tous les users.
    public function findAllUsers(): void{
        //Type de header dans les cas d'un GET
        header('Content-Type: application/json');      
        //recupère de toute la liste des users           
        $allUsers = $this->userService->findAllUsers();
        if (count($allUsers) === 0) {
            http_response_code(404);
            echo json_encode(["error" => "Aucun utilisateur trouvé"]);
            return;
        }
        //envoie de résultat sous forme de JSON
        $result = [];
        $result = array_map(fn(User $user) => $user->toArray(), $allUsers);        
        //foreach ($allUsers as $user) {
        //    $result[] = $user->toArray();
        //}

        echo json_encode($result);
    }
}

