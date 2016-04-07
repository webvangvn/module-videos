<?php

/**
 * @Project VIDEOS 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @Website tradacongnghe.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate Oct 08, 2015 10:47:41 AM
 */

if( ! defined( 'NV_ADMIN' ) ) die( 'Stop!!!' );

if( ! function_exists('nv_news_array_cat_admin') )
{
	/**
	 * nv_news_array_cat_admin()
	 *
	 * @return
	 */
	function nv_news_array_cat_admin( $module_data )
	{
		global $db;

		$array_cat_admin = array();
		$sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_admins ORDER BY userid ASC';
		$result = $db->query( $sql );

		while( $row = $result->fetch() )
		{
			$array_cat_admin[$row['userid']][$row['catid']] = $row;
		}

		return $array_cat_admin;
	}
}

$is_refresh = false;
$array_cat_admin = nv_news_array_cat_admin( $module_data );

if( ! empty( $module_info['admins'] ) )
{
	$module_admin = explode( ',', $module_info['admins'] );
	foreach( $module_admin as $userid_i )
	{
		if( ! isset( $array_cat_admin[$userid_i] ) )
		{
			$db->query( 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_admins (userid, catid, admin, add_content, pub_content, edit_content, del_content) VALUES (' . $userid_i . ', 0, 1, 1, 1, 1, 1)' );
			$is_refresh = true;
		}
	}
}
if( $is_refresh )
{
	$array_cat_admin = nv_news_array_cat_admin( $module_data );
}

$admin_id = $admin_info['admin_id'];
$NV_IS_ADMIN_MODULE = false;
$NV_IS_ADMIN_FULL_MODULE = false;
if( defined( 'NV_IS_SPADMIN' ) )
{
	$NV_IS_ADMIN_MODULE = true;
	$NV_IS_ADMIN_FULL_MODULE = true;
}
else
{
	if( isset( $array_cat_admin[$admin_id][0] ) )
	{
		$NV_IS_ADMIN_MODULE = true;
		if( intval( $array_cat_admin[$admin_id][0]['admin'] ) == 2 )
		{
			$NV_IS_ADMIN_FULL_MODULE = true;
		}
	}
}

$allow_func = array( 'main', 'view', 'stop', 'publtime', 'waiting', 'declined', 're-published', 'content', 'rpc', 'del_content', 'alias', 'vid_info', 'playlistajax', 'sourceajax', 'tagsajax' );

if( ! isset( $site_mods['cms'] ) )
{
	$submenu['content'] = $lang_module['content_add'];
}

if( $NV_IS_ADMIN_MODULE )
{
	$submenu['cat'] = $lang_module['categories'];
	$submenu['tags'] = $lang_module['tags'];
	$submenu['groups'] = $lang_module['block'];
	$submenu['playlists'] = $lang_module['playlists'];
	$submenu['sources'] = $lang_module['sources'];
	$submenu['admins'] = $lang_module['admin'];
	$submenu['setting'] = $lang_module['setting'];

	$allow_func[] = 'cat';
	$allow_func[] = 'change_cat';
	$allow_func[] = 'list_cat';
	$allow_func[] = 'del_cat';

	$allow_func[] = 'admins';

	$allow_func[] = 'sources';
	$allow_func[] = 'change_source';
	$allow_func[] = 'list_source';
	$allow_func[] = 'del_source';

	$allow_func[] = 'block';
	$allow_func[] = 'groups';
	$allow_func[] = 'del_block_cat';
	$allow_func[] = 'list_block_cat';
	$allow_func[] = 'chang_block_cat';
	$allow_func[] = 'change_block';
	$allow_func[] = 'list_block';
	
	$allow_func[] = 'playlist';
	$allow_func[] = 'playlists';
	$allow_func[] = 'del_playlist_cat';
	$allow_func[] = 'list_playlist_cat';
	$allow_func[] = 'change_playlist_cat';
	$allow_func[] = 'change_playlist';
	$allow_func[] = 'list_playlist';

	$allow_func[] = 'tags';
	$allow_func[] = 'setting';
	$allow_func[] = 'move';
}

$array_url_instruction['content'] = 'https://github.com/anhyeuviolet/module-videos/wiki/Module-Videos---NukeViet-4.x#c%C3%A1c-%C4%91%E1%BB%8Bnh-d%E1%BA%A1ng---li%C3%AAn-k%E1%BA%BFt-h%E1%BB%97-tr%E1%BB%A3';
$array_url_instruction['setting'] = 'https://github.com/anhyeuviolet/module-videos/wiki/Module-Videos---NukeViet-4.x';
$array_url_instruction['playlists'] = 'https://github.com/anhyeuviolet/module-videos/wiki/Module-Videos---NukeViet-4.x#m%E1%BB%99t-s%E1%BB%91-ch%E1%BB%A9c-n%C4%83ng-h%E1%BB%97-tr%E1%BB%A3';
$array_url_instruction['groups'] = 'https://github.com/anhyeuviolet/module-videos/wiki/Module-Videos---NukeViet-4.x#m%E1%BB%99t-s%E1%BB%91-ch%E1%BB%A9c-n%C4%83ng-h%E1%BB%97-tr%E1%BB%A3';
