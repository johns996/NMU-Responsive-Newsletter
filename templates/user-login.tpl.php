<?php
$strBaseURL = $GLOBALS['base_url'];
$strURL = $strBaseURL.'/saml_login';
?>
<style>
#drupal_tabs{display:none;}
</style>
<h2>NMU Content Management System</h2>
<p>Welcome to the NMU Content Management System (CMS).  Access to websites stored within the CMS is limited to authorized users.
Before access to any site is granted, a CMS training session must be completed first.  To request a training session and
access to a site, please contact <a href="mailto:ericjohn@nmu.edu">Eric Johnson</a>.</p>
<div id="cms_login">
	<strong>Authorized Users:</strong><br />
	<a href="<?php print $strURL; ?>">Click here to log into the NMU CMS</a>
</div>

<?php
$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
$arrBossIPs = array('ejmac.nmu.edu',		//198.110.203.106
					'ericjohnpc.nmu.edu',	//198.110.203.203
					'ejimac.nmu.edu',		//198.110.203.107
					//'ericjohn.nmu.edu',	//198.110.203.105 - this was my old lenovo IP address before network issues required that it be changed
					//'ericjohn.nmu.edu',	//198.110.200.158 - this is my new lenovo IP address
					'aquinn.nmu.edu',		//198.110.203.200
					'aqimac.nmu.edu',		//198.110.203.196
					'aqmini.nmu.edu');		//198.110.203.197

if (in_array($hostname, $arrBossIPs) || $_SERVER['HTTP_HOST'] !== 'www.nmu.edu')  //this is going to show the boss login on any server except charlie.  charlie will only show it for the IPs listed above
{
	echo '<br /><div class="boss_login">';
	print $rendered;
	echo '</div>';
}

?>
