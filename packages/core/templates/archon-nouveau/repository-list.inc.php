<?php

isset($_ARCHON) or die();
echo json_encode( $_ARCHON->getAllRepositories());
?>
