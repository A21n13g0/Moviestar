<?php
    require_once("models/User.php");
    require_once("models/Message.php");
    require_once("dao/UserDAO.php");
    require_once("globals.php");
    require_once("connectdb.php");

    $user = new User();
    $message = new Message($BASE_URL);
    $userDao = new UserDAO($connectdb, $BASE_URL);

    //RESGATA O TIPO DE FORMULÁRIO
    $typeForm = filter_input(INPUT_POST, "type");

    //RESGATA DADOS DO USUARIO
    $userData = $userDao->verifyToken();

    //ATUALIZA USUARIO
    if($typeForm === "update")
    {
        //RECEBE DADOS DO POST
        $name = filter_input(INPUT_POST, "name");
        $lastName = filter_input(INPUT_POST, "lastname");
        $email = filter_input(INPUT_POST, "email");
        $biography = filter_input(INPUT_POST, "biography");

        if(!empty($name) && !empty($lastName) && !empty($email))
        {
            //PREENCHE OS DADOS DO USUARIO
            $userData->name = $name;
            $userData->lastName = $lastName;
            $userData->email = $email;
            $userData->biography = $biography;

            //UPLOAD DA IMAGEM
            if(isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"]))
            {
                $imageUser = $_FILES["image"];
                $imageTypes = ["image/jpeg", "image/jpg", "image/png"];
                $extImage = strtolower(substr($imageUser["name"],-4));

                //CHECAGEM DE TIPO DE IMAGEM
                if(in_array($imageUser["type"], $imageTypes))
                {
                    //CHECA SE É JPEG/JPG
                    if($extImage == ".jpg")
                    {
                        //IMAGEM É JPG
                        $imageFile = imagecreatefromjpeg($imageUser["tmp_name"]);
                    }
                    elseif($extImage == ".jpeg")
                    {
                        //IMAGEM É JPEG
                        $imageFile = imagecreatefromjpeg($imageUser["tmp_name"]);
                    }
                    elseif($extImage == ".png")
                    {
                        //IMAGEM É PNG
                        $imageFile = imagecreatefrompng($imageUser["tmp_name"]);
                    }
                    else
                    {
                        $message->setMessage("Tipo invalido de imagem, insira imagens do tipo Jpeg, jpg ou png!", "error", "back");
                    }
                    
                    //REMOVE A FOTO DE PERFIL DO USUARIO
                    if(!empty($userData->imageUser))
                    {
                        $userData->deleteImageUser($userData->imageUser);
                    }
                    
                    $imageName = $user->generateImageName($extImage);
                    imagejpeg($imageFile, "./img/users/" . $imageName, 100);
                    $userData->imageUser = $imageName;
                }
                else
                {
                    $message->setMessage("Tipo invalido de imagem, insira imagens do tipo Jpeg, jpg ou png!", "error", "back");
                }
            }

            $userDao->updateUser($userData);
        }
        else
        {
            $message->setMessage("Os campos nome, sobrenome e e-mail não podem ficar em branco.", "error", "back");
        }
    }
    elseif($typeForm === "changepassword")
    {
        //RECEBE DADOS DO POST
        $password = filter_input(INPUT_POST, "password");
        $confirmpassword = filter_input(INPUT_POST, "confirmpassword");

        //RESGATA DADOS DO USUARIO
        $userData = $userDao->verifyToken();
        $id = $userData->id;

        if(strlen($password) < 6)
        {
            //SENHAS DEVEM POSSUIR 6 OU MAIS CARACTERES
            $message->setMessage("A senha deve possuir 6 ou mais caracteres.", "error", "back");
        }
        elseif($password != $confirmpassword)
        {
            $message->setMessage("Confirmação da senha não idêntica, tente novamente", "error", "back");
        }
        else
        {
            //ATUALIZA SENHA DO USUARIO
            $finalPassword = $user->generatePassword($password);
            $user->password = $finalPassword;
            $user->id = $id;

            $userDao->changePassword($user);
        }
    }
    else
    {
        $message->setMessage("Formulario invalido!", "error", "index.php");
    }
?>