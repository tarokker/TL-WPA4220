<?php

// powerline config
if ($argc !== 3) {
    echo "Invalid arguments.\nSyntax: php " . $argv[0] . " powerline_ip powerline_password\nExample: php " . $argv[0] . " 192.168.0.4 myadminpassword\n";
    exit(1);
}

$ip = $argv[1];
$pwd = $argv[2];

// get tokens
$ret=callUrlWithParams($ip, "?code=7&asyn=1");

$ptokens = explode("\r\n", chop($ret));

if ( count($ptokens) != 6 || $ptokens[0] != "00007" || $ptokens[1] != "00004" || $ptokens[2] != "00000" ) {
    echo "Invalid tokens reply: $ret\n";
    exit(2);
}

// calculate the token id
$encpwd=encrypt($pwd);
$mid=encrypt($ptokens[3], $encpwd, $ptokens[4]);

// perform the login
$ret=callUrlWithParams($ip, "?code=7&asyn=0&id=" . encodeURIComponent($mid));

$ptokens = explode("\r\n", chop($ret));
if ( count($ptokens) != 1 || $ptokens[0] != "00000" ) {
    echo "Invalid login reply: $ret\n";
    exit(3);
}

// perform the reboot
$ret=callUrlWithParams($ip, "?code=6&asyn=1&id=" . encodeURIComponent($mid));

$ptokens = explode("\r\n", chop($ret));
if ( count($ptokens) != 1 || $ptokens[0] != "00000" ) {
    echo "Invalid reboot reply: $ret\n";
    exit(4);
}

echo "Success!\n";

exit(0);

//////////// functions
function encodeURIComponent($str) {
    $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
    return strtr(rawurlencode($str), $revert);
}

function callUrlWithParams($ip, $geturi, $payload = NULL) {
    $url = "http://$ip/";
    $referer = "http://$ip/";
    $userAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36';

    $fullUrl = $url . $geturi;
    echo "Calling url: $fullUrl\n";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $fullUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);

    if ($payload !== NULL) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Referer: ' . $referer,
        'User-Agent: ' . $userAgent
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return false;
    }

    curl_close($ch);
    return $response;
}

function encrypt($e, $t = "RDpbLfCPsJZ7fiv", $r = "yLwVl0zKqws7LgKPRQ84Mdt708T1qQ3Ha7xv3H7NyU84p21BriUWBU43odz3iP4rBL3cD02KZciXTysVXiV8ngg6vL48rPJyAUw0HurW20xqxv9aYb4M9wK1Ae0wlro510qXeU07kV57fQMc8L6aLgMLwygtc0F10a0Dg70TOoouyFhdysuRMO51yY5ZlOZZLEal1h0t9YQW0Ko7oBwmCAHoic4HYbUyVeU3sfQ1xtXcPcf1aT303wAQhv66qzW") {
    $n = strlen($e);
    $a = strlen($t);
    $s = strlen($r);
    $i = max($n, $a);

    $l = '';
    for ($h = 0; $h < $i; $h++) {
        $d = 187;
        $u = 187;

        if ($n <= $h) {
            $d = ord($t[$h]);
        } elseif ($a <= $h) {
            $u = ord($e[$h]);
        } else {
            $u = ord($e[$h]);
            $d = ord($t[$h]);
        }

        $l .= $r[($u ^ $d) % $s];
    }

    return $l;
}
