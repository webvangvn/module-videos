<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 2-10-2010 18:49
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
	$sql = 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_playlists WHERE playlist_id=' . $playlist_id;
	$numrows = $db->query( $sql )->fetchColumn();
	if( $numrows != 1 ) die( 'NO_' . $playlist_id );

	$sql = 'SELECT playlist_id FROM ' . NV_PREFIXLANG . '_' . $module_data . '_playlists WHERE playlist_id!=' . $playlist_id . ' ORDER BY weight ASC';
	$result = $db->query( $sql );

	$weight = 0;
	while( $row = $result->fetch() )
	{
		++$weight;
		if( $weight == $new_vid ) ++$weight;
		$sql = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_playlists SET weight=' . $weight . ' WHERE playlist_id=' . $row['playlist_id'];
		$db->query( $sql );
	}

	$sql = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_playlists SET weight=' . $new_vid . ' WHERE playlist_id=' . $playlist_id;
	$db->query( $sql );

	$content = 'OK_' . $playlist_id;
	nv_del_moduleCache( $module_name );
}

include NV_ROOTDIR . '/includes/header.php';
echo $content;
include NV_ROOTDIR . '/includes/footer.php';