<?php
include("bdd.php");

if(!isset($_SESSION["id"])) header("Location: Connexion.php");

$isAdmin = False;

$bd = connectBD("Siam");
if(isAdmin($bd, $_SESSION["id"])){
    $isAdmin = True;
}

?>

<!DOCTYPE html>
<html lang="fr">
    <?php include("header.php"); ?>
<body>
    <?php if($isAdmin) include("GestionMenu/MenuHtmlAdmin.php"); else include("GestionMenu/MenuHtmlUtilisateur.php"); ?>
    <h1>Liste partie</h1>
    <div>
        <?php
            if($isAdmin){
                $listPartie = listPartie($bd);
                $cpt = 0;
                while($row = $listPartie->fetch(PDO::FETCH_ASSOC))
                {
                    if($row["estPartie"] == "0")
                        echo "<p>N°".$row["idGrille"].": Partie en attente d'un joueur <a href=\"Rejoindre.php?grille=".$row["idGrille"]."\"  style=\"color:green;\">Rejoindre</a> <a href=\"SupprimerPartie.php?grille=".$row["idGrille"]."\" ><img src=\"ressources/supp.png\" height=\"1%\" width=\"1%\" alt=\"supprimer\"> </a></p>";
                    else if($row["estPartie"] == "2")
                        echo "<p>N°".$row["idGrille"].": Partie termine <a href=\"Rejoindre.php?grille=".$row["idGrille"]."\"  style=\"color:green;\">Rejoindre</a> <a href=\"SupprimerPartie.php?grille=".$row["idGrille"]."\" ><img src=\"ressources/supp.png\" height=\"1%\" width=\"1%\" alt=\"supprimer\"> </a></p>";
                    else
                        echo "<p>N°".$row["idGrille"].": Partie en cours <a href=\"Partie.php?grille=".$row["idGrille"]."\"  style=\"color:green;\">Visioner la partie</a> <a href=\"SupprimerPartie.php?grille=".$row["idGrille"]."\" ><img src=\"ressources/supp.png\" height=\"1%\" width=\"1%\" alt=\"supprimer\"> </a></p>";
                    $cpt++;
                }
                if($cpt == 0) echo "<p>Il n'y a pas de partie en attente.</p>";
            }
            else{
                $listPartie = listPartieId($bd, $_SESSION["id"]);
                $cpt = 0;
                while($row = $listPartie->fetch(PDO::FETCH_ASSOC))
                {
                    echo "<p>N°".$row["idGrille"].": Partie en attente d'un joueur <a href=\"Rejoindre.php?grille=".$row["idGrille"]."\"  style=\"color:green;\">Rejoindre</a> </p>";
                    $cpt++;
                }
                if($cpt == 0) echo "<p>Il n'y a pas de partie en attente.</p>";
            }
        ?>
    </div>
</body>
</html>