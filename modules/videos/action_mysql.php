<?php

/**
 * @Project VIDEOS 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @Website tradacongnghe.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate Oct 08, 2015 10:47:41 AM
 */

if( ! defined( 'NV_IS_FILE_MODULES' ) ) die( 'Stop!!!' );

$sql_drop_module = array();
$array_table = array(
	'admins',
	'block',
	'block_cat',
	'playlist',
	'playlist_cat',
	'bodytext',
	'cat',
	'config_post',
	'rows',
	'sources',
	'tags',
	'tags_id'
);
$table = $db_config['prefix'] . '_' . $lang . '_' . $module_data;
$result = $db->query( 'SHOW TABLE STATUS LIKE ' . $db->quote( $table . '_%' ) );
while( $item = $result->fetch( ) )
{
	$name = substr( $item['name'], strlen( $table ) + 1 );
	if( preg_match( '/^' . $db_config['prefix'] . '\_' . $lang . '\_' . $module_data . '\_/', $item['name'] ) and ( preg_match( '/^([0-9]+)$/', $name ) or in_array( $name, $array_table ) or preg_match( '/^bodyhtml\_([0-9]+)$/', $name ) ) )
	{
		$sql_drop_module[] = 'DROP TABLE IF EXISTS ' . $item['name'];
	}
}

