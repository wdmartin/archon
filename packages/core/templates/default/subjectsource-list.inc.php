<?php

isset($_ARCHON) or die();

//echo print_r($_REQUEST) ;
//echo print_r($_ARCHON);


// echo print_r($arrCountries);

if ($_REQUEST['apilogin'] && $_REQUEST['apipassword']) {
    if (!$_ARCHON->Security->verifyCredentials($_REQUEST['apilogin'], $_REQUEST['apipassword'])) {
        $_ARCHON->declareError("Authentication Failed");
    }
    if (!$_ARCHON->Error) {
        echo json_encode($_ARCHON->getAllSubjectSources());
    } else {
        echo "Authentication Failed";
    }
} else {
    echo "Please provide Username and Password";
}




?>