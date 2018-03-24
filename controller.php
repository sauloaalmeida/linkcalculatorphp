<?php

include 'Portal.php';

$portal = null;

try {
    $portal = new Portal($_POST["portal_setup"]);
    
    echo $portal->getLinkRangeInMeters() . " m";
    echo "--";
    echo $portal->getLinkRangeInKilometers()  . " km";
    
} catch (Exception $e) {
    
    //Error Handling
    switch ($e->getMessage()) {
        case Portal::EXCEPTION_INVALID_PORTAL_SETUP:
            echo "Invalid portal setup. Correct is: bla bla bla";
            break;
        case Portal::EXCEPTION_INVALID_AMOUNT_OF_MODS:
            echo "We only have four slots for MODs";
            break;
        default:
            echo $e->getMessage();
            break;
    }
        
}



?>