<?php

/**
 * @Project VIDEOS 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @Website tradacongnghe.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate Oct 08, 2015 10:47:41 AM
 */

if( ! defined( 'NV_IS_MOD_VIDEOS' ) ) die( 'Stop!!!' );

$show_no_image = $module_config[$module_name]['show_no_image'];
if(empty($show_no_image))
{
	$show_no_image = 'themes/default/images/' . $module_name . '/' . 'video_placeholder.png';
}

$array_mod_title[] = array(
	'catid' => 0,
	'title' => $module_info['funcs'][$op]['func_custom_name'],
	'link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['playlists']
);

$alias = isset( $array_op[1] ) ? trim( $array_op[1] ) : '';
$playlist_array = array();

if( !empty( $alias ) )
{
	$page = ( isset( $array_op[2] ) and substr( $array_op[2], 0, 5 ) == 'page-' ) ? intval( substr( $array_op[2], 5 ) ) : 1;

	$stmt = $db->prepare( 'SELECT playlist_id, title, alias, image, description, keywords, hitstotal, add_time, favorite ,status, private_mode, userid FROM ' . NV_PREFIXLANG . '_' . $module_data . '_playlist_cat WHERE alias= :alias' );
	$stmt->bindParam( ':alias', $alias, PDO::PARAM_STR );
	$stmt->execute();

	list( $playlist_id, $page_title, $alias, $playlist_image, $description, $key_words, $hitstotal, $add_time, $favorite, $status,  $private_mode, $p_userid ) = $stmt->fetch(3);
	$playlist_info = array(
		'playlist_id' => $playlist_id,
		'title' => $page_title,
		'alias' => $alias,
		'playlist_image' => $playlist_image,
		'description' => $description,
		'key_words' => $key_words,
		'hitstotal' => $hitstotal,
		'add_time' => $add_time,
		'favorite' => $favorite,
		'status' => $status,
		'private_mode' => $private_mode,
		'userid' => $p_userid
	);
	
	if( $playlist_id > 0 AND $status > 0 )
	{
		if( defined( 'NV_IS_MODADMIN' ) OR ($status == 1) )
		{
			$time_set = $nv_Request->get_int( $module_data . '_' . $op . '_' . $playlist_id, 'session' );
			if( empty( $time_set ) )
			{
				$nv_Request->set_Session( $module_data . '_' . $op . '_' . $playlist_id, NV_CURRENTTIME );
				$query = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_playlist_cat SET hitstotal=hitstotal+1 WHERE playlist_id=' . $playlist_id;
				$db->query( $query );
			}
		}
		
		$base_url_rewrite = $base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['playlists'] . '/' . $alias;
		if( $page > 1 )
		{
			$page_title .= ' ' . NV_TITLEBAR_DEFIS . ' ' . $lang_global['page'] . ' ' . $page;
			$base_url_rewrite .= '/page-' . $page;
		}
		$base_url_rewrite = nv_url_rewrite( $base_url_rewrite, true );
		if( $_SERVER['REQUEST_URI'] != $base_url_rewrite and NV_MAIN_DOMAIN . $_SERVER['REQUEST_URI'] != $base_url_rewrite )
		{
			Header( 'Location: ' . $base_url_rewrite );
			die();
		}

		$array_mod_title[] = array(
			'catid' => 0,
			'title' => $page_title,
			'link' => $base_url
		);

		$item_array = array();
		$end_weight = 0;

		$db->sqlreset()
			->select( 'COUNT(*)' )
			->from( NV_PREFIXLANG . '_' . $module_data . '_rows t1' )
			->join( 'INNER JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_playlist t2 ON t1.id = t2.id' )
			->where( 't2.playlist_id= ' . $playlist_id . ' AND t1.status= 1' );

		$num_items = $db->query( $db->sql() )->fetchColumn();

		$db->select( 't1.id, t1.catid, t1.admin_id, t1.author, t1.sourceid, t1.addtime, t1.edittime, t1.publtime, t1.title, t1.alias, t1.hometext, t1.homeimgfile, t1.homeimgalt, t1.homeimgthumb, t1.allowed_rating, t1.hitstotal, t1.hitscm, t1.total_rating, t1.click_rating, t2.playlist_sort' )
			->order( 't2.playlist_sort ASC' )
			->limit( $per_page )
			->offset( ($page - 1) * $per_page );

		$end_publtime = 0;

		$result = $db->query( $db->sql() );
		while( $item = $result->fetch() )
		{
			if( $item['homeimgthumb'] == 1 OR $item['homeimgthumb'] == 2 ) //image file
			{
				$item['imghome'] = videos_thumbs($item['id'], $item['homeimgfile'], $module_upload, $module_config[$module_name]['homewidth'], $module_config[$module_name]['homeheight'], 90 );
			}
			elseif( $item['homeimgthumb'] == 3 )//image url
			{
				$item['src'] = $item['homeimgfile'];
			}
			elseif( ! empty( $show_no_image ) )//no image
			{
				$item['src'] = NV_BASE_SITEURL . $show_no_image;
			}
			else
			{
				$item['imghome'] = '';
			}
			$item['alt'] = ! empty( $item['homeimgalt'] ) ? $item['homeimgalt'] : $item['title'];
			$item['width'] = $module_config[$module_name]['blockwidth'];

			$end_publtime = $item['publtime'];

			$item['link'] = $global_array_cat[$item['catid']]['link'] . '/' . $item['alias'] . '-' . $item['id'] . $global_config['rewrite_exturl'];
			$item['num_items'] = $num_items;
			$playlist_array[] = $item;
		}
		$result->closeCursor();
		unset( $result, $row );

		$playlist_other_array = array(); // check to remove
		$generate_page = nv_alias_page( $page_title, $base_url, $num_items, $per_page, $page );

		$pl_ss = md5( $playlist_id . session_id() . $global_config['sitekey'] );
		$contents = playlist_theme( $playlist_array, $playlist_other_array, $generate_page, $playlist_info, $playlist_id, $pl_ss );
	}
	else
	{
		Header( 'Location: ' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['playlists'], true ) );
		exit();
	}
}
else
{
	$playlist_info = '';
	$page_title = $module_info['custom_title'];
	$key_words = $module_info['keywords'];
	$playlist_info['title'] = $lang_module['playlist_show_list'];

	$result = $db->query( 'SELECT playlist_id as id, title, alias, image, hitstotal, description as hometext, keywords, add_time as publtime, private_mode, userid FROM ' . NV_PREFIXLANG . '_' . $module_data . '_playlist_cat WHERE status=1 ORDER BY weight ASC' );
	while( $item = $result->fetch() )
	{
		if( ! empty( $item['image'] ) AND file_exists( NV_ROOTDIR. '/' . NV_UPLOADS_DIR . '/' . $module_upload . '/img/' . $item['image'] ) )//image file
		{
			$item['src'] = videos_thumbs($item['id'], $item['image'], $module_upload, $module_config[$module_name]['homewidth'], $module_config[$module_name]['homeheight'], 90 );
		}
		elseif(nv_is_url($item['image']))
		{
			$item['src'] = $item['image'];
		}
		elseif( ! empty( $show_no_image ) )//no image
		{
			$item['src'] = NV_BASE_SITEURL . $show_no_image;
		}
		else
		{
			$item['src'] = '';
		}
		$item['alt'] = ! empty( $item['homeimgalt'] ) ? $item['homeimgalt'] : $item['title'];
		$item['width'] = $module_config[$module_name]['blockwidth'];

		$item['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['playlists'] . '/' . $item['alias'];
		$item['fake_id'] = 0;
		
		$db->sqlreset()
			->select( 'COUNT(*)' )
			->from( NV_PREFIXLANG . '_' . $module_data . '_playlist' )
			->where( 'playlist_id= ' . $item['id']);

		$num_items = $db->query( $db->sql() )->fetchColumn();
		$item['num_items'] = $num_items;
		
		if( $item['private_mode'] == 1 AND $user_info['userid'] != $item['userid'] AND !defined( 'NV_IS_MODADMIN' ) ) // Playlist rieng, chi cho phep MOD ADMIN va nguoi tao xem
		{
			unset ($item);
		}
		else
		{
			$playlist_array[] = $item;
		}
	}
	$result->closeCursor();
	unset( $result, $row );

	$playlist_other_array = array();
	$contents = playlist_theme( $playlist_array, '', '', $playlist_info, '','' );
}
include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
