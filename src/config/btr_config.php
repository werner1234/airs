<?php

$headers = getallheaders();
$envApiUrl = getenv("AIRS_CONFIG__API_URL");
$envFrontendUrl = getenv("AIRS_CONFIG__FRONTEND_URL");

// Set frontend enabled or disabled based on url parameters.
// Remove header var if new frontend is not enabled
$envFrontendEnabled = false;
if ( ! empty($envFrontendUrl) && ! empty($envApiUrl) ) {
  $envFrontendEnabled = true;
} else {
  $headers = array();
}

$__BTR_CONFIG["FRONTEND_ENABLED"] = $envFrontendEnabled;
$__BTR_CONFIG["API_URL"] = !empty($envApiUrl) ? $envApiUrl : "http://host.docker.internal:3300";
$__BTR_CONFIG["FRONTEND_URL"] = !empty($envFrontendUrl) ? $envFrontendUrl : "http://127.0.0.1:4200";

// Set this variable if the request is from the new frontend with a new frontend form
$isBTR = array_key_exists('Btr', $headers) || array_key_exists('btr', $headers);
$__BTR_CONFIG["IS_NEW_FRONTEND_REQUEST"] = $isBTR;
$__BTR_CONFIG["REMOVE_QUERY_LIMIT"] = $isBTR;
$__BTR_CONFIG["CUSTOM_HTML_ELEMENTS"] = $isBTR;
$__BTR_CONFIG["CUSTOM_NAWONLY_TEMPLATE"] = $isBTR;

// convert utf-8 to win-1252 for encoding issues
if ( $isBTR ) {
  foreach ( $_POST as $key => $value ) {
    if (gettype($value) === 'string')
    {
        $_POST[$key] = mb_convert_encoding($value, "windows-1252", 'UTF-8');
    }
  }
  foreach ( $_GET as $key => $value ) {
    if (gettype($value) === 'string')
    {
        $_GET[$key] = mb_convert_encoding($value, "windows-1252", 'UTF-8');
    }
  }
}