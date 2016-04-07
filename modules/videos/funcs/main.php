<?php

/**
 * @Project VIDEOS 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @Website tradacongnghe.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate Oct 08, 2015 10:47:41 AM
 */

if( ! defined( 'NV_IS_MOD_VIDEOS' ) ) die( 'Stop!!!' );

$page_title = $module_info['custom_title'];
$key_words = $module_info['keywords'];

$contents = '';
$cache_file = '';

$base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name;
$base_url_rewrite = nv_url_rewrite( $base_url, true );
$page_url_rewrite = $page ? nv_url_rewrite( $base_url . '/page-' . $page, true ) : $base_url_rewrite;
$request_uri = $_SERVER['REQUEST_URI'];
if( ! ( $home OR $request_uri == $base_url_rewrite OR $request_uri == $page_url_rewrite OR NV_MAIN_DOMAIN . $request_uri == $base_url_rewrite OR NV_MAIN_DOMAIN . $request_uri == $page_url_rewrite ) )
{
	$redirect = '<meta http-equiv="Refresh" content="3;URL=' . $base_url_rewrite . '" />';
	nv_info_die( $lang_global['error_404_title'], $lang_global['error_404_title'], $lang_global['error_404_content'] . $redirect );
}
if( ! defined( 'NV_IS_MODADMIN' ) and $page < 5 )
{
	$cache_file = NV_LANG_DATA . '_' . $module_info['template'] . '-' . $op . '-' . $page . '-' . NV_CACHE_PREFIX . '.cache';
	if( ( $cache = $nv_Cache->getItem( $module_name, $cache_file ) ) != false )
	{
		$contents = $cache;
	}
}

