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
    <meta http-equiv="refresh" content="1800;URL=../">
    <script>
	if (localStorage.getItem('oldIp')) {
            old_ip = localStorage.getItem('oldIp');
	    document.cookie = "old_ip=" + old_ip + "; SameSite=Lax";
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
            $y = 1;

            while (isset($parsed_json[strval($y)])) {

                if (strcmp($parsed_json[strval($i)]['ip'], $parsed_json[strval(($y))]['ip']) == 0 && strcmp($i, $y) != 0) {
                    $error = '107';
                    $url = '../error?code=' . urlencode($error);
                    header('Location: ' . $url);
                    exit();
                }

                $y += 1;
            }
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

$tmp_ip = null;
$tmp_ip = $_COOKIE['old_ip'];

if ($tmp_ip === null) {
    $i = check_device($parsed_json, $ip);
} else {
    $i = check_device($parsed_json, $tmp_ip);
    if ($i === null) {
        $i = check_device($parsed_json, $ip);
    } else {
        replace_ip($i, $ip, $parsed_json);
    }
}

if ($i === null) {
    $error = '101';
    $url = '../error?code=' . urlencode($error);
    header('Location: ' . $url);
    exit();
}

//  PARSING CONFIG OF DEVICE  \\

$title = $parsed_json[$i]['title_site'];
$interval = intval($parsed_json[$i]['interval']);
$urls = $parsed_json[$i]['url_agenda'];
$names = $parsed_json[$i]['name_agenda'];

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

        .hide { display: none; }
        .show { display: flex; transform: translateX(0); }
        .hide-left { transform: translateX(-100%); }
        .hide-right { transform: translateX(100%); }
        .offscreen-right { transform: translateX(100%); }
        .offscreen-left { transform: translateX(-100%); }
    </style>
</head>
<body class="h-screen overflow-hidden">
    <?php
        foreach ($urls as $index => $url) {
            $name = htmlspecialchars($names[$index]);
            $id = "calendar" . ($index + 1);
            echo <<<HTML
            <div id="$id" class="absolute flex flex-col justify-center items-center w-full h-screen offscreen-right transition-transform duration-1000 m-auto">
                <h1 class="text-2xl mb-8 font-sans text-center">$name</h1>
                <iframe title="Calendar" sandbox="allow-same-origin allow-scripts allow-pointer-lock"
                        src="$url" class="border-2 rounded-lg w-4/5 h-4/5">
                </iframe>
            </div>
            HTML;
        }
    ?>

    <script>
        let calendars = [];
        <?php
            foreach ($urls as $index => $url) {
                $id = "calendar" . ($index + 1);
                echo "calendars.push(document.getElementById('$id'));\n";
            }
        ?>
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
