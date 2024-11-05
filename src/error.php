<?php

$ip = $_SERVER['REMOTE_ADDR'];

$error = isset($_GET['code']) ? $_GET['code'] : null;

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Erreur de l'agenda</title>
    <link href="./css/output.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <meta http-equiv="refresh" content="5;url=../">
</head>
<body class="h-screen overflow-hidden">
    <div id="calendar1" class="absolute flex flex-col justify-center items-center w-full h-screen offscreen-right transition-transform duration-1000 m-auto">
        <h1 class="text-3xl mb-8 font-sans font-bold text-center">
	    <?php
		if ($error == null) {
		    echo 'ERREUR INCONNUE : CETTE ERREUR N\'EST PAS RÉPERTORIÉE !';
		    return;
		}
		if (strcmp($error, "1") == 0) {
		    echo 'ERREUR LE FICHIER DE CONFIGURATION EST INTROUVABLE.' . "<br>" . 'RÉFÉREZ-VOUS À LA DOCUMENTATION !';
		    return;
		}
		if (strcmp($error, "2") == 0) {
		    echo 'ERREUR DE LECTURE DU FICHIER DE CONFIGURATION.' . "<br>" . 'VOTRE FICHIER NE RESPECTE PAS LA NORME (LES COMMENTAIRES SONT INTERDITS).';
		    return;
		}
		if (strcmp($error, "3") == 0) {
		    echo 'ERREUR VOTRE APPAREIL A CHANGÉ D\'IP, IMPOSSIBLE D\'ÉCRIRE LA NOUVELLE IP DANS LE FICHIER DE CONFIGURATION.' . "<br>" . 'VEUILLEZ FAIRE UNE RÉSERVATION DANS LE DHCP OU DONNER LES DROITS D\'ÉCRITURE AU FICHIER DE CONFIGURATION.' . "<br>" . 'POUR CELA RÉFÉREZ-VOUS À LA DOCUMENTATION.';
		    return;
		}
		if (strcmp($error, "4") == 0) {
		    echo 'ERREUR, IL N\'EXISTE PAS DE CONFIGURATION LIER À L\'ADRESSE IP DE CET APPAREIL.' . "<br>" . 'VOTRE ADRESSE IP EST ' . $ip . ', RÉFÉREZ-VOUS À LA DOCUMENTATION.';
		    return;
		}
	    ?>
	</h1>
    </div>
</body>
</html>
