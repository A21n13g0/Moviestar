<?php
    class Review
    {
        public $id;
        public $rating;
        public $review;
        public $userId;
        public $movieId;
        public $user;
    }

    interface ReviewDAOInterface
    {
        public function buildReview($dataReview);
        public function createReview(Review $review);
        public function getMoviesReview($id);
        public function hasAlreadyReviewed($id, $userId);
        public function getRating($id);
    }
?>