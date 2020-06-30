<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.beez3
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

JLoader::import('joomla.filesystem.file');

// {DST
$doc = JFactory::getDocument();
$doc->setGenerator(null);
// }DST

// Check modules
$showRightColumn = ($this->countModules('position-3') or $this->countModules('position-6') or $this->countModules('position-8'));
$showbottom      = ($this->countModules('position-9') or $this->countModules('position-10') or $this->countModules('position-11'));
$showleft        = ($this->countModules('position-4') or $this->countModules('position-7') or $this->countModules('position-5'));

if ($showRightColumn == 0 and $showleft == 0)
{
	$showno = 0;
}

//JHtml::_('behavior.framework', true);

// Get params
$color          = $this->params->get('templatecolor');
$logo           = $this->params->get('logo');
$navposition    = $this->params->get('navposition');
$headerImage    = $this->params->get('headerImage');
$app            = JFactory::getApplication();
$templateparams = $app->getTemplate(true)->params;
$config         = JFactory::getConfig();
$bootstrap      = explode(',', $templateparams->get('bootstrap'));
$jinput         = JFactory::getApplication()->input;
$option         = $jinput->get('option', '', 'cmd');

// Param 2 Variable
$change_content_width = $templateparams->get('change_content_width');
$content_width = $templateparams->get('content_width');

// Output as HTML5
$this->setHtml5(true);

// {DST
/*if (in_array($option, $bootstrap))
{
	// Load optional rtl Bootstrap css and Bootstrap bugfixes
	JHtml::_('bootstrap.loadCss', true, $this->direction);
}*/
//$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/jui/jquery.min.js');
// }DST

$this->addStyleSheet($this->baseurl . '/templates/system/css/system.css');
// {DST
$this->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/jui/bootstrap.min.css');
$this->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/style.css?v=87');
$this->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/style-newmenu.css?v=107');
//$this->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/style-newmenu_half_50.css?v=115');
//$this->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/style-newmenu_half_100.css?v=121');
$this->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/top-menu.css?v=1');
?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
<?php
$this->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/owl.carousel.min.css?v=1');
// }DST
$this->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/position.css?v=1', 'text/css', 'screen');
$this->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/layout.css?v=7', 'text/css', 'screen');
$this->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/print.css', 'text/css', 'print');
$this->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/general.css?v=1', 'text/css', 'screen');
$this->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/' . htmlspecialchars($color, ENT_COMPAT, 'UTF-8') . '.css', 'text/css', 'screen');

if ($this->direction == 'rtl')
{
	$this->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/template_rtl.css');
	if (file_exists(JPATH_SITE . '/templates/' . $this->template . '/css/' . htmlspecialchars($color, ENT_COMPAT, 'UTF-8') . '_rtl.css'))
	{
		$this->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/' . htmlspecialchars($color, ENT_COMPAT, 'UTF-8') . '_rtl.css');
	}
}