if( empty( $contents ) )
{
	$viewcat = $module_config[$module_name]['indexfile'];
	$show_no_image = $module_config[$module_name]['show_no_image'];
	if(empty($show_no_image))
	{
		$show_no_image = 'themes/default/images/' . $module_name . '/' . 'video_placeholder.png';
	}
	$array_catpage = array();
	$array_cat_other = array();

	if(  $viewcat == 'viewcat_none' )
	{
		$contents = '';
	}
	elseif( $viewcat == 'viewcat_page_new' or $viewcat == 'viewcat_page_old' )
	{
		$order_by = ( $viewcat == 'viewcat_page_new' ) ? 'publtime DESC' : 'publtime ASC';
		$db->sqlreset()
			->select( 'COUNT(*)' )
			->from( NV_PREFIXLANG . '_' . $module_data . '_rows' )
			->where( 'status= 1 AND inhome=1' );

		$num_items = $db->query( $db->sql() )->fetchColumn();

		$db->select( 'id, catid, listcatid, admin_id, admin_name, author, sourceid, addtime, edittime, publtime, title, alias, hometext, homeimgfile, homeimgalt, homeimgthumb, allowed_rating, hitstotal, hitscm, total_rating, click_rating' )
			->order( $order_by )
			->limit( $per_page )
			->offset( ( $page - 1 ) * $per_page );

		$end_publtime = 0;

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
				$item['uploader_link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=uploader/' . $item['admin_name']  . '-' . $item['admin_id'];
			}

			$item['newday'] = $global_array_cat[$item['catid']]['newday'];
			$item['link'] = $global_array_cat[$item['catid']]['link'] . '/' . $item['alias'] . '-' . $item['id'] . $global_config['rewrite_exturl'];
			$array_catpage[] = $item;
			$end_publtime = $item['publtime'];
		}

		if( $st_links > 0)
		{
			$db->sqlreset()
				->select('id, catid, addtime, edittime, publtime, title, alias, hitstotal')
				->from( NV_PREFIXLANG . '_' . $module_data . '_rows' );

			if( $viewcat == 'viewcat_page_new' )
			{
				$db->where( 'status= 1 AND inhome=1 AND publtime < ' . $end_publtime );
			}
			else
			{
				$db->where( 'status= 1 AND inhome=1 AND publtime > ' . $end_publtime );
			}
			$db->order( $order_by )->limit( $st_links );

			$result = $db->query( $db->sql() );
			while( $item = $result->fetch() )
			{
				$item['newday'] = $global_array_cat[$item['catid']]['newday'];
				$item['link'] = $global_array_cat[$item['catid']]['link'] . '/' . $item['alias'] . '-' . $item['id'] . $global_config['rewrite_exturl'];
				$array_cat_other[] = $item;
			}
		}

		$viewcat = 'viewcat_page_new';
		$generate_page = nv_alias_page( $page_title, $base_url, $num_items, $per_page, $page );
		$contents = call_user_func( $viewcat, $array_catpage, $array_cat_other, $generate_page );
	}
	elseif( $viewcat == 'viewgrid_by_cat' )
	{
		$array_cat = array();

		$key = 0;
		$db->sqlreset()
		->select('id, listcatid, admin_id, admin_name, author, sourceid, addtime, edittime, publtime, title, alias, hometext, homeimgfile, homeimgalt, homeimgthumb, allowed_rating, hitstotal, hitscm, total_rating, click_rating' )
		->order( 'publtime DESC' );

		foreach( $global_array_cat as $_catid => $array_cat_i )
		{
			if( $array_cat_i['parentid'] == 0 and $array_cat_i['inhome'] == 1 )
			{
				$array_cat[$key] = $array_cat_i;
				$db->from( NV_PREFIXLANG . '_' . $module_data . '_' . $_catid )->where( 'status= 1 AND inhome=1' )->limit( $array_cat_i['numlinks'] );

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

					$item['newday'] = $array_cat_i['newday'];
					$item['link'] = $array_cat_i['link'] . '/' . $item['alias'] . '-' . $item['id'] . $global_config['rewrite_exturl'];
					if($item['admin_name'] == $lang_module['guest_post'] )
					{
						unset($item['uploader_link']);
					}
					else
					{
						$item['upload_alias'] = change_alias(  $item['admin_name']  );
						$item['upload_alias'] = strtolower( $item['upload_alias'] );
						$item['uploader_link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=uploader/' . $item['upload_alias']  . '-' . $item['admin_id'];
					}
					$array_cat[$key]['content'][] = $item;
				}
				++$key;
			}
		}

		$viewcat = 'viewgrid_by_cat';
		$contents = viewsubcat_main( $viewcat, $array_cat );
	}
	elseif( $viewcat == 'viewcat_grid_new' or $viewcat == 'viewcat_grid_old' )
	{
		$order_by = ( $viewcat == 'viewcat_grid_new' ) ? ' publtime DESC' : ' publtime ASC';
		$db->sqlreset()
			->select( 'COUNT(*) ')
			->from( NV_PREFIXLANG . '_' . $module_data . '_rows' )
			->where( 'status= 1 AND inhome=1' );

		$num_items = $db->query( $db->sql() )->fetchColumn();

		$db->select( 'id, catid, admin_id, admin_name, author, sourceid, addtime, edittime, publtime, title, alias, hometext, homeimgfile, homeimgalt, homeimgthumb, allowed_rating, hitstotal, hitscm, total_rating, click_rating' )
			->order( $order_by )
			->limit($per_page )
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
			elseif( !empty( $show_no_image ) )
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
			
			$item['newday'] = $global_array_cat[$item['catid']]['newday'];
			$item['link'] = $global_array_cat[$item['catid']]['link'] . '/' . $item['alias'] . '-' . $item['id'] . $global_config['rewrite_exturl'];
			$array_catpage[] = $item;
		}

		$viewcat = 'viewcat_grid_new';
		$generate_page = nv_alias_page( $page_title, $base_url, $num_items, $per_page, $page );
		$contents = call_user_func( $viewcat, $array_catpage, 0, $generate_page );
	}

	if( ! defined( 'NV_IS_MODADMIN' ) and $contents != '' and $cache_file != '' )
	{
		$nv_Cache->setItem( $module_name, $cache_file, $contents );
	}
}

if( $page > 1 )
{
	$page_title .= ' ' . NV_TITLEBAR_DEFIS . ' ' . $lang_global['page'] . ' ' . $page;
}

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
