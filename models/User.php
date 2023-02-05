<?php
    class User
    {
        public $id;
        public $name;
        public $lastName;
        public $email;
        public $password;
        public $imageUser;
        public $token;
        public $biography;

        public function getFullName($user)
        {
            return $user->name . " " . $user->lastName;
        }

        public function generateToken()
        {
            return bin2hex(random_bytes(50));
        }

        public function generatePassword($password)
        {
            return password_hash($password, PASSWORD_DEFAULT);
        }

        public function generateImageName($extImage)
        {
            return bin2hex(random_bytes(60)) . $extImage;
        }

        public function deleteImageUser($imageUser)
        {
            unlink("./img/users/" . $imageUser);
        }
    }

    interface UserDAOInterface
    {
        public function buildUser($data);
        public function createUser(User $user, $authenticateUser = false);
        public function updateUser(User $user, $redirect = true);
        public function verifyToken($protected = false);
        public function setTokenToSession($token, $redirect = true);
        public function authenticateUser($email, $password);
        public function findByEmail($email);
        public function findById($id);
        public function findByToken($token);
        public function destroyToken();
        public function changePassword(User $user);
    }
?>