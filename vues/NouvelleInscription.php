<?php $pagetitle= "DevBlog - Inscription";
include 'include/header.php'; ?>
        <main>
            <div class="structure">
                <div class="structure-header">
                    <div class="lettre">I</div>
                    <h2> Inscription </h2>
                    <h4>veuillez remplire le formulaire d'inscription</h4>
                </div>
        <form action="index.php" method="post">
            <div>
                <label for="prenom">Prénom</label>
                <input type="text" name="prenom" id="prenom" value="<?php echo isset($_POST['prenom']) ? trim($_POST['prenom']) : "" ?>" placeholder="Prénom"><br>
            </div>
            <div>
                <label for="titre">Nom</label>
                <input type="text" name="nom" id="nom" value="<?php echo isset($_POST['nom']) ? trim($_POST['nom']) : "" ?>" placeholder="Nom"><br>
            </div>
            <div>
                <label for="username">Nom utilisateur</label>
                <input type="text" name="username" id="username" value="<?php echo isset($_POST['username']) ? trim($_POST['username']) : "" ?>" placeholder="Nom utilisateur"><br>
            </div>
            <div>
                <label for="passwd1">Mot de passe</label>
                <input type="password" name="passwd1" id="passwd1" value="" placeholder="Mot de passe"><br>
            </div>
            <div>
                <label for="passwd2">Confirmation mot de passe</label>
                <input type="password" name="passwd2" id="passwd2" value="" placeholder="Confirmation mot de passe"><br>
            </div>          
            <input  type="hidden" name="action" value="NouvelleInscription">
            <input type="submit" name="submit" value="Créer compte"  class="btn-1">         
            <input type="submit" name="submit" value="Annuler la creation" class="btn-2">    
        </form>
        <p class="erreur"> <?= isset($msgErreur) ? nl2br($msgErreur) : "" ?> </p> 
    </main> 
    <?php include 'include/footer.php'; ?>
 </body>
</html>
