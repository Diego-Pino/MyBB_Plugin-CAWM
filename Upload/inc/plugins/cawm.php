<?php
/*
 * MyBB: Cool announcements (with Marquee)
 *
 * File: cawm.php
 * 
 * Authors: Alex & Vintagedaddyo
 *
 * MyBB Version: 1.8
 *
 * Plugin Version: 1.1
 * 
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$plugins->add_hook("index_start", "cawm_index_start");

function cawm_info()
{
    global $lang;

    $lang->load("cawm");
    
    $lang->cawm_Desc = '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="float:right;">' .
        '<input type="hidden" name="cmd" value="_s-xclick">' . 
        '<input type="hidden" name="hosted_button_id" value="AZE6ZNZPBPVUL">' .
        '<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">' .
        '<img alt="" border="0" src="https://www.paypalobjects.com/pl_PL/i/scr/pixel.gif" width="1" height="1">' .
        '</form>' . $lang->cawm_Desc;

    return Array(
        'name' => $lang->cawm_Name,
        'description' => $lang->cawm_Desc,
        'website' => $lang->cawm_Web,
        'author' => $lang->cawm_Auth,
        'authorsite' => $lang->cawm_AuthSite,
        'version' => $lang->cawm_Ver,
        'compatibility' => $lang->cawm_Compat
    );
}

function cawm_install()
{
    global $settings, $mybb, $db, $lang;

    $lang->load("cawm");
	
if($db->field_exists("cawmsis", "users"))
	{
	$db->write_query("ALTER TABLE ".TABLE_PREFIX."users DROP cawmsis"); 
	}
	
    $settings_group = array(
        'gid'          => 'NULL',
        'name' => $lang->cawm_name_setting_0,
        'title' => $lang->cawm_title_setting_0,
        'description' => $lang->cawm_description_setting_0,
        'disporder'    => '1',
        'isdefault'    => 'no'
    );
    $db->insert_query('settinggroups', $settings_group);
    $gid = $db->insert_id();
	
    $setting_1 = array(
        'sid'          => 'NULL',
        'name' => $lang->cawm_name_setting_1,
        'title' => $lang->cawm_title_setting_1,
        'description' => $lang->cawm_description_setting_1,
        'optionscode'  => 'textarea',
        'value'        => '<b>Edit Announcements Settings!</b>',
        'disporder'    => '2',
        'gid'          => intval( $gid )
    );

	$setting_2 = array(
	'sid'          => 'NULL',
        'name' => $lang->cawm_name_setting_2,
        'title' => $lang->cawm_title_setting_2,
        'description' => $lang->cawm_description_setting_2,
        'optionscode'  => 'text',
        'value'        => '#EEEEEE url(images/buttons_bg.png) top left repeat-x',
        'disporder'    => '3',
        'gid'          => intval( $gid )
    );

    $setting_3 = array(
	'sid'          => 'NULL',
        'name' => $lang->cawm_name_setting_3,
        'title' => $lang->cawm_title_setting_3,
        'description' => $lang->cawm_description_setting_3,
        'optionscode'  => 'text',
        'value'        => '#0066A2',
        'disporder'    => '4',
        'gid'          => intval( $gid )
    );

 $setting_4 = array(
	'sid'          => 'NULL',
        'name' => $lang->cawm_name_setting_4,
        'title' => $lang->cawm_title_setting_4,
        'description' => $lang->cawm_description_setting_4,
        'optionscode'  => 'text',
        'value'        => '#0066A2',
        'disporder'    => '4',
        'gid'          => intval( $gid )
    );
		$db->insert_query("settings", $setting_1);
		$db->insert_query("settings", $setting_2);
		$db->insert_query("settings", $setting_3);
		$db->insert_query("settings", $setting_4);

	$db->write_query("ALTER TABLE ".TABLE_PREFIX."users ADD cawmsis int NOT NULL default 0");

	rebuild_settings();
	
	$insertarray = array(
		"title" => "cawm",
		"template" => "<div style=\"background: {\$mybb->settings[\'annc\']};
	border-top: 1px solid {\$mybb->settings[\'annb\']}; border-right: 1px solid {\$mybb->settings[\'annb\']}; border-left: 1px solid  {\$mybb->settings[\'annb\']}; border-bottom: 1px solid {\$mybb->settings[\'annb\']};border-radius: 6px;text-align: center; margin: 10px auto; padding: 5px 20px;color:{\$mybb->settings[\'anncc\']};\"><center><marquee>{\$mybb->settings[\'ann\']}</marquee></center></div>
		<br />",
		"sid" => -1,
		"dateline" => TIME_NOW
	);
	
	$db->insert_query("templates", $insertarray);
}

function cawm_is_installed()
{
	global $db;
	
	if($db->field_exists("cawmsis", "users"))
	{
		return true;
	}
	
	return false;
}

function cawm_activate()
{
	global $db;
	
	include MYBB_ROOT."/inc/adminfunctions_templates.php";
	
	find_replace_templatesets("index", "#".preg_quote("{\$header}")."#i", "{\$header}\r\n{\$cawm}");
}

function cawm_deactivate()
{
	global $db;
	
	include MYBB_ROOT."/inc/adminfunctions_templates.php";
	
	find_replace_templatesets("index", "#".preg_quote("\r\n{\$cawm}")."#i", "", 0);
}

function cawm_uninstall()
{
	global $db;
	
	if($db->field_exists("cawmsis", "users"))
	{
		$db->write_query("ALTER TABLE ".TABLE_PREFIX."users DROP cawmsis"); 
	}
	$db->query("DELETE FROM ".TABLE_PREFIX."settinggroups WHERE name='cawm'");
	$db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name='ann'");
	$db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name='annc'");
	$db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name='annb'");
	$db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name='anncc'");
	rebuild_settings();
	
	$db->delete_query("templates", "title = 'cawm'");
}

function cawm_index_start()
{
	global $db, $mybb, $templates, $cawm, $lang;
	
	eval("\$cawm = \"".$templates->get("cawm")."\";");
}
?>