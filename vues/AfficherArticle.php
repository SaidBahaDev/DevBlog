<?php 
$pagetitle= "DevBlog - ". $donneesArticle["titre"];
include 'include/header.php'; 
?>
        <main>
            <?php 
                             
                if(isset($_SESSION["idUsager"]))
                {
                    $modifier = ($donneesArticle["IdUsager"] == $_SESSION["idUsager"]) ? "<a href='index.php?action=ModifierArticle&idArticle=".$_GET["idArticle"]."'>Modifier cet article</a>" :"";
                    $supprimer = ($donneesArticle["IdUsager"] == $_SESSION["idUsager"]) ? "<a href='index.php?action=SupprimerArticle&idArticle=".$_GET["idArticle"]."'>Supprimer cet article</a>" :"";
                }
                else{
                    $modifier = "";
                    $supprimer = "";
                }
                echo '<div class="structure">';
                    echo '<div class="structure-container">';   
                        echo '<div class="structure-header">';
                            echo '<div class="lettre">'. nl2br($donneesArticle["titre"][0]) .'</div>';
                            echo "<h2>". nl2br($donneesArticle["titre"]) ."</h2>";
                            $sMotsClees = GetStringMotsclesByIdArticle($_GET["idArticle"], ", ");
                            echo "<h4>Ecrit par: ".$donneesArticle["auteur"]."<br>   Mots clés : ".$sMotsClees."</h4>";     
                            if ($modifier) {
                                echo "<div class='modifier-btn'>" . $modifier . "</div>";
                                echo "<div class='modifier-btn'>" . $supprimer . "</div>";
                                }  
                        echo "</div>";             
                        echo "<p>". nl2br($donneesArticle["texte"]) . "</p>";
                    echo "</div>";
                echo "</div>";
                
            ?>
        </main>
        <?php include 'include/footer.php'; ?>
    </body>
</html>