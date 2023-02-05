<?php
    require_once("models/Movie.php");
    require_once("models/Message.php");
    require_once("models/Review.php");
    require_once("dao/MovieDAO.php");
    require_once("dao/UserDAO.php");
    require_once("dao/ReviewDAO.php");
    require_once("globals.php");
    require_once("connectdb.php");

    $movie = new Movie();
    $message = new Message($BASE_URL);
    $userDao = new UserDAO($connectdb, $BASE_URL);
    $movieDao = new MovieDAO($connectdb, $BASE_URL);
    $reviewDao = new ReviewDao($connectdb, $BASE_URL);

    //RESGATA DADOS DO USUARIO
    $userData = $userDao->verifyToken();

    //RESGATA O TIPO DE FORMULÁRIO
    $typeForm = filter_input(INPUT_POST, "type");

    if($typeForm === "create")
    {
        //RECEBENDO DADOS DO POST
        $rating = filter_input(INPUT_POST, "rating");
        $review = filter_input(INPUT_POST, "review");
        $movieId = filter_input(INPUT_POST, "movies_id");
        $userId = $userData->id;

        if(!empty($rating) && !empty($review) && !empty($movieId))
        {
            $reviewObject = new Review();

            $movieData = $movieDao->findById($movieId);
    
            //VALIDANDO SE O FILME EXISTE
            if(!empty($movieData))
            {
                $reviewObject->rating = $rating;
                $reviewObject->review = $review;
                $reviewObject->movieId = $movieId;
                $reviewObject->userId = $userId;

                $reviewDao->createReview($reviewObject);
            }
            else
            {
                $message->setMessage("Filme não encontrado!", "error", "index.php");
            }
        }
        else
        {
            $message->setMessage("Adicione uma nota e comentario sobre o filme!", "error", "back");
        }
    }
    else
    {
        $message->setMessage("Formulario invalido!", "error", "index.php");
    }
?>