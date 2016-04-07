<?php

/**
 * @Project VIDEOS 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @Website tradacongnghe.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate Oct 08, 2015 10:47:41 AM
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$page_title = $lang_module['playlist'];

$sql = 'SELECT playlist_id, title FROM ' . NV_PREFIXLANG . '_' . $module_data . '_playlist_cat ORDER BY weight ASC';
$result = $db->query( $sql );

$array_playlist = array();
while( list( $playlist_id_i, $title_i ) = $result->fetch( 3 ) )
{
	$array_playlist[$playlist_id_i] = $title_i;
}
if( empty( $array_playlist ) )
{
	Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=playlists' );
}

$cookie_playlist_id = $nv_Request->get_int( 'int_playlist_id', 'cookie', 0 );
if( empty( $cookie_playlist_id ) or ! isset( $array_playlist[$cookie_playlist_id] ) )
{
	$cookie_playlist_id = 0;
}

$playlist_id = $nv_Request->get_int( 'playlist_id', 'get,post', $cookie_playlist_id );
if( ! in_array( $playlist_id, array_keys( $array_playlist ) ) )
{
	$playlist_id_array_id = array_keys( $array_playlist );
	$playlist_id = $playlist_id_array_id[0];
}

if( $cookie_playlist_id != $playlist_id )
{
	$nv_Request->set_Cookie( 'int_playlist_id', $playlist_id, NV_LIVE_COOKIE_TIME );
}
$page_title = $array_playlist[$playlist_id];

if( $nv_Request->isset_request( 'checkss,idcheck', 'post' ) and $nv_Request->get_string( 'checkss', 'post' ) == md5( session_id() ) )
{
	$sql = 'SELECT id FROM ' . NV_PREFIXLANG . '_' . $module_data . '_playlist WHERE playlist_id=' . $playlist_id;
	$result = $db->query( $sql );
	$_id_array_exit = array();
	while( list( $_id ) = $result->fetch( 3 ) )
	{
		$_id_array_exit[] = $_id;
	}

	$id_array = array_map( 'intval', $nv_Request->get_array( 'idcheck', 'post' ) );
	foreach( $id_array as $id )
	{
		if( ! in_array( $id, $_id_array_exit ) )
		{
			try
			{
				$db->query( 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_playlist (playlist_id, id, playlist_sort) VALUES (' . $playlist_id . ', ' . $id . ', 0)' );
			}
			catch( PDOException $e )
			{
				trigger_error( $e->getMessage() );
			}
		}
	}
	nv_fix_playlist( $playlist_id );
	$nv_Cache->delMod( $module_name );
	Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&playlist_id=' . $playlist_id );
	die();
}

$select_options = array();
foreach( $array_playlist as $xplaylist_id => $playlistname )
{
	$select_options[NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;playlist_id=' . $xplaylist_id] = $playlistname;
}

$xtpl = new XTemplate( 'playlist.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'GLANG', $lang_global );
$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'OP', $op );

$listid = $nv_Request->get_string( 'listid', 'get', '' );
if( $listid == '' and $playlist_id )
{
	$xtpl->assign( 'PLAYLIST_LIST', nv_show_playlist_list( $playlist_id ) );
}
else
{
	$page_title = $lang_module['addtoplaylists'];
	$id_array = array_map( 'intval', explode( ',', $listid ) );

	$db->sqlreset()->select( 'id, title' )->from( NV_PREFIXLANG . '_' . $module_data . '_rows' )->order( 'publtime DESC' )->where( 'status=1 AND id IN (' . implode( ',', $id_array ) . ')' );

	$result = $db->query( $db->sql() );

	while( list( $id, $title ) = $result->fetch( 3 ) )
	{
		$xtpl->assign( 'ROW', array(
			'checked' => in_array( $id, $id_array ) ? ' checked="checked"' : '',
			'title' => $title,
			'id' => $id
		) );

		$xtpl->parse( 'main.news.loop' );
	}

	foreach( $array_playlist as $xplaylist_id => $playlistname )
	{
		$xtpl->assign( 'PLAYLIST_ID', array(
			'key' => $xplaylist_id,
			'title' => $playlistname,
			'selected' => $xplaylist_id == $playlist_id ? ' selected="selected"' : ''
		) );
		$xtpl->parse( 'main.news.playlist_id' );
	}

	$xtpl->assign( 'CHECKSESS', md5( session_id() ) );
	$xtpl->parse( 'main.news' );
}

$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );

$set_active_op = 'playlists';
include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';