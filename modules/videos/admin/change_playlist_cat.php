<?php

/**
 * @Project VIDEOS 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @Website tradacongnghe.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate Oct 08, 2015 10:47:41 AM
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );
if( ! defined( 'NV_IS_AJAX' ) ) die( 'Wrong URL' );

$playlist_id = $nv_Request->get_int( 'playlist_id', 'post', 0 );
$mod = $nv_Request->get_string( 'mod', 'post', '' );
$new_vid = $nv_Request->get_int( 'new_vid', 'post', 0 );

if( empty( $playlist_id ) ) die( 'NO_' . $playlist_id );
$content = 'NO_' . $playlist_id;

if( $mod == 'weight' and $new_vid > 0 )
{
	$sql = 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_playlist_cat WHERE playlist_id=' . $playlist_id;
	$numrows = $db->query( $sql )->fetchColumn();
	if( $numrows != 1 ) die( 'NO_' . $playlist_id );

	$sql = 'SELECT playlist_id FROM ' . NV_PREFIXLANG . '_' . $module_data . '_playlist_cat WHERE playlist_id!=' . $playlist_id . ' ORDER BY weight ASC';
	$result = $db->query( $sql );

	$weight = 0;
	while( $row = $result->fetch() )
	{
		++$weight;
		if( $weight == $new_vid ) ++$weight;
		$sql = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_playlist_cat SET weight=' . $weight . ' WHERE playlist_id=' . $row['playlist_id'];
		$db->query( $sql );
	}

	$sql = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_playlist_cat SET weight=' . $new_vid . ' WHERE playlist_id=' . $playlist_id;
	$db->query( $sql );

	$content = 'OK_' . $playlist_id;
}
elseif( $mod == 'status' and $playlist_id > 0 )
{
	$new_vid = ( intval( $new_vid ) );
	$sql = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_playlist_cat SET status=' . $new_vid . ' WHERE playlist_id=' . $playlist_id;
	$db->query( $sql );
	$content = 'OK_' . $playlist_id;
}
elseif( $mod == 'private_mode' and $playlist_id > 0 )
{
	$new_vid = ( intval( $new_vid ) == 1 ) ? 1 : 0;
	$sql = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_playlist_cat SET private_mode=' . $new_vid . ' WHERE playlist_id=' . $playlist_id;
	$db->query( $sql );
	$content = 'OK_' . $playlist_id;
}
elseif( $mod == 'numlinks' and $new_vid >= 0 and $new_vid <= 50 )
{
	$sql = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_playlist_cat SET numbers=' . $new_vid . ' WHERE playlist_id=' . $playlist_id;
	$db->query( $sql );
	$content = 'OK_' . $playlist_id;
}

$nv_Cache->delMod( $module_name );

include NV_ROOTDIR . '/includes/header.php';
echo $content;
include NV_ROOTDIR . '/includes/footer.php';