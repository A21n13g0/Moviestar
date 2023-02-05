<?php
    require_once("models/User.php");
    require_once("models/Message.php");

    class UserDAO implements UserDAOInterface
    {
        private $connectdb;
        private $url;
        private $message;

        public function __construct(PDO $connectdb, $url)
        {
            $this->connectdb = $connectdb;
            $this->url = $url;
            $this->message = new Message($url);
        }

        public function buildUser($userData)
        {
            $user = new User();
            
            $user->id = $userData["ID_USER"];
            $user->name = $userData["NAME"];
            $user->lastName = $userData["LAST_NAME"];
            $user->email = $userData["EMAIL"];
            $user->password = $userData["PASSWORD"];
            $user->imageUser = $userData["IMAGE_USER"];
            $user->biography = $userData["BIOGRAPHY"];
            $user->token = $userData["TOKEN"];

            return $user;
        }

        public function createUser(User $user, $authenticateUser = false)
        {
            $stmt = $this->connectdb->prepare("INSERT INTO USERS(NAME, LAST_NAME, EMAIL, PASSWORD, TOKEN)
            VALUES(:name, :lastName, :email, :password, :token)");

            $stmt->bindParam(":name", $user->name);
            $stmt->bindParam(":lastName", $user->lastName);
            $stmt->bindParam(":email", $user->email);
            $stmt->bindParam(":password", $user->password);
            $stmt->bindParam(":token", $user->token);
            $stmt->execute();

            //AUTENTICA USUARIO
            if($authenticateUser)
            {
                $this->setTokenToSession($user->token);
            }
        }

        public function updateUser(User $user, $redirect = true)
        {
            $stmt = $this->connectdb->prepare("UPDATE USERS SET NAME = :name, 
            LAST_NAME = :lastName, EMAIL = :email, IMAGE_USER = :imageUser, BIOGRAPHY = :biography, TOKEN = :token 
            WHERE ID_USER = :id");

            $stmt->bindParam(":name", $user->name);
            $stmt->bindParam(":lastName", $user->lastName);
            $stmt->bindParam(":email", $user->email);
            $stmt->bindParam(":imageUser", $user->imageUser);
            $stmt->bindParam(":biography", $user->biography);
            $stmt->bindParam(":token", $user->token);
            $stmt->bindParam(":id", $user->id);
            $stmt->execute();

            if($redirect)
            {
                //REDIRECIONA PARA O PERFIL DO USUARIO
                $this->message->setMessage("Dados atualizados com sucesso!", "success", "editprofile.php");
            }
        }

        public function verifyToken($protected = false)
        {
            if(!empty($_SESSION["token"]))
            {
                //PEGA O TOKEN DA SESSION
                $token = $_SESSION["token"];

                $user = $this->findByToken($token);

                if($user)
                {
                    return $user;
                }
                elseif($protected)
                {
                    //REDIRECIONA USUARIO NÃO AUTENTICADO
                    $this->message->setMessage("Faça login para navegar pelo site!", "error", "index.php");
                }
            }
            elseif($protected)
            {
                $this->message->setMessage("Faça login para navegar pelo site!", "error", "index.php");
            }
        }

        public function setTokenToSession($token, $redirect = true)
        {
            //SALVA TOKEN NA SESSION
            $_SESSION["token"] = $token;

            if($redirect)
            {
                //REDIRECIONA PARA O PERFIL DO USUARIO
                $this->message->setMessage("Seja bem-vindo!", "success", "editprofile.php");
            }
        }

        public function authenticateUser($email, $password)
        {
            $user = $this->findByEmail($email);

            if($user)
            {
                //VERIFICA SE A SENHA ESTÁ CORRETA
                if(password_verify($password, $user->password))
                {
                    //GERA O TOKEN PARA SESSION
                    $token = $user->generateToken();
                    $this->setTokenToSession($token, false);

                    //ATUALIZAR TOKEN NO USUARIO
                    $user->token = $token;
                    $this->updateUser($user, false);
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }

        public function findByEmail($email)
        {
            if(!empty($email))
            {
                $stmt = $this->connectdb->prepare("SELECT * FROM USERS WHERE EMAIL = :email");
                $stmt->bindParam(":email", $email);
                $stmt->execute();

                if($stmt->rowCount() > 0)
                {
                    $data = $stmt->fetch();
                    $user = $this->buildUser($data);
                    return $user;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }

        public function findById($userId)
        {
            if(!empty($userId))
            {
                $stmt = $this->connectdb->prepare("SELECT * FROM USERS WHERE ID_USER = :userId");
                $stmt->bindParam(":userId", $userId);
                $stmt->execute();

                if($stmt->rowCount() > 0)
                {
                    $data = $stmt->fetch();
                    $user = $this->buildUser($data);
                    return $user;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }

        public function findByToken($token)
        {
            if(!empty($token))
            {
                $stmt = $this->connectdb->prepare("SELECT * FROM USERS WHERE TOKEN = :token");
                $stmt->bindParam(":token", $token);
                $stmt->execute();

                if($stmt->rowCount() > 0)
                {
                    $data = $stmt->fetch();
                    $user = $this->buildUser($data);
                    return $user;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }

        public function destroyToken()
        {
            //REMOVE O TOKEN DA SESSION
            $_SESSION["token"] = "";

            //REDIRECIONA USUARIO
            $this->message->setMessage("Volte Sempre!", "success", "index.php");
        }

        public function changePassword(User $user)
        {
            $stmt = $this->connectdb->prepare("UPDATE USERS SET PASSWORD = :password WHERE ID_USER = :id");
            $stmt->bindParam(":id", $user->id);
            $stmt->bindParam(":password", $user->password);
            $stmt->execute();

            $this->message->setMessage("Senha alterada com sucesso!", "success", "editprofile.php");
        }
    }
?>