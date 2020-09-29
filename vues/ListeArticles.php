<?php 
$pagetitle= "DevBlog - Derniers Articles";
include 'include/header.php'; ?>
        <main>
            <div>
                <h3 class="titre-page">derniers articles</h3>
            </div>
            <?php 
                while($rangeeArticle = mysqli_fetch_assoc($resultatAllArticles))
                {                 
                    if(isset($_SESSION["idUsager"]))
                    {
                        $modifier = ($rangeeArticle["IdUsager"] == $_SESSION["idUsager"]) ? "<a href='index.php?action=ModifierArticle&idArticle=".$rangeeArticle["IdArticle"]."'>Modifier cet article</a>" :"";
                        $supprimer = ($rangeeArticle["IdUsager"] == $_SESSION["idUsager"]) ? "<a href='index.php?action=SupprimerArticle&idArticle=".$rangeeArticle["IdArticle"]."'>Supprimer cet article</a>" :"";
                     }
                    else{
                        $modifier = "";
                        $supprimer = "";
                    }
                    echo '<div class="structure">';
                        echo '<div class="structure-container">';   
                            echo '<div class="structure-header">';
                                echo '<div class="lettre">'. nl2br($rangeeArticle["titre"][0]) .'</div>';
                                echo "<h2>". nl2br($rangeeArticle["titre"]);  
                                $sMotsClees = GetStringMotsclesByIdArticle($rangeeArticle["IdArticle"], ", ");
                                echo "<h4>Ecrit par: ".$rangeeArticle["auteur"];     
                            echo "</div>";             
                            echo "<p>". nl2br( substr($rangeeArticle["texte"], 0, 350) )."...</p>";
                            echo "<h4> Mots clés : ".$sMotsClees."</h4>";
                            echo  "<div class='lire-plus-btn'><a href='index.php?action=AfficherArticle&idArticle=".$rangeeArticle["IdArticle"]."'>lire plus</a></div>";
                            if ($modifier) {
                               echo "<div class='modifier-btn'>" . $modifier . "</div>";
                               echo "<div class='modifier-btn'>" . $supprimer . "</div>";                                                                
                            }
                        echo "</div>";
                    echo "</div>";
                }
            ?>
        </main>
        <?php include 'include/footer.php'; ?>
 
</body>
</html>