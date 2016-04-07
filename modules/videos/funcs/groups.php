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

if( isset( $array_op[1] ) )
{
	$alias = trim( $array_op[1] );
	$page = (isset( $array_op[2] ) and substr( $array_op[2], 0, 5 ) == 'page-') ? intval( substr( $array_op[2], 5 ) ) : 1;

	$stmt = $db->prepare( 'SELECT bid, title, alias, image, description, keywords FROM ' . NV_PREFIXLANG . '_' . $module_data . '_block_cat WHERE alias= :alias' );
	$stmt->bindParam( ':alias', $alias, PDO::PARAM_STR );
	$stmt->execute();
	list( $bid, $page_title, $alias, $image_group, $description, $key_words ) = $stmt->fetch( 3 );
	if( $bid > 0 )
	{
		$base_url_rewrite = $base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['groups'] . '/' . $alias;

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
			->join( 'INNER JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_block t2 ON t1.id = t2.id' )
			->where( 't2.bid= ' . $bid . ' AND t1.status= 1' );

		$num_items = $db->query( $db->sql() )->fetchColumn();

		$db->select( 't1.id, t1.catid, t1.admin_id, t1.admin_name, t1.author, t1.sourceid, t1.addtime, t1.edittime, t1.publtime, t1.title, t1.alias, t1.hometext, t1.homeimgfile, t1.homeimgalt, t1.homeimgthumb, t1.allowed_rating, t1.hitstotal, t1.hitscm, t1.total_rating, t1.click_rating, t2.weight' )
			->order( 't2.weight ASC' )
			->limit( $per_page )
			->offset( ($page - 1) * $per_page );

		$result = $db->query( $db->sql() );
		while( $item = $result->fetch() )
		{
			if( $item['homeimgthumb'] == 1 OR $item['homeimgthumb'] == 2 ) //image file
			{
				$item['src'] = videos_thumbs($item['id'], $item['homeimgfile'], $module_upload, $module_config[$module_name]['homewidth'], $module_config[$module_name]['homeheight'], 90 );
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
				$item['src'] = '';
			}

			$item['title_cut'] = nv_clean60( $item['title'], $module_config[$module_name]['titlecut'], true );
			$item['alt'] = ! empty( $item['homeimgalt'] ) ? $item['homeimgalt'] : $item['title'];
			$item['width'] = $module_config[$module_name]['homewidth'];
			
			if($item['admin_name'] == $lang_module['guest_post'] )
			{
				unset($item['uploader_link']);
			}
			else
			{
				$item['upload_alias'] = change_alias(  $item['admin_name']  );
				$item['upload_alias'] = strtolower( $item['upload_alias'] );
				$item['uploader_link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=uploader/' . $item['upload_alias'] . '-' . $item['admin_id'];
			}

			$end_weight = $item['weight'];

			$item['link'] = $global_array_cat[$item['catid']]['link'] . '/' . $item['alias'] . '-' . $item['id'] . $global_config['rewrite_exturl'];
			$item_array[] = $item;
		}
		$result->closeCursor();
		unset( $query, $row );

		$item_array_other = array();
		if ( $st_links > 0)
		{
			$db->sqlreset()
				->select( 't1.id, t1.catid, t1.addtime, t1.edittime, t1.publtime, t1.title, t1.alias, t1.hitstotal' )
				->from( NV_PREFIXLANG . '_' . $module_data . '_rows t1' )
				->join( 'INNER JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_block t2 ON t1.id = t2.id' )
				->where( 't2.bid= ' . $bid . ' AND t2.weight > ' . $end_weight )
				->order( 't2.weight ASC' )
				->limit( $st_links );
			$result = $db->query( $db->sql() );
			while( $item = $result->fetch() )
			{
				$item['link'] = $global_array_cat[$item['catid']]['link'] . '/' . $item['alias'] . '-' . $item['id'] . $global_config['rewrite_exturl'];
				$item_array_other[] = $item;
			}
			unset( $query, $row );
		}

		$generate_page = nv_alias_page( $page_title, $base_url, $num_items, $per_page, $page );
		if( ! empty( $image_group ) )
		{
			$image_group = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $module_upload . '/' . $image_group;
		}
		$contents = tag_theme( $item_array, $item_array_other, $generate_page, $page_title, $description, $image_group );
	}
}
else
{
	$array_cat = array();
	$groups_info = '';
	$groups_info['title'] = $lang_module['groups_show_list'];
	
	// cache call
	$cache_file = NV_LANG_DATA . '_' . $module_name . '_' . $op . '_' .  md5( $op ) .'_' . NV_CACHE_PREFIX . '.cache';
	if( ! defined( 'NV_IS_MODADMIN' ) )
	{
		if( ( $cache = $nv_Cache->getItem( $module_name, $cache_file ) ) != false )
		{
			$time_set_cache = NV_CURRENTTIME - filemtime( NV_ROOTDIR . '/' . NV_CACHEDIR .'/'. $module_name . '/' . $cache_file);
			$contents = $cache;
		}
	}
	if( empty( $contents )  )
	{
		$query_cat = $db->query( 'SELECT bid as id, title, alias, image, description, weight FROM ' . NV_PREFIXLANG . '_' . $module_data . '_block_cat ORDER BY weight ASC' );

		while( $item = $query_cat->fetch() )
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
			$item['alt'] = $item['title'];
			$item['width'] = $module_config[$module_name]['blockwidth'];

			$item['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['groups'] . '/' . $item['alias'];
			$item['publtime'] = 0;
			
			$array_cat[] = $item;
		}
		
		$query_cat->closeCursor();
		unset( $query_cat, $item );
		
		$contents = playlist_theme( $array_cat, '', '', $groups_info, '','' );

		if( ! defined( 'NV_IS_MODADMIN' ) and $contents != '' and $cache_file != '' )
		{
			$nv_Cache->setItem( $module_name, $cache_file, $contents );
		}
	}

	$page_title = $module_info['funcs']['groups']['func_custom_name'];
	$key_words = $module_info['keywords'];
}

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
