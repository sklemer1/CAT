<?php

/*
 * ******************************************************************************
 * Copyright 2011-2017 DANTE Ltd. and GÉANT on behalf of the GN3, GN3+, GN4-1 
 * and GN4-2 consortia
 *
 * License: see the web/copyright.php file in the file structure
 * ******************************************************************************
 */
require_once(dirname(dirname(__DIR__)) . "/config/_config.php");

$therealm = filter_input(INPUT_GET, 'realm', FILTER_SANITIZE_STRING);
$thevisited = filter_input(INPUT_GET, 'visited', FILTER_SANITIZE_STRING);

if ($therealm !== FALSE && $thevisited !== FALSE) {
    $validatedRealm = $validator->realm($therealm);
    if ($validatedRealm === FALSE) {
        throw new Exception("That realm looked suspicious.");
    }
    $telepath = new \core\diag\Telepath($validatedRealm, $_GET['visited']);
    $validator = new \web\lib\common\InputValidation();
    
    echo "<pre>";
    echo "Testing " . $validatedRealm . " in " . $validator->string($thevisited);
    print_r($telepath->magic());
    echo "</pre>";
}