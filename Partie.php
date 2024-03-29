<?php
    include("bdd.php");

    if(!isset($_SESSION["id"]) && !isset($_GET["grille"])) header("Location: Connexion.php");

    $bd = connectBD("Siam");
    $isAdmin = isAdmin($bd, $_SESSION["id"]);

    $tab = recupTable($bd, $_GET["grille"]);

    $sql = "SELECT * from Grille where idGrille = ".$_GET["grille"];
    $result = selectTable($bd, $sql);
    $grille = $result->fetch(PDO::FETCH_ASSOC);

    $sql = "SELECT numJoueur From Participe Where idJoueur = ".$_SESSION["id"]." AND idGrille = ".$_GET["grille"];
    $result = selectTable($bd, $sql);
    $tour = $result->fetch(PDO::FETCH_ASSOC);

    $sql = "SELECT Count(Pion.idPion) As nb From Pion Inner Join Joue On Joue.idPion = Pion.idPion Where idGrille = ".$_GET["grille"]." And position = \"-1:-1\" AND Role = ( Select numJoueur From Participe Where idGrille = ".$_GET["grille"]." And idJoueur = ".$_SESSION["id"]." )";
    $result = selectTable($bd, $sql);
    $nbPionNonJouer = $result->fetch(PDO::FETCH_ASSOC)["nb"];

    if($grille["tour"] != $tour["numJoueur"] && !$isAdmin) header("Location: Connexion.php");

    if($grille["estPartie"] == 2) header("Location: Connexion.php");
?>

<!DOCTYPE html>
<html lang="fr">
<?php include("header.php"); ?>

