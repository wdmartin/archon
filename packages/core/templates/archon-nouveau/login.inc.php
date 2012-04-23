<?php
/**
 * Login template
 *
 *
 * The Archon API is available through the variable:
 *
 *  $_ARCHON
 *
 * Refer to the Archon class definition in lib/archon.inc.php
 * for available properties and methods.
 *
 * @package Archon
 * @author Will Martin
 */

isset($_ARCHON) or die();

$parts = explode(' | ', $strPageTitle);
$heading = $parts[0];
?>
<h2><?php print $heading; ?></h2>

<p><?php echo($registerButton); ?></p>
<p>OR</p>
<div class='researchformbox bground'>

<?php echo($form); ?>

  <div id="userformsubmit">
    <?php echo($strSubmitButton); ?>
  </div>

<br />
<p class="center"><a href="?p=core/privacy"><?php echo($strPrivacyNote); ?></a></p>
</div>

