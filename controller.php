<?php

include 'Portal.php';

$portal = new Portal($_POST["portal_setup"]);

echo $portal->getLinkRangeInMeters();

?>