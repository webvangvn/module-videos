<?php

/**
 * @Project VIDEOS 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @Website tradacongnghe.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate Oct 08, 2015 10:47:41 AM
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$id = $nv_Request->get_int( 'id', 'post', 0 );
$playlist_id = $nv_Request->get_int( 'playlist_id', 'post', 0 );
$mod = $nv_Request->get_string( 'mod', 'post', '' );
$new_vid = $nv_Request->get_int( 'new_vid', 'post', 0 );
$del_list = $nv_Request->get_string( 'del_list', 'post', '' );
$content = "NO_" . $playlist_id;

if( $playlist_id > 0 )
{
	if( $del_list != '' )
	{
		$array_id = array_map( "intval", explode( ',', $del_list ) );
		foreach( $array_id as $id )
		{
			if( $id > 0 )
			{
				$db->query( "DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_playlist WHERE playlist_id=" . $playlist_id . " AND id=" . $id );
			}
		}
		nv_news_fix_playlist( $playlist_id );
		$content = "OK_" . $playlist_id;
	}
	elseif( $id > 0 )
	{
		list( $playlist_id, $id ) = $db->query( "SELECT playlist_id, id FROM " . NV_PREFIXLANG . "_" . $module_data . "_playlist WHERE playlist_id=" . intval( $playlist_id ) . " AND id=" . intval( $id ) )->fetch( 3 );
		if( $playlist_id > 0 and $id > 0 )
		{
			if( $mod == "playlist_sort" and $new_vid > 0 )
			{
				$query = "SELECT id FROM " . NV_PREFIXLANG . "_" . $module_data . "_playlist WHERE playlist_id=" . $playlist_id . " AND id!=" . $id . " ORDER BY playlist_sort ASC";
				$result = $db->query( $query );

				$playlist_sort = 0;
				while( $row = $result->fetch() )
				{
					++$playlist_sort;
					if( $playlist_sort == $new_vid ) ++$playlist_sort;
					$sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_playlist SET playlist_sort=" . $playlist_sort . " WHERE playlist_id=" . $playlist_id . " AND id=" . intval( $row['id'] );
					$db->query( $sql );
				}

				$result->closeCursor();
				$sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_playlist SET playlist_sort=" . $new_vid . " WHERE playlist_id=" . $playlist_id . " AND id=" . intval( $id );
				$db->query( $sql );

				$content = "OK_" . $playlist_id;
			}
			elseif( $mod == "delete" )
			{
				$db->query( "DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_playlist WHERE playlist_id=" . $playlist_id . " AND id=" . intval( $id ) );
				$content = "OK_" . $playlist_id;
			}
		}
	}

	nv_news_fix_playlist( $playlist_id );
	$nv_Cache->delMod( $module_name );
}

include NV_ROOTDIR . '/includes/header.php';
echo $content;
include NV_ROOTDIR . '/includes/footer.php';