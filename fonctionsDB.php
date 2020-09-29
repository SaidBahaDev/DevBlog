<?php
              

    //  Fonction de connexion
    function connectDB()
    {
        $c = mysqli_connect("localhost", "admin_devblog", "startxZA1608+", "admin_devblog");      //  connexion au serveur local
        //$c = mysqli_connect("localhost", "e1795681", "ah4fj6yBawBerUkdDg4C", "e1795681");         // Connexion au serveur webdev
        
        if(!$c)
            trigger_error("Erreur de connexion... " . mysqli_connect_error());
        
        mysqli_query($c, "SET NAMES 'utf8'");
        return $c;
    }

    $connexion = connectDB();      //   connexion

   /*
    *
    *   Authentification
    *   Paramètres : username et pass
    *   Cette fonction cherche le nom utilisateur et le mot de passe dans la table usager et retourne true si elle les trouve et false dans le cas contraire
    */

    function Authentification($username, $pass)
    {
        global $connexion;
        
        $requete = "SELECT passwd FROM usager WHERE username = '" . filtre($username) . "'";   // recupérer le hash du mot de pass qui correspond au nom utilisateur "username"
    
        $resultat = mysqli_query($connexion, $requete);
    
        if($rangee = mysqli_fetch_assoc($resultat))
        {
            if(password_verify($pass, $rangee["passwd"]))    // comparer le hash du mot de passe de la base de données avec celui saisi par l'utilisateur            
                return true;
            else
                return false;
        }
        else
            return false;
    }
   

   /*
    *
    *   GetAllArticles
    *   Paramètres : sans paramètre
    *   Retourne tous les articles ainsi que le nom de l'auteur
    */

    function GetAllArticles()
    {
        global $connexion;
        //  Récupérer Tous les articles
        // On Récupére les infos de l'article et le nom de l'auteur via une jointure de la table Article avec la table Usager
        $requete = "SELECT article.Id as IdArticle, titre, texte,IdUsager, CONCAT(prenom,' ',nom) as auteur  FROM article 
                    JOIN usager ON article.IdUsager = usager.Id                          
                    ORDER BY IdArticle DESC";
        $resultat = mysqli_query($connexion, $requete);
        return $resultat;
    }


   /*
    *
    *   GetArticleById
    *   Paramètres : id de l'article dont on veut récupérer les infos
    *   Retourne les infos de l'articles dont on a spécifié l' Id
    */

    function GetArticleById($id)
    {
        global $connexion;

        $requete = "SELECT article.Id , titre, texte,IdUsager, CONCAT(prenom,' ',nom) as auteur FROM article 
                    JOIN usager ON article.IdUsager = usager.Id
                    WHERE article.Id =".filtre($id) ;
        $resultat = mysqli_query($connexion, $requete);
        //  On récupère les infos de l'article
        $article = mysqli_fetch_assoc($resultat);      
         // On retourne les infos de l'article
        return $article;
    }

   /*
    *
    *   GetArticleByIdMotcle
    *   Paramètres : id du mot clé
    *   Retourne tous les articles qui contiennent IdMotsCles ainsi que les infos correspondantes
    */
    function GetArticleByIdMotcle($idMotcle){
        global $connexion;
        $requete = "SELECT article.Id as IdArticle, titre, texte,IdUsager, CONCAT(prenom,' ',nom) as auteur  FROM article 
                    JOIN usager ON article.IdUsager = usager.Id
                    JOIN articles_motscles ON  article.Id = articles_motscles.IdArticle 
                    Where articles_motscles.IdMotsCles  = ".filtre($idMotcle)."                 
                    ORDER BY IdArticle DESC";
        $resultat = mysqli_query($connexion, $requete);
        return $resultat;
    }


    /*
    *
    *   GetAllMotscles
    *   Paramètres : sans paramètre
    *   Retourne tous les mots clés
    */
    function GetAllMotscles()
    {
        global $connexion;
        /* On retourne tous les mots clés avec les infos "mot" et "idArticle" de l'article dont il se trouve 
        et ceci avec une jointure de la table motscles et la table articles_motscles*/
        $requete = "SELECT IdArticle, mot FROM motscles 
                    JOIN articles_motscles on articles_motscles.IdMotscles = motscles.Id ";

        $resultat = mysqli_query($connexion, $requete);
        return $resultat;           
    }

   /*
    *
    *   GetMotsclesByIdArticle
    *   Paramètres : id de l'article
    *   Retourne tous les mots clés d'un article
    */
    function GetMotsclesByIdArticle($idArticle)
    {
        global $connexion;
        // Sélection de tous les mots d'un article donné via une jointure de la table motscles et la table articles_motscles
        $requete = "SELECT mot FROM articles_motscles  
                    JOIN motscles on articles_motscles.IdMotscles = motscles.Id 
                    WHERE articles_motscles.IdArticle = ".filtre($idArticle);  

        $resultat = mysqli_query($connexion, $requete);
        return $resultat;           
    }

    /*
    *
    *   TestIfMotcleExist
    *   Paramètres : mot clé
    *   Retourne 0 si le mot clé n'existe pas et retourne l'id du mot clé si le mot clé existe dans la table motscles
    */
    function TestIfMotcleExist($sMotcle)
    {
        global $connexion;
        $id = 0;    //  si le mot n'existe pas id =0
        $requete = "SELECT Id FROM motscles WHERE mot='".filtre($sMotcle)."'"; 
        $resultat = mysqli_query($connexion, $requete);      

        if($rangee = mysqli_fetch_assoc($resultat))
        {            
            $id = $rangee["Id"];  // id different de 0 si le mot clé existe dans la table motscles
        } 
        return $id;
    }

    /*
    *
    *   TestIfMotcleExistInArticles_motscles
    *   Paramètres : mot clé
    *   Retourne 0 si le mot clé n'existe pas et retourne l'id du mot clé si le mot clé existe dans la table articles_motscles
    */
    function TestIfMotcleExistInArticles_motscles($idMotcle)
    {
        global $connexion;
        $id = 0;    //  si le mot n'existe pas id =0
        $requete = "SELECT IdArticle FROM articles_motscles WHERE IdMotscles=".filtre($idMotcle); 
        $resultat = mysqli_query($connexion, $requete);         

        if($rangee = mysqli_fetch_assoc($resultat))
        {            
            $id = $rangee["IdArticle"];  // id different de 0 si le mot clé existe dans la table motscles
        } 
        return $id;
    }


   /*
    *
    *   InsertNewMotcle
    *   Paramètres : mot clé
    *   Retourne true si l'insertion a été faite avec succés et false si l'insertion échoue
    */
    function InsertNewMotcle($smotcle)
    {
        global $connexion;
         
        $requete = "INSERT INTO motscles(mot) VALUES('".filtre($smotcle)."')"; //   Insertion du mot clé "motcle" dans la table motscles
        $resultat = mysqli_query($connexion, $requete);         
        return $resultat;
    }


   /*
    *
    *   DeleteMotcleFromArticles_motsclesTable
    *   Paramètres : Id du mot clé, id de l'article
    *   Retourne true si la suppression de l'enregistrement de la table articles_motscles a été faite avec succés et false si la suppression échoue
    */
    function DeleteMotcleFromArticles_motsclesTable($idMotcle, $idArticle )
    {
        global $connexion;
         
        $requete = "DELETE FROM articles_motscles WHERE IdMotscles='".filtre($idMotcle)."' AND IdArticle='".filtre($idArticle)."'";
        $resultat = mysqli_query($connexion, $requete);         
        return $resultat;
    }

    /*
    *
    *   DeleteMotcleFromMotsclesTable
    *   Paramètres : Id du mot clé à supprimer
    *   Retourne true si la suppression du mot clé de la table motscles a été faite avec succés et false si la suppression échoue
    */
    function DeleteMotcleFromMotsclesTable($idMotcle)
        {
            global $connexion;
             
            $requete = "DELETE FROM motscles WHERE Id=".filtre($idMotcle);
            $resultat = mysqli_query($connexion, $requete);    
            return $resultat;
        }


    /*
    *
    *   GetIdUsagerByUsername
    *   Paramètres : username
    *   Retourne  id de l'usager si son username se trouve dans la table "usager"
    */
    function GetIdUsagerByUsername($username)
    {
        global $connexion;
        $requete = "SELECT Id FROM usager WHERE username = '".filtre($username)."' ";    // retourne id de l'usager
        $resultat = mysqli_query($connexion, $requete);  
        if($rangee = mysqli_fetch_assoc($resultat))
        {            
            $id = $rangee["Id"]; 
        }   
        return $id;  // Retourner id
    }


   /*
    *
    *   InsertArticle
    *   Paramètres : titre, texte, idUsager
    *   Retourne  true si l'article a été bien ajouté et false dans le cas contraire 
    */
    function InsertArticle($titre, $texte, $idUsager )
    {   
        global $connexion;
        // Insertion de l'article
        $requete = "INSERT INTO article(titre, texte, IdUsager) VALUES ('".filtre($titre)."','".filtre($texte)."','".filtre($idUsager)."')";
        $resultat = mysqli_query($connexion, $requete);
        return $resultat;
    }


   /*
    *
    *   UpdateArticle
    *   Paramètres : titre, texte, idArticle
    *   Retourne  true si l'article a été bien mis à jour et false dans le cas contraire 
    */
    function UpdateArticle($titre, $texte, $idArticle) 
    {
        global $connexion;
        $requete = "UPDATE article SET titre='".filtre($titre)."', texte='".filtre($texte)."' WHERE Id = ".filtre($idArticle);  // mise à jour de l'article
        $resultat = mysqli_query($connexion, $requete);  
        return $resultat;
    };

    /*
    *
    *   DeleteArticle
    *   Paramètres :  idArticle a upprimer
    *   Retourne  true si l'article a été bien supprimer et false dans le cas contraire 
    */
    function DeleteArticle($idArticle) 
    {
        global $connexion;
        $requete = "DELETE FROM article  WHERE Id = ".filtre($idArticle);  // Suppression d'un article
        $resultat = mysqli_query($connexion, $requete);  
        echo $requete;
        return $resultat;
    };

 
    /*
    *
    *   GetNewIdArticle
    *   Paramètres : idUsager
    *   Retourne  l'id du dernier article ajouté par l'usager qui a la session ouverte 
    */
    function GetNewIdArticle($idUsager)
    {
        global $connexion;
        $requete = "SELECT Id FROM article 
                    WHERE idUsager= ".filtre($idUsager).
                    " ORDER BY id DESC limit 1 ";            // récuperer id du dernier article
        $resultat = mysqli_query($connexion, $requete);          
        if($rangee = mysqli_fetch_assoc($resultat))
        {            
            $id = $rangee["Id"]; 
        }   
        return $id;           
    }

   /*
    *
    *   InsertInArticlesMotscles
    *   Paramètres : idArticle, $idMotcle
    *   Retourne  true si l'article a été bien ajouté et false dans le cas contraire 
    */
    function InsertInArticlesMotscles($idArticle, $idMotcle)
    {
        global $connexion;
        $requete = "INSERT INTO articles_motscles(IdArticle, IdMotscles) VALUES ($idArticle, $idMotcle)";            // insertion du mot clé
        $resultat = mysqli_query($connexion, $requete);    
        return $resultat;
    }

    /*
    *
    *   GetAllMotsclesByRank
    *   Paramètres : sans paramètre
    *   Retourne  tous les mots clés classés par popularité 
    */
    function GetAllMotsclesByRank(){
        global $connexion;
        $requete = "SELECT IdMotscles, mot, COUNT(IdArticle) as popularite FROM articles_motscles
                    JOIN motscles ON articles_motscles.IdMotscles=motscles.Id
                    GROUP BY IdMotscles
                    ORDER BY popularite DESC";
        $resultat = mysqli_query($connexion, $requete);
        return $resultat;
    }


    /*
    *
    *    VerifierArticleUsager
    *   Paramètres : IdArticle, IdUsager
    *   Retourne  true si Idusager est l'auteur de l'article et false dans le cas contraire
    */
    function VerifierArticleUsager($IdArticle, $IdUsager){

        global $connexion;
        $verif = false; // l'usager n'est pas prprio de l'article
        $requete = "SELECT * FROM article WHERE Id=".filtre($IdArticle)." AND  IdUsager=".filtre($IdUsager);
        $resultat = mysqli_query($connexion,$requete);
        if($rangee = mysqli_fetch_assoc($resultat)){
            $verif = true; // l'usager est  prprio de l'article
        }  
        return $verif;
    }


    /*
    *
    *    IsExistUsername
    *   Paramètres : username
    *   Retourne  true si username existe dans la table Usager et false dans le cas contraire
    */
    function  IsExistUsername($username){
        
        global $connexion;
        $verif = false;// username n'existe pas dans la table Usager
        $requete = "SELECT * FROM usager WHERE username='".filtre($username)."'" ;
        $resultat = mysqli_query($connexion,$requete);
        if($rangee = mysqli_fetch_assoc($resultat)){
            $verif = true; //  username existe dans la table Usager
        }  
        return $verif;
    }

    /*
    *
    *    InsertNouvelUtilisateur
    *   Paramètres : sans paramètre
    *   Retourne  true si l'article a été bien ajouté et false dans le cas contraire 
    */

    function InsertNouvelUtilisateur($prenom, $nom, $username,  $passwd){
        
        global $connexion;
        $passwd = password_hash($passwd, PASSWORD_DEFAULT);     //  convertir le mot de passe en mot de passe hashé
        $requete = "INSERT INTO usager(prenom, nom, username, passwd) 
                    VALUE('".filtre($prenom)."', '".filtre($nom)."', '".filtre($username)."' , '".filtre($passwd)."' )"; // insertion du nouvelle utilisateur
        $resultat = mysqli_query($connexion, $requete);   
        return $resultat;
    }
    
    /*
    *
    *   filtre
    *   Paramètres : var
    *   Permet de filtrer le code entré par l'usager afin de se prévenir contre les attaqued XSS et retourne varFiltre
    */
    function filtre($var)
    {
        global $connexion;
        
        $varFiltre = mysqli_real_escape_string($connexion, $var);
        //appliquer d'autres filtres
        //se prémunir contre les attaques de type XSS (cross-site scripting)
        $varFiltre = strip_tags($varFiltre, "<a><b><em>");
        
        return $varFiltre;
    }

?>