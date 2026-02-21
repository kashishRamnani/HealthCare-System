<?php 
namespace App\Models;
use PDO;
class User{
    private $conn;
    public function __construct($dbConnection){
        $this->conn = $dbConnection;
    }
    public function createUser($name,$email,$password,$role){
        $passwordHash = password_hash($password,PASSWORD_BCRYPT);
        $stmt = $this->conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$name, $email, $passwordHash, $role]);
    }

     public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function login($email,$password){
    $user = $this->getUserByEmail($email);
    if($user && password_verify($password, $user['password'])){
       
       $_SESSION['user_id'] = $user['id'];
       $_SESSION['role'] = $user['role'];
       $_SESSION['name'] = $user['name'];
       return true;
    }
     return false;
}


    public function getUserById($id){
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getAllUsers(){
        $stmt = $this->conn->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
    public function deleteUser($id){
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id=?");
        return $stmt->execute($id);
    }
}
?>