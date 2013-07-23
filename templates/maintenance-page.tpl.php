<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php print $language->language ?>" lang="<?php print $language->language ?>" dir="<?php print $language->dir ?>">
  <head>
    <title><?php print $head_title; ?></title>
    <?php print $head; ?>
    <meta name="viewport" content="width=device-width">
	<link type="text/css" rel="stylesheet" href="/sites/all/themes/omega_nmu/css/modules/maintenance.css" />
  </head>
  <body>
<div id="page">
	<div id="page_wrapper">
		  <?php print $page_top; ?>

		  <div id="branding">
			<img src="/sites/all/themes/nmu/images/nmu_logo_black.png" align="right" width="151" height="57" />
		  </div>

			<div id="content" class="clearfix">
			  <?php if ($title): ?><h1 class="page-title"><?php print $title; ?></h1><?php endif; ?>
			  <?php /*
			  //this stuff is not currently used
			  <?php if ($messages): ?>
				<div id="console"><?php print $messages; ?></div>
			  <?php endif; ?>
			  <?php if ($help): ?>
				<div id="help">
				  <?php print $help; ?>
				</div>
			  <?php endif; ?>
			  */ ?>
			  <p><?php print $content; ?></p>
			</div>

		  <?php print $page_bottom; ?>
	</div>
</div><!-- end content -->
  </body>
</html>
