<?php

include("bdd.php");

$value = NULL;

if(isset($_POST["identifiant"]) && isset($_POST["MDP"]) && isset($_POST["verifMDP"])){
  $bd = connectBD("Siam");
  if($_POST["MDP"] == $_POST["verifMDP"]){
    if($bd != NULL){
      $mdp = hash("md5", $_POST["MDP"]);
      $requete = 'INSERT Into Joueur(identifiant, motDePasse, isAdmin) Values("'.$_POST["identifiant"].'", "'.$mdp.'", "False")';
      modifieTable($bd, $requete);
      $value = "success";
    }
    else $value = "bd";
  }
  else $value = "pass";
}
?>

<!DOCTYPE html>
<html lang="fr" dir="ltr">
  <?php include("header.php"); ?>
  <body>
    <p style="color:green;"><?php if($value == "success") echo "Le compte a été créé !!"; ?></p>
    <h1>Création d'un compte</h1>
    <center>
      <form action="Inscription.php" method="post">
        <fieldset>
          <legend>Création d'un compte</legend>
          <label>identfiant: </label><input type="text" name="identifiant"><br>
          <label>mot de passe: </label><input type="password" name="MDP"><br>
          <label>confirmer: </label><input type="password" name="verifMDP"> <label style="color:red"><?php if($value=="pass") echo "Non valide !"; ?></label><br>
        </fieldset>
        <p>
          <input type="submit" value="Valider">
          <br>
          <a href="javascript:history.go(-1)">Retour</a>
        </p>
      </form>
    </center>
  </body>
</html>
