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
    <title>Calendar loading error</title>
    <link href="css/input.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <script>
        if (localStorage.getItem('oldIp')) {
            old_ip = localStorage.getItem('oldIp');
            document.cookie = "old_ip=" + old_ip + "; SameSite=Lax";
        }

        <?= "var ip = \"$ip\";" ?>

        localStorage.setItem("oldIp", ip);
    </script>
	
    <meta http-equiv="refresh" content="30;url=../">
</head>
<body class="h-screen overflow-hidden">
    <div id="calendar1" class="absolute flex flex-col justify-center items-center w-full h-screen offscreen-right transition-transform duration-1000 m-auto">
        <h1 class="text-3xl mb-8 font-sans font-bold text-center">
	    <?php
		if (strcmp($error, "84") == 0) {
		    echo "<span class='text-red-600'>" . 'ERROR 84' . "</span>" . "<br><br>" . 'CONFIGURATION FILE NOT FOUND !' . "<br>" . 'REFER TO DOCUMENTATION.';
		    return;
                } elseif (strcmp($error, "107") == 0) {
                    echo "<span class='text-red-600'>" . 'ERREUR 107' . "</span>" . "<br><br>" . 'CONFLICT IN CONFIGURATION FILE.' . "<br>" . 'THERE ARE SEVERAL CONFIGURATIONS WITH THE SAME IP. REFER TO DOCUMENTATION.';
                    return;
		} elseif (strcmp($error, "104") == 0) {
		    echo "<span class='text-red-600'>" . 'ERROR 104' . "</span>" . "<br><br>" . 'CANNOT READ CONFIGURATION FILE.' . "<br>" . 'REFER TO DOCUMENTATION.';
		    return;
		} elseif (strcmp($error, "102") == 0) {
		    echo "<span class='text-red-600'>" . 'ERROR 102' . "</span>" . "<br><br>" . 'CANNOT WRITE TO CONFIGURATION FILE.' . "<br>" . 'REFER TO DOCUMENTATION.';
		    return;
		} elseif (strcmp($error, "101") == 0) {
		    echo "<span class='text-red-600'>" . 'ERROR 101' . "</span>" . "<br><br>" . 'NO CONFIGURATION TO THE IP ADDRESS OF THIS DEVICE.' . "<br>" . 'YOUR IP ADDRESS IS ' . "<span class='text-red-600'>" . $ip . "</span>"  . ', REFER TO DOCUMENTATION.';
		    return;
		} else {
		    echo "<span class='text-red-600'>" . 'UNKNOWN ERROR' . "</span>"  . "<br><br>" . 'THIS ERROR IS NOT LISTED !';
		    return;
		}
	    ?>
	</h1>
    </div>
</body>
</html>
