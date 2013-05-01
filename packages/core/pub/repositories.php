<?php

/**
 * Output json Repositorieslist 
 *
 * @package Archon
 * @author Randy Taylor
 */

isset($_ARCHON) or die();

 if($_REQUEST['archonlogin'] && $_REQUEST['archonpassword'])
        {
            if(!$this->verifyCredentials($_REQUEST['archonlogin'], $_REQUEST['archonpassword'], 0))
            {
                $_ARCHON->declareError("Authentication Failed");
         
            }
            
            
            
        }
        else
        {
            
           // $_ARCHON->declareError("Authentication Failed");

        }
        if (!$_ARCHON->Error) {
                 eval($_ARCHON->PublicInterface->Templates['core']['Repositorylist']);
          }
        unset($_REQUEST['archonlogin']);
        unset($_REQUEST['archonpassword']);
   

?>
