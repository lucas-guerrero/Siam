<?php 
  if(!isset($_COOKIE["id"])) header("Location: Connexion.php");

  if(isset($_GET["menu"])){
    switch ($_GET["menu"]) {
      case 'liste':
        header("Location: ListePartie.php");
        break;

      case 'creer':
        header("Location: CreerPartie.php");
        break;

      case 'creerCompte':
        header("Location: Inscription.php");
        break;

      case 'compte':
        header("Location: Compte.php");
        break;

      case 'deco':
        setcookie("id", "", (time()+30*24*30));
        header("Location: Connexion.php");
        break;
    }
  }
  else header("Location: Compte.php");
?>
