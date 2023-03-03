<!DOCTYPE html>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="css/style.css"/>
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<?php
    //Création de la BDD
    $pdo = new PDO('sqlite:data/donnees.db');

    function pdoSelect(PDO $dataBase,String $query){
        //Création d'une requête
        $statement = $dataBase->query($query);

        //Execution de la requête
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    function pdoAffichageCreneau(PDO $pdo, int $s, Array $infoTypeUser, Array $infoUser){
        //Affichage formaté des créneaux
        $heureActuel = 0;
        $cours = false;
        $listeJours = Array(
                            0 => 'Mon',
                            1 => 'Tue',
                            2 => 'Wed',
                            3 => 'Thu',
                            4 => 'Fri',
                            5 => 'Sat',
                            6 => 'Sun'
                        );
        
        for($i=8; $i<18; $i++){
            echo sprintf('
            <div class="row">
                <div class="col-sm-2"></div>
                <div class="col-sm-1 heure"> %s h </div>
            ',$i);

            //Requete vers la BDD
            if($infoTypeUser[0]['id'] == 1){
                $promoId = $infoUser[0]['promo_id'];
                if(isset($_POST['filtre'])){
                    $queryPourCreneaux = "SELECT * FROM creneaux WHERE promo_id=".$promoId." AND".$_POST['filtre']." AND heure_debut betWeen ".($i*100)." AND ".(($i*100)+59)." ORDER BY heure_debut, date_cours";
                }else{
                    $queryPourCreneaux = "SELECT * FROM creneaux WHERE promo_id=".$promoId." AND heure_debut betWeen ".($i*100)." AND ".(($i*100)+59)." ORDER BY date_cours, heure_debut";
                }
    
                $array = pdoSelect($pdo,$queryPourCreneaux);
    
            }
            if($infoTypeUser[0]['id'] == 2){
                $id = $infoUser[0]['id'];
                if(isset($_POST['filtre'])){
                    $queryPourCreneaux = "SELECT * FROM creneaux WHERE ".$_POST['filtre']." AND heure_debut betWeen ".($i*100)." AND ".(($i*100)+59)." ORDER BY date_cours, heure_debut";
                }else{
                    $queryPourCreneaux = "SELECT * FROM creneaux WHERE prof_id=".$id." AND heure_debut betWeen ".($i*100)." AND ".(($i*100)+59)." ORDER BY date_cours, heure_debut";
                }
    
                $array = pdoSelect($pdo,$queryPourCreneaux);
    
            }
            if($infoTypeUser[0]['id'] == 3){
                $queryPourCreneaux = "SELECT * FROM creneaux WHERE heure_debut betWeen ".($i*100)." AND ".(($i*100)+59)." ORDER BY date_cours, heure_debut";
                $array = pdoSelect($pdo,$queryPourCreneaux);
            }
            
            for($j=0;$j<7;$j++){
                foreach($array as $row => $creneaux){
                    //Vérifie si c'est le bon jour
                    $tmp = $creneaux['date_cours'];
                    $dateFracture = explode('-',$tmp);
        
                    $ddate = mktime(0,0,0,$dateFracture[1],$dateFracture[2],$dateFracture[0]);
                    $semaine = (int)date('W',$ddate);
                    
                    if($listeJours[$j] == date('D',$ddate) && $s == $semaine){
                        $matiere = pdoSelect($pdo,"SELECT nom FROM modules WHERE id=".$creneaux['module_id'])[0]['nom'];
                        $date = $dateFracture[2]."/".$dateFracture[1]."/".$dateFracture[0];
                        $heure_debut = $creneaux['heure_debut'];
                        $heure_fin = $creneaux['heure_fin'];
                        $salle = $creneaux['salle_id'];
                        $prof = $creneaux['prof_id'];
                        $cours = pdoSelect($pdo,"SELECT nom FROM cours WHERE id=".$creneaux['cours_id'])[0]['nom'];
            
                        $prof_nom = pdoSelect($pdo,"SELECT nom FROM utilisateurs WHERE id =".$prof)[0]['nom'];
                        $prof_prenom = pdoSelect($pdo,"SELECT prenom FROM utilisateurs WHERE id =".$prof)[0]['prenom'];
                        $salle = pdoSelect($pdo, "SELECT nom FROM salle WHERE id =".$salle)[0]['nom'];
            
                        if(strlen($heure_debut) == 3){
                            $arrayHeureD = str_split($heure_debut,1);
                            $heure_debut = $arrayHeureD[0]." h ".$arrayHeureD[1].$arrayHeureD[2];
                        }else{
                            $arrayHeureD = str_split($heure_debut,2);
                            $heure_debut = $arrayHeureD[0]." h ".$arrayHeureD[1];
                        }
                        if(strlen($heure_fin) == 3){
                            $arrayHeure = str_split($heure_fin,1);
                            $heure_fin = $arrayHeure[0]." h ".$arrayHeure[1].$arrayHeure[2];
                        }else{
                            $arrayHeure = str_split($heure_fin,2);
                            $heure_fin = $arrayHeure[0]." h ".$arrayHeure[1];
                        }
        
        
                        echo sprintf('
                            <div class="col-sm-1 blocCours">
                            %s - %s <br>
                            %s %s <br>
                            Salle : %s<br>
                            %s %s
                            </div>
                        ',$heure_debut,$heure_fin,$cours,$matiere,$salle,$prof_nom,$prof_prenom);
                        $cours=true;
                    }else{
                        $cours = false;
                    }
                }
                if(! $cours){                    
                        echo '
                            <div class="col-sm-1 blocNoCours">
                            <br>
                            <br>
                            <br>
                            <br>
                            </div>
                        ';
                }
            }

            //Fin de la semaine
            echo '<div class="col-sm-2"></div></div>';
        }
    }

    function pdoGetUserInfo(String $nom, String $prenom, PDO $db){
        //Prépare la requête
        $query = $db->prepare("SELECT * FROM utilisateurs WHERE nom = :nomValue AND prenom = :prenomValue");

        //Lie les paramètres avec les paramétres de la fonction
        $query->bindParam(':nomValue',$nom,PDO::PARAM_STR, 50);
        $query->bindParam(':prenomValue',$prenom,PDO::PARAM_STR, 50);

        //Éxécution de la requête
        $result = $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    function pdoGetTypeInfo(String $nom, String $prenom, PDO $db){
        //Récupération des infos de l'utilisateur
        $infoUser = pdoGetUserInfo($nom,$prenom,$db);

        $q = "SELECT * FROM type_utilisateur WHERE id=".($infoUser[0]['type_utilisateur_id']);

        //Prépare la requête
        $query = $db->query($q);
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

?>
<html>
<head>
    <title>EdTOnline</title>
</head>
<body>
<?php

    if(isset($_POST["nomUser"]) && isset($_POST["prenomUser"])){
        $infoUser = pdoGetUserInfo($_POST["nomUser"],$_POST["prenomUser"],$pdo);
        
        print_r($infoUser);
        if(sizeof($infoUser) == 0){
            header('Location: connexion.php');
        }else{
            $infoTypeUser = pdoGetTypeInfo($_POST["nomUser"],$_POST["prenomUser"],$pdo);
            if($infoUser[0]['nom'] && $infoUser[0]['prenom']){
                setcookie("nomUser",$_POST["nomUser"],time()+(86400*30),"/");
                setcookie("prenomUser",$_POST["prenomUser"],time()+(86400*30),"/");
                setcookie("typeUser",$infoTypeUser[0]['type'],time()+(86400*30),"/");
                echo "<meta http-equiv='refresh' content='0'>";
            }
        }

    }
    if(! isset($_COOKIE['nomUser']) || ! isset($_COOKIE['prenomUser'])){
        header('Location: connexion.php');
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
        <li class="active"><a  href="index.php">Votre emplois du temps</a></li>
        <li>'.$plusAdmin.'</li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
        <li><p class="navbar-text">'.$_COOKIE['nomUser'].' '.$_COOKIE['prenomUser'].'</p></li>
        <li><a href="connexion.php"><span class="glyphicon glyphicon-user"></span> Changer d\'utilisateur</a></li>
        </ul>
    </div>
    </nav>
    <br><br>
    ';
        //Détection de la semaine actuel ou a afficher
        if(isset($_GET["lundi"])){
            $ts = $_GET["lundi"];
        }else{ 
            $jour = (date('w') - 1);
            $diff = $jour * 86400;
            $ts = (mktime() - $diff);
        }
    
        //Initialisation des variables
        $semaineAffichee = date('W', $ts); //Semaine en cours
        $avant = $ts - 604800; //TimeStamp Lundi précédant
        $apres = $ts + 604800; //TimeStamp Lundi suivant

        $infoUser = pdoGetUserInfo($_COOKIE["nomUser"],$_COOKIE["prenomUser"],$pdo);
        $infoTypeUser = pdoGetTypeInfo($_COOKIE["nomUser"],$_COOKIE["prenomUser"],$pdo);
    
?>
<div class="container-fluid">
    <br>
    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-3">
        <form action="index.php" method="post" title="Filtrer">
            <span class="lead">Afficher les cours de  : </span>
            <select name = "filtre">
                        <option value = "" selected=selected></option>
                        <option value = " promo_id = 1" >Maths L1</option>
                        <option value = " promo_id = 2" >Maths L2</option>
                        <option value = " promo_id = 3" >Maths L3</option>
                        <option value = " promo_id = 4" >Maths M1</option>
                        <option value = " promo_id = 5" >Maths M2</option>
                        <option value = " promo_id = 6" >Info L1</option>
                        <option value = " promo_id = 7" >Info L2</option>
                        <option value = " promo_id = 8" >Info L3</option>
                        <option value = " promo_id = 9" >Info M1</option>
                        <option value = " promo_id = 10" >Info M2</option>
                        <option value = " promo_id = 11" >SVT L1</option>
                        <option value = " promo_id = 12" >SVT L2</option>
                        <option value = " promo_id = 13" >SVT L3</option>
                        <option value = " promo_id = 14" >SVT M1</option>
                        <option value = " promo_id = 15" >SVT M2</option>
                        <option value = " promo_id = 16" >Chimie L1</option>
                        <option value = " promo_id = 17" >Chimie L2</option>
                        <option value = " promo_id = 18" >Chimie L3</option>
                        <option value = " promo_id = 19" >Chimie M1</option>
                        <option value = " promo_id = 20" >Chimie M2</option>
                        <?php
                            $listeProf = pdoSelect($pdo,"SELECT * FROM utilisateurs WHERE type_utilisateur_id=2;");
                            $nb = count($listeProf);
                            for($i=0;$i<$nb;$i++){
                                $id = $listeProf[$i]['id'];
                                $nom = $listeProf[$i]['nom'];
                                $prenom = $listeProf[$i]['prenom'];
                                echo printf('<option value = " prof_id = %s">%s %s</option>',$id,$nom, $prenom);
                            }
                            $listeSalle = pdoSelect($pdo,"SELECT * FROM salle;");
                                $nbSalle = count($listeSalle);
                                for($i=0;$i<$nbSalle;$i++){
                                    $id = $listeSalle[$i]['id'];
                                    $nom = $listeSalle[$i]['nom'];
                                    echo printf('<option value = " salle_id = %s">%s</option>',$id,$nom);
                            }
                        ?>
                </select> 
            <input type="submit" value="Filtrer">
        </form>
        </div>
        <div class="col-sm-2">
            <div class="text-center">
                <a href="./index.php?lundi=<?echo $avant;?>"><button><<</button></a> Semaine <?echo $semaineAffichee;?> <a href="./index.php?lundi=<?echo $apres;?>"><button>>></button></a>
            </div>
        </div>
        <div class="col-sm-3">
        <?php
            if($infoTypeUser[0]['type'] == 'etudiants'){
                echo '<span class="lead">Emplois du temps des ';
                    $infoPromo = pdoSelect($pdo,"SELECT * from promotion WHERE id=".$infoUser[0]['promo_id']);
                    $infoDep = pdoSelect($pdo,"SELECT * from departement WHERE id=".$infoPromo[0]['departement_id']);
                    if($infoPromo[0]['annee'] < 4){
                        $display = "Licence ".$infoPromo[0]['annee'];
                    }else{
                        $display = "Master ".($infoPromo[0]['annee']-3);
                    }
                    echo sprintf('%s %s.',$display,$infoDep[0]['nom']);
                }
                echo "</span>";
            ?>

        </div>
        <div class="col-sm-2"></div>
    </div>
    <div class="row">
        <div class="col-sm-2">
        </div>
        <div class="col-sm-1">
        </div>
            <?php
                $listeJours = Array(
                    0 => 'Lun',
                    1 => 'Mar',
                    2 => 'Mer',
                    3 => 'Jeu',
                    4 => 'Ven',
                    5 => 'Sam',
                    6 => 'Dim'
                );

                for($i=0;$i<7;$i++){
                    echo sprintf('
                        <div class="col-sm-1">
                            %s %s
                        </div>
                    ',$listeJours[$i],date('d/m/Y', $ts));
                    $ts += 86400;
                }

            ?>
        <div class="col-sm-2">
        </div>
    </div>
    <!-- Affichage des créneaux -->
    <?php
       pdoAffichageCreneau($pdo,$semaineAffichee,$infoTypeUser,$infoUser);

?>
</div>
</body>
<!-- By Thomas Dignoire, Zeineddine DBILIJ-->  
</html>
