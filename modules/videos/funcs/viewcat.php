<?php

/**
 * @Project VIDEOS 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @Website tradacongnghe.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate Oct 08, 2015 10:47:41 AM
 */

if( ! defined( 'NV_IS_MOD_VIDEOS' ) ) die( 'Stop!!!' );

$cache_file = '';
$contents = '';
$viewcat = $global_array_cat[$catid]['viewcat'];

$base_url_rewrite = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $global_array_cat[$catid]['alias'];
if( $page > 1 )
{
	$base_url_rewrite .= '/page-' . $page;
}
$base_url_rewrite = nv_url_rewrite( $base_url_rewrite, true );
if( $_SERVER['REQUEST_URI'] != $base_url_rewrite and NV_MAIN_DOMAIN . $_SERVER['REQUEST_URI'] != $base_url_rewrite )
{
	Header( 'Location: ' . $base_url_rewrite );
	die();
}

$set_view_page = ( $page > 1 and substr( $viewcat, 0, 13 ) == 'viewcat_main_' ) ? true : false;

if( ! defined( 'NV_IS_MODADMIN' ) and $page < 5 )
{
	if( $set_view_page )
	{
		$cache_file = NV_LANG_DATA . '_' . $module_info['template'] . '_' . $op . '_' . $catid . '_page_' . $page . '_' . NV_CACHE_PREFIX . '.cache';
	}
	else
	{
		$cache_file = NV_LANG_DATA . '_' . $module_info['template'] . '_' . $op . '_' . $catid . '_' . $page . '_' . NV_CACHE_PREFIX . '.cache';
	}
	if( ( $cache = $nv_Cache->getItem( $module_name, $cache_file ) ) != false )
	{
		$contents = $cache;
	}
}

$page_title = ( ! empty( $global_array_cat[$catid]['titlesite'] ) ) ? $global_array_cat[$catid]['titlesite'] : $global_array_cat[$catid]['title'];
$key_words = $global_array_cat[$catid]['keywords'];
$description = $global_array_cat[$catid]['description'];
$global_array_cat[$catid]['description'] = $global_array_cat[$catid]['descriptionhtml'];
if( ! empty($global_array_cat[$catid]['image']))
{
	$meta_property['og:image'] = NV_MY_DOMAIN . NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/img/' . $global_array_cat[$catid]['image'];
}

