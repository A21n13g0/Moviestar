<?php
    require_once("models/Review.php");
    require_once("models/Message.php");
    require_once("dao/UserDAO.php");

    class ReviewDao implements ReviewDAOInterface
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

        public function buildReview($reviewData)
        {
            $reviewObject = new Review();
            $reviewObject->id = $reviewData["ID_REVIEW"];
            $reviewObject->rating = $reviewData["RATING"];
            $reviewObject->review = $reviewData["REVIEW"];
            $reviewObject->userId = $reviewData["FK_USER_ID"];
            $reviewObject->movieId = $reviewData["FK_MOVIES_ID"];

            return $reviewObject; 
        }

        public function createReview(Review $review)
        {
            $stmt = $this->connectdb->prepare("INSERT INTO REVIEWS(RATING, REVIEW, FK_USER_ID, FK_MOVIES_ID) VALUES(:rating, :review, :userId, :movieId)");

            $stmt->bindParam(":rating", $review->rating);
            $stmt->bindParam(":review", $review->review);
            $stmt->bindParam(":userId", $review->userId);
            $stmt->bindParam(":movieId", $review->movieId);

            $stmt->execute();

            $this->message->setMessage("Critica enviada com sucesso!", "success", "index.php");
        }

        public function getMoviesReview($movieId)
        {
            $reviews = [];

            $stmt = $this->connectdb->prepare("SELECT * FROM REVIEWS WHERE FK_MOVIES_ID = :movieId");
            $stmt->bindParam(":movieId", $movieId);
            $stmt->execute();

            if($stmt->rowCount() > 0)
            {
                $reviewsData = $stmt->fetchAll();
                $userDao = new UserDao($this->connectdb, $this->url);

                foreach($reviewsData as $review)
                {
                    $reviewObject = $this->buildReview($review);

                    //PEGA OS DADOS DO USUARIO
                    $user = $userDao->findById($reviewObject->userId);

                    $reviewObject->user = $user;

                    $reviews[] = $reviewObject;
                }
            }

            return $reviews;
        }

        public function hasAlreadyReviewed($movieId, $userId)
        {
            $stmt = $this->connectdb->prepare("SELECT * FROM REVIEWS WHERE FK_USER_ID = :userId AND FK_MOVIES_ID = :movieId");
            $stmt->bindParam(":userId", $userId);
            $stmt->bindParam(":movieId", $movieId);
            $stmt->execute();

            if($stmt->rowCount() > 0)
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        public function getRating($movieId)
        {
            $stmt = $this->connectdb->prepare("SELECT * FROM REVIEWS WHERE FK_MOVIES_ID = :movieId");
            $stmt->bindParam(":movieId", $movieId);
            $stmt->execute();

            if($stmt->rowCount() > 0)
            {
                $rating = 0;
                $reviews = $stmt->fetchAll();

                foreach($reviews as $review)
                {
                    $rating += $review["RATING"];
                }

                $rating = $rating / count($reviews);
            }
            else
            {
                $rating = "Não Avaliado";
            }

            return $rating;
        }
    }
?>