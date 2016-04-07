<?php

/**
 * @Project VIDEOS 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @Website tradacongnghe.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate Oct 08, 2015 10:47:41 AM
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$playlist_id = $nv_Request->get_int( 'playlist_id', 'post', 0 );

$contents = "NO_" . $playlist_id;
$playlist_id = $db->query( "SELECT playlist_id FROM " . NV_PREFIXLANG . "_" . $module_data . "_playlist_cat WHERE playlist_id=" . intval( $playlist_id ) )->fetchColumn();
if( $playlist_id > 0 )
{
	nv_insert_logs( NV_LANG_DATA, $module_name, 'log_del_playlistcat', "playlist_catid " . $playlist_id, $admin_info['userid'] );
	$query = "DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_playlist_cat WHERE playlist_id=" . $playlist_id;
	if( $db->exec( $query ) )
	{
		$query = "DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_playlist WHERE playlist_id=" . $playlist_id;
		$db->query( $query );
		nv_fix_playlist_cat();
		$nv_Cache->delMod( $module_name );
		$contents = "OK_" . $playlist_id;
	}
}

include NV_ROOTDIR . '/includes/header.php';
echo $contents;
include NV_ROOTDIR . '/includes/footer.php';