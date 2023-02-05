<?php
    require_once("templates/header.php");
    require_once("dao/MovieDAO.php");

    $movieDAO = new MovieDAO($connectdb, $BASE_URL);
    
    $latestMovies = $movieDAO->getLatestMovies();
    $actionMovies = $movieDAO->getMoviesByCategory("Ação");
    $comedyMovies = $movieDAO->getMoviesByCategory("Comédia");
?>

<div id="main-container" class="container-fluid">
    <h2 class="section-title">Novos Filmes</h2>
    <p class="section-descrption">Vejá as criticas dos ultimos filmes adicionados</p>
    <div class="movies-container">
        <?php foreach($latestMovies as $movie): ?>
            <?php require("templates/movie_card.php"); ?>
        <?php endforeach; ?>
        <?php if(count($latestMovies) === 0): ?>
            <p class="empty-list">Ainda não há filmes cadastrados</p>
        <?php endif; ?>
    </div>
    <h2 class="section-title">Ação</h2>
    <p class="section-descrption">Vejá os melhores filmes de ação</p>
    <div class="movies-container">
        <?php foreach($actionMovies as $movie): ?>
            <?php require("templates/movie_card.php"); ?>
        <?php endforeach; ?>
        <?php if(count($actionMovies) === 0): ?>
            <p class="empty-list">Ainda não há filmes de ação cadastrados</p>
        <?php endif; ?>
    </div>
    <h2 class="section-title">Comédia</h2>
    <p class="section-descrption">Vejá os melhores filmes de comédia</p>
    <div class="movies-container">
        <?php foreach($comedyMovies as $movie): ?>
            <?php require("templates/movie_card.php"); ?>
        <?php endforeach; ?>
        <?php if(count($comedyMovies) === 0): ?>
            <p class="empty-list">Ainda não há filmes de comédia cadastrados</p>
        <?php endif; ?>
    </div>
</div>

<?php
    require_once("templates/footer.php");
?>