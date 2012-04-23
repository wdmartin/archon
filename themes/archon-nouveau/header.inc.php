<?php
/**
 * Header file for Archon Nouveau theme
 *
 * @package Archon
 * @author Will Martin
 */
isset($_ARCHON) or die();

if($_ARCHON->Script == 'packages/collections/pub/findingaid.php')
{
   require("faheader.inc.php");
   return;
}

$_ARCHON->PublicInterface->Header->OnLoad .= "";

if($_ARCHON->Error)
{
   $_ARCHON->PublicInterface->Header->OnLoad .= " alert('" . encode(str_replace(';', "\n", $_ARCHON->processPhrase($_ARCHON->Error)), ENCODE_JAVASCRIPT) . "');";
}

if(defined('PACKAGE_COLLECTIONS'))
{

   if($objCollection->Repository)
   {
      $RepositoryName = $objCollection->Repository->getString('Name');
   }
   elseif($objDigitalContent->Collection->Repository)
   {
      $RepositoryName = $objDigitalContent->Collection->Repository->getString('Name');
   }
   else
   {
      $RepositoryName = $_ARCHON->Repository ? $_ARCHON->Repository->getString('Name') : '';
   }

   $_ARCHON->PublicInterface->Title = $_ARCHON->PublicInterface->Title ? $_ARCHON->PublicInterface->Title . ' | ' . $RepositoryName : $RepositoryName;

   if($_ARCHON->QueryString && $_ARCHON->Script == 'packages/core/pub/search.php')
   {
      $_ARCHON->PublicInterface->addNavigation("Search Results For \"" . $_ARCHON->getString(QueryString) . "\"", "?p=core/search&amp;q=" . $_ARCHON->QueryStringURL, true);
   }
}
else
{
   $RepositoryName = $_ARCHON->Repository ? $_ARCHON->Repository->getString('Name') : 'Archon';

   $_ARCHON->PublicInterface->Title = $_ARCHON->PublicInterface->Title ? $_ARCHON->PublicInterface->Title . ' | ' . $RepositoryName : $RepositoryName;

   if($_ARCHON->QueryString)
   {
      $_ARCHON->PublicInterface->addNavigation("Search Results For \"" . encode($_ARCHON->QueryString, ENCODE_HTML) . "\"", "?p=core/search&amp;q=" . $_ARCHON->QueryStringURL, true);
   }
}

$_ARCHON->PublicInterface->addNavigation('Archon', 'index.php', true);

$message = false;
if($_ARCHON->PublicInterface->Header->Message && $_ARCHON->PublicInterface->Header->Message != $_ARCHON->Error)
{
	$message = '<div class="message">' . $_ARCHON->PublicInterface->Header->Message, ENCODE_HTML) . "</div>\n";
}

$search_list = false;
if(defined('PACKAGE_COLLECTIONS') && CONFIG_COLLECTIONS_SEARCH_BOX_LISTS)
{
	$search_list = '<input type="hidden" name="content" value="1" />';
}


$page_title = strip_tags($_ARCHON->PublicInterface->Title);

$theme_path = $_ARCHON->PublicInterface->Theme;
$image_path = $_ARCHON->PublicInterface->ImagePath;
$js_path = $_ARCHON->PublicInterface->ThemeJavascriptPath;

$onLoad = $_ARCHON->PublicInterface->Header->OnLoad;
$onUnload = $_ARCHON->PublicInterface->Header->OnUnload;

$search_value = encode($_ARCHON->QueryString, ENCODE_HTML);

?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title><?php print $page_title; ?></title>

	<link rel="shortcut icon" href="themes/<?php print $image_path; ?>/favicon.ico"/>

	<link rel="stylesheet" type="text/css" href="themes/<?php print $theme_path; ?>/style.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="themes/<?php print $theme_path; ?>/print.css" media="print" />

	<!--[if lte IE 8]>
		<link rel="stylesheet" type="text/css" href="themes/<?php print $theme_path; ?>/ie.css" />
	<![endif]-->

	<script type="text/javascript" src="<?php print $js_path; ?>/jquery-1.7.2.min.js"></script>
<!--
<?php echo($_ARCHON->getJavascriptTags('archon')); ?>
-->
	<script type="text/javascript">
		/* <![CDATA[ */
		$(document).ready(function() {<?php print $onLoad; ?>});
		$(window).unload(function() {<?php print $onUnload; ?>});
		/* ]]> */
	</script>
</head>
<body>

<?php if($message){ print $message; } ?>

<div id="main">
<header>
<img src="themes/<?php print $image_path; ?>/archon-logo.png" alt="Archon" width="322" height="70" />
	<nav>
		<ul>

			<li class="last">
				<form action="index.php" accept-charset="UTF-8" method="get" id="archon-search-form">
				<label for="archon-search" class="visually-hidden">Search Finding Aids</label>
				<input
					type="search"
					size="25"
					title="search"
					maxlength="150"
					name="q"
					id="archon-search"
					class="search-box"
					placeholder="Search Finding Aids"
					value="<?php print $search_value; ?>"
				/>

				<input type="hidden" name="p" value="core/search" />
				<?php if($search_list){ print $search_list; } ?>
				
				<input type="submit" value="Search" />
				</form>
			</li>
		</ul>
	</nav>
</header>

<section>
<h2>Alfredus Rex &amp; Cernis Panes</h2>
<p>Stuff</p>
</section>

