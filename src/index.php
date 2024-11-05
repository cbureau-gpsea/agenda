<?php

$ip = $_SERVER['REMOTE_ADDR'];

//  PARSING JSON FILE  \\

$json = file_get_contents("/etc/agenda/config.json");

if ($json === false) {
    $error = '84';
    $url = '../error?code=' . urlencode($error);
    header('Location: ' . $url);
    exit();
}

$parsed_json = json_decode($json, true);

if ($parsed_json === null) {
    $error = '104';
    $url = '../error?code=' . urlencode($error);
    header('Location: ' . $url);
    exit();
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <script>
	if (localStorage.getItem('oldIp')) {
            old_ip = localStorage.getItem('oldIp');
	    document.cookie = "old_ip = " + old_ip;
	}

	<?= "var ip = \"$ip\";" ?>

	localStorage.setItem("oldIp", ip);
    </script>

<?php

//  FUNCTION TO SEARCH DEVICE  \\

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

//  FUNCTION TO REPLACE OLD IP BY NEW  \\

function replace_ip($i, $ip, $parsed_json)
{
    $parsed_json[strval($i)]['ip'] = $ip;
    $new_ip = json_encode($parsed_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    if (file_put_contents("/etc/agenda/config.json", $new_ip) === false) {
	$error = '102';
    	$url = '../error?code=' . urlencode($error);
    	header('Location: ' . $url);
    	exit();
    }
}

//  COMPARE CURRENT IP WITH IP IN JSON FILE  \\

$i = check_device($parsed_json, $ip);

$tmp_ip = $_COOKIE['old_ip'];

if ($i === null && $tmp_ip === null) {

    $error = '101';
    $url = '../error?code=' . urlencode($error);
    header('Location: ' . $url);
    exit();

} else {

    if ($i != null) {
	$ip_json = $parsed_json[strval($i)]['ip'];
    } else {
	$i = check_device($parsed_json, $tmp_ip);
	if ($i === null) {
            $error = '101';
    	    $url = '../error?code=' . urlencode($error);
    	    header('Location: ' . $url);
    	    exit();
	} else {
            $ip_json = $parsed_json[strval($i)]['ip'];
	    replace_ip($i, $ip, $parsed_json);
	}
    }
}

//  PARSING CONFIG OF DEVICE  \\

$title = $parsed_json[strval($i)]['title_site'];

$interval = intval($parsed_json[strval($i)]['interval']);

$urls = [];

for ($x = 0; $x < count($parsed_json[$i]['url_agenda']); $x++) {
    $urls[$x] = $parsed_json[$i]['url_agenda'][$x];
    $urls[$x + 1] = null;
}

$names = [];

for ($x = 0; $x < count($parsed_json[$i]['name_agenda']); $x++) {
    $names[$x] = $parsed_json[$i]['name_agenda'][$x];
    $names[$x + 1] = null;
}

?>

    <title><?php echo $title; ?></title>
    <link href="css/input.css" rel="stylesheet">
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
    <div id="calendar1" class="absolute flex flex-col justify-center items-center w-full h-screen offscreen-right transition-transform duration-1000 m-auto">
        <h1 class="text-2xl mb-8 font-sans text-center"><?php echo $names[0]; ?></h1>
        <iframe id="salle_venise" title="Calendar" sandbox="allow-same-origin allow-scripts allow-pointer-lock"
                src="<?php echo $urls[0]; ?>" class="border-2 rounded-lg w-4/5 h-4/5">
        </iframe>
    </div>

    <div id="calendar2" class="absolute flex flex-col justify-center items-center w-full h-screen offscreen-right transition-transform duration-1000 m-auto">
        <h1 class="text-2xl mb-8 font-sans text-center"><?php echo $names[1]; ?></h1>
        <iframe id="salle_r1" title="Calendar" sandbox="allow-same-origin allow-scripts allow-pointer-lock"
                src="<?php echo $urls[1]; ?>" class="border-2 rounded-lg w-4/5 h-4/5">
        </iframe>
    </div>

    <div id="calendar3" class="absolute flex flex-col justify-center items-center w-full h-screen offscreen-right transition-transform duration-1000 m-auto">
        <h1 class="text-2xl mb-8 font-sans text-center"><?php echo $names[2]; ?></h1>
        <iframe id="salle_r3" title="Calendar" sandbox="allow-same-origin allow-scripts allow-pointer-lock"
                src="<?php echo $urls[2]; ?>" class="border-2 rounded-lg w-4/5 h-4/5">
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

	<?= "var interval = \"$interval\" * 1000;" ?>

        setInterval(switchCalendars, interval);

    //  HIDE CURSOR IF INACTIVE  \\

        (function() {
	    let timeout;
	    const delay = 5000;

	    function hideCursor() {
        	document.body.style.cursor = 'none';
	    }

	    function displayCursor() {
		document.body.style.cursor = '';
		clearTimeout(timeout);
		timeout = setTimeout(hideCursor, delay);
	    }

	    document.addEventListener('mousemove', displayCursor);

	    timeout = setTimeout(hideCursor, delay);

	})();

    </script>
</body>
</html>
