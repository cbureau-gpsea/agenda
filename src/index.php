<?php

$ip = $_SERVER['REMOTE_ADDR'];

$json = file_get_contents("/etc/agenda/config.json");

if ($json === false) {
    echo 'PAS DE FICHIER JSON TROUVÉ !' . "\n";
    return;
}

$parsed_json = json_decode($json, true);

if ($parsed_json === null) {
    echo 'ERREUR DE DÉCODAGE JSON' . "\n";
    return;
}

function check_device($parsed_json, $ip)
{
    $i = 1;

    while (isset($parsed_json[strval($i)])) {
        $ip_json = $parsed_json[strval($i)]['ip'];

        if (strcmp($ip, $ip_json) == 0) {
            return $i;
        }

        $i += 1;
    }

    return null;
}

$i = check_device($parsed_json, $ip);

if ($i === null) {
    echo 'AUCUNE IP NE CORRESPOND AU FICHIER DE CONFIGURATION, TON IP EST : ' . $ip . "\n";
    return;
} else {
    $ip_json = $parsed_json[strval($i)]['ip'];
    echo 'IP trouvée : ' . $ip_json . "\n";
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Agenda des salles - Siège</title>
    <link href="./css/output.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            @apply h-screen overflow-hidden;
            font-family: Arial, sans-serif;
            font-weight: bold;
        }

        .hide {
            display: none;
        }

        .show {
            display: flex;
            transform: translateX(0);
        }

        .hide-left {
            transform: translateX(-100%);
        }

        .hide-right {
            transform: translateX(100%);
        }

        .offscreen-right {
            transform: translateX(100%);
        }

        .offscreen-left {
            transform: translateX(-100%);
        }
    </style>
</head>
<body class="h-screen overflow-hidden">
    <div id="calendar1" class="absolute flex flex-col justify-center items-center w-full h-screen offscreen-right transition-transform duration-1000">
        <h1 class="text-2xl mb-8 font-sans text-center">Agenda de la salle Venise</h1>
        <iframe id="salle_venise" title="Calendar"
                src="https://outlook.office365.com/owa/calendar/c516fd086386493a88e3a63ce0e9f3c4@gpsea.fr/8c44c50e0d51424099819b8cc3c600568043929034609727240/calendar.html"
                class="border-2 mb-2 rounded-lg w-4/5 h-4/5">
        </iframe>
    </div>

    <div id="calendar2" class="absolute flex flex-col justify-center items-center w-full h-screen offscreen-right transition-transform duration-1000">
        <h1 class="text-2xl mb-8 font-sans text-center">Agenda de la salle R1</h1>
        <iframe id="salle_r1" title="Calendar"
                src="https://outlook.office365.com/owa/calendar/ace1c98dfc3941e8a486b53b1580ef52@gpsea.fr/b08c378062ab4fb3b918b318ccd4911d1332831934204548775/calendar.html"
                class="border-2 mb-2 rounded-lg w-4/5 h-4/5">
        </iframe>
    </div>

    <div id="calendar3" class="absolute flex flex-col justify-center items-center w-full h-screen offscreen-right transition-transform duration-1000">
        <h1 class="text-2xl mb-8 font-sans text-center">Agenda de la salle R3</h1>
        <iframe id="salle_r3" title="Calendar"
                src="https://outlook.office365.com/owa/calendar/501d8a3aae5d4475a1f950ca6d20f320@gpsea.fr/2a17fcb966ad4da996a37efb8f3499fa1616275169415754077/calendar.html"
                class="border-2 mb-2 rounded-lg w-4/5 h-4/5">
        </iframe>
    </div>

    <!-- Ajouter calendarX -->

    <script>
        let calendars = [document.getElementById('calendar1'), document.getElementById('calendar2'), document.getElementById('calendar3')]; //Ajouter calendarX
        let current = 0;

        if (localStorage.getItem('currentCalendar')) {
            current = parseInt(localStorage.getItem('currentCalendar'));
        }

        calendars.forEach((calendar, index) => {
            if (index === current) {
                calendar.classList.remove('offscreen-right', 'offscreen-left');
                calendar.classList.add('show');
            } else {
                calendar.classList.add('offscreen-right');
            }
        });


        function switchCalendars() {
            calendars[current].classList.remove('show');
            calendars[current].classList.add('hide-left');

            current = (current + 1) % calendars.length;

            localStorage.setItem('currentCalendar', current);

            calendars[current].querySelector('h1').classList.remove('hide');
            calendars[current].querySelector('iframe').classList.remove('hide');
            calendars[current].classList.remove('offscreen-right', 'offscreen-left', 'hide-left', 'hide-right');
            calendars[current].classList.add('show');
            if (calendars[current + 1] !== undefined && calendars[current + 1] !== null) {
                calendars[current + 1].classList.remove('hide-left');
                calendars[current + 1].querySelector('h1').classList.add('hide');
                calendars[current + 1].querySelector('iframe').classList.add('hide');
                calendars[current + 1].classList.add('hide-right');
            } else {
                calendars[0].classList.remove('hide-left');
                calendars[0].querySelector('h1').classList.add('hide');
                calendars[0].querySelector('iframe').classList.add('hide');
                calendars[0].classList.add('hide-right');
            }
        }

        setInterval(switchCalendars, 10000);
    </script>
</body>
</html>
