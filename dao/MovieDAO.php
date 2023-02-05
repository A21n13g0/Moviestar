<?php
    require_once("models/Movie.php");
    require_once("models/Message.php");
    require_once("dao/ReviewDAO.php");

    class MovieDAO implements MovieDAOInterface
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

        public function buildMovie($movieData)
        {
            $movie = new Movie();

            $movie->id = $movieData["ID_MOVIE"];
            $movie->title = $movieData["TITLE"];
            $movie->description = $movieData["DESCRIPTION"];
            $movie->imageMovie = $movieData["IMAGE_MOVIE"];
            $movie->trailer = $movieData["TRAILER"];
            $movie->category = $movieData["CATEGORY"];
            $movie->length = $movieData["LENGTH"];
            $movie->userId = $movieData["FK_USER_ID"];

            //RECEBE AS RATINGS DO FILME
            $reviewDao = new ReviewDao($this->connectdb, $this->url);
            $rating = $reviewDao->getRating($movie->id);
            $movie->rating = $rating;

            return $movie;
        }

        public function findAll()
        {

        }

        public function getLatestMovies()
        {
            $movies = [];

            $stmt = $this->connectdb->query("SELECT * FROM MOVIES ORDER BY ID_MOVIE DESC LIMIT 3");
            $stmt->execute();

            if($stmt->rowCount() > 0)
            {
                $moviesArray = $stmt->fetchAll();

                foreach($moviesArray as $movie)
                {
                    $movies[] = $this->buildMovie($movie);
                }
            }

            return $movies;
        }

        public function getMoviesByCategory($category)
        {
            $movies = [];

            $stmt = $this->connectdb->prepare("SELECT * FROM MOVIES WHERE CATEGORY = :category ORDER BY ID_MOVIE DESC LIMIT 4");
            $stmt->bindParam(":category", $category);
            $stmt->execute();

            if($stmt->rowCount() > 0)
            {
                $moviesArray = $stmt->fetchAll();

                foreach($moviesArray as $movie)
                {
                    $movies[] = $this->buildMovie($movie);
                }
            }

            return $movies;
        }

        public function getMoviesByUserId($userId)
        {
            $movies = [];

            $stmt = $this->connectdb->prepare("SELECT * FROM MOVIES WHERE FK_USER_ID = :userId ORDER BY ID_MOVIE ASC");
            $stmt->bindParam(":userId", $userId);
            $stmt->execute();

            if($stmt->rowCount() > 0)
            {
                $moviesArray = $stmt->fetchAll();

                foreach($moviesArray as $movie)
                {
                    $movies[] = $this->buildMovie($movie);
                }
            }

            return $movies;
        }

        public function findById($movieId)
        {
            $movie = [];

            $stmt = $this->connectdb->prepare("SELECT * FROM MOVIES WHERE ID_MOVIE = :id");
            $stmt->bindParam(":id", $movieId);
            $stmt->execute();

            if($stmt->rowCount() > 0)
            {
                $movieData = $stmt->fetch();
                $movie = $this->buildMovie($movieData);
                return $movie;
            }
            else
            {
                return false;
            }
        }

        public function findByTitle($title)
        {
            $movies = [];

            $stmt = $this->connectdb->prepare("SELECT * FROM MOVIES WHERE TITLE LIKE :title");
            $stmt->bindValue(":title", '%' . $title . '%');
            $stmt->execute();

            if($stmt->rowCount() > 0)
            {
                $movieArray = $stmt->fetchAll();
                
                foreach($movieArray as $movie)
                {
                    $movies[] = $this->buildMovie($movie);
                }
            }
            
            return $movies;
        }

        public function createMovie(Movie $movie)
        {
            $stmt = $this->connectdb->prepare("INSERT INTO MOVIES(TITLE, DESCRIPTION, IMAGE_MOVIE, TRAILER, CATEGORY, LENGTH, FK_USER_ID) 
            VALUES(:title, :description, :imageMovie, :trailer, :category, :length, :userId)");

            $stmt->bindParam(":title", $movie->title);
            $stmt->bindParam(":description", $movie->description);
            $stmt->bindParam(":imageMovie", $movie->imageMovie);
            $stmt->bindParam(":trailer", $movie->trailer);
            $stmt->bindParam(":category", $movie->category);
            $stmt->bindParam(":length", $movie->length);
            $stmt->bindParam(":userId", $movie->userId);
            $stmt->execute();

            $this->message->setMessage("Filme adicionado com sucesso!", "success", "index.php");
        }

        public function updateMovie(Movie $movie)
        {
            $stmt = $this->connectdb->prepare("UPDATE MOVIES SET TITLE = :title, DESCRIPTION = :description, IMAGE_MOVIE = :imageMovie, 
            TRAILER = :trailer, CATEGORY = :category, LENGTH = :length WHERE ID_MOVIE = :id");

            $stmt->bindParam(":title", $movie->title);
            $stmt->bindParam(":description", $movie->description);
            $stmt->bindParam(":imageMovie", $movie->imageMovie);
            $stmt->bindParam(":trailer", $movie->trailer);
            $stmt->bindParam(":category", $movie->category);
            $stmt->bindParam(":length", $movie->length);
            $stmt->bindParam(":id", $movie->id);
            $stmt->execute();

            $this->message->setMessage("Filme atualizado com sucesso!", "success", "dashboard.php");
        }

        public function destroyMovie($movieId)
        {
            $stmt = $this->connectdb->prepare("DELETE FROM MOVIES WHERE ID_MOVIE = :id");
            $stmt->bindParam(":id", $movieId);
            $stmt->execute();

            $this->message->setMessage("Filme excluido com sucesso!", "success", "dashboard.php");
        }
    }
?>