$result = $db->query( "SHOW TABLE STATUS LIKE '" . $db_config['prefix'] . "\_" . $lang . "\_comment'" );
$rows = $result->fetchAll();
if( sizeof( $rows ) )
{
	$sql_drop_module[] = "DELETE FROM " . $db_config['prefix'] . "_" . $lang . "_comment WHERE module='" . $module_name . "'";
}
$sql_create_module = $sql_drop_module;

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_cat (
	  catid smallint(5) unsigned NOT NULL AUTO_INCREMENT,
	  parentid smallint(5) unsigned NOT NULL DEFAULT '0',
	  title varchar(250) NOT NULL,
	  titlesite varchar(250) DEFAULT '',
	  alias varchar(250) NOT NULL DEFAULT '',
	  description text,
	  descriptionhtml text,
	  image varchar(250) DEFAULT '',
	  viewdescription tinyint(2) NOT NULL DEFAULT '0',
	  weight smallint(5) unsigned NOT NULL DEFAULT '0',
	  sort smallint(5) NOT NULL DEFAULT '0',
	  lev smallint(5) NOT NULL DEFAULT '0',
	  viewcat varchar(50) NOT NULL DEFAULT 'viewcat_page_new',
	  numsubcat smallint(5) NOT NULL DEFAULT '0',
	  subcatid varchar(250) DEFAULT '',
	  inhome tinyint(1) unsigned NOT NULL DEFAULT '0',
	  numlinks tinyint(2) unsigned NOT NULL DEFAULT '3',
	  newday tinyint(2) unsigned NOT NULL DEFAULT '2',
	  featured int(11) NOT NULL DEFAULT '0',
	  keywords text,
	  admins text,
	  add_time int(11) unsigned NOT NULL DEFAULT '0',
	  edit_time int(11) unsigned NOT NULL DEFAULT '0',
	  groups_view varchar(250) DEFAULT '',
	  PRIMARY KEY (catid),
	  UNIQUE KEY alias (alias),
	  KEY parentid (parentid)
	) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_sources (
	 sourceid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	 title varchar(250) NOT NULL DEFAULT '',
	 link varchar(250) DEFAULT '',
	 logo varchar(250) DEFAULT '',
	 weight mediumint(8) unsigned NOT NULL DEFAULT '0',
	 add_time int(11) unsigned NOT NULL,
	 edit_time int(11) unsigned NOT NULL,
	 PRIMARY KEY (sourceid),
	 UNIQUE KEY title (title)
	) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_playlist_cat (
	 playlist_id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
	 userid smallint(5) unsigned NOT NULL ,
	 status smallint(5) unsigned NOT NULL DEFAULT '1',
	 private_mode smallint(5) unsigned NOT NULL DEFAULT '1',
	 numbers smallint(5) NOT NULL DEFAULT '10',
	 title varchar(250) NOT NULL DEFAULT '',
	 alias varchar(250) NOT NULL DEFAULT '',
	 image varchar(250) DEFAULT '',
	 description varchar(250) DEFAULT '',
	 weight smallint(5) NOT NULL DEFAULT '0',
	 keywords text,
	 hitstotal mediumint(8) unsigned NOT NULL default '0',
	 favorite mediumint(8) unsigned NOT NULL default '0',
	 add_time int(11) NOT NULL DEFAULT '0',
	 edit_time int(11) NOT NULL DEFAULT '0',
	 PRIMARY KEY (playlist_id),
	 UNIQUE KEY title (title),
	 UNIQUE KEY alias (alias)
	) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_playlist (
	 playlist_id smallint(5) unsigned NOT NULL,
	 id int(11) unsigned NOT NULL,
	 playlist_sort int(11) unsigned NOT NULL,
	 UNIQUE KEY playlist_id (playlist_id,id)
	) ENGINE=MyISAM";
	
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_block_cat (
	 bid smallint(5) unsigned NOT NULL AUTO_INCREMENT,
	 adddefault tinyint(4) NOT NULL DEFAULT '0',
	 numbers smallint(5) NOT NULL DEFAULT '10',
	 title varchar(250) NOT NULL DEFAULT '',
	 alias varchar(250) NOT NULL DEFAULT '',
	 image varchar(250) DEFAULT '',
	 description varchar(250) DEFAULT '',
	 weight smallint(5) NOT NULL DEFAULT '0',
	 keywords text,
	 add_time int(11) NOT NULL DEFAULT '0',
	 edit_time int(11) NOT NULL DEFAULT '0',
	 PRIMARY KEY (bid),
	 UNIQUE KEY title (title),
	 UNIQUE KEY alias (alias)
	) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_block (
	 bid smallint(5) unsigned NOT NULL,
	 id int(11) unsigned NOT NULL,
	 weight int(11) unsigned NOT NULL,
	 UNIQUE KEY bid (bid,id)
	) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_rows (
	 id int(11) unsigned NOT NULL auto_increment,
	 catid smallint(5) unsigned NOT NULL default '0',
	 listcatid varchar(250) NOT NULL default '',
	 admin_id mediumint(8) unsigned NOT NULL default '0',
	 admin_name varchar(250) default '',
	 author varchar(250) default '',
	 artist varchar(250) default '',
	 sourceid mediumint(8) NOT NULL default '0',
	 addtime int(11) unsigned NOT NULL default '0',
	 edittime int(11) unsigned NOT NULL default '0',
	 status tinyint(4) NOT NULL default '1',
	 publtime int(11) unsigned NOT NULL default '0',
	 exptime int(11) unsigned NOT NULL default '0',
	 archive tinyint(1) unsigned NOT NULL default '0',
	 title varchar(250) NOT NULL default '',
	 alias varchar(250) NOT NULL default '',
	 hometext text NOT NULL,
	 vid_path varchar(250) default '',
	 vid_type varchar(250) default '',
	 vid_duration varchar(250) default '',
	 homeimgfile varchar(250) default '',
	 homeimgalt varchar(250) default '',
	 homeimgthumb tinyint(4) NOT NULL default '0',
	 inhome tinyint(1) unsigned NOT NULL default '0',
	 allowed_comm varchar(250) default '',
	 allowed_rating tinyint(1) unsigned NOT NULL default '0',
	 hitstotal mediumint(8) unsigned NOT NULL default '0',
	 hitscm mediumint(8) unsigned NOT NULL default '0',
	 total_rating int(11) NOT NULL default '0',
	 click_rating int(11) NOT NULL default '0',
	 PRIMARY KEY (id),
	 KEY catid (catid),
	 KEY admin_id (admin_id),
	 KEY author (author),
	 KEY title (title),
	 KEY addtime (addtime),
	 KEY publtime (publtime),
	 KEY exptime (exptime),
	 KEY status (status)
	) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_bodytext (
	 id int(11) unsigned NOT NULL,
	 bodytext mediumtext NULL,
	 PRIMARY KEY (id)
	) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_bodyhtml_1 (
	 id int(11) unsigned NOT NULL,
	 bodyhtml longtext NOT NULL,
	 sourcetext varchar(250) default '',
	 copyright tinyint(1) NOT NULL default '0',
	 allowed_send tinyint(1) NOT NULL default '0',
	 allowed_save tinyint(1) NOT NULL default '0',
	 gid mediumint(8) NOT NULL default '0',
	 PRIMARY KEY (id)
	) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_logs (
	 id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	 sid mediumint(8) NOT NULL DEFAULT '0',
	 userid mediumint(8) unsigned NOT NULL DEFAULT '0',
	 status tinyint(4) NOT NULL DEFAULT '0',
	 note varchar(250) NOT NULL,
	 set_time int(11) unsigned NOT NULL DEFAULT '0',
	 PRIMARY KEY (id),
	 KEY sid (sid),
	 KEY userid (userid)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_config_post (
	 group_id smallint(5) NOT NULL,
	 addcontent tinyint(4) NOT NULL,
	 postcontent tinyint(4) NOT NULL,
	 editcontent tinyint(4) NOT NULL,
	 delcontent tinyint(4) NOT NULL,
	 PRIMARY KEY (group_id)
	) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_admins (
	 userid mediumint(8) unsigned NOT NULL default '0',
	 catid smallint(5) NOT NULL default '0',
	 admin tinyint(4) NOT NULL default '0',
	 add_content tinyint(4) NOT NULL default '0',
	 pub_content tinyint(4) NOT NULL default '0',
	 edit_content tinyint(4) NOT NULL default '0',
	 del_content tinyint(4) NOT NULL default '0',
	 app_content tinyint(4) NOT NULL default '0',
	 UNIQUE KEY userid (userid,catid)
	) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_tags (
	 tid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	 numnews mediumint(8) NOT NULL DEFAULT '0',
	 alias varchar(250) NOT NULL DEFAULT '',
	 image varchar(250) DEFAULT '',
	 description text,
	 keywords varchar(250) DEFAULT '',
	 PRIMARY KEY (tid),
	 UNIQUE KEY alias (alias)
	) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_tags_id (
	 id int(11) NOT NULL,
	 tid mediumint(9) NOT NULL,
	 keyword varchar(65) NOT NULL,
	 UNIQUE KEY sid (id,tid)
	) ENGINE=MyISAM";

$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'indexfile', 'viewgrid_by_cat')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'per_page', '20')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'st_links', '10')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'per_line', '3')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'homewidth', '270')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'homeheight', '200')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'blockwidth', '150')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'blockheight', '100')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'titlecut', '75')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'copyright', '')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'timecheckstatus', '0')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'config_source', '0')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'show_no_image', '')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'allowed_rating_point', '1')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'facebookappid', '')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'socialbutton', '1')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'alias_lower', '1')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'tags_alias', '0')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'auto_tags', '0')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'tags_remind', '1')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'structure_upload', 'Ym')";

$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'allow_user_plist', '1')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'playlist_moderate', '1')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'playlist_allow_detele', '1')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'playlist_max_items', '20')";

$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'youtube_api', '')";

$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'jwplayer_license', '')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'jwplayer_autoplay', 'false')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'jwplayer_loop', 'false')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'jwplayer_controlbar', 'true')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'jwplayer_mute', 'false')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'jwplayer_logo', '0')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'jwplayer_logo_file', '')";
// Comments
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'auto_postcomm', '1')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'allowed_comm', '-1')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'view_comm', '6')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'setcomm', '4')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'activecomm', '1')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'emailcomm', '0')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'adminscomm', '')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'sortcomm', '0')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'captcha', '1')";