if ($color == 'image')
{
	$this->addStyleDeclaration("
	.logoheader {
		background: url('" . $this->baseurl . "/" . htmlspecialchars($headerImage) . "') no-repeat right;
	}
	body {
		background: " . $templateparams->get('backgroundcolor') . ";
	}");
}

// Check for a custom CSS file
$userCss = JPATH_SITE . '/templates/' . $this->template . '/css/user.css';

if (file_exists($userCss) && filesize($userCss) > 0)
{
	$this->addStyleSheetVersion($this->baseurl . '/templates/' . $this->template . '/css/user.css');
}

// {DST
/*JHtml::_('bootstrap.framework');*/
$this->addStyleSheetVersion($this->baseurl . '/templates/' . $this->template . '/css/all.css');
//$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/jui/jquery.min.js');
// }DST
if($templateparams->get('fontsizeselect')){
	$this->addScript($this->baseurl . '/templates/' . $this->template . '/javascript/md_stylechanger.js');
}
$this->addScript($this->baseurl . '/templates/' . $this->template . '/javascript/hide.js');
$this->addScript($this->baseurl . '/templates/' . $this->template . '/javascript/respond.src.js');
$this->addScript($this->baseurl . '/templates/' . $this->template . '/javascript/template.js');
// {DST
$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/jui/bootstrap.min.js');
$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/scripts.js?v=34');
$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/wow.min.js?v=1');
$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/owl.carousel.min.js?v=1');
// }DST
require __DIR__ . '/jsstrings.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=3.0, user-scalable=yes"/>
		<meta name="HandheldFriendly" content="true" />
		<meta name="apple-mobile-web-app-capable" content="YES" />
		<jdoc:include type="head" />
		<?php // {DST ?>
		<!-- Google Tag Manager -->
		<script>
		(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
		j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
		'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer','GTM-KFQ4766');
		</script>
		<!-- End Google Tag Manager -->
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600&amp;subset=cyrillic-ext" rel="stylesheet">
		<?php // }DST ?>
		<!--[if IE 7]><link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/ie7only.css" rel="stylesheet" /><![endif]-->
		<!--[if lt IE 9]><script src="<?php echo JUri::root(true); ?>/media/jui/js/html5.js"></script><![endif]-->
	</head>
	<body id="shadow" >
		<?php // {DST ?>
		<!-- Google Tag Manager (noscript) -->
		<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KFQ4766"
		height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
		<!-- End Google Tag Manager (noscript) -->
		<?php // }DST ?>
		<div id="all" <?php /* if ($templateparams->get('change_content_width')) {
			echo ' style="max-width: ' . $this->params->get('content_width') . 'px"';
			} */ ?>
			class=" bg-g rey"
			>
			<div id="back " >
				<header id="header">
					<?php if ($this->params->get('fontsizeselect') OR $this->countModules('positon-0')) { ?>
					<div id="line">
						<?php if ($templateparams->get('fontsizeselect')) { ?>
						<div id="fontsize"></div>
						<?php } ?>
						<h3 class="unseen"><?php echo JText::_('TPL_BEEZ3_SEARCH'); ?></h3>
						<jdoc:include type="modules" name="position-0" />
					</div>
					<?php } ?>
					<?php if ($templateparams->get('main_menu_position') == 'main_menu_top') { ?>
						<jdoc:include type="modules" name="position-1" />
					<?php } ?>

					<ul class="skiplinks">
						<li><a href="#main" class="u2"><?php echo JText::_('TPL_BEEZ3_SKIP_TO_CONTENT'); ?></a></li>
						<li><a href="#nav" class="u2"><?php echo JText::_('TPL_BEEZ3_JUMP_TO_NAV'); ?></a></li>
						<?php if ($showRightColumn) : ?>
							<li><a href="#right" class="u2"><?php echo JText::_('TPL_BEEZ3_JUMP_TO_INFO'); ?></a></li>
						<?php endif; ?>
					</ul>
					<h2 class="unseen"><?php echo JText::_('TPL_BEEZ3_NAV_VIEW_SEARCH'); ?></h2>
					<h3 class="unseen"><?php echo JText::_('TPL_BEEZ3_NAVIGATION'); ?></h3>
					<?php if ($templateparams->get('main_menu_position') == 'main_menu_bottom') { ?>
						<jdoc:include type="modules" name="position-1" />
					<?php } ?>
				</header><!-- end header -->
				<div id="<?php echo $showRightColumn ? 'contentarea2' : 'contentarea'; ?>">
					<div id="breadcrumbs" class="container block-size">
						<jdoc:include type="modules" name="position-2" />
					</div>
					<div class="bg-grey">


						
						
					<div class="container block-size">
					<div class="row bg-grey">
						<div class="col-xs-12 col-md-3 col-lg-2 row-flex">
<!--							<div class="hidden-xs hidden-sm">-->
							<div class="filter-module">
							<jdoc:include type="modules" name="position-15" />
							</div>
<!--
							<div class="hidden-sm hidden-md hidden-lg">
								<button type="button" id="filter-mobile" class="btn btn-success btn-lg btn-block">ФИЛЬТР</button>
								<div id="filter-mobile-content" style="display:none">
									<jdoc:include type="modules" name="position-15" />
								</div>
							</div>
-->
						</div>
						<div class="col-xs-12 col-md-9 col-lg-10 row-flex">
							<div id="breadcrumbs" class="container block-size">
								<jdoc:include type="modules" name="position-4" />
							</div>
							<jdoc:include type="component" />
						</div>
					</div>
					</div></div>
					<?php
					$r = $templateparams->get('nav2cont_ratio','25');
					// {DST
					// if ($navposition == 'left' and $showleft) :
					// }DST
					if ($navposition == 'left' and $showleft and false) :
						?>
						<nav class="left1"  <?php if ($showRightColumn == null) { echo 'style="width:'.$r.'%;min-width:100px" ';} ?> id="nav">
							<jdoc:include type="modules" name="position-7" style="beezDivision" headerLevel="3" />
							<jdoc:include type="modules" name="position-4" style="beezHide" headerLevel="3" state="0 " />
							<jdoc:include type="modules" name="position-5" style="beezTabs" headerLevel="2"  id="3" />
						</nav><!-- end navi -->
					<?php endif; ?>
					<?php 
					// {DST
					if (false) :
					// }DST
					?>
					<div id="<?php echo $showRightColumn ? 'wrapper' : 'wrapper2'; ?>"
					<?php if (isset($showno)){
						echo 'class="shownocolumns" ';
					} else {
						echo 'style="width:'.(96-$r).'%;min-width:200px" ';
					}?>>
						<div id="main">

							<?php if ($this->countModules('position-12')) : ?>
								<div id="top">
									<jdoc:include type="modules" name="position-12" />
								</div>
							<?php endif; ?>

							<jdoc:include type="message" />
							<?php 
							/* {DST */ 
							 //type="component" 
							/* DST 	*/
							?>
							
						</div><!-- end main -->
					</div><!-- end wrapper -->
					<?php
					// {DST
					endif; 
					// }DST
					?>

					<?php if ($showRightColumn) : ?>
						<div id="close">
							<a href="#" onclick="auf('right')">
							<span id="bild">
								<?php echo JText::_('TPL_BEEZ3_TEXTRIGHTCLOSE'); ?>
							</span>
							</a>
						</div>

						<aside id="right">
							<h2 class="unseen"><?php echo JText::_('TPL_BEEZ3_ADDITIONAL_INFORMATION'); ?></h2>
							<jdoc:include type="modules" name="position-6" style="beezDivision" headerLevel="3" />
							<jdoc:include type="modules" name="position-8" style="beezDivision" headerLevel="3" />
							<jdoc:include type="modules" name="position-3" style="beezDivision" headerLevel="3" />
						</aside><!-- end right -->
					<?php endif; ?>

					<?php if ($navposition == 'center' and $showleft) : ?>
						<nav class="left <?php if ($showRightColumn == null) { echo 'leftbigger'; } ?>" id="nav" >

							<jdoc:include type="modules" name="position-7"  style="beezDivision" headerLevel="3" />
							<jdoc:include type="modules" name="position-4" style="beezHide" headerLevel="3" state="0 " />
							<jdoc:include type="modules" name="position-5" style="beezTabs" headerLevel="2"  id="3" />

						</nav><!-- end navi -->
					<?php endif; ?>

					<div class="wrap"></div>
				</div> <!-- end contentarea -->
			</div><!-- back -->
		</div><!-- all -->

		<div id="footer-outer">
			<?php if ($showbottom) : ?>
				<div id="footer-inner" >
					<div id="bottom">
						<div class="box box1"> <jdoc:include type="modules" name="position-9" style="beezDivision" headerlevel="3" /></div>
						<div class="box box2"> <jdoc:include type="modules" name="position-10" style="beezDivision" headerlevel="3" /></div>
						<div class="box box3"> <jdoc:include type="modules" name="position-11" style="beezDivision" headerlevel="3" /></div>
					</div>

				</div>
			<?php endif; ?>
			<?php if ($this->countModules('position-14') and FALSE ) { ?>
				<div id="footer-sub">
					<footer id="footer">
<!--						<jdoc:include type="modules" name="position-14" />-->
					</footer><!-- end footer -->
				</div>
			<?php } ?>
		</div>
		<jdoc:include type="modules" name="position-14" />
		<jdoc:include type="modules" name="debug" />
	</body>
</html>
