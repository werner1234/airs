<?php

$action = $_POST['action'];
if( $action !== 'validate')
{
    ?>
        <html>
            <head>
            </head>
            <body>
                <form method="POST" name="validateSessionForm">
                    <input type="hidden" name="token" value="" />
                    <input type="hidden" name="action" value="validate" />
                </form>
                <script>
                    function submitForm(){
                        var currentUrl = document.URL;
	                    urlParts = currentUrl.split('#');
                        document.forms['validateSessionForm'].token.value = (urlParts.length > 1) ? urlParts[1] : null;
                        document.forms['validateSessionForm'].submit();
                    }
                    window.onload = function(){ submitForm() }
                </script>
            </body>
        </html>

    <?
    die();
}

include_once("../config/btr_config.php");

if( file_exists("../config/sanitize-request-check.php") ) {
    include_once("../config/sanitize-request-check.php");
}

// $mnu = New Menu();
header("Access-Control-Allow-Origin: ".$__BTR_CONFIG["FRONTEND_URL"]);

// TODO: get token from header $_SERVER["HTTP_"]
if (!$_POST["token"]) {
    http_response_code(400);
    echo "missing token param in request";
    exit;
}

$info = array();
$curl = curl_init();

curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_HTTPHEADER, array("authorization: bearer " . $_POST["token"]));
curl_setopt($curl, CURLOPT_URL, $__BTR_CONFIG["API_URL"]."/auth/sso/sessionid");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

$result = curl_exec($curl);
$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

if ($http_code !== 200) {
    http_response_code(400);
    echo 'invalid http response code ('.$http_code.') from '.$__BTR_CONFIG["API_URL"].'/auth/sso/sessionid api request';
    exit;
}
$token = json_decode($result);

if (!$token->username) {
    http_response_code(400);
    echo "invalid token response from /auth/sessionid api request: missing username\n" . $result;
    exit;
}

if (!$token->sessionId) {
    http_response_code(400);
    echo "invalid token response from /auth/sessionid api request: missing sessionId\n" . $result;
    exit;
}

$secureCookie = true;
$httpOnlyCookie = true;
$domain = null;

setcookie("username", $token->username, 0, "/", $domain, $secureCookie, $httpOnlyCookie );
//setcookie("PHPSESSID", $token->sessionId, 0, "/", $domain, $secureCookie, $httpOnlyCookie );
setcookie("SSO", "enabled", 0, "/", $domain, $secureCookie, $httpOnlyCookie );

session_id($token->sessionId);
session_start();

curl_close($curl);

// this redirects back to home
http_response_code(200);