<?php $pagetitle= "DevBlog - Liste Mots Cles";
include 'include/header.php'; ?>
        <main>
            <div class="structure">
                <div class="structure-header">
                    <div class="lettre">I</div>
                    <h2> Liste Mots Cles </h2>
                </div>
        <?php
            $compteur = 0;
            echo"<table> <tr> ";
            foreach( $arrayMotscles as $arrayMotcle)
            {      
                echo "<td> <a href='index.php?action=ListeMotscles&IdMotcle=".$arrayMotcle[0]."'>" .$arrayMotcle[1]." (".$arrayMotcle[2].")</a></td>";
                $compteur ++ ;              
                if ($compteur % 6 == 0) //   6 mots clés par ligne
                {
                    echo " </tr> <tr>";
                }                                                   
            }                  
            echo "</tr></table>";

            if(isset($resultatArticlesByIdMotcle))
            {
                while($rangeeArticle = mysqli_fetch_assoc($resultatArticlesByIdMotcle))
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
            }
        ?>
    </main>
    <?php include 'include/footer.php'; ?>
    </body>
</html>