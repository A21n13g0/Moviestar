<footer id="footer">
        <div id="social-container">
            <ul>
                <li>
                    <a href="#"><i class="fab fa-facebook-square"></i></a>
                </li>
                <li>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </li>
                <li>
                    <a href="#"><i class="fab fa-github"></i></a>
                </li>
            </ul>
        </div>
        <div id="footer-links-container">
            <ul>
                <li><a href="<?= $BASE_URL ?>newmovie.php" class="nav-link">Adicionar Filme</a></li>
                <?php if(empty($userData)): ?>
                    <li><a href="<?= $BASE_URL ?>authenticate.php" class="nav-link">Entrar / Cadastrar-se</a></li>
                <?php else: ?>
                    <li><a href="<?= $BASE_URL ?>dashboard.php" class="nav-link">Meus Filmes</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <p>&copy; 2023 - Angelo do Nascimento</p>
    </footer>
    <!-- CSS do Bootstrap JS / JQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" 
    integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.1/js/bootstrap.js" 
    integrity="sha512-0agUJrbt+sO/RcBuV4fyg3MGYU4ajwq2UJNEx6bX8LJW6/keJfuX+LVarFKfWSMx0m77ZyA0NtDAkHfFMcnPpQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>
</html>