<?php
    require_once("models/User.php");
    require_once("models/Message.php");
    require_once("dao/UserDAO.php");
    require_once("globals.php");
    require_once("connectdb.php");

    $message = new Message($BASE_URL);
    $userDao = new UserDAO($connectdb, $BASE_URL);

    //RESGATA O TIPO DE FORMULÁRIO
    $typeForm = filter_input(INPUT_POST, "type");

    //VERIFICAÇÃO DO TIPO DE FORMULÁRIO
    if ($typeForm === "register")
    {
        $name = filter_input(INPUT_POST, "name");
        $lastName = filter_input(INPUT_POST, "lastname");
        $email = filter_input(INPUT_POST, "email");
        $password = filter_input(INPUT_POST, "password");
        $confirmPassword = filter_input(INPUT_POST, "confirmpassword");

        //VERIFICAÇÃO DE DADOS
        if (!empty($name) && !empty($lastName) && !empty($email) && !empty($password) && !empty($confirmPassword))
        {
            if(strlen($password) < 6)
            {
                //SENHAS DEVEM POSSUIR 6 OU MAIS CARACTERES
                $message->setMessage("A senha deve possuir 6 ou mais caracteres.", "error", "back");
            }
            elseif($password != $confirmPassword)
            {
                //SENHAS NÃO SÃO IDÊNTICAS
                $message->setMessage("Confirmação da senha não idêntica, tente novamente.", "error", "back");
            }
            else
            {
                //VARIFICA SE O E-MAIL JÁ ESTÁ CADASTRADO
                if($userDao->findByEmail($email) === false)
                {
                    $user = new User();

                    //CRIAÇÃO DE TOKEN E SENHA
                    $userToken = $user->generateToken();
                    $finalPassword = $user->generatePassword($password);

                    $user->name = $name;
                    $user->lastName = $lastName;
                    $user->email = $email;
                    $user->password = $finalPassword;
                    $user->token = $userToken;

                    $authenticate = true;

                    $userDao->createUser($user, $authenticate);
                }
                else
                {
                    //E-MAIL JÁ ESTÁ CADASTRADO
                    $message->setMessage("E-mail já cadastrado.", "error", "back");
                }
            }
        }
        else
        {
            //DADOS FALTANTES
            $message->setMessage("Por favor, preencha todos os campos.", "error", "back");
        }
    }
    elseif($typeForm === "login")
    {
        $email = filter_input(INPUT_POST, "email");
        $password = filter_input(INPUT_POST, "password");

        //AUTENTICA USUARIO
        if($userDao->authenticateUser($email, $password))
        {
            $message->setMessage("Seja bem-vindo!", "success", "editprofile.php");
        }
        else
        {
            //REDIRECIONA O USUARIO, CASO NÃO SEJA AUTENTICADO
            $message->setMessage("Usuário e/ou senha incorretos.", "error", "back");
        }
    }
    else
    {
        $message->setMessage("Formulario invalido!", "error", "index.php");
    }
?>