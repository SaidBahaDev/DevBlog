<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
    <meta name="description" content="DevBlog" />
    <!-- stylesheets -->
    <link rel="stylesheet" href="css/reset.css" />
    <link rel="stylesheet" href="css/style.css" />    
    <!-- fonts -->
    <link href="https://fonts.googleapis.com/css?family=Josefin+Sans&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=EB+Garamond&display=swap" rel="stylesheet">
    <!-- favicon -->
    <link rel="shortcut icon" href="img/favicon.ico" />
    <title><?= $pagetitle ?></title>
</head>
    <body>
        <header>
            <div class="header-container">
                <div class="logo"><a href= "index.php">DevBlog</a></div>
                <div><a href= "index.php">Accueil</a></div>        
                <div><a href= 'index.php?action=AjouterArticle'><?= (isset($_SESSION["username"]) ? "Ajouter un article" : "" )?> </a></div>
                <div><a href='index.php?action=ListeMotscles'>Liste mots cles</a></div> 
                <div><a href='index.php?action=Logout'><?= (isset($_SESSION["username"]) ? "Se déconnecter" : "" )?></a></div>
                <div><a href=  <?= (isset($_SESSION["username"]) ? "" : "'index.php?action=Login'" )?> > <?= (isset($_SESSION["username"]) ? "Bonjour ".$_SESSION["username"] : "Se connecter" )?> </a></div>
            </div>
            <div class="entete"></div>
        </header>
