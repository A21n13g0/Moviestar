<?php
    require_once("templates/header.php");
    require_once("dao/MovieDAO.php");
    require_once("dao/ReviewDAO.php");
    require_once("models/Movie.php");

    $user = new User();
    $userDao = new UserDAO($connectdb, $BASE_URL);
    $movieDao = new MovieDAO($connectdb, $BASE_URL);
    $reviewDao = new ReviewDao($connectdb, $BASE_URL);

    //PEGA ID DO FILME
    $idMovie = filter_input(INPUT_GET, "id");

    $movie;

    $movieDao = new MovieDAO($connectdb, $BASE_URL);

    if(empty($idMovie))
    {
        $message->setMessage("O Filme não foi encontrado!", "error", "index.php");
    }
    else
    {
        $movie = $movieDao->findById($idMovie);

        //VERIFICA SE O FILME EXISTE
        if(!$movie)
        {
            $message->setMessage("O Filme não foi encontrado!", "error", "index.php");
        }
    }

    //CHECA SE O FILME TEM IMAGEM
    if(empty($movie->imageMovie))
    {
        $movie->imageMovie = "movie_cover.jpg";
    }

    //CHECA SE O FILME É DO USUARIO
    $userOwnsMovie = false;

    if(!empty($userData))
    {
        if($userData->id === $movie->userId)
        {
            $userOwnsMovie = true;
        }

        $alreadyReviewed = $reviewDao->hasAlreadyReviewed($idMovie, $userData->id);
    }

    //RESGATA AS REVIEWS DO FILME
    $movieReview = $reviewDao->getMoviesReview($idMovie);
?>

<div id="main-container" class="container-fluid">
    <div class="row">
        <div class="offset-md-1 col-md-6 movie-container">
            <h1 class="page-title"><?= $movie->title; ?></h1>
            <p class="movie-details">
                <span>Duração: <?= $movie->length; ?></span>
                <span class="pipe"></span>
                <span>Categoria: <?= $movie->category; ?></span>
                <span class="pipe"></span>
                <span><i class="fas fa-star"></i> <?= $movie->rating; ?></span>
            </p>
            <iframe src="<?= $movie->trailer; ?>" width="650" height="480" frameborder="0" 
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            <p><?= $movie->description; ?></p>
        </div>
        <div class="col-md-4">
            <div class="movie-image-container" style="background-image: url('<?= $BASE_URL ?>img/movies/<?= $movie->imageMovie; ?>')"></div>
        </div>
        <div class="offset-md-1 col-md-10" id="reviews-container">
            <h3 id="reviews-title">Avaliações</h3>
            <!-- VERIFICA SE HABILITA AS REVIEWS PARA O USUARIO OU NÃO -->
            <?php if(!empty($userData) && !$userOwnsMovie && !$alreadyReviewed):?>
                <div class="col-md-12" id="review-form-container">
                    <h4>Envie sua avaliação</h4>
                    <p class="page-description">Preencha o formulário para enviar sua avaliação</p>
                    <form action="<?= $BASE_URL ?>review_process.php" id="review-form" method="POST">
                        <input type="hidden" name="type" value="create">
                        <input type="hidden" name="movies_id" value="<?= $movie->id; ?>">
                        <div class="form-group">
                            <label for="rating">Nota do Filme</label>
                            <select name="rating" id="rating" class="form-control">
                                <option value="">Selecione</option>
                                <option value="10">10</option>
                                <option value="9">9</option>
                                <option value="8">8</option>
                                <option value="7">7</option>
                                <option value="6">6</option>
                                <option value="5">5</option>
                                <option value="4">4</option>
                                <option value="3">3</option>
                                <option value="2">2</option>
                                <option value="1">1</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="review">Seu Comentário</label>
                            <textarea name="review" id="review" rows="3" class="form-control" placeholder="O que você achou do filme?"></textarea>
                        </div>
                        <input type="submit" class="btn card-btn" value="Enviar">
                    </form>
                </div>
            <?php endif;?>
            <!-- COMENTÁRIOS -->
            <?php foreach($movieReview as $review): ?>
                <?php require("templates\user_review.php"); ?>
            <?php endforeach; ?>
            <?php if(count($movieReview) == 0): ?>
                <p class="empty-list">Não há comentarios para este filme ainda...</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
    require_once("templates/footer.php");
?>