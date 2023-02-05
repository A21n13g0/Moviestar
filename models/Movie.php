<?php
    class Movie
    {
        public $id;
        public $title;
        public $description;
        public $imageMovie;
        public $trailer;
        public $category;
        public $length;
        public $userId;
        public $rating;

        public function generateImageName($extImage)
        {
            return bin2hex(random_bytes(60)) . $extImage;
        }

        public function deleteImageMovie($imageMovie)
        {
            unlink("./img/movies/" . $imageMovie);
        }
    }

    interface MovieDAOInterface
    {
        public function buildMovie($data);
        public function findAll();
        public function getLatestMovies();
        public function getMoviesByCategory($category);
        public function getMoviesByUserId($userId);
        public function findById($id);
        public function findByTitle($title);
        public function createMovie(Movie $movie);
        public function updateMovie(Movie $movie);
        public function destroyMovie($id);
    }
?>