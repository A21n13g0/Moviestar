<?php
    require_once("models/Movie.php");
    require_once("models/Message.php");
    require_once("dao/MovieDAO.php");
    require_once("dao/UserDAO.php");
    require_once("globals.php");
    require_once("connectdb.php");

    $movie = new Movie();
    $message = new Message($BASE_URL);
    $userDao = new UserDAO($connectdb, $BASE_URL);
    $movieDao = new MovieDAO($connectdb, $BASE_URL);

    //RESGATA O TIPO DE FORMULÁRIO
    $typeForm = filter_input(INPUT_POST, "type");

    //RESGATA DADOS DO USUARIO
    $userData = $userDao->verifyToken();

    if($typeForm === "create")
    {
        //RECEBE DADOS DO POST
        $title = filter_input(INPUT_POST, "title");
        $length = filter_input(INPUT_POST, "length");
        $category = filter_input(INPUT_POST, "category");
        $trailer = filter_input(INPUT_POST, "trailer");
        $description = filter_input(INPUT_POST, "description");
        $imageMovie = filter_input(INPUT_POST, "image");

        //VALIDAÇÃO MINIMA DOS DADOS
        if(!empty($title) && !empty($length) && !empty($category) && !empty($trailer) && !empty($description) && !empty($userData->id))
        {
            $movie->title = $title;
            $movie->length = $length;
            $movie->category = $category;
            $movie->trailer = $trailer;
            $movie->description = $description;
            $movie->userId = $userData->id;

            //UPLOAD DA IMAGEM
            if(isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"]))
            {
                $imageMovie = $_FILES["image"];
                $imageTypes = ["image/jpeg", "image/jpg", "image/png"];
                $extImage = strtolower(substr($imageMovie["name"],-4));

                //CHECAGEM DE TIPO DE IMAGEM
                if(in_array($imageMovie["type"], $imageTypes))
                {
                    //CHECA SE É JPEG/JPG
                    if($extImage == ".jpg")
                    {
                        //IMAGEM É JPG
                        $imageFile = imagecreatefromjpeg($imageMovie["tmp_name"]);
                    }
                    elseif($extImage == ".jpeg")
                    {
                        //IMAGEM É JPEG
                        $imageFile = imagecreatefromjpeg($imageMovie["tmp_name"]);
                    }
                    elseif($extImage == ".png")
                    {
                        //IMAGEM É PNG
                        $imageFile = imagecreatefrompng($imageMovie["tmp_name"]);
                    }
                    else
                    {
                        $message->setMessage("Tipo invalido de imagem, insira imagens do tipo Jpeg, jpg ou png!", "error", "back");
                    }

                    if(!empty($movie->imageMovie))
                    {
                        $movie->deleteImageMovie($movie->imageMovie);
                    }

                    $imageName = $movie->generateImageName($extImage);
                    imagejpeg($imageFile, "./img/movies/" . $imageName, 100);
                    $movie->imageMovie = $imageName;   
                }
            }
            
            $movieDao->createMovie($movie);
        }
        else
        {
            $message->setMessage("Preencha todos os campos para incluir o filme!", "error", "back");
        }
    }
    elseif($typeForm === "update")
    {
        //RECEBE DADOS DO POST
        $title = filter_input(INPUT_POST, "title");
        $length = filter_input(INPUT_POST, "length");
        $category = filter_input(INPUT_POST, "category");
        $trailer = filter_input(INPUT_POST, "trailer");
        $description = filter_input(INPUT_POST, "description");
        $imageMovie = filter_input(INPUT_POST, "image");
        $id = filter_input(INPUT_POST, "id");

        $movieData = $movieDao->findById($id);

        //VERIFICA SE ENCONTROU O FILME
        if($movieData)
        {
            //VERIFICA SE O FILME É DO USUARIO
            if($movieData->userId === $userData->id)
            {
                //VALIDAÇÃO MINIMA DOS DADOS
                if(!empty($title) && !empty($length) && !empty($category) && !empty($trailer) && !empty($description) && !empty($userData->id))
                {
                    $movieData->title = $title;
                    $movieData->length = $length;
                    $movieData->category = $category;
                    $movieData->trailer = $trailer;
                    $movieData->description = $description;

                    //UPLOAD DA IMAGEM
                    if(isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"]))
                    {
                        $imageMovie = $_FILES["image"];
                        $imageTypes = ["image/jpeg", "image/jpg", "image/png"];
                        $extImage = strtolower(substr($imageMovie["name"],-4));

                        //CHECAGEM DE TIPO DE IMAGEM
                        if(in_array($imageMovie["type"], $imageTypes))
                        {
                            //CHECA SE É JPEG/JPG
                            if($extImage == ".jpg")
                            {
                                //IMAGEM É JPG
                                $imageFile = imagecreatefromjpeg($imageMovie["tmp_name"]);
                            }
                            elseif($extImage == ".jpeg")
                            {
                                //IMAGEM É JPEG
                                $imageFile = imagecreatefromjpeg($imageMovie["tmp_name"]);
                            }
                            elseif($extImage == ".png")
                            {
                                //IMAGEM É PNG
                                $imageFile = imagecreatefrompng($imageMovie["tmp_name"]);
                            }
                            else
                            {
                                $message->setMessage("Tipo invalido de imagem, insira imagens do tipo Jpeg, jpg ou png!", "error", "back");
                            }

                            if(!empty($movieData->imageMovie))
                            {
                                $movieData->deleteImageMovie($movieData->imageMovie);
                            }

                            $imageName = $movieData->generateImageName($extImage);
                            imagejpeg($imageFile, "./img/movies/" . $imageName, 100);
                            $movieData->imageMovie = $imageName;   
                        }
                    }
                    
                    $movieDao->updateMovie($movieData);
                }
                else
                {
                    $message->setMessage("Preencha todos os campos para incluir o filme!", "error", "back");
                }
            }
            else
            {
                $message->setMessage("Filme não pertence ao seu usuario, Formulario invalido!", "error", "index.php");
            }
        }
    }
    elseif($typeForm === "delete")
    {
        //RECEBE DADOS DO FORMULARIO
        $id = filter_input(INPUT_POST, "id");

        $movie = $movieDao->findById($id);

        if($movie)
        {
            //VERIFICA SE O FILME É DO USUARIO
            if($movie->userId === $userData->id)
            {
                //REMOVE A IMAGEM DO FILME
                if(!empty($movie->imageMovie))
                {
                    $movie->deleteImageMovie($movie->imageMovie);
                }

                $movieDao->destroyMovie($movie->id);
            }
            else
            {
                $message->setMessage("Filme não pertence ao seu usuario, Formulario invalido!", "error", "index.php");
            }
        }
        else
        {
            $message->setMessage("Erro ao excluir, Formulario invalido!", "error", "index.php");
        }
    }
    else
    {
        $message->setMessage("Formulario invalido!", "error", "index.php");
    }
?>