if( empty( $contents ) )
{
	$array_catpage = array();
	$array_cat_other = array();
	$base_url = $global_array_cat[$catid]['link'];
	$show_no_image = $module_config[$module_name]['show_no_image'];
	if(empty($show_no_image))
	{
		$show_no_image = 'themes/default/images/' . $module_name . '/' . 'video_placeholder.png';
	}

	if( $viewcat == 'viewcat_page_new' or $viewcat == 'viewcat_page_old' or $set_view_page )
	{
		$order_by = ( $viewcat == 'viewcat_page_new' ) ? 'publtime DESC' : 'publtime ASC';

		$db->sqlreset()
			->select( 'COUNT(*)' )
			->from( NV_PREFIXLANG . '_' . $module_data . '_' . $catid )
			->where( 'status=1' );

		$num_items = $db->query( $db->sql() )->fetchColumn();

		$db->select( 'id, listcatid, admin_id, admin_name, author, sourceid, addtime, edittime, publtime, title, alias, hometext, homeimgfile, homeimgalt, homeimgthumb, allowed_rating, hitstotal, hitscm, total_rating, click_rating' );

		$db->where( 'status=1' )
			->order( $order_by )
			->limit( $per_page )
			->offset( ( $page - 1 ) * $per_page );
		$result = $db->query( $db->sql() );
		$end_publtime = 0;
		while( $item = $result->fetch() )
		{
			if( $item['homeimgthumb'] == 1 OR $item['homeimgthumb'] == 2 ) //image file
			{
				$item['imghome'] = videos_thumbs($item['id'], $item['homeimgfile'], $module_upload, $module_config[$module_name]['homewidth'], $module_config[$module_name]['homeheight'], 90 );
			}
			elseif( $item['homeimgthumb'] == 3 ) //image url
			{
				$item['imghome'] = $item['homeimgfile'];
			}
			elseif( ! empty( $show_no_image ) ) //no image
			{
				$item['imghome'] = NV_BASE_SITEURL . $show_no_image;
			}
			else
			{
				$item['imghome'] = '';
			}
			$item['newday'] = $global_array_cat[$catid]['newday'];
			$item['link'] = $global_array_cat[$catid]['link'] . '/' . $item['alias'] . '-' . $item['id'] . $global_config['rewrite_exturl'];
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
			$array_catpage[] = $item;
			$end_publtime = $item['publtime'];
		}
		if( $st_links > 0)
		{
			$db->sqlreset()
				->select( 'id, listcatid, addtime, edittime, publtime, title, alias, hitstotal' )
				->from( NV_PREFIXLANG . '_' . $module_data . '_' . $catid )
				->order( $order_by )
				->limit( $st_links );
			if( $viewcat == 'viewcat_page_new' )
			{
				$db->where( 'status=1 AND publtime < ' . $end_publtime );
			}
			else
			{
				$db->where( 'status=1 AND publtime > ' . $end_publtime );
			}
			$result = $db->query( $db->sql() );
			while( $item = $result->fetch() )
			{
				$item['newday'] = $global_array_cat[$catid]['newday'];
				$item['link'] = $global_array_cat[$catid]['link'] . '/' . $item['alias'] . '-' . $item['id'] . $global_config['rewrite_exturl'];
				$array_cat_other[] = $item;
			}
		}
		$generate_page = nv_alias_page( $page_title, $base_url, $num_items, $per_page, $page );
		$contents = viewcat_page_new( $array_catpage, $array_cat_other, $generate_page );
	}
	elseif( $viewcat == 'viewgrid_by_cat' ) // Phan theo tung chuyen muc dang luoi
	{
		$array_catcontent = array();
		$array_subcatpage = array();

		$db->sqlreset()
			->select( 'COUNT(*)' )
			->from( NV_PREFIXLANG . '_' . $module_data . '_' . $catid )
			->where( 'status=1' );

		$num_items = $db->query( $db->sql() )->fetchColumn();

		$db->select( 'id, listcatid, admin_id, admin_name, author, sourceid, addtime, edittime, publtime, title, alias, hometext, homeimgfile, homeimgalt, homeimgthumb, allowed_rating, hitstotal, hitscm, total_rating, click_rating' );

		$db->order( 'id DESC' )
			->where( 'status=1' )
			->limit( $per_page )
			->offset( ( $page - 1 ) * $per_page );

		$result = $db->query( $db->sql() );
		while( $item = $result->fetch() )
		{
			if( $item['homeimgthumb'] == 1 OR $item['homeimgthumb'] == 2 ) //image file
			{
				$item['imghome'] = videos_thumbs($item['id'], $item['homeimgfile'], $module_upload, $module_config[$module_name]['homewidth'], $module_config[$module_name]['homeheight'], 90 );
			}
			elseif( $item['homeimgthumb'] == 3 ) //image url
			{
				$item['imghome'] = $item['homeimgfile'];
			}
			elseif( ! empty( $show_no_image ) ) //no image
			{
				$item['imghome'] = NV_BASE_SITEURL . $show_no_image;
			}
			else
			{
				$item['imghome'] = '';
			}
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
			$item['newday'] = $global_array_cat[$catid]['newday'];
			$item['link'] = $global_array_cat[$catid]['link'] . '/' . $item['alias'] . '-' . $item['id'] . $global_config['rewrite_exturl'];
			$array_catcontent[] = $item;
		}
		unset( $sql, $result );

		$array_cat_other = array();

		if( $global_array_cat[$catid]['subcatid'] != '' )
		{
			$key = 0;
			$array_catid = explode( ',', $global_array_cat[$catid]['subcatid'] );

			foreach( $array_catid as $catid_i )
			{
				$array_cat_other[$key] = $global_array_cat[$catid_i];
				$db->sqlreset()
					->select( 'id, catid, listcatid, admin_id, admin_name, author, sourceid, addtime, edittime, publtime, title, alias, hometext, homeimgfile, homeimgalt, homeimgthumb, allowed_rating, hitstotal, hitscm, total_rating, click_rating' )
					->from( NV_PREFIXLANG . '_' . $module_data . '_' . $catid_i )
					->where( 'status=1' )->limit( $global_array_cat[$catid_i]['numlinks'] )
					->order( 'publtime DESC' );
				$result = $db->query( $db->sql() );
				while( $item = $result->fetch() )
				{
					if( $item['homeimgthumb'] == 1 OR $item['homeimgthumb'] == 2 ) //image file
					{
						$item['imghome'] = videos_thumbs($item['id'], $item['homeimgfile'], $module_upload, $module_config[$module_name]['homewidth'], $module_config[$module_name]['homeheight'], 90 );
					}
					elseif( $item['homeimgthumb'] == 3 ) //image url
					{
						$item['imghome'] = $item['homeimgfile'];
					}
					elseif( ! empty( $show_no_image ) ) //no image
					{
						$item['imghome'] = NV_BASE_SITEURL . $show_no_image;
					}
					else
					{
						$item['imghome'] = '';
					}

					$item['newday'] = $global_array_cat[$catid_i]['newday'];
					$item['link'] = $global_array_cat[$catid_i]['link'] . '/' . $item['alias'] . '-' . $item['id'] . $global_config['rewrite_exturl'];
					$array_cat_other[$key]['content'][] = $item;
				}

				unset( $sql, $result );
				++$key;
			}

			unset( $array_catid );
		}
		$generate_page = nv_alias_page( $page_title, $base_url, $num_items, $per_page, $page );
		$contents .= call_user_func( 'viewsubcat_main', $viewcat, $array_cat_other );
	}
	elseif( $viewcat == 'viewcat_grid_new' or $viewcat == 'viewcat_grid_old' )
	{
		$order_by = ( $viewcat == 'viewcat_grid_new' ) ? 'publtime DESC' : 'publtime ASC';

		$db->sqlreset()
			->select( 'COUNT(*)' )
			->from( NV_PREFIXLANG . '_' . $module_data . '_' . $catid )
			->where( 'status=1' );

		$num_items = $db->query( $db->sql() )->fetchColumn();

		$db->select( 'id, listcatid, admin_id, admin_name, author, sourceid, addtime, edittime, publtime, title, alias, hometext, homeimgfile, homeimgalt, homeimgthumb, allowed_rating, hitstotal, hitscm, total_rating, click_rating' )
			->order( $order_by )
			->limit( $per_page )
			->offset( ( $page - 1 ) * $per_page );

		$result = $db->query( $db->sql() );
		while( $item = $result->fetch() )
		{
			if( $item['homeimgthumb'] == 1 OR $item['homeimgthumb'] == 2 ) //image file
			{
				$item['imghome'] = videos_thumbs($item['id'], $item['homeimgfile'], $module_upload, $module_config[$module_name]['homewidth'], $module_config[$module_name]['homeheight'], 90 );
			}
			elseif( $item['homeimgthumb'] == 3 ) //image url
			{
				$item['imghome'] = $item['homeimgfile'];
			}
			elseif( ! empty( $show_no_image ) ) //no image
			{
				$item['imghome'] = NV_BASE_SITEURL . $show_no_image;
			}
			else
			{
				$item['imghome'] = '';
			}
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
			$item['newday'] = $global_array_cat[$catid]['newday'];
			$item['link'] = $global_array_cat[$catid]['link'] . '/' . $item['alias'] . '-' . $item['id'] . $global_config['rewrite_exturl'];
			$array_catpage[] = $item;
		}

		$viewcat = 'viewcat_grid_new';
		$generate_page = nv_alias_page( $page_title, $base_url, $num_items, $per_page, $page );
		$contents = call_user_func( $viewcat, $array_catpage, $catid, $generate_page );
	}

	if( ! defined( 'NV_IS_MODADMIN' ) and $contents != '' and $cache_file != '' )
	{
		$nv_Cache->setItem( $module_name, $cache_file, $contents );
	}
}

if( $page > 1 )
{
	$page_title .= ' ' . NV_TITLEBAR_DEFIS . ' ' . $lang_global['page'] . ' ' . $page;
	$description .= ' ' . $page;
}

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
