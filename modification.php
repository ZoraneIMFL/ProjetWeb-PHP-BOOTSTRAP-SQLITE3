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
    <title>EdTOnline - Modification EdT</title>
</head>
<body>
<?php

    //Vérifie que l'utilisateur est connecté et est bien un prof
    if(! isset($_COOKIE['nomUser']) || ! isset($_COOKIE['prenomUser'])){
        header('Location: connexion.php');
    }else if($_COOKIE['typeUser'] != 'prof'){
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
    //Création de la BDD
    $pdo = new PDO('sqlite:data/donnees.db');

    function pdoSelect(PDO $dataBase,String $query){
        //Création d'une requête
        $statement = $dataBase->query($query);

        //Execution de la requête
        if(!$statement)return FALSE;
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    function pdoGetUserInfo(String $nom, String $prenom, PDO $db){
        //Prépare la requête
        $query = $db->prepare("SELECT * FROM utilisateurs WHERE nom = :nomValue AND prenom = :prenomValue");

        //Lie les paramètres avec les paramétres de la fonction
        $query->bindParam(':nomValue',$nom,PDO::PARAM_STR, 50);
        $query->bindParam(':prenomValue',$prenom,PDO::PARAM_STR, 50);

        //Éxécution de la requête
        $query->execute();
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    $profId = pdoGetUserInfo($_COOKIE["nomUser"],$_COOKIE["prenomUser"],$pdo)[0]['id'];
?>

<div class="container-fluid">
    <div class="row ">
        <div class="col-sm-2">
        </div>

        <div class="col-sm-8">
            <div class="text-center">
                <h1>Modification de l'emplois du temps</h1>
            </div>
            <div class="row">
                <div class="alert alert-info text-center">
                    Les cours ont lieu de <strong>8 H 00</strong> à <strong>18 H 00</strong>.
                </div>
                <div class="col-sm-6 trait1">
                    <!-- Créer les variable POST creneauMatiere, creneauDate, creneauHeure, creneauMinute, creneauCours, creneauPromo, creneauSalle, nbRepet -->
                    <form action="modification.php" method="post" title="Ajouter un créneau">
                        <div class="lead">Ajouter un créneau :</div>
                            Date : <input type="date" name="creneauDate">
                            Heure : <input type="number" name="creneauHeure" style="width:40px"> H <input type="number" name="creneauMinute" style="width:40px"> <br><br>
                                <div class="row">
                                    <div class="col-sm-3">
                                        Matière :
                                        <br>
                                        <select name = "creneauMatiere" size = "5" multiple>
                                            <?php
                                                $listeModule = pdoSelect($pdo,"SELECT * FROM modules WHERE prof_id =".$profId.";");
                                                $nbCours = count($listeModule);
                                                $text;
                                                for($i=0;$i<$nbCours;$i++){
                                                    $id = $listeModule[$i]['id'];
                                                    $nom = $listeModule[$i]['nom'];
                                                    if($i == 0)$text = printf('<option value = "%s" selected=selected>%s</option>',$id,$nom);
                                                    else $text = printf('<option value = "%s">%s</option>',$id,$nom);
                                                    echo $text;
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        Type : 
                                        <br>
                                        <select name = "creneauCours" size = "5" multiple>
                                            <?php
                                                $listeCours = pdoSelect($pdo,"SELECT * FROM cours;");
                                                $nbCours = count($listeCours);
                                                $text;
                                                for($i=0;$i<$nbCours;$i++){
                                                    $id = $listeCours[$i]['id'];
                                                    $nom = $listeCours[$i]['nom'];
                                                    if($id == 1)$text = printf('<option value = "%s" selected=selected>%s</option>',$id,$nom);
                                                    else $text = printf('<option value = "%s">%s</option>',$id,$nom);
                                                    echo $text;
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        Promo :
                                        <br>
                                        <select name = "creneauPromo" size = "5" multiple>
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
                                    </div>
                                    <div class="col-sm-3">
                                        Salle :
                                        <br>
                                        <select name = "creneauSalle" size = "5" multiple>
                                            <?php

                                                $listeSalle = pdoSelect($pdo,"SELECT * FROM salle;");
                                                $nbSalle = count($listeSalle);
                                                for($i=0;$i<$nbSalle;$i++){
                                                    $id = $listeSalle[$i]['id'];
                                                    $nom = $listeSalle[$i]['nom'];
                                                    if($id == 1)$text = printf('<option value = "%s" selected=selected>%s</option>',$id,$nom);
                                                    else $text = printf('<option value = "%s">%s</option>',$id,$nom);
                                                    echo $text;
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            <br>
                            Nombre de répétitions : <input type="number" name="nbRepet" value="1" placeholder="1" style="width:40px">   </input><input type="submit" value="Créer">
                    </form>
                    <br>
                    <!-- Créer les variables POST 'suppDate' 'suppHeure' 'suppMinute' -->
                    <form action="modification.php" method="post" title="supprimer un créneau">
                            <div class="lead">Supprimer un créneau</div>
                            Date : <input type="date" name="suppDate">
                            Heure : <input type="number" name="suppHeure" style="width:40px"> H <input type="number" name="suppMinute" style="width:40px">
                            <input type="submit" value="Supprimer">
                    </form>
                </div>
                <div class="col-sm-6 v-divider">  
                    <!-- Créer les variable POST modSur, oldDate, oldHeure, oldMinute, modMatiere, modDate, modHeure, modMinute, modCours, modPromo, modSalle -->
                    <form action="modification.php" method="post" title="Modifier un créneau">
                        <div class="lead">Modifier un créneau :</div>
                            Date : <input type="date" name="oldDate">
                            Heure : <input type="number" name="oldHeure" style="width:40px"> H <input type="number" name="oldMinute" style="width:40px">
                            <br><br>
                            <div class="lead"> Nouvelles valeurs : </div>
                            Date : <input type="date" name="modDate">
                            Heure : <input type="number" name="modHeure" style="width:40px"> H <input type="number" name="modMinute" style="width:40px">
                            <br><br>
                            <div class="row">
                                <div class="col-sm-3">
                                    Matière :
                                    <br>
                                    <select name = "modMatiere" size = "5" multiple>
                                        <?php
                                            $listeModule = pdoSelect($pdo,"SELECT * FROM modules WHERE prof_id =".$profId.";");
                                            $nbCours = count($listeModule);
                                            $text;
                                            for($i=0;$i<$nbCours;$i++){
                                                $id = $listeModule[$i]['id'];
                                                $nom = $listeModule[$i]['nom'];
                                                if($i == 0)$text = printf('<option value = "%s" selected=selected>%s</option>',$id,$nom);
                                                else $text = printf('<option value = "%s">%s</option>',$id,$nom);
                                                echo $text;
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    Type : 
                                    <br>
                                    <select name = "modCours" size = "5" multiple>
                                        <?php
                                            $listeCours = pdoSelect($pdo,"SELECT * FROM cours;");
                                            $nbCours = count($listeCours);
                                            $text;
                                            for($i=0;$i<$nbCours;$i++){
                                                $id = $listeCours[$i]['id'];
                                                $nom = $listeCours[$i]['nom'];
                                                if($id == 1)$text = printf('<option value = "%s" selected=selected>%s</option>',$id,$nom);
                                                else $text = printf('<option value = "%s">%s</option>',$id,$nom);
                                                echo $text;
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    Promo :
                                    <br>
                                    <select name = "modPromo" size = "5" multiple>
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
                                </div>
                                <div class="col-sm-3">
                                    Salle :
                                    <br>
                                    <select name = "modSalle" size = "5" multiple>
                                        <?php
                                            $listeSalle = pdoSelect($pdo,"SELECT * FROM salle;");
                                            $nbSalle = count($listeSalle);
                                            for($i=0;$i<$nbSalle;$i++){
                                                $id = $listeSalle[$i]['id'];
                                                $nom = $listeSalle[$i]['nom'];
                                                if($id == 1)$text = printf('<option value = "%s" selected=selected>%s</option>',$id,$nom);
                                                else $text = printf('<option value = "%s">%s</option>',$id,$nom);
                                                echo $text;
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <br>
                            Êtes-vous sûr ? <input type="checkbox" name="modSur" value="1">  <input type="submit" value="Modifier">
                    </form>
            </div>
        </div>

        <div class="col-sm-2">
        </div>
        <?php

            function modificationHeure($heure,$minute,$duree){
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

            //Gestion des requêtes POST
            if(isset($_POST['creneauMatiere']) && isset($_POST['creneauDate']) && isset($_POST['creneauHeure']) && isset($_POST['creneauMinute']) && isset($_POST['creneauCours']) && isset($_POST['creneauPromo']) && isset($_POST['creneauSalle']) && isset($_POST['nbRepet'])){
            
                //Ajout du cours dans la bdd
                if($_POST['creneauHeure'] < 8 || $_POST['creneauHeure'] >= 18){
                    echo '<div class="alert alert-danger text-center">Désolé mais l\'Université sera fermé à cette heure là.</div>';
                }else if($_POST['creneauMinute'] > 59 || $_POST["creneauMinute"] < 0){
                    echo '<div class="alert alert-danger text-center">Désolé mais les minutes entrées sont invalide.</div>';
                }else{
                    $heureDebut = ($_POST['creneauHeure'])*100 + $_POST['creneauMinute'];
                    //Modification de l'heure de fin du nouveau creneau
                    $heureFin = modificationHeure($_POST['creneauHeure'],$_POST['creneauMinute'],pdoSelect($pdo,"SELECT duree from cours where id=".$_POST["creneauCours"])[0]["duree"]);

                    $nbRepetition;
                    if($_POST['nbRepet'] <= 1){
                        $nbRepetition = 1;
                    }else if($_POST['nbRepet'] >= 53){
                        $nbRepetition = 53;
                    }else{
                        $nbRepetition = filter_input(INPUT_POST,'nbRepet');
                    }

                    for ($i=0; $i<$nbRepetition; $i++){
                        //Modification de la date
                        $date = filter_input(INPUT_POST,'creneauDate');
                        
                        $date = date('Y-m-d',strtotime($date.'+ '.($i*7).' days'));
                        //Vérifie si on peut la place est libre
                        $verif = pdoSelect($pdo,'SELECT COUNT(*) FROM creneaux WHERE date_cours = "'.$date.'" and ((heure_debut > '.$heureDebut.' and heure_debut < '.$heureFin.') or (heure_fin > '.$heureDebut.' and heure_fin < '.$heureFin.')) and prof_id='.$profId.';');
                        
                        if($verif[0]["COUNT(*)"] > 0){
                            echo '<div class="alert alert-danger text-center">Vous avez déjà un cours à : '.$_POST['creneauHeure'].' h '.$_POST['creneauMinute'].' le '.$date.'.</div>';
                        }else{
                            //Prépare la requête        
                            $sql = 'INSERT INTO creneaux(module_id,date_cours,heure_debut,heure_fin,cours_id,salle_id,promo_id,prof_id) VALUES 
                            (:matiereValue,:dateValue,'.$heureDebut.','.$heureFin.',:coursValue,:salleValue,:promoValue,'.$profId.');';
                            $stmt = $pdo->prepare($sql);
                            //Lie les paramètres avec les paramétres de la fonction
                            $matiere = filter_input(INPUT_POST,'creneauMatiere');
                            $stmt->bindValue(':matiereValue',$matiere,PDO::PARAM_STR);

                            $stmt->bindValue(':dateValue',$date,PDO::PARAM_STR);
                            
                            $cours = filter_input(INPUT_POST,'creneauCours');
                            $stmt->bindValue(':coursValue',$cours,PDO::PARAM_INT);

                            $salle = filter_input(INPUT_POST,'creneauSalle');
                            $stmt->bindValue(':salleValue',$salle,PDO::PARAM_INT);

                            $promo = filter_input(INPUT_POST,'creneauPromo');
                            $stmt->bindValue(':promoValue',$promo,PDO::PARAM_INT);

                            //Execute la requete
                            $result = $stmt->execute();
                            $alert = $result;
                            if($result){
                                echo '<div class="alert alert-success text-center">Ajout du créneau réussi.</div>';
                            }else{
                                echo '<div class="alert alert-danger text-center">Echec de l\'ajout du créneau.</div>';
                            }
                        }
                    }
                }        
            }else if(isset($_POST['suppDate']) && isset($_POST['suppHeure']) && isset($_POST['suppMinute'])){
                $profId = pdoGetUserInfo($_COOKIE["nomUser"],$_COOKIE["prenomUser"],$pdo)[0]['id'];
                $heure=($_POST['suppHeure']*100)+$_POST['suppMinute'];
                if(pdoSelect($pdo,"select * from creneaux where date_cours='".$_POST['suppDate']."' and heure_debut=".$heure." and prof_id=".$profId)){
                    //Prépare la requête
                    $sql = 'DELETE FROM creneaux WHERE date_cours=:dateValue and heure_debut=:heureValue and prof_id=:profValue';
                    $stmt = $pdo->prepare($sql);
                    //Lie les paramètres avec les paramétres de la fonction
                    $date = filter_input(INPUT_POST, 'suppDate');
                    $stmt->bindValue(':dateValue',$date,PDO::PARAM_STR);
                    $stmt->bindValue(':heureValue',$heure,PDO::PARAM_INT);
                    $stmt->bindValue(':profValue',$profId,PDO::PARAM_INT);        
                    //Execute la requete
                    $result = $stmt->execute();
                    echo '<div class="alert alert-success text-center">Suppression réussi.</div>';
                }else{
                    echo '<div class="alert alert-danger text-center">Ce créneau n\'éxiste pas.</div>';
                }    
            }else if(isset($_POST['modSur']) && $_POST['modSur'] == 1 && isset($_POST['oldDate']) && isset($_POST['oldHeure']) && isset($_POST['oldMinute'])){
                $profId = pdoGetUserInfo($_COOKIE["nomUser"],$_COOKIE["prenomUser"],$pdo)[0]['id'];
                $heure=($_POST['oldHeure']*100)+$_POST['oldMinute'];
                $creneau=pdoSelect($pdo,"select * from creneaux where date_cours='".$_POST['oldDate']."' and heure_debut=".$heure." and prof_id=".$profId)[0];
                if($creneau){
                    if(isset($_POST['modMatiere']) && $_POST['modMatiere'] != ''){
                        $matiere=filter_input(INPUT_POST,'modMatiere');
                    }else{
                        $matiere=$creneau['matiere'];
                    }
                    if(isset($_POST['modDate']) && $_POST['modDate'] != ''){
                        $date=filter_input(INPUT_POST,'modDate');
                    }else{
                        $date=$creneau['date_cours'];
                    }
                    if(isset($_POST['modHeure']) && isset($_POST['modMinute'])  && $_POST['modHeure'] != ''  && $_POST['modMinute'] != ''){
                        $heureDebut = ($_POST['modHeure'])*100 + $_POST['modMinute'];
                        $heureFin = modificationHeure($_POST['modHeure'],$_POST['modMinute'],pdoSelect($pdo,"SELECT duree from cours where id=".$_POST["modCours"])[0]["duree"]);
                    }else{
                        $heureDebut=$creneau['heure_debut'];
                        $heureFin=$creneau['heure_fin'];
                    }
                    if(isset($_POST['modCours']) && $_POST['modCours'] != '' ){
                        $cours=filter_input(INPUT_POST,'modCours');
                    }else{
                        $cours=$creneau['cours_id'];
                    }
                    if(isset($_POST['modSalle']) && $_POST['modSalle'] != ''){
                        $salle=filter_input(INPUT_POST,'modSalle');
                    }else{
                        $salle=$creneau['salle_id'];
                    }
                    if(isset($_POST['modPromo']) && $_POST['modPromo'] != ''){
                        $promo=filter_input(INPUT_POST,'modPromo');
                    }else{
                        $promo=$creneau['promo_id'];
                    }
                    //Prépare la requête

                    $verif = pdoSelect($pdo,'SELECT COUNT(*) FROM creneaux WHERE date_cours = "'.$date.'" and ((heure_debut > '.$heureDebut.' and heure_debut < '.$heureFin.') or (heure_fin > '.$heureDebut.' and heure_fin < '.$heureFin.')) and prof_id='.$profId.';');
                    if($verif[0]["COUNT(*)"] > 0){
                        echo '<div class="alert alert-danger text-center">Vous avez déjà un cours à : '.$_POST['creneauHeure'].' h '.$_POST['creneauMinute'].' le '.$date.'.</div>';
                    }else{
                        $sql = 'UPDATE creneaux SET module_id=?,date_cours=?,heure_debut=?,heure_fin=?,cours_id=?,salle_id=?,promo_id=?WHERE id='.$creneau['id'].';';
                        $stmt = $pdo->prepare($sql);
                        //Execute la requete
                        $result = $stmt->execute([$matiere,$date,$heureDebut,$heureFin,$cours,$salle,$promo]);
                        if($result){
                            echo '<div class="alert alert-success text-center">Modification du créneau réussi.</div>';
                        }else{
                            echo '<div class="alert alert-danger text-center">Echec de la modification du créneau.</div>';
                        }
                    }
                    
                }else{
                    echo '<div class="alert alert-danger text-center">Ce créneau n\'éxiste pas.</div>';
                }
            }
            ?>


    </div>
</div>
</body>
<!-- By Thomas Dignoire, Zeineddine DBILIJ-->  
</html>
