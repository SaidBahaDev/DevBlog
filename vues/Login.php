<?php 
    $pagetitle= "DevBlog - Se Connecter";
    include 'include/header.php'; 
?>
        <main>
            <div class="structure">
                <div class="structure-header">
                    <div class="lettre">S</div>
                    <h2> Se connecter </h2>
                    <h4>veuillez entrer votre nom d'utilisateur et mot de passe</h4>
                </div>
                <form action="index.php" method="post" class="login">
                    <label>Identifiant :</label><input type="text" name="username" placeholder="Nom utilisateur" value="<?= isset($_GET["username"])? $_GET["username"]:""?>"/><br>
                    <label>Mot de passe :</label><input type="password" name="passwd" placeholder="Mot de passe"/><br>
                    <input type="hidden" name="action" value="VerifierUsernamePasswd"/>
                    <input type="submit" value="Se connecter" class="btn-1"/>
                    <div class="signin"> <a href="index.php?action=NouvelleInscription">Nouvel utilisateur</a></div>
                </form>
                <p class="erreur"> <?= isset($msgErreur) ? nl2br($msgErreur) : "" ?> </p> 
             </div>  
        </main>
        <?php include 'include/footer.php'; ?>
    </body>
</html>