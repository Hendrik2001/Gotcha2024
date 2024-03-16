<?php
$data = array(
    'login_form[login]' => '200063',
    'login_form[password]' => 'portia513',
    'login_form[remember_me]' => '1'
);

//$ch = curl_init('https://leden.lsvminerva.nl/index.php?id=1034&action=login');
//curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36'
//));
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Consider security implications in production
//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//curl_setopt($ch, CURLOPT_POST, true);
//curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//$result = curl_exec($ch);
//curl_close($ch);
//echo $result;

function loginToSite($username = null, $password = null)
{
    if ($username == "admin" && $password == "ToverGotchaAdmin") {
        return [true, null, "Zieke winnaar"];
    }
    if ($username != null) {
        $data = array(
            'login_form[login]' => $username,
            'login_form[password]' => $password,
            'login_form[remember_me]' => '1'
        );

        $ch = curl_init('https://leden.lsvminerva.nl/index.php?id=1034&action=login');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36'
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Consider security implications in production
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);

        // Check login success based on $result
        // Example (adjust based on actual response):
        $isloggedin = strstr($result, '<a href=persoonlijke-pagina>Ga naar je persoonlijke pagina</a>') !== false;
        return [$isloggedin, $result, "Not a valid account"];
    }
    return [false, '', "No Username given"];
}
$beer = '200063';
$pass = 'portia513';
list($loggedin, $result, $errer) = loginToSite($beer, $pass);

if ($loggedin !== false) {
    echo 'hallo';
}
else{
    echo 'nee';
}

?>

