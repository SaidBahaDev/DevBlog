<?php $pagetitle= "DevBlog - Modifier l'article";
include 'include/header.php'; ?>
           <main>
            <div class="structure">
                <div class="structure-header">
                    <div class="lettre">M</div>
                    <h2> Modifier l'article </h2>
                    <h4>veuillez remplire le formulaire de modification d'article</h4>
                </div>
                <form action="index.php" method="post">
                    <div>
                        <label for="titre">Titre de l'article</label>
                        <input type="text" name="titre" id="titre" value="<?php echo isset($_POST['titre']) ? trim($_POST['titre']) : $donneesArticle["titre"] ?>" placeholder="Titre de l'article"   ><br>
                    </div>
                    <div>
                        <label for="texte">Texte de l'article</label> : <br>
                        <textarea name="texte" id="texte" cols="150" rows="15" placeholder="Texte de l'article"><?php echo isset($_POST['texte']) ? trim($_POST['texte']) : $donneesArticle["texte"] ?></textarea>
                    </div>
                    <div>
                        <label for="motscles">Mots clés de l'article</label>
                        <input type="text" name="motscles" id="motscles" value = "<?php echo isset($_POST['motscles']) ? trim($_POST['motscles']) : $sMotsClees ?>"placeholder="Mots clés de l'article" ><br>
                    </div>
                    <input id="AjouterArticle" type="hidden" name="action" value="AjouterArticle">

                    <input   type="hidden" name="action" value="MettreAJour">
                    <input   type="hidden" name="idArticle" value="<?= $donneesArticle["Id"] ?>">
                    <input type="submit" name="submit" value="Modifier l'article" class="btn-1">         
                    <input type="submit" name="submit" value="Annuler la modif" class="btn-2" >    
                </form>
                <p> <?= isset($msgErreur) ? nl2br($msgErreur) : "" ?> </p> 
            </div>
    </main>
    <?php include 'include/footer.php'; ?>
 </body>
</html>
