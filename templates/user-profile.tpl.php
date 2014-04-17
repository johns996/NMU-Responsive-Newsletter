<script type="text/javascript">
jQuery(document).ready(function($) {
	$("#toggle_cm_users").click(function(){
		$('#show_label').toggle();
		$("#div_cm_users").toggle('slow');
	});
	$("#read_more_link").toggle(function(){
		$('.read_more').slideDown("slow");},
		function(){
		$('.read_more').slideUp("slow");}
		);
	$("#view_all_link").toggle(function(){
		$('#sites_list').slideDown("slow");
		$('#view_all_link').html("Hide all sites");},
		function(){
		$('#sites_list').slideUp("slow");
		$('#view_all_link').html("Show all sites");}
		);
});
</script>
<div id="drupal_user_profile">
<?php
require_once "/htdocs/cmsphp/Admin/Includes/FunctionsCommon.php";
require_once "/htdocs/cmsphp/Admin/Includes/Constants.php";
$classSqlQuery = new SqlDataQueries();
$classSqlQuery->SpecifyDB(Const_connLocalhostHost, Const_connLocalhostDrupalDefaultDB, Const_connLocalhostDrupalUser, Const_connLocalhostDrupalPwd);
$QueryUID = $GLOBALS['user']->uid;
$strBaseURL = $GLOBALS['base_url'];

//sites a user can access
function site_access_check($str_uid, $user_name)
{
	$classSqlQuery = new SqlDataQueries();
	$classSqlQuery->SpecifyDB(Const_connLocalhostHost, Const_connLocalhostDrupalDefaultDB, Const_connLocalhostDrupalUser, Const_connLocalhostDrupalPwd);
	//build two arrays of all of the sites (and site names) a user has access to
		$strQuery = "SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME LIKE 'Drupal%'";
		$aResults = $classSqlQuery->MySQL_Queries($strQuery);
		if(!empty($aResults))
		{
			foreach($aResults as $aRow)
			{
				$strQuery = "SELECT uid FROM ".$aRow['SCHEMA_NAME'].".users_roles WHERE `uid` ='".$str_uid."'";
				$aResults = $classSqlQuery->MySQL_Queries($strQuery);
				if(!empty($aResults))
				{
					$strQuery2 = "SELECT value FROM ".$aRow['SCHEMA_NAME'].".variable WHERE `name` ='syslog_identity' LIMIT 1";
					$aResults2 = $classSqlQuery->MySQL_Queries($strQuery2);
					$str_siteAddress = unserialize($aResults2[0]['value']);
					$str_siteAddress = str_replace('Drupal','',$str_siteAddress);
					$arr_siteAddress[] = strtolower($str_siteAddress);

					$strQuery3 = "SELECT value FROM ".$aRow['SCHEMA_NAME'].".variable WHERE name = 'site_name' LIMIT 1";
					$aResults3 = $classSqlQuery->MySQL_Queries($strQuery3);
					$arr_siteName[] = unserialize($aResults3[0]['value']);
				}
			}
		}
	//print out the list of sites a user can access
	$int_numberOfSites = count($arr_siteAddress);
	if($int_numberOfSites > 0)
	{
		echo '<p>The user <strong>'.$user_name.'</strong> has access to edit the following sites:</p>';
		echo '<ul>';
		if($int_numberOfSites < 5)
		{
			for($x=0; $x<$int_numberOfSites; $x++)
			{
				echo '<li><a href="/'.$arr_siteAddress[$x].'/user">'.$arr_siteName[$x].'</a></li>';
			}
		}
		else
		{
			for($x=0; $x<5; $x++)
			{
				echo '<li><a href="/'.$arr_siteAddress[$x].'/user">'.$arr_siteName[$x].'</a></li>';
			}
			echo '<li><span id="view_all_link">Show all sites</span></li>';
			echo '<div id="sites_list">';
			for($x=5; $x<$int_numberOfSites; $x++)
			{
				echo '<li><a href="/'.$arr_siteAddress[$x].'/user">'.$arr_siteName[$x].'</a></li>';
			}
			echo '</div>';
		}
		echo '</ul><br />';
	}
}

//get the site's name
$q_SiteName = db_query("SELECT value FROM {variable} WHERE name = 'site_name' LIMIT 1")->fetchCol();
if(!empty($q_SiteName))
{
	foreach($q_SiteName as $key => $val)
	{
		$a_SiteName = explode('"',$val);
		$SiteName = $a_SiteName[1];
	}
}
else
	$SiteName = 'Undefined';

if(INT_ByPassLogin)
{
	//boss mode engaged
	//use this for testing new features without letting the world see them
}