<body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <?php if($isAdmin) include("GestionMenu/MenuHtmlAdmin.php"); else include("GestionMenu/MenuHtmlUtilisateur.php"); ?>

    <?php if($isAdmin && $grille["tour"] != $tour["numJoueur"]){ ?>

    <h1>Visionage de la grille N°<?php echo $_GET["grille"] ?></h1>
    <center>
        <table>
            <?php for ($i=0; $i < 5 ; $i++) { ?>
                <tr>
                <?php for ($j=0; $j < 5 ; $j++) {
                    $isPlacer = false;
                    foreach($tab as $value){
                        if($j.":".$i == $value["position"]){
                            $isPlacer = true;
                            if($value["role"] == "0") echo "<td><img src=\" ressources/rocher.gif\" height=\"100%\" width=\"100%\" ></button></td>";
                            else{
                                if($value["idPion"] == $value["idPionJouer"]) echo "<td style=\"border: 5px solid red;\"><img src=\" ressources/".$value["role"]."".$value["direction"].".gif\" height=\"100%\" width=\"100%\" ></button></td>";
                                else echo "<td><img src=\" ressources/".$value["role"]."".$value["direction"].".gif\" height=\"100%\" width=\"100%\" ></button></td>";
                            }
                        }
                    }
                    if(!$isPlacer) echo "<td><img src=\"ressources/croix.png\" height=\"100%\" width=\"100%\" ></button></td>";
                } ?>
                </tr>
            <?php } ?>
        </table>
    <center>

    <?php } else { ?>

    <h1>Jouer sur la grille N°<?php echo $_GET["grille"] ?></h1>
    <center>
        <?php if($grille["estSelectPion"] == "0") echo "<h5>Selection d'un pion ou Ajouter un pion</h5>";
        else if(estPionCourantSurGrille($bd, $_GET["grille"])) echo "<h5>Selection d'une action</h5>";
        else echo "<h5>Selection un emplacement pour poser le pion</h5>";
        ?>
        <table style="float:center">
            <?php for ($i=0; $i < 5 ; $i++) { ?>
                <tr>
                <?php for ($j=0; $j < 5 ; $j++) {
                    $isPlacer = false;
                    foreach($tab as $value){
                        if($j.":".$i == $value["position"]){
                            $isPlacer = true;
                            if($value["role"] == "0"){
                                if($value["estSelectPion"] == "0")
                                    echo "<td><img onClick=\"selectPionOnGrille(".$j.", ".$i.", ".$_GET["grille"].")\" src=\"ressources/rocher.gif\" height=\"100%\" width=\"100%\" ></button></td>";
                                else{
                                    if(estPionCourantSurGrille($bd, $_GET["grille"]))
                                        echo "<td><img src=\"ressources/rocher.gif\" height=\"100%\" width=\"100%\" ></button></td>";
                                    else
                                        echo "<td><img onClick=\"PlacerPionOnGrille(".$j.", ".$i.", ".$_GET["grille"].")\" src=\"ressources/rocher.gif\" height=\"100%\" width=\"100%\" ></button></td>";
                                }
                            }
                            else{
                                if($value["idPion"] == $value["idPionJouer"]){
                                    if($value["estSelectPion"] == "0")
                                        echo "<td style=\"border: 5px solid red;\"><img onClick=\"selectPionOnGrille(".$j.", ".$i.", ".$_GET["grille"].")\" src=\"ressources/".$value["role"]."".$value["direction"].".gif\" height=\"100%\" width=\"100%\" ></button></td>";
                                    else{
                                        if(estPionCourantSurGrille($bd, $_GET["grille"]))
                                            echo "<td style=\"border: 5px solid red;\"><img src=\"ressources/".$value["role"]."".$value["direction"].".gif\" height=\"100%\" width=\"100%\" ></button></td>";
                                        else
                                            echo "<td style=\"border: 5px solid red;\"><img onClick=\"PlacerPionOnGrille(".$j.", ".$i.", ".$_GET["grille"].")\" src=\"ressources/".$value["role"]."".$value["direction"].".gif\" height=\"100%\" width=\"100%\" ></button></td>";
                                    }
                                }
                                else{
                                    if($value["estSelectPion"] == "0")
                                        echo "<td><img onClick=\"selectPionOnGrille(".$j.", ".$i.", ".$_GET["grille"].")\" src=\"ressources/".$value["role"]."".$value["direction"].".gif\" height=\"100%\" width=\"100%\" ></button></td>";
                                    else{
                                        if(estPionCourantSurGrille($bd, $_GET["grille"]))
                                            echo "<td><img src=\"ressources/".$value["role"]."".$value["direction"].".gif\" height=\"100%\" width=\"100%\" ></button></td>";
                                        else
                                            echo "<td><img onClick=\"PlacerPionOnGrille(".$j.", ".$i.", ".$_GET["grille"].")\" src=\"ressources/".$value["role"]."".$value["direction"].".gif\" height=\"100%\" width=\"100%\" ></button></td>";
                                    }
                                }
                            }
                        }
                    }
                    if(!$isPlacer) {
                        if($value["estSelectPion"] == "0")
                            echo "<td><img onClick=\"selectPionOnGrille(".$j.", ".$i.", ".$_GET["grille"].")\" src=\"ressources/croix.png\" height=\"100%\" width=\"100%\" ></button></td>";
                        else{
                            if(estPionCourantSurGrille($bd, $_GET["grille"]))
                                echo "<td><img src=\"ressources/croix.png\" height=\"100%\" width=\"100%\" ></button></td>";
                            else
                                echo "<td><img onClick=\"PlacerPionOnGrille(".$j.", ".$i.", ".$_GET["grille"].")\" src=\"ressources/croix.png\" height=\"100%\" width=\"100%\" ></button></td>";
                        }
                    }
                } ?>
                </tr>
            <?php } ?>

        <?php if($grille["estSelectPion"] == "0"){ ?>
            <div style="float:right;vertical-align:middle;padding-left: 10px;padding-right: 300px">
                <button class="button" id="placerPion" onClick="placerPion()">Ajouter un pion</button>
                <div>
                    <button  id="haut" onClick="ajouterPion(<?php echo $_GET["grille"]; ?>, 0)" hidden><img src="ressources/<?php echo $grille["tour"]; ?>0.gif"></button>
                </div>
                <div>
                    <button id="gauche" onClick="ajouterPion(<?php echo $_GET["grille"]; ?>, 3)" hidden><img src="ressources/<?php echo $grille["tour"]; ?>3.gif"></button>
                    <img src="ressources/croix.png">
                    <button id="droite" onClick="ajouterPion(<?php echo $_GET["grille"]; ?>, 1)" hidden><img src="ressources/<?php echo $grille["tour"]; ?>1.gif"></button>
                </div>
                <button id="bas" onClick="ajouterPion(<?php echo $_GET["grille"]; ?>, 2)" hidden><img src="ressources/<?php echo $grille["tour"]; ?>2.gif"></button>
            </div>

        <?php }
        else {
        if(estPionCourantSurGrille($bd, $_GET["grille"])) {?>
            <div style="float:right;vertical-align:middle;padding-left: 10px;padding-right: 300px">
                <div>
                    <button id="haut" onClick="tournerPion(<?php echo $_GET["grille"]; ?>, 0)" hidden><img src="ressources/<?php echo $grille["tour"]; ?>0.gif"></button>
                </div>
                <div>
                    <button id="gauche" onClick="tournerPion(<?php echo $_GET["grille"]; ?>, 3)" hidden><img src="ressources/<?php echo $grille["tour"]; ?>3.gif"></button>
                    <img src="ressources/croix.png">
                    <button id="droite" onClick="tournerPion(<?php echo $_GET["grille"]; ?>, 1)" hidden><img src="ressources/<?php echo $grille["tour"]; ?>1.gif"></button>
                </div>
                <button id="bas" onClick="tournerPion(<?php echo $_GET["grille"]; ?>, 2)" hidden><img src="ressources/<?php echo $grille["tour"]; ?>2.gif"></button>
            </div>



            <div style="float:right;">
                <div>
                    <button class="button" id="avancer" onClick="avancerPion(<?php echo $_GET["grille"]; ?>)">Avancer le pion selectionné</button>
                    <button  id="tournerGauche" onClick="tournerGauchePion(<?php echo $_GET["grille"]; ?>)" hidden>Tourner à gauche</button>
                </div>
                <br>
                <div>
                    <button class="button" id="tourner" onClick="tournerPions()">Tourner le pion selectionné</button>
                    <button  id="tournerDroite" onClick="tournerDroitePion(<?php echo $_GET["grille"]; ?>)" hidden>Tourner à gauche</button>
                </div>
                <br>
                <div>
                    <button class="button" id="retirer" onClick="retirerPion(<?php echo $_GET["grille"]; ?>)" >Retirer le pion selectionné</button>
                </div>
                <br>
                <div>
                    <button class="button" id="annuler" onClick="annuleSelection(<?php echo $_GET["grille"]; ?>)" >Annuler la selection</button>
                </div>
            </div>

        <?php }} ?>
        </table>

        <br>
        <?php for ($i=0; $i<$nbPionNonJouer ;$i++){ ?>
        <img src="ressources/<?php echo $grille["tour"]; ?>0.gif">
        <?php } ?>

    <center>

    <?php } ?>

