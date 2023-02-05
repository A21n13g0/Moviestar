<?php
    require_once("templates/header.php");
    require_once("dao/MovieDAO.php");

    $movieDAO = new MovieDAO($connectdb, $BASE_URL);
    
    //RESGATA CONSULTA DO USUARIO
    $query = filter_input(INPUT_GET, "query");

    $movies = $movieDAO->findByTitle($query);
?>

<div id="main-container" class="container-fluid">
    <h2 class="section-title" id="search-title">você está buscando por: <span id="search-result"><?= $query ?></span></h2>
    <p class="section-descrption">Resultados da consulta com base na sua pesquisa: </p>
    <div class="movies-container">
        <?php foreach($movies as $movie): ?>
            <?php require("templates/movie_card.php"); ?>
        <?php endforeach; ?>
        <?php if(count($movies) === 0): ?>
            <p class="empty-list">Nenhum resultado encontrado com base na sua pesquisa! <a href="<?= $BASE_URL ?>" class="back-link">Voltar</a></p>
        <?php endif; ?>
    </div>
</div>

<?php
    require_once("templates/footer.php");
?>