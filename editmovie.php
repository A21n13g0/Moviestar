<?php
    require_once("templates/header.php");
    require_once("dao/UserDAO.php");
    require_once("dao/MovieDAO.php");
    require_once("models/User.php");

    $user = new User();
    $userDao = new UserDAO($connectdb, $BASE_URL);

    //VERIFICA SE O USUARIO ESTÁ AUTENTICADO
    $userData = $userDao->verifyToken(true);

    $movieDao = new MovieDAO($connectdb, $BASE_URL);

    $idMovie = filter_input(INPUT_GET, "id");

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
    }
?>

<div id="main-container" class="container-fluid">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-6 offset-md-1">
                <h1><?= $movie->title ?></h1>
                <p class="page-description">Altere os dados do filme no formulario abaixo</p>
                <form id= "edit-movie-form" action="<?= $BASE_URL ?>movie_process.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="type" value="update">
                    <input type="hidden" name="id" value="<?= $movie->id; ?>">
                    <div class="form-group">
                        <label for="title">Titulo</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Digite o titulo do seu filme" value="<?= $movie->title; ?>">
                    </div>
                    <div class="form-group">
                        <label for="image">imagem</label>
                        <input type="file" class="form-control-file" id="image" name="image">
                    </div>
                    <div class="form-group">
                        <label for="length">Duração</label>
                        <input type="text" class="form-control" id="length" name="length" placeholder="Digite a duração do filme" value="<?= $movie->length; ?>">
                    </div>
                    <div class="form-group">
                        <label for="category">Categoria</label>
                        <select name="category" id="category" class="form-control">
                            <option value="">Selecione</option>
                            <option value="Ação" <?= $movie->category === "Ação" ? "selected" : "" ?>>Ação</option>
                            <option value="Terror" <?= $movie->category === "Terror" ? "selected" : "" ?>>Terror</option>
                            <option value="Ficção cientifica" <?= $movie->category === "Ficção cientifica" ? "selected" : "" ?>>Ficção cientifica</option>
                            <option value="Aventura" <?= $movie->category === "Aventura" ? "selected" : "" ?>>Aventura</option>
                            <option value="Comédia" <?= $movie->category === "Comédia" ? "selected" : "" ?>>Comédia</option>
                            <option value="Drama" <?= $movie->category === "Drama" ? "selected" : "" ?>>Drama</option>
                            <option value="Romance" <?= $movie->category === "Romance" ? "selected" : "" ?>>Romance</option>
                            <option value="Musical" <?= $movie->category === "Musical" ? "selected" : "" ?>>Musical</option>
                            <option value="Fantasia" <?= $movie->category === "Fantasia" ? "selected" : "" ?>>Fantasia</option>
                            <option value="Biográfico" <?= $movie->category === "Biográfico" ? "selected" : "" ?>>Biográfico</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="trailer">Trailer</label>
                        <input type="text" class="form-control" id="trailer" name="trailer" placeholder="Insira o link do trailer" value="<?= $movie->trailer; ?>">
                    </div>
                    <div class="form-group">
                        <label for="description">Descrição</label>
                        <textarea name="description" id="description" rows="5" class="form-control" placeholder="Descreva o filme..."><?= $movie->description; ?></textarea>
                    </div>
                    <input type="submit" class="btn card-btn" value="Editar Filme">
                </form>
            </div>
            <div class="col-md-3">
                <div class="movie-image-container" style="background-image: url('<?= $BASE_URL ?>img/movies/<?= $movie->imageMovie; ?>')"></div>
            </div>
        </div>
    </div>
</div>

<?php
    require_once("templates/footer.php");
?>