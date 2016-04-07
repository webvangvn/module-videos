<?php

/**
 * @Project VIDEOS 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @Website tradacongnghe.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate Oct 08, 2015 10:47:41 AM
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$bid = $nv_Request->get_int( 'bid', 'post', 0 );

$contents = "NO_" . $bid;
$bid = $db->query( "SELECT bid FROM " . NV_PREFIXLANG . "_" . $module_data . "_block_cat WHERE bid=" . intval( $bid ) )->fetchColumn();
if( $bid > 0 )
{
	nv_insert_logs( NV_LANG_DATA, $module_name, 'log_del_blockcat', "block_catid " . $bid, $admin_info['userid'] );
	$query = "DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_block_cat WHERE bid=" . $bid;
	if( $db->exec( $query ) )
	{
		$query = "DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_block WHERE bid=" . $bid;
		$db->query( $query );
		nv_fix_block_cat();
		$nv_Cache->delMod( $module_name );
		$contents = "OK_" . $bid;
	}
}

include NV_ROOTDIR . '/includes/header.php';
echo $contents;
include NV_ROOTDIR . '/includes/footer.php';