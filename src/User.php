<?php

namespace src;

use PDO;
use PDOException;

class User
{
    private $bd;

    public function __construct(PDO $bd)
    {
        $this->bd = $bd;
    }


    private function getUserByEmail($email)
    {
        $sql = $this->bd->prepare('SELECT * FROM users WHERE email = :email');
        $sql->execute([
            'email' => $email
        ]);
        $user = $sql->fetch(PDO::FETCH_ASSOC);
        return $user;
    }



    private function getUserById($id)
    {
        $sql = $this->bd->prepare('SELECT * FROM users WHERE id = :id');
        $sql->execute([
            'id' => $id
        ]);
        $user = $sql->fetch(PDO::FETCH_ASSOC);
        return $user;
    }


    public function getAllUsers(): array
    {
        $sql = $this->bd->prepare('SELECT `users`.`id`, `users`.`email`, `users`.`role`, `users`.`country`, `users`.`is_active`
            FROM `users`');
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }


    public function email_exists($email): bool
    {
        $sql = $this->bd->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
        $sql->execute([
            'email' => $email
        ]);

        return $sql->fetchColumn() > 0;
    }

    public function switchaccountStatus($id): bool
    {
        $user = $this->getUserById($id);
        if ($user["is_active"] == 0) {
            $sql = $this->bd->prepare('UPDATE users SET is_active = 1 WHERE id = :id');
        } else {
            $sql = $this->bd->prepare('UPDATE users SET is_active = 0 WHERE id = :id');
        }
        return $sql->execute(['id' => $id]);
    }


    public function verify($data)
    {
        $user = $this->getUserByEmail($data['email']);
        if ($user) {
            if (hash_equals($user['password'], crypt($data['password'], $user['password']))) {
                $message = "Email et mot de passe correct";
                $result = [
                    "success" => true,
                    "message" => $message,
                    "role" => $user['role'],
                    "email" => $user['email'],
                    "country" => $user['country'],
                    "is_active" => $user['is_active']
                ];
            } else {
                $message = "error1";
                $result = [
                    "success" => false,
                    "message" => $message
                ];
            }
        } else {
            $message = "error2";
            $result = [
                "success" => false,
                "message" => $message
            ];
        }
        return  $result;
    }


    public function create(array $data)
    {

        $options = [
            'cost' => 12,
        ];
        $password = password_hash($data['password'], PASSWORD_BCRYPT, $options);
        $country = $data['country'];
        $role = $data['role'];
        $req = $this->bd->prepare("INSERT INTO users (email, country, password, role) VALUES (:email, :country, :password, :role)");

        if ($req->execute([
            'email' => $data['email'],
            'country' => $country,
            'password' => $password,
            'role' => $role
        ])) {
            return true;
        } else {
            error_log("Erreur lors de l'insertion : " . implode(" ", $req->errorInfo()));
            return false;
        }
    }


    public function deleteUser($id): bool
    {
        $sql = $this->bd->prepare('DELETE FROM users WHERE id = :id');
        return $sql->execute(['id' => $id]);
    }

}