</body>
</html>

<script>

    function tournerPions(){
        $("#avancer").hide();
        $("#tourner").hide();
        $("#retirer").hide();
        $("#annuler").hide();

        $("#haut").show();
        $("#bas").show();
        $("#gauche").show();
        $("#droite").show();

        /*$("#tournerGauche").show();
        $("#tournerDroite").show();
        $("#valider").show();*/
    }

    function placerPion(){
        $("#placerPion").hide();
        $("#haut").show();
        $("#bas").show();
        $("#gauche").show();
        $("#droite").show();
    }

    function selectPionOnGrille(x, y, idGrille){
        $.ajax({
            url: 'Action/selectPion.php',
            type: 'GET',
            cache: true,
            data: 'x='+ x + '&y=' + y + '&idGrille=' + idGrille,
            success: function(reponse) {
                console.log(reponse);
                if(reponse == "Error")
                    alert("Il faut cliquer sur un pion !!");
                else if(reponse == "Error Joueur")
                    alert("Jouer avec vos Pions");
                else
                    document.location.reload(true);
            }
        });
    }

    function ajouterPion(idGrille, direction){
        console.log("ajouter pion");
        $.ajax({
            url: 'Action/ajouterPion.php',
            type: 'GET',
            cache: true,
            data: 'idGrille=' + idGrille + "&direction=" + direction,
            success: function(reponse) {
                console.log(reponse);
                if(reponse == "Error")
                    alert("Vous n'avez plus de pion a placer");
                else
                    document.location.reload(true);
            }
        });
    }

    function annuleSelection(idGrille){
        console.log("annuler Select");
        $.ajax({
            url: 'Action/annuleSelect.php',
            type: 'GET',
            cache: true,
            data: 'idGrille=' + idGrille,
            success: function(reponse) {
                console.log(reponse + " test");
                document.location.reload(true);
            }
        });
    }

    function retirerPion(idGrille){
        console.log("retirer");
        $.ajax({
            url: 'Action/retirerPion.php',
            type: 'GET',
            cache: true,
            data: 'idGrille=' + idGrille,
            success: function(reponse) {
                console.log(reponse);
                finTour(idGrille);
            }
        });
    }

    function PlacerPionOnGrille(x, y, idGrille){
        console.log("placer on " + x + ", " + y);
        $.ajax({
            url: 'Action/placerPion.php',
            type: 'GET',
            cache: true,
            data: 'x='+ x + '&y=' + y + '&idGrille=' + idGrille,
            success: function(reponse) {
                console.log(reponse);
                if(reponse == "Error")
                    alert("Pas de selection de Pion !!");
                else if(reponse == "Error Placement")
                    alert("Placer votre pion sur un emplacement libre !!");
                else
                    finTour(idGrille);
            }
        });
    }

    function tournerPion(idGrille, direction){
        console.log("tourner vers " + direction);
        $.ajax({
            url: 'Action/tournerPion.php',
            type: 'GET',
            cache: true,
            data: 'idGrille=' + idGrille + "&direction=" + direction,
            success: function(reponse) {
                console.log(reponse);
                if(reponse == "Error")
                    alert("Aucun pion n'est selectionné !!");
                else
                    finTour(idGrille);
            }
        });
    }

    function avancerPion(idGrille){
        console.log("avancer");
        $.ajax({
            url: 'Action/avancerPion.php',
            type: 'GET',
            cache: true,
            data: 'idGrille=' + idGrille,
            success: function(reponse) {
                console.log(reponse);
                if(reponse == "Non"){
                    alert("Ton pion ne peut pas poser !!");
                    document.location.reload(true);
                }
                else if(reponse == "Deplacer")
                    finTour(idGrille);
                else
                    finPartie(reponse);
            }
        });
    }

    function finTour(idGrille){
        console.log("Fin tour");
        $.ajax({
            url: 'Action/finTour.php',
            type: 'GET',
            cache: true,
            data: 'idGrille=' + idGrille,
            success: function(reponse) {
                console.log(reponse);
                document.location.reload(true);
            }
        });
    }

    function finPartie(vainqueur){
        console.log("Fin Partie");
        $.ajax({
            url: 'Action/finPartie.php',
            type: 'GET',
            cache: true,
            data: 'vainqueur=' + vainqueur,
            success: function(reponse) {
                console.log(reponse);
                document.location.reload(true);
            }
        });
    }
</script>
