<?php
session_start(); // Ouverture de la session qui se fait au début du fichier

    // La page ListeArticles est la page par default à afficher
    if(isset($_REQUEST["action"]))
    {
        $action = $_REQUEST["action"];
    }
    else
    {
        $action = "ListeArticles";
    }                                 
    //inclure le modèle
    require_once("fonctionsDB.php");

    //structure décisionnelle
    switch($action)
    {
        case "ListeArticles":
            // Obtention de la liste des articles afin de les afficher dans la ListeArticles
            $resultatAllArticles = GetAllArticles();
            require_once("vues/ListeArticles.php");            
            break; 
            
        case "AfficherArticle":
            if(isset($_GET["idArticle"]))
            {
                //Récupérer l'article par son Id
                $donneesArticle = GetArticleById($_GET["idArticle"]);
                require_once("vues/AfficherArticle.php"); 
            }
            else
            {
                header("Location: index.php"); 
            }                  
            break; 
         
        case "Login":
            //  Appelle de la page de Login
            require_once("vues/Login.php");
            break;

        // Vérification du nom de l'utilisateur et le mot de passe
        case "VerifierUsernamePasswd":
            //vérifier l'Identifiant et le mot de passe
            if(isset($_POST["username"]) && isset($_POST["passwd"]))
            {
                $resultat = Authentification($_POST["username"], $_POST["passwd"]);
                
                if($resultat)
                {
                    // Lorsque l'authentification a réussi on sauvegarde le username et le Id de l'usager
                    $_SESSION["username"] = $_POST["username"]; 
                    $_SESSION["idUsager"] = GetIdUsagerByUsername($_SESSION["username"]) ;   
                    header("Location: index.php?action=ListeArticles"); 
                }
                else
                {
                    // Si l'authentification échoue on affiche un message d'erreur
                    $msgErreur = "Identifiant ou Mot de passe invalide.";
                    require_once("vues/Login.php");
                }
            }
            else
            {
                header("Location: index.php");
            }
            break;

        case "ModifierArticle":  
            //  Avant la modification, on teste si la session est ouverte, la presence de Id Article et aussi si cet usager a les doits de modifier cet article afin de protéger 
            //  l'article de toute modification faite par copier-coller l'adresse dans la barre de navigation            
            if(isset($_SESSION["username"]) && isset($_GET["idArticle"]) && VerifierDroitModification($_GET["idArticle"]))
            {
                //Récupérer l'article par son Id
                $donneesArticle = GetArticleById($_GET["idArticle"]);
                // Récupérer la liste des mots clés et les séparés par &                  
                $sMotsClees = GetStringMotsclesByIdArticle($_GET["idArticle"], "&");
                require_once("vues/ModifierArticle.php"); 
            }
            else
            {
                header("Location: index.php"); 
            }                  
            break;

        // Mettre à jour l'article
        case "MettreAJour" :    
            // Avant de procéder à la mise à jour d'une article  on vérifie si la session est toujours ouverte et si "submit" contient "Modifier l'article"
            if(isset($_SESSION["username"]) && isset($_POST["submit"])  &&  $_POST["submit"] == "Modifier l'article")
            {   
                $msgErreur = "";  
                if(isset($_POST["titre"])  &&  $_POST["titre"] != "" && isset($_POST["texte"])  &&  $_POST["texte"] != "" && isset($_POST["idArticle"])  &&  is_numeric($_POST["idArticle"]))
                {               
                    //On procède à la mise à jour de l'article      
                    UpdateArticle($_POST["titre"], $_POST["texte"], $_POST["idArticle"] );  
                    /* Pour la mise à jour des mots clés le principe est simple on compare l'ancienne liste de mots clés avec la nouvelle, si le mot clé de l'ancienne liste se trouve 
                    dans la nouvelle liste alors on le supprime des deux listes sinon on fait rien, à la fin on supprime tous les mots clés qui se trouve dans l'ancienne 
                    liste modifiée de mots clés et on ajoute au complet la nouvelle liste modifiée de mots clés */
                    //Tester si les mots clés existent
                    if(isset($_POST["motscles"]))  
                    {    
                        // On recupère les anciens mots clés afin de les supprimer
                        $sOldMotsCles = GetStringMotsclesByIdArticle($_POST["idArticle"], "&");   // ancien ensemble de mots clés                        
                        if($_POST["motscles"] != $sOldMotsCles )     // On teste si les nouveaux mots clés sont differents des anciens mots clés s'ils sont égaux alors on fait rien
                        {  
                            $sNewMotscles = $_POST["motscles"];
                            $aNewMotscles = explode("&", $sNewMotscles); // Mettre l'ancien ensemble de mots cles dans un tableau
                            // Suppression des anciens mots clés de l'article                            
                            if ($sOldMotsCles != "")   //  Si les anciens mots clés sont différents de vide
                            {
                                $aOldMotscles = explode("&", $sOldMotsCles); // Mettre l'ancien ensemble de mots cles dans un tableau                                
 
                                // Avant la suppression on teste mot par mot si l'ancien mot appartient au nouveau ensemble de mots clés  s'il appartient on l'enleve de l'ancien et
                                // du nouveau enseble de mots clés dans le cas ou il n'appartient pas on fait rien (donc on le laisse dans l'ancien enseble de mots clés) afin 
                                //qu'il soit supprimé (car l'ancien ensemle va etre supprimé)                                 
                                
                                foreach($aOldMotscles as $sOldMotcle)     
                                {  
                                    if(in_array(strtolower($sOldMotcle), array_map("strtolower", $aNewMotscles)))
                                    { 
                                        // suppression des mots cles de deux liste afin de le conserver dans la table 
                                        $aNewMotscles = RemoveElementFromArray($sOldMotcle, $aNewMotscles);
                                        $aOldMotscles = RemoveElementFromArray($sOldMotcle, $aOldMotscles);                                            
                                    }
                                }
                                // On supprime les anciens mots clés qui ne sont pas répétés dans le nouvean ensemble de mots clés
                                $sOldMotsCles = implode("&", $aOldMotscles);
                                DeleteMotsclesFromArticle($sOldMotsCles, $_POST["idArticle"]);
                            }
                            // L'ajout des nouveaux mots clés à l'article                            
                            $sNewMotscles = implode("&", $aNewMotscles);  
                            if ($sNewMotscles != "")   //  Si les nouveaux mots sont différents du vide
                            {                                 
                                //  On ajoute les nouveaux mots clés à l'article                                
                                TreatAndInsertMotsCles($_POST["idArticle"], $sNewMotscles);
                            }
                        }  
                    }               
                    header("Location: index.php"); 
                }
                else
                {    
                    //  Tester si le titre et le texte de l'article sont vides afin d'afficher le bon message               
                    if(!isset($_POST["titre"])  ||  $_POST["titre"] == "")
                            $msgErreur = "Veuillez remplir le titre\n";
                    if(!isset($_POST["texte"])  ||   $_POST["texte"] == "")
                            $msgErreur .= "Veuillez remplir le texte";                        
                    require_once("vues/ModifierArticle.php");                    
                }   
            }
            else
            {
                header("Location: index.php");
            }                
            break;   

        // suppression d'un article

        case "SupprimerArticle" :    
            //  Avant la suppression de l'article, on teste si la session est ouverte, la presence de Id Article et aussi si cet usager a les doits de supprimer cet article afin de protéger 
            //  l'article de toute modification faite par copier-coller l'adresse dans la barre de navigation            
            if(isset($_SESSION["username"]) && isset($_GET["idArticle"]) && VerifierDroitModification($_GET["idArticle"]))
            {                 
                // Récupérer la liste des mots clés en chaine de caractères séparés par &                  
                $sMotsClees = GetStringMotsclesByIdArticle($_GET["idArticle"], "&"); 
                // On teste si la chaine n'est pas vide pour procéder à la suppression de ces mots clés
                if($sMotsClees != ""){
                    // Suppression des mots cles de la table articles_notscles et eventuellement de la table motscles
                    DeleteMotsclesFromArticle($sMotsClees, $_GET["idArticle"]);
                }
                // Suppression de l'article de la table article
                DeleteArticle($_GET["idArticle"]);
                
                // Retour a la lsite des articles (page d'accueil)
                header("Location: index.php"); 
            }
            else
            {
                // Au cas ou erreur eetour  a la page d'accueil
                header("Location: index.php"); 
            } 
 
            break;   

        //  L'ajout de l'article
        case "AjouterArticle":   
                          
            if(isset($_SESSION["username"]))
            {           
                // Si "submit" contient "Annuler l'ajout" on retourne à l'index
                if(isset($_POST["submit"]) && $_POST["submit"] == "Annuler l'ajout")
                {
                    header("Location: index.php");

                }
                else
                {                    
                    // Avant l'insertion on vérifie  le titre et le texte de l'article   
                    if(isset($_POST["titre"])  &&  $_POST["titre"] != "" && isset($_POST["texte"])  &&  $_POST["texte"] != "" )
                    {     
                        // Insertion de l'article                                     
                        InsertArticle($_POST["titre"], $_POST["texte"], $_SESSION["idUsager"] );
                        // Insertion des mots clés
                        if(isset($_POST["motscles"])  &&  $_POST["motscles"] != "")
                        {
                            $idArticle = GetNewIdArticle($_SESSION["idUsager"]);  // recuperer Id de l'article qu'on vient d'inserer                        
                            TreatAndInsertMotsCles($idArticle, $_POST["motscles"]);
                        }
                        header("Location: index.php");
                    }
                    else
                    {  
                        //  Personnalisation de message d'erreur 
                        $msgErreur = "";                   
                        if(isset($_POST["submit"]) && $_POST["submit"] == "Ajouter l'article")
                        {
                            if(!isset($_POST["titre"])  ||  $_POST["titre"] == "")
                                $msgErreur = "Veuillez remplir le titre\n";
                            if(!isset($_POST["texte"])  ||   $_POST["texte"] == "")
                                $msgErreur .= "Veuillez remplir le texte";
                        }     
                           
                        require_once("vues/AjouterArticle.php");                    
                    }                    
                }  
            }
            else
                header("Location: index.php");
            break;

        //  Affichage de la liste des mots clés    
        case "ListeMotscles" :
            //  Récupération de la liste des mots clés classés par ordre décroissant de popularité
            $resultatAllMotscles = GetAllMotsclesByRank();
            //  Remplir un tableau multidimensionnel contenant IdMotscles, mot et popularité à partir du resultat de la requete précédente.
            $arrayMotscles = [];
            while($rangeeMotscles = mysqli_fetch_assoc($resultatAllMotscles))
            {
                array_push($arrayMotscles, array($rangeeMotscles["IdMotscles"], $rangeeMotscles["mot"],$rangeeMotscles["popularite"]));
            }
            if(isset($_GET["IdMotcle"]))
            {
                $resultatArticlesByIdMotcle = GetArticleByIdMotcle($_GET["IdMotcle"]);
            }
            require_once("vues/ListeMotscles.php");
            break;

        //  Inscription d'un nouvel utilisateur
        case "NouvelleInscription":
            //  Tester le nom, prénom, username et mot de passe afin de personnaliser le message d'erreur
            $msgErreur = "";
            if(isset($_POST["submit"])  &&  $_POST["submit"] == "Créer compte")
            {
                if(!isset($_POST["prenom"])  ||  $_POST["prenom"] == "")
                    $msgErreur = "Veuillez remplir le champ prenom\n";
                if(!isset($_POST["nom"])  ||   $_POST["nom"] == "")
                    $msgErreur .= "Veuillez remplir le champ nom\n";
                if(!isset($_POST["username"])  ||   $_POST["username"] == "")
                    $msgErreur .= "Veuillez remplir le champ nom utilisateur\n";
                if(!isset($_POST["passwd1"])  ||   $_POST["passwd1"] == "")
                    $msgErreur .= "Veuillez remplir le champ mot de passe\n";
                if(!isset($_POST["passwd2"])  ||   $_POST["passwd2"] == "")
                    $msgErreur .= "Veuillez remplir le champ confirmation mot de passe\n";  
                if($msgErreur == "")
                {
                    if($_POST["passwd1"] !== $_POST["passwd2"])
                    {
                        $msgErreur = "Le mot de passe et sa confirmation ne se correspondent pas\n";
                    }
                    if(IsExistUsername( $_POST["username"]))
                    {
                        $msgErreur .= "Le nom utilisateur existe deja, choisir un autre nom utilsateur\n";
                    }
                    //  S'il n y a pas d'erreur on insère le nouvel utilisateur
                    if($msgErreur == "")
                    {
                        InsertNouvelUtilisateur($_POST["prenom"], $_POST["nom"], $_POST["username"],  $_POST["passwd1"]); 
                        // Après que l'inscription a réussi on retourne à la page connexion
                        header("Location: index.php?action=Login&username=".$_POST["username"]."&msgErreur=Compte crée avec succés");                    
                    }
                    else
                    {
                        require_once("vues/NouvelleInscription.php");
                    }
                }
                else
                {
                    require_once("vues/NouvelleInscription.php");
                }

            }
            //  on retourne à la page connexion dans le cas ou on clique sur le bouton annuler la création d'un nouvel utilisateur
            else    if(isset($_POST["submit"])  &&  $_POST["submit"] == "Annuler la creation")      
                    {
                        header("Location: index.php?action=Login");
                    }
            else            
            {
                require_once("vues/NouvelleInscription.php");
            }
            break;
        
        // Déconnexion et la fermeture de la session
        case "Logout":
            //vider le tableau $_SESSION
            $_SESSION = array(); 
            
            //supprimer le cookie de session
            if(isset($_COOKIE[session_name()]))
            {
                setcookie(session_name(), '', time() - 3600);
            }
            
            //détruire la session complètement
            session_destroy();
            header("Location: index.php");
            break;  
    }


    /*
    *
    *   GetStringMotsclesByIdArticle
    *   Paramètres : L'id de l'article dont on veut obtenir ses mots clés
    *   Retourne un string des mots clés séparer par "separator" qui peut etre soit "&" ou ","
    */
    function GetStringMotsclesByIdArticle($idArticle, $separator)
    {
        $donneesMotscles = GetMotsclesByIdArticle($idArticle);
        $sMotsClees = "";
        while($rangee = mysqli_fetch_assoc($donneesMotscles)){
            $sMotsClees .= $rangee["mot"] . $separator;
        };
        $sMotsClees = rtrim($sMotsClees, $separator);
        return $sMotsClees;
    }

    /*
    *
    *   TreatAndInsertMotsCles
    *   Paramètres : L'id de l'article dont on veut insérer les mots clés et un  string des mots clés séparer par &
    *   Retourne un tableau de mots clés qui sont ensuite insérer dans la table articles_motscles
    */
    function TreatAndInsertMotsCles($idArticle, $sMotscles)
    {
        $aMotscles = explode("&",$sMotscles);  // explose la chaine en tableau
        foreach( $aMotscles as $sMotcle)
        {
            $idMotcle = TestIfMotcleExist($sMotcle);  
            if($idMotcle == 0)  //  si le mot clé n'existe pas on l'insère
            {
                InsertNewMotcle( $sMotcle); // on insert le mot clé dans la table motscles
                $idMotcle = TestIfMotcleExist($sMotcle);  // on récupere le id du mot clé nouvellement inseré                 
            }             
            InsertInArticlesMotscles($idArticle, $idMotcle);                          
        }
    }


    /*
    *
    *   DeleteMotsclesFromArticle : consiste à supprimer un ensemble de mots clés liés à un article
    *   Paramètres : La liste des mots clés en string (séparés par &) et l'id de l'article 
    *   Retourne la suppression de tout les mots donnés de l'article
    */
    function DeleteMotsclesFromArticle($sMotscles, $idArticle)
    {
        //1.Exploser la chaine en tableau
        $aMotscles = explode("&", $sMotscles);
        //2. Pour chaque mot clé on le supprime de la table articles_motscles puis on teste s'il existe une référence de ce mot clé dans la table articles_motscles
        //s'il n'existe pas de référence on le supprime de la table motscles
        foreach( $aMotscles as $sMotcle)
        {   
            $idMotcle = TestIfMotcleExist($sMotcle);  // On obtient l'Id du mot clé
            DeleteMotcleFromArticles_motsclesTable($idMotcle, $idArticle );     // On supprime le mot clé de la table articles_motscles
            $otherIdArticle = TestIfMotcleExistInArticles_motscles($idMotcle);   //  On teste s'il existe d'autres références de ce mot clé dans la table articles_motscles
            if($otherIdArticle == 0){ // s'il n'existe pas de références de ce mot clé dans la table articles_motscles
                DeleteMotcleFromMotsclesTable($idMotcle);  //  Alors on supprime ce mot clé de la table motscles
            }                                                       
        }         
    }

    /*
    *
    *   VerifierDroitModification
    *   Paramètres : L'id de l'article dont on veut vérifier les droits
    *   Retourne un booléen (true ou false) si l'usager a les droits sur l'article on retourne true sinon false, cette
    *   fonction est utile pour la sécurité de notre site web
    */
    function VerifierDroitModification($IdArticle)
    {
        $bDroitModif = false;  
        if($_SESSION["idUsager"])
        { 
            $bDroitModif = VerifierArticleUsager($IdArticle, $_SESSION["idUsager"]);
        }  
        return $bDroitModif;
    }


    /*
    *
    *   RemoveElementFromArray
    *   Paramètres : l'élément $sElement  à supprimer du tableau $aArray
    *   Retourne un tableau sans l'élément $sElement
    */

    function RemoveElementFromArray($sElement, $aArray ){
        foreach ($aArray as $key => $value) 
        {
            if (strtolower($value) == strtolower($sElement)) {
                unset($aArray[$key]);  
            }
        }
        return $aArray;
    }


    
   
?>