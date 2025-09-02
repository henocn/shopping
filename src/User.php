<?php
namespace src;

use PDO;
use PDOException;

class User{
      private $bd;

    public function __construct(PDO $bd)
    {
        $this->bd = $bd;
    }


    private function getOne($email)
    {
        $sql = $this->bd->prepare('SELECT * FROM users WHERE email = :email');
        $sql->execute([
            'email' => $email
        ]);

        $user = $sql->fetch(PDO::FETCH_ASSOC);
        return $user;
    }

    private function email_exists($email): bool
    {
        $sql = $this->bd->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
        $sql->execute([
            'email' => $email
        ]);

        return $sql->fetchColumn() > 0;
    }


    public function verify($data)
    {
        $user = $this->getOne($data['email']);
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



    // Ajout d'assistante.
    public function create(array $data)
    {
        if (!$this->email_exists($data['email'])) {
            $options = [
                'cost' => 12,
            ];
            $password = password_hash($data['password'], PASSWORD_BCRYPT, $options);
            $numero = $data['numero'];
            $country = $data['country'];
            $role = "assistante";
            $req = $this->bd->prepare("INSERT INTO users (username, numero, country, password, role) VALUES (:username, :numero, :country, :password, :role)");

            if ($req->execute([
                'username' => $data['username'],
                'numero' => $numero,
                'country' => $country,
                'password' => $password,
                'role' => $role
            ])) {
                return true;
            } else {
                error_log("Erreur lors de l'insertion : " . implode(" ", $req->errorInfo()));
                return false;
            }
        } else {
            error_log("Le nom d'utilisateur existe déjà.");
            return false;
        }
    }



    public function getAllAssistantes(): array
    {
        $sql = $this->bd->prepare('SELECT `users`.`id`, `users`.`email`, `users`.`role`, `users`.`country`, `users`.`is_active`
            FROM `users` WHERE role = "assistante"');
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteAssistante($id): bool
    {
        $sql = $this->bd->prepare('DELETE FROM users WHERE id = :id AND role = "assistante"');
        return $sql->execute(['id' => $id]);
    }

    public function getAssistanteById($id)
    {
        $sql = $this->bd->prepare('SELECT * FROM users WHERE id = :id AND role = "assistante"');
        $sql->execute(['id' => $id]);
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function updateAssistanteStatus($id, $status): bool
    {
        $sql = $this->bd->prepare('UPDATE users SET status = :status WHERE id = :id AND role = "assistante"');
        return $sql->execute(['id' => $id, 'status' => $status]);
    }

}