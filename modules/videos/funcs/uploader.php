<?php

/**
 * @Project VIDEOS 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @Website tradacongnghe.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate Oct 08, 2015 10:47:41 AM
 */

if( ! defined( 'NV_IS_MOD_VIDEOS' ) ) die( 'Stop!!!' );

$array_page = explode( '-', $array_op[1] );
$uploader_id = intval( end( $array_page ) );
$number = strlen( $uploader_id ) + 1;
$alias = substr( $array_op[1], 0, -$number );

if( isset( $array_op[2] ) )
{
	if( sizeof( $array_op ) == 3 and preg_match( '/^page\-([0-9]+)$/', $array_op[2], $m ) )
	{
		$page = intval( $m[1] );
	}
	else
	{
		$alias = '';
	}
}

$u_info = array();
$db->sqlreset()
	->select( 'userid, username, first_name, last_name' )
	->from( NV_USERS_GLOBALTABLE  )
	->where( 'userid=' . $uploader_id );
	$result = $db->query( $db->sql() );
	while( $uploader_info = $result->fetch() )
	{
		if(!empty($uploader_info))
		{
			if ( !empty($uploader_info['first_name']) OR !empty($uploader_info['last_name']) )
			{
				$uploader_info['uploader_name'] = $uploader_info['first_name'] . ' ' . $uploader_info['last_name'];
			}
			else
			{
				$uploader_info['uploader_name'] = $uploader_info['username'];
			}
		}
		else
		{
			$alias = trim( str_replace( '-', ' ', $alias ) );
			$uploader_info['uploader_name'] = $alias;
		}
		$u_info = $uploader_info;
	}

$show_no_image = $module_config[$module_name]['show_no_image'];
if(empty($show_no_image))
{
	$show_no_image = 'themes/default/images/' . $module_name . '/' . 'video_placeholder.png';
}

if( ! empty( $alias ) and ! empty( $uploader_id ) )
{
	$item_array = array();
	$end_publtime = 0;
	$alias = $db->dblikeescape( nv_htmlspecialchars( $alias ) );

	$db->sqlreset()
		->select( 'COUNT(*)' )
		->from( NV_PREFIXLANG . '_' . $module_data . '_rows' )
		->where( 'status=1 AND admin_id=' . $uploader_id );
	
	$num_items = $db->query( $db->sql() )->fetchColumn();
	$description = $num_items . $lang_module['playlist_num_news'];
	if($num_items > 0)
	{
		$db->select( 'id, catid, admin_id, admin_name, author, artist, sourceid, addtime, edittime, publtime, title, alias, hometext, homeimgfile, homeimgalt, homeimgthumb, allowed_rating, hitstotal, hitscm, total_rating, click_rating' )
			->order( 'publtime DESC' )
			->limit( $per_page )
			->offset( ( $page - 1 ) * $per_page );

		$result = $db->query( $db->sql() );
		while( $item = $result->fetch() )
		{
			if( $item['homeimgthumb'] == 1 OR $item['homeimgthumb'] == 2 ) //image file
			{
				$item['src'] = videos_thumbs($item['id'], $item['homeimgfile'], $module_upload, $module_config[$module_name]['homewidth'], $module_config[$module_name]['homeheight'], 90 );
			}
			elseif( $item['homeimgthumb'] == 3 ) //image url
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
			$item['width'] = $module_config[$module_name]['homewidth'];

			$end_publtime = $item['publtime'];

			$item['link'] = $global_array_cat[$item['catid']]['link'] . '/' . $item['alias'] . '-' . $item['id'] . $global_config['rewrite_exturl'];
			$item['title_cut'] = nv_clean60( $item['title'], $module_config[$module_name]['titlecut'], true );
			$item_array[] = $item;
		}
		$result->closeCursor();
		unset( $query, $row );

		$page_title = $lang_module['uploaded_by'] . ' ' . $u_info['uploader_name'];
		$base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=uploader/' . $alias . $uploader_id;
		if( $page > 1 )
		{
			$page_title .= ' ' . NV_TITLEBAR_DEFIS . ' ' . $lang_global['page'] . ' ' . $page;
		}

		$array_mod_title[] = array(
			'catid' => 0,
			'title' => $page_title,
			'link' => $base_url
		);
		$generate_page = nv_alias_page( $page_title, $base_url, $num_items, $per_page, $page );
		$contents = tag_theme( $item_array, $generate_page, $page_title, $description, '' );

		if( $page > 1 )
		{
			$page_title .= ' ' . NV_TITLEBAR_DEFIS . ' ' . $lang_global['page'] . ' ' . $page;
		}
		include NV_ROOTDIR . '/includes/header.php';
		echo nv_site_theme( $contents );
		include NV_ROOTDIR . '/includes/footer.php';
	}
	else
	{
		$redirect = '<meta http-equiv="Refresh" content="3;URL=' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name, true ) . '" />';
		nv_info_die( $lang_global['error_404_title'], $lang_global['error_404_title'], $lang_global['error_404_content'] . $redirect );
	}
}
$redirect = '<meta http-equiv="Refresh" content="3;URL=' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name, true ) . '" />';
nv_info_die( $lang_global['error_404_title'], $lang_global['error_404_title'], $lang_global['error_404_content'] . $redirect );
