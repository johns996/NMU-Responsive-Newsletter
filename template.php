<?php

/**
 * @file
 * This file is empty by default because the base theme chain (Alpha & Omega) provides
 * all the basic functionality. However, in case you wish to customize the output that Drupal
 * generates through Alpha & Omega this file is a good place to do so.
 *
 * Alpha comes with a neat solution for keeping this file as clean as possible while the code
 * for your subtheme grows. Please read the README.txt in the /preprocess and /process subfolders
 * for more information on this topic.
 */

 //tell omega to use my custom user-login and user-profile templates
function omega_newsletter_theme() {
	$items = array();
	$items['user_login'] = array(
		'render element' => 'form',
		'template' => 'templates/user-login'
	);
	$items['user_profile'] = array(
		'render element' => 'form',
		'template' => 'templates/user-profile'
	);
	return $items;
}

//write the login form to be used for the 'boss mode' login
function omega_newsletter_preprocess_user_login(&$variables) {
  $variables['login_link'] = t('saml_login');
  $variables['rendered'] = drupal_render_children($variables['form']);
}


function omega_newsletter_preprocess_html(&$vars)
{
	//add a site-specific class to the body tag
	$siteID = str_replace('Drupal', '', $GLOBALS['conf']['syslog_identity']);
	$vars['attributes_array']['class'][] = $siteID;

	//add a conditional stylesheet for IE8 and under
	drupal_add_css(path_to_theme() . '/css/ie-lt-9.css', array(
		'group' => CSS_THEME,
		'browsers' => array('IE' => 'lt IE 9', '!IE' => FALSE),
		'every_page' => TRUE)
	);

	if(isset($GLOBALS['conf']['syslog_identity']))  //use this area to set site-specific stylesheets
	{

		if($GLOBALS['conf']['syslog_identity'] === 'DrupalFinancialAidToolbox')  //match any site with C&M in its identity
		{
			drupal_add_css(drupal_get_path('theme', 'omega_newsletter') . '/css/finaidtoolbox.css');
			$css = drupal_add_css();
			$styles = drupal_get_css($css);
		}
	}


}