<footer>
<p>&copy; Footer Stuff</p>
<p>Marble background image courtesy of <a href="http://www.spiralgraphics.biz/packs/marble/index.htm?21">Spiral Graphics</a></p>
</footer>
</div>

         <div id="researchblock">
            <?php
                     if($_ARCHON->Security->isAuthenticated())
                     {
                        echo("<span class='bold'>Welcome, " . $_ARCHON->Security->Session->User->toString() . "</span><br/>");

                        $logoutURI = preg_replace('/(&|\\?)f=([\\w])*/', '', $_SERVER['REQUEST_URI']);
                        $Logout = (encoding_strpos($logoutURI, '?') !== false) ? '&amp;f=logout' : '?f=logout';
                        $strLogout = encode($logoutURI, ENCODE_HTML) . $Logout;
                        echo("<a href='$strLogout'>Logout</a>");
                     }
                     elseif($_ARCHON->config->ForceHTTPS)
                     {
                        echo("<a href='index.php?p=core/login&amp;go='>Log In</a>");
                     }
                     else
                     {
                        echo("<a href='#' onclick='$(window).scrollTo(\"#archoninfo\"); if($(\"#userlogin\").is(\":visible\")) $(\"#loginlink\").html(\"Log In\"); else $(\"#loginlink\").html(\"Hide\"); $(\"#userlogin\").slideToggle(\"normal\"); $(\"#ArchonLoginField\").focus(); return false;'>Log In</a>");
                     }

                     if(!$_ARCHON->Security->userHasAdministrativeAccess())
                     {
                        $emailpage = defined('PACKAGE_COLLECTIONS') ? "collections/research" : "core/contact";

                        echo(" | <a href='?p={$emailpage}&amp;f=email&amp;referer=" . urlencode($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) . "'>Contact Us</a>");
                        if($_ARCHON->Security->isAuthenticated())
                        {
                           echo(" | <a href='?p=core/account&amp;f=account'>My Account</a>");
                        }
                        if(defined('PACKAGE_COLLECTIONS'))
                        {
                           $_ARCHON->Security->Session->ResearchCart->getCart();
                           $EntryCount = $_ARCHON->Security->Session->ResearchCart->getCartCount();
                           $class = $_ARCHON->Repository->ResearchFunctionality & RESEARCH_COLLECTIONS ? '' : 'hidewhenempty';
                           $hidden = ($_ARCHON->Repository->ResearchFunctionality & RESEARCH_COLLECTIONS || $EntryCount) ? '' : "style='display:none'";

                           echo("<span id='viewcartlink' class='$class' $hidden>| <a href='?p=collections/research&amp;f=cart&amp;referer=" . urlencode($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) . "'>View Cart (<span id='cartcount'>$EntryCount</span>)</a></span>");
                        }
                     }
            ?>
                  </div>


         <?php
                     $arrP = explode('/', $_REQUEST['p']);
                     $TitleClass = $arrP[0] == 'collections' && $arrP[1] != 'classifications' ? 'currentBrowseLink' : 'browseLink';
                     $ClassificationsClass = $arrP[1] == 'classifications' ? 'currentBrowseLink' : 'browseLink';
                     $SubjectsClass = $arrP[0] == 'subjects' ? 'currentBrowseLink' : 'browseLink';
                     $CreatorsClass = $arrP[0] == 'creators' ? 'currentBrowseLink' : 'browseLink';
                     $DigitalLibraryClass = $arrP[0] == 'digitallibrary' ? 'currentBrowseLink' : 'browseLink';
         ?>
                     <div id="browsebyblock">
                        <span id="browsebyspan">
                           Browse:
                        </span>
                        <span class="<?php echo($TitleClass); ?>">
                           <a href="?p=collections/collections" onclick="js_highlighttoplink(this.parentNode); return true;">Collections</a>
                        </span>
                        <span class="<?php echo($DigitalLibraryClass); ?>">
                           <a href="?p=digitallibrary/digitallibrary" onclick="js_highlighttoplink(this.parentNode); return true;">Digital Content</a>
                        </span>
                        <span class="<?php echo($SubjectsClass); ?>">
                           <a href="?p=subjects/subjects" onclick="js_highlighttoplink(this.parentNode); return true;">Subjects</a>
                        </span>
                        <span class="<?php echo($CreatorsClass); ?>">
                           <a href="?p=creators/creators" onclick="js_highlighttoplink(this.parentNode); return true;">Creators</a>
                        </span>
                        <span class="<?php echo($ClassificationsClass); ?>">
                           <a href="?p=collections/classifications" onclick="js_highlighttoplink(this.parentNode); return true;">Record Groups</a>
                        </span>
                     </div>
                  </div>
                  <div id="breadcrumbblock"><span class='bold'>Location: </span><?php echo($_ARCHON->PublicInterface->createNavigation()); ?></div>
      <div id="breadcrumbclearblock">.</div>
      <script type="text/javascript">
         /* <![CDATA[ */
         if ($.browser.msie && parseInt($.browser.version, 10) <= 8){
            $.getScript('packages/core/js/jquery.corner.js', function(){                                                              
               $("#searchblock").corner("5px");
               $("#browsebyblock").corner("tl 10px");
                         
               $(function(){
                  $(".bground").corner("20px");
                  $(".mdround").corner("10px");
                  $(".smround").corner("5px");
                  $("#dlsearchblock").corner("bottom 10px");
               });
            });
         }
         /* ]]> */
      </script>
      <div id="main">