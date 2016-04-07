<?php

/**
 * @Project VIDEOS 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @Website tradacongnghe.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate Oct 08, 2015 10:47:41 AM
 */

if( ! defined( 'NV_IS_FILE_SITEINFO' ) ) die( 'Stop!!!' );

$lang_siteinfo = nv_get_lang_module( $mod );

// Tong so bai viet
$number = $db->query( 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $mod_data . '_rows WHERE status= 1' )->fetchColumn();
if( $number > 0 )
{
	$siteinfo[] = array( 'key' => $lang_siteinfo['siteinfo_publtime'], 'value' => $number );

}


// Playlist chua duyet
$number = $db->query( 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $mod_data . '_playlist_cat WHERE status= 2' )->fetchColumn();
if( $number > 0 )
{
	$siteinfo[] = array( 'key' => $lang_siteinfo['siteinfo_playlist_pending'], 'value' => $number );
	
	$pendinginfo[] = array(
		'key' => $lang_siteinfo['siteinfo_playlist_pending'],
		'value' => $number,
		'link' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $mod . '&amp;' . NV_OP_VARIABLE . '=playlists',
	);
}

//So bai viet thanh vien gui toi
if( ! empty( $site_mods[$mod]['admins'] ) )
{
	$admins_module = explode( ',', $site_mods[$mod]['admins'] );
}
else
{
	$admins_module = array();
}
$result = $db->query( 'SELECT admin_id FROM ' . NV_AUTHORS_GLOBALTABLE . ' WHERE lev=1 OR lev=2' );
while( $row = $result->fetch() )
{
	$admins_module[] = $row['admin_id'];
}
$number = $db->query( 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $mod_data . '_rows WHERE admin_id NOT IN (' . implode( ',', $admins_module ) . ')' )->fetchColumn();
if( $number > 0 )
{
	$siteinfo[] = array( 'key' => $lang_siteinfo['siteinfo_users_send'], 'value' => $number );
}

// So bai viet cho dang tu dong
$number = $db->query( 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $mod_data . '_rows WHERE status= 1 AND publtime > ' . NV_CURRENTTIME . ' AND (exptime=0 OR exptime>' . NV_CURRENTTIME . ')' )->fetchColumn();
if( $number > 0 )
{
	$siteinfo[] = array( 'key' => $lang_siteinfo['siteinfo_pending'], 'value' => $number );
}

// So bai viet da het han
$number = $db->query( 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $mod_data . '_rows WHERE exptime > 0 AND exptime<' . NV_CURRENTTIME )->fetchColumn();
if( $number > 0 )
{
	$siteinfo[] = array( 'key' => $lang_siteinfo['siteinfo_expired'], 'value' => $number );
}

// So bai viet sap het han
$number = $db->query( 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $mod_data . '_rows WHERE status = 1 AND exptime>' . NV_CURRENTTIME )->fetchColumn();
if( $number > 0 )
{
	$siteinfo[] = array( 'key' => $lang_siteinfo['siteinfo_exptime'], 'value' => $number );
}

// Tong so binh luan duoc dang
$number = $db->query( 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_comment WHERE module=' . $db->quote( $mod ) . ' AND status = 1' )->fetchColumn();
if( $number > 0 )
{
	$siteinfo[] = array( 'key' => $lang_siteinfo['siteinfo_comment'], 'value' => $number );
}

// Nhac nho cac tu khoa chua co mo ta
if( ! empty( $module_config[$mod]['tags_remind'] ) )
{
	$number = $db->query( 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $mod_data . '_tags WHERE description = \'\'' )->fetchColumn();

	if( $number > 0 )
	{
		$pendinginfo[] = array(
			'key' => $lang_siteinfo['siteinfo_tags_incomplete'],
			'value' => $number,
			'link' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $mod . '&amp;' . NV_OP_VARIABLE . '=tags&amp;incomplete=1',
		);
	}
}
// Thieu License JWplayer
$check_lic = $db->query( 'SELECT config_value FROM ' . NV_CONFIG_GLOBALTABLE . ' WHERE lang = "' . NV_LANG_DATA . '" AND module = "' . $mod . '" AND config_name = "jwplayer_license"' )->fetchColumn();
if( empty($check_lic) OR !isset($check_lic) )
{
	$pendinginfo[] = array(
	'key' => $lang_siteinfo['no_jwp_lic'],
	'value' => 'No License',
	'link' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $mod . '&amp;' . NV_OP_VARIABLE . '=setting#jwplayer_license'
	);
}
