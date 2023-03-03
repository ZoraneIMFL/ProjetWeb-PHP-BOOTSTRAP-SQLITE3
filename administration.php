<!DOCTYPE html>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="css/style.css"/>

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<html>
<head>
    <title>EdTOnline - Administration</title>
</head>
<body>
<?php

    //Création de la BDD
    $pdo = new PDO('sqlite:data/donnees.db');

    function pdoSelect(PDO $dataBase,String $query){
        //Création d'une requête
        $statement = $dataBase->query($query);

        //Execution de la requête
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    function modificationHeure($heure,$duree){
        if($heure == 0){
            $heure = 0;
            $minute = 0;
        }else if(strlen($heure) == 3){
            $tmp = str_split($heure,1);
            $heure = $tmp[0];
            $minute = ($tmp[1]*10)+$tmp[2];
        }else{
            $tmp = str_split($heure,2);
            $heure = $tmp[0];
            $minute = $tmp[1];
        }
        if(strlen($duree) == 3){
            $tmp = str_split($duree,1);
            $modHeure = $tmp[0];
            $modMinute = ($tmp[1]*10)+$tmp[2];
        }else{
            $tmp = str_split($duree,2);
            $modHeure = $tmp[0];
            $modMinute = $tmp[1];
        }
        
        if($modMinute == 0){
            return ($heure+$modHeure)*100+$minute;
        }else{
            $modHeure = 1;
            $minuteFin = $minute+$modMinute;
            if($minuteFin > 59){
                $modHeure++;
                $minuteFin = $minuteFin-60;
            }
            return ($heure+$modHeure)*100 + $minuteFin;
        }
    }

    //Vérifie que l'utilisateur est connecté et est bien un admin
    if(! isset($_COOKIE['nomUser']) || ! isset($_COOKIE['prenomUser'])){
        header('Location: connexion.php');
    }else if($_COOKIE['typeUser'] != 'admin'){
        header('Location: index.php');
    }
    // Barre de naviguation
    $plusAdmin = "";
    if($_COOKIE['typeUser'] == 'admin')$plusAdmin = ' <a href="administration.php"> Administration</a> ';
    if($_COOKIE['typeUser'] == 'prof')$plusAdmin = ' <a href="modification.php"> Modification de l\'EdT</a> ';
    echo '
    <nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
        <a class="navbar-brand" href="#">EdtOnline</a>
        </div>
        <ul class="nav navbar-nav">
        <li><a  href="index.php">Votre emplois du temps</a></li>
        <li class="active">'.$plusAdmin.'</li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
        <li><p class="navbar-text">'.$_COOKIE['nomUser'].' '.$_COOKIE['prenomUser'].'</p></li>
        <li><a href="connexion.php"><span class="glyphicon glyphicon-user"></span> Changer d\'utilisateur</a></li>
        </ul>
    </div>
    </nav>
    <br><br>
    ';
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-2">

        </div>
        <div class="col-sm-8">
            <div class="text-center">
                <h1>Administration</h1>
            </div>
            <div class="row">
                <div class="col-sm-4">
                <form action="administration.php" method="post" title="Ajouter un utilisateur">
                    <div class="lead">Ajout d'un utilisateur :</div>
                        <input type="text" name="newNomUser" placeholder="Nom"> <br>
                        <input type="text" name="newPrenomUser" placeholder="Prénom"><br>
                        <br>
                        Type d'utilisateur : 
                        <input type="radio" name="newTypeUser" value="1" checked="checked"> Élève 
                        <input type="radio" name="newTypeUser" value="2"> Professeur
                        <input type="radio" name="newTypeUser" value="3"> Administrateur<br>
                        <br>
                        Promo :
                        <br>
                        <select name = "newPromoUser" size = "5" multiple>
                                    <option value = "1" selected=selected>Maths L1</option>
                                    <option value = "2" >Maths L2</option>
                                    <option value = "3" >Maths L3</option>
                                    <option value = "4" >Maths M1</option>
                                    <option value = "5" >Maths M2</option>
                                    <option value = "6" >Info L1</option>
                                    <option value = "7" >Info L2</option>
                                    <option value = "8" >Info L3</option>
                                    <option value = "9" >Info M1</option>
                                    <option value = "10" >Info M2</option>
                                    <option value = "11" >SVT L1</option>
                                    <option value = "12" >SVT L2</option>
                                    <option value = "13" >SVT L3</option>
                                    <option value = "14" >SVT M1</option>
                                    <option value = "15" >SVT M2</option>
                                    <option value = "16" >Chimie L1</option>
                                    <option value = "17" >Chimie L2</option>
                                    <option value = "18" >Chimie L3</option>
                                    <option value = "19" >Chimie M1</option>
                                    <option value = "20" >Chimie M2</option>
                        </select>
                        <br>
                        <input type="submit" value="Créer">
                    </form>
                </div>
                <div class="col-sm-4 trait2">
                    <form action="administration.php" method="post" title="Créer un nouveau cours">
                        <div class="lead">Créer un nouveau type de cours :</div>
                        <input type="text" name="newCours" placeholder="Nom">
                        Durée : <input type="number" name="newDureeHeure" style="width:40px"> H <input type="number" name="newDureeMinute" style="width:40px"><br><br>
                        <input type="submit" value="Créer">
                    </form>
                    <br>
                    <form action="administration.php" method="post" title="Créer une nouvelle salle">
                        <div  class="lead">Créer une nouvelle salle :</div>
                        <input type="text" name="newSalle" PlaceHolder="Nom">
                        <input type="submit" value="Créer">
                    </form>
                    <br>
                    <form action="administration.php" method="post" title="Service EDT">
                        <div class="lead">Voir le nombre d'heures d'un professeur :</div>
                        <select name = "profHeure" size = "5" multiple>
                            <?php
                                $listeProf = pdoSelect($pdo,"SELECT * FROM utilisateurs WHERE type_utilisateur_id=2;");
                                $nb = count($listeProf);
                                for($i=0;$i<$nb;$i++){
                                    $id = $listeProf[$i]['id'];
                                    $nom = $listeProf[$i]['nom'];
                                    $prenom = $listeProf[$i]['prenom'];
                                    if($i == 0)$text = printf('<option value = "%s" selected=selected>%s %s</option>',$id,$nom,$prenom);
                                    else $text = printf('<option value = "%s">%s %s</option>',$id,$nom, $prenom);
                                    echo $text;
                                }
                            ?>
                            </select>
                            <br>
                        <input type="submit" value="Voir">
                    </form>
                        <?php
                            if(isset($_POST['profHeure'])){
                                $total=0;
                                $professeur = pdoSelect($pdo,"SELECT nom, prenom from utilisateurs where id=".$_POST['profHeure'])[0];
                                $listeCreneau = pdoSelect($pdo,"SELECT cours_id from creneaux WHERE prof_id=".$_POST['profHeure']);
                                
                                if(sizeof($listeCreneau) == 0){
                                    $display = sprintf('
                                        <br><div> Le professeur %s %s ne travaille pas encore. </div>
                                    ',$professeur['nom'],$professeur['prenom']);
                                    echo $display;
                                }else{
                                    for($i=0;$i<sizeof($listeCreneau);$i++){
                                        $duree = pdoSelect($pdo,"SELECT duree from cours WHERE id=".$listeCreneau[$i]['cours_id'])[0]["duree"];
                                        $total = modificationHeure($total,$duree);
                                    }
                                    
                                    if(strlen($total) == 3){
                                        $arrayHeure = str_split($total,1);
                                        $total = $arrayHeure[0]." h ".$arrayHeure[1].$arrayHeure[2];
                                    }else{
                                        $arrayHeure = str_split($total,2);
                                        $total = $arrayHeure[0]." h ".$arrayHeure[1];
                                    }
                        
                                    $display = sprintf('
                                        <br><div> Le professeur %s %s travaille %s. </div>
                                    ',$professeur['nom'],$professeur['prenom'],$total);
                                    echo $display;
                                }                                
                            }
                        ?>
                    <?php

                    ?>
                </div>
                <div class="col-sm-4">

                    <form action="administration.php" method="post" title="Créer un nouveau module">
                        <div  class="lead">Créer un nouveau module :</div>
                        Professeur référant :
                        <br>
                        <select name = "profModule" size = "5" multiple>
                            <?php
                                $listeProf = pdoSelect($pdo,"SELECT * FROM utilisateurs WHERE type_utilisateur_id=2;");
                                $nb = count($listeProf);
                                for($i=0;$i<$nb;$i++){
                                    $id = $listeProf[$i]['id'];
                                    $nom = $listeProf[$i]['nom'];
                                    $prenom = $listeProf[$i]['prenom'];
                                    if($i == 0)$text = printf('<option value = "%s" selected=selected>%s %s</option>',$id,$nom,$prenom);
                                    else $text = printf('<option value = "%s">%s %s</option>',$id,$nom, $prenom);
                                    echo $text;
                                }
                            ?>
                        </select>
                        <br>
                        <input type="text" name="newModule" placeholder="Nom du module"> 
                        <input type="submit" value="Créer">
                    </form>            
            </div>

        </div>

        <div class="col-sm-2">

        </div>
        <br>
    <?php 
        if(isset($_POST["newNomUser"]) && isset($_POST["newPrenomUser"]) && isset($_POST["newTypeUser"]) && isset($_POST["newPromoUser"]) && $_POST["newPrenomUser"] != "" && $_POST["newNomUser"] != ""){
            
            $existeDeja = "SELECT * FROM utilisateurs WHERE nom LIKE :nom AND prenom LIKE:prenom";
            $stmtTest = $pdo->prepare($existeDeja);
            $nom = filter_input(INPUT_POST,'newNomUser');
            $stmtTest->bindValue(':nom',$nom,PDO::PARAM_STR);
            $prenom = filter_input(INPUT_POST,'newPrenomUser');
            $stmtTest->bindValue(':prenom',$prenom,PDO::PARAM_STR);
            $stmtTest->execute();

            if(sizeof($stmtTest->fetchAll()) == 0){
                if($_POST["newTypeUser"] == 1){
                    // Si le nouvelle utilisateur est un élève
                    //Prépare la requête
                    $sql = 'INSERT INTO utilisateurs(nom,prenom,type_utilisateur_id,promo_id) VALUES 
                    (:nomValue,:prenomValue,:typeValue,:promoValue)';
                    $stmt = $pdo->prepare($sql);
                    //Lie les paramètres avec les paramétres de la fonction
                    $nom = filter_input(INPUT_POST,'newNomUser');
                    $stmt->bindValue(':nomValue',$nom,PDO::PARAM_STR);
                    
                    $prenom = filter_input(INPUT_POST,'newPrenomUser');
                    $stmt->bindValue(':prenomValue',$prenom,PDO::PARAM_STR);
    
                    $type = filter_input(INPUT_POST,'newTypeUser');
                    $stmt->bindValue(':typeValue',$type,PDO::PARAM_INT);
    
                    $promo = filter_input(INPUT_POST,'newPromoUser');
                    $stmt->bindValue(':promoValue',$promo,PDO::PARAM_INT);
    
                    //Execute la requete
                    $result = $stmt->execute();
                    if($result){
                        echo '<div class="alert alert-success text-center">L\'utilisateur '.$_POST['newNomUser'].' '.$_POST['newPrenomUser'].' a été ajouté.</div>';
                        $pdo = NULL;
                    }else{
                        echo '<div class="alert alert-danger text-center">Une erreur s\'est produite.';
                        $pdo = NULL;
                    }
                }else{
                    // Si le nouvelle utilisateur n'est pas un élève
                    //Prépare la requête
                    $sql = 'INSERT INTO utilisateurs(nom,prenom,type_utilisateur_id,promo_id) VALUES 
                    (:nomValue,:prenomValue,:typeValue,:promoValue)';
                    $stmt = $pdo->prepare($sql);
                    //Lie les paramètres avec les paramétres de la fonction
                    $nom = filter_input(INPUT_POST,'newNomUser');
                    $stmt->bindValue(':nomValue',$nom,PDO::PARAM_STR);
                    
                    $prenom = filter_input(INPUT_POST,'newPrenomUser');
                    $stmt->bindValue(':prenomValue',$prenom,PDO::PARAM_STR);
    
                    $type = filter_input(INPUT_POST,'newTypeUser');
                    $stmt->bindValue(':typeValue',$type,PDO::PARAM_INT);
    
                    $stmt->bindValue(':promoValue',"NULL",PDO::PARAM_INT);
    
                    //Execute la requete
                    $result = $stmt->execute();
                    if($result){
                        echo '<div class="alert alert-success text-center">L\'utilisateur '.$_POST['newNomUser'].' '.$_POST['newPrenomUser'].' a été ajouté.</div>';
                        $pdo = NULL;
                    }else{
                        echo '<div class="alert alert-danger text-center">Une erreur s\'est produite.</div>';
                        $pdo = NULL;
                    }
                }
            }else{
                echo '<div class="alert alert-danger text-center">Cet utilisateur existe déjà.</div>';
                $pdo = NULL;
            }          
        }

        if(isset($_POST["newCours"]) && $_POST['newCours'] != '' && isset($_POST["newDureeHeure"]) && isset($_POST["newDureeMinute"])){
            if($_POST["newDureeHeure"] > 10 || $_POST["newDureeHeure"] < 0){
                echo '<div class="alert alert-danger text-center">Désolé mais l\'heure entrée est invalide.</div>';
            }else if($_POST["newDureeMinute"] > 59 || $_POST["newDureeMinute"] < 0){
                echo '<div class="alert alert-danger text-center">Désolé mais les minutes entrées sont invalides.</div>';
            }else{
                $stmtTest = $pdo->prepare("SELECT * FROM cours WHERE nom LIKE :nom");
                $nom = filter_input(INPUT_POST,'newCours');
                $stmtTest->bindValue(':nom',$nom,PDO::PARAM_STR);
                $stmtTest->execute();

                if(sizeof($stmtTest->fetchAll()) == 0){
                    //Prépare la requête
                    $sql = 'INSERT INTO cours(nom,duree) VALUES
                    (:nom, :duree)';
                    $stmt = $pdo->prepare($sql);
                    //Lie les paramètres avec les paramétres de la fonction
                    $nom = filter_input(INPUT_POST, 'newCours');
                    $stmt->bindValue(':nom',$nom,PDO::PARAM_STR);
                    
                    $heure = (filter_input(INPUT_POST, 'newDureeHeure')*100) + filter_input(INPUT_POST, 'newDureeMinute');
                    $stmt->bindValue(':duree',$heure,PDO::PARAM_INT);

                    //Execute la requete
                    $result = $stmt->execute();
                    if($result){
                        echo '<div class="alert alert-success text-center">Le cours '.$_POST['newCours'].' a été créé. </div>';
                        $pdo = NULL;
                    }else{
                        echo '<div class="alert alert-danger text-center">Une erreur s\'est produite.</div>';
                        $pdo = NULL;
                    }
                }else{
                    echo '<div class="alert alert-danger text-center">Ce cours existe déjà.</div>';
                    $pdo = NULL;
                }                
            }
        }

        if(isset($_POST["newSalle"]) && $_POST['newSalle'] != ''){

            $stmtTest = $pdo->prepare("SELECT * FROM salle WHERE nom LIKE :nom");
            $nom = filter_input(INPUT_POST,'newSalle');
            $stmtTest->bindValue(':nom',$nom,PDO::PARAM_STR);
            $stmtTest->execute();

            if(sizeof($stmtTest->fetchAll()) == 0){
                //Prépare la requête
                $sql = 'INSERT INTO salle(nom) VALUES 
                (:nom)';
                $stmt = $pdo->prepare($sql);
                //Lie les paramètres avec les paramétres de la fonction
                $nom = filter_input(INPUT_POST, 'newSalle');
                $stmt->bindValue(':nom',$nom,PDO::PARAM_STR);
                //Execute la requete
                $result = $stmt->execute();
                if($result){
                    echo '<div class="alert alert-success text-center">La salle '.$_POST['newSalle'].' a été créée.</div>';
                    $pdo = NULL;
                }else{
                    echo '<div class="alert alert-danger text-center">Une erreur s\'est produite.';
                    $pdo = NULL;
                }
            }else{
                echo '<div class="alert alert-danger text-center">Cette salle existe déjà.</div>';
                $pdo = NULL;
            }
        }

        if(isset($_POST["newModule"]) && isset($_POST["profModule"]) && $_POST['newModule'] != ''){

            $stmtTest = $pdo->prepare("SELECT * FROM modules WHERE nom LIKE :nom");
            $nom = filter_input(INPUT_POST,'newModule');
            $stmtTest->bindValue(':nom',$nom,PDO::PARAM_STR);
            $stmtTest->execute();

            if(sizeof($stmtTest->fetchAll()) == 0){
                $sql = 'INSERT INTO modules(nom,prof_id) VALUES (:nom, :id);';
                $stmt = $pdo->prepare($sql);
                $nom = filter_input(INPUT_POST, 'newModule');
                $stmt->bindValue(':nom',$nom,PDO::PARAM_STR);
                $stmt->bindValue(':id',$_POST["profModule"],PDO::PARAM_INT);
                //Execute la requete
                $result = $stmt->execute();
                if($result){
                    echo '<div class="alert alert-success text-center">Le module '.$_POST['newModule'].' a été créé.</div>';
                    $pdo = NULL;
                }else{
                    echo '<div class="alert alert-danger text-center">Une erreur s\'est produite.';
                    $pdo = NULL;
                }
            }else{
                echo '<div class="alert alert-danger text-center">Ce module existe déjà.</div>';
                $pdo = NULL;
            }
        }
    ?>
    </div>
</div>
</body>
<!-- By Thomas Dignoire, Zeineddine DBILIJ-->  
</html>