if (!node_access('update', 'node/1')) //check to see if the user has access to manage this site
{
	echo '<h2>NMU Content Management System</h2><p>Welcome to the NMU Content Management System.  This system is used to manage many of Northern Michigan University\'s websites.  A list of the sites that your user ID has been authorized to access is provided below.</p>';
	site_access_check($QueryUID, $user->name);
	echo 'Your user ID does not currently have access to the <strong>'.$SiteName.'</strong> site.  To request access this or any other site managed by NMU\'s CMS, please contact <a href="mailto:ericjohn@nmu.edu">Eric Johnson</a> (x2313) in the Communications and Marketing office.  <a href="'.$strBaseURL.'/user/logout">Log Out</a>';
}
else
{
	echo'<h2>NMU Content Management System</h2>',
			'<p>Welcome to the NMU Content Management System (CMS).  Use the links across the top of the page to view and edit this site\'s content. The links below will help you use the CMS and report issues with your website. <a href="'.$strBaseURL.'/user/logout">Log Out</a>';

			$q_UserCreated = db_query("SELECT created FROM {users} WHERE uid = '".$QueryUID."'")->fetchCol();
			$str_OneWeekFromToday = time() - (7 * 24 * 60 * 60); //one week from now

			//analytics (only showing for configured sites)
			$q_AnalyticsReport = db_query("SELECT value FROM {variable} WHERE name = 'google_analytics_reports_profile_id' LIMIT 1")->fetchCol();
			if(!empty($q_AnalyticsReport))
			{
				foreach($q_AnalyticsReport as $key => $val)
				{
					$a_ProfileID = explode('"',$val);
					$ProfileID = $a_ProfileID[1];
				}
				$user_button_ga = '<a href="'.$strBaseURL.'/admin/reports/google-analytics"><img src="/sites/all/themes/nmu/images/user_icons/googleanalytics.png" width="100" height="120" title="Google Analytics for Profile '.$ProfileID.'" style="padding-right:5px;" /></a>';
				//echo '<div class="user_msg new_feature"><p>A Google Analytics module was recently added to the NMU CMS.  Google Analytics is used on the NMU website to track visitor activity on our web pages.  You can view a one-month summary of collected data by clicking on the orange Google Analytics icon below.  The data presented in the report is updated every three days.</p></div>';
			}
			else
			{
				$user_button_ga = '';
			}

				/*
				echo '<div class="user_msg new_feature"><p>This website has recently been converted to use a new theme.  This theme makes a web site respond to a user\'s screen size allowing an improved mobile-web experience.</p>';
				echo '<p>The basic structure of your website and the content within it has not changed.  This theme, however, uses a completely different set of styles to control the look of NMU pages so you may notice some things behaving differently.<p>';
				echo '<p>We have tested all converted sites to ensure nothing was broken by the new theme.  If you find something that does not look right, please email <a href="mailto:ericjohn@nmu.edu">ericjohn@nmu.edu</a> with the URL of the page, a description of the issue and the browser you were using when you noticed the problem.  If something critical has been broken by the theme, please call Eric Johnson (2313) or the Communications and Marketing office (2720) and we will fix the problem or revert your site to the previous theme while we work on a solution.</p></div>';
			*/
				/*
				echo '<div class="user_msg new_feature"><p>If you are having problems editing a web page using the CMS, please follow the steps outlined in <a href="http://www.nmu.edu/node/274">this guide</a> to resolve the issue.</p></div>';
				echo '<div class="user_msg new_feature"><p>Two changes have recently been made to the file manager used by the NMU CMS.  These changes have been made based on user feedback in hopes of improving the usability of the file manager. <span id="read_more_link">Read More</span>
						<div class="user_msg read_more"><p><strong>1.)</strong> When a file is uploaded into a folder already containing a file with the same name as the one being uploaded, the system will now prompt the user and ask if they want to replace the file on the server with the one being uploaded.  If "yes" is selected, the file will be replaced and all links to the file will now direct users to the updated version.  If "no" is selected, nothing will be uploaded and the file on the server will remain untouched.</p>
						<p><strong>2.)</strong> The thumbnails options that appear when uploading files have been adjusted to better suit images on NMU web pages.  These thumbnail options, when selected, will create a scaled version of the image being uploaded to the approximate dimension of the thumbnail option selected.  The scaling process will preserve an image\'s aspect ratio and it will never make an image larger than the original.</p></div></p></div>';
				*/

			if($q_UserCreated[0] > $str_OneWeekFromToday)  //for a week after the user is created, show the CMS eval message
			{
				echo '<div class="user_msg first_timer"><p>It looks like you are new to the NMU CMS.  If that is the case, please take a minute to complete the <a href="http://www.nmu.edu/cmseval">CMS training evaluation survey</a>.  Your feedback will help us improve the training process and is greatly appreciated.</p></div>';
			}

			echo '<div>',
			'<a href="https://docs.google.com/document/d/1eK3EtEycdW7Gsmaeift6R2JYxIFQFyRZCfDJRDdPJps/edit" target="_blank"><img src="/sites/all/themes/nmu/images/user_icons/guide.png" width="100" height="120" title="CMS Guide" style="padding-right:5px;" /></a>',
			'<a href="https://share.nmu.edu/moodle/mod/forum/discuss.php?d=107" target="_blank"><img src="/sites/all/themes/nmu/images/user_icons/issues.png" width="100" height="120" title="Report Website Issues" style="padding-right:5px;" /></a>',
			'<a href="https://share.nmu.edu/moodle/mod/forum/discuss.php?d=108" target="_blank"><img src="/sites/all/themes/nmu/images/user_icons/changes.png" width="100" height="120" title="Request changes or enhancements to your website" style="padding-right:5px;" /></a>';
			echo $user_button_ga;
			echo '<a href="/webadmin" target="_blank"><img src="/sites/all/themes/nmu/images/user_icons/webadmin.png" width="100" height="120" title="Web Admin" style="padding-right:5px;" /></a>',
			'<a href="http://images.nmu.edu" target="_blank"><img src="/sites/all/themes/nmu/images/user_icons/imagedb.png" width="100" height="120" title="Image Database" style="padding-right:5px;" /></a>',
			'<a href="http://myweb.nmu.edu/forms" target="_blank"><img src="/sites/all/themes/nmu/images/user_icons/formservices.png" width="100" height="120" title="Form Services" style="padding-right:5px;" /></a>',
			'</div>';

	//recently edited
	$q_RecentlyEdited = db_query("SELECT nid, title FROM {node_revision} WHERE uid = '".$QueryUID."' ORDER BY timestamp DESC LIMIT 10")->fetchAllKeyed();
	if(!empty($q_RecentlyEdited))
	{
		echo '<p>Files recently edited by <strong>'.$user->name.'</strong>:</p>';
		echo '<ul>';
		foreach($q_RecentlyEdited as $key => $val)
		{
			echo '<li><a href="'.$strBaseURL.'/node/'.$key.'">'.$val.'</a></li>';
		}
		echo '</ul><br />';
	}

	site_access_check($QueryUID, $user->name);

	echo '<p>The following users have access to <strong>'.$SiteName.'</strong>.  <a href="https://share.nmu.edu/moodle/mod/forum/discuss.php?d=109" target="_blank">Use this forum</a> to request user removal.</p>';

	//users with access
		$strQuery = "SELECT Username FROM Administrative.UserType";
		$aResults = $classSqlQuery->MySQL_Queries($strQuery);
		if(!empty($aResults))
		{
			foreach($aResults as $aRow)
				$a_CMUsers[] = $aRow['Username'];
		}
	$q_SiteUsers = db_query("SELECT DISTINCT uid FROM {users_roles}")->fetchCol();
	if(!empty($q_SiteUsers))
	{
		echo '<ul>';
		$str_site_users = '';
		$str_cm_users = '';
		foreach($q_SiteUsers as $key => $val)
		{
			$q_UserNames = db_query("SELECT name FROM {users} WHERE uid = '".$val."'")->fetchCol();
			if(!empty($q_UserNames))
			{
				foreach($q_UserNames as $key => $val)
					if(in_array($val, $a_CMUsers))
						$str_cm_users .= '<li class="class_cm_user">'.$val.' - Communications & Marketing User</li>'."\n";
					else
						$str_site_users .= '<li>'.$val.'</li>'."\n";
			}
		}
		echo $str_site_users;
		echo '<div id="toggle_cm_users"><li class="class_cm_user" id="show_label">Show Communications & Marketing Users</li>';
			echo '<div id="div_cm_users">';
				echo $str_cm_users;
			echo '</div>';
		echo '</div>';

		echo '</ul><br />';
	}

	/*
	   info about the databse statement interface
	   http://api.drupal.org/api/drupal/includes!database!database.inc/interface/DatabaseStatementInterface/7

	   info about the user profile pages
	   http://www.webopius.com/content/319/custom-user-profile-page-in-drupal
	   http://drupal.org/node/310072
	*/
	echo '<br /><hr />';
	//print_r(get_defined_vars());

	echo render($user_profile);
}

?>
</div>
