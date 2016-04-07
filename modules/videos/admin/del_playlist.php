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
$checkss = $nv_Request->get_string( 'checkss', 'post' );

$contents = 'NO_' . $playlist_id;

list( $playlist_id, $image ) = $db->query( 'SELECT playlist_id, image FROM ' . NV_PREFIXLANG . '_' . $module_data . '_playlists WHERE playlist_id=' . intval( $playlist_id ) )->fetch( 3 );
if( $playlist_id > 0 )
{
	nv_insert_logs( NV_LANG_DATA, $module_name, 'log_del_playlist', 'playlist_id ' . $playlist_id, $admin_info['userid'] );
	$check_del_playlist_id = false;

	$query = $db->query( 'SELECT id, listcatid FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE playlist_id = ' . $playlist_id );
	$_rows = $query->fetchAll();
	$check_rows = sizeof( $_rows );

	if( $check_rows > 0 and $checkss == md5( $playlist_id . session_id() . $global_config['sitekey'] ) )
	{
		foreach ($_rows as $row )
		{
			$arr_catid = explode( ',', $row['listcatid'] );
			foreach( $arr_catid as $catid_i )
			{
				$db->query( 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_' . $catid_i . ' SET playlist_id = 0 WHERE id =' . $row['id'] );
			}
			$db->query( 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_rows SET playlist_id = 0 WHERE id =' . $row['id'] );
		}
		$check_del_playlist_id = true;
	}
	elseif( $check_rows > 0 )
	{
		$contents = 'ERR_ROWS_' . $playlist_id . '_' . md5( $playlist_id . session_id() . $global_config['sitekey'] ) . '_' . sprintf( $lang_module['delplaylist_msg_rows'], $check_rows );
	}
	else
	{
		$check_del_playlist_id = true;
	}
	if( $check_del_playlist_id )
	{
		$query = 'DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_playlists WHERE playlist_id=' . $playlist_id;
		if( $db->exec( $query ) )
		{
			nv_fix_playlist();
			if( is_file( NV_ROOTDIR . '/' . NV_FILES_DIR . '/' . $module_upload . '/playlists/' . $image ) )
			{
				nv_deletefile( NV_ROOTDIR . '/' . NV_FILES_DIR . '/' . $module_upload . '/playlists/' . $image );
			}
			$contents = 'OK_' . $playlist_id;
		}
	}
	$nv_Cache->delMod( $module_name );
}

include NV_ROOTDIR . '/includes/header.php';
echo $contents;
include NV_ROOTDIR . '/includes/footer.php';