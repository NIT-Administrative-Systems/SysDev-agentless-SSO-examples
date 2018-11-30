<?php
$netid = 'not authenticated';
$webSSOApi = 'https://websso.it.northwestern.edu/amserver/identity/attributes';

/**
 * Send the user to the online passport login page.
 */
function redirectToLogin()
{
    // Get this page's URL
    $protocol = 'http';
    if (array_key_exists('HTTPS', $_SERVER) == true)
    {
        $protocol = 'https';
    }

    $redirect = urlencode($protocol . '://' . $_SERVER['SERVER_NAME'] . '/' . $_SERVER['REQUEST_URI']);

    header("Location: https://websso.it.northwestern.edu/amserver/UI/Login?goto=$redirect");
    exit;
} // end getPageUrl

/**
 * Get the value of a cookie, if it exists.
 */
function getCookieValue($name)
{
    $token = null;
    if (array_key_exists($name, $_COOKIE) == true)
    {
        $token = $_COOKIE[$name];
    }

    return $token;
} // end getCookieValue

/**
 * Extracts the netID from the websso API response, if available.
 */
function getNetid($payload)
{
    $seek = 'userdetails.attribute.value=';

    foreach (explode("\n", $payload) as $line)
    {
        if (substr($line, 0, strlen($seek)) == $seek)
        {
            return trim(substr($line, strlen($seek)));
        }
    }

    return null;
} // end getNetid

// Do we have a session?
$token = getCookieValue('openAMssoToken');
if ($token == null)
{
    redirectToLogin();
}

/*
* Is the session valid?
*
* You can use any HTTP library you want here.
* I'm using stream_context_create since it's a PHP built-in,
* but I recommend using Guzzle instead!
*/
$context  = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header'=> "Content-type: application/x-www-form-urlencoded\r\n",
        'content' => http_build_query([
            'subjectid' => $token,
            'attributenames' => 'UserToken',
        ]),
    ]
]);

$result = file_get_contents($webSSOApi, false, $context);
if ($result === FALSE)
{
    // Invalid token
    redirectToLogin();
}
else
{
    $netid = getNetid($result);
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

  </head>
  <body>
    <h1>Hello, world!</h1>
    <p>I am authenticated as <strong id='netid'><?= $netid; ?></strong>!</p>
    <p>If you want to see how to add Duo MFA, they have an example in their <a href='https://github.com/duosecurity/duo_php/tree/master/demos'>PHP SDK</a>.</p>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  </body>
</html>
