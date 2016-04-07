<?php

/**
 * @Project VIDEOS 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @Website tradacongnghe.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate Oct 08, 2015 10:47:41 AM
 */

if( ! defined( 'NV_IS_MOD_VIDEOS' ) ) die( 'Stop!!!' );

$alias = $nv_Request->get_title( 'alias', 'get' );
$array_op = explode( '/', $alias );
$alias = $array_op[0];

if( isset( $array_op[1] ) )
{
	if( sizeof( $array_op ) == 2 and preg_match( '/^page\-([0-9]+)$/', $array_op[1], $m ) )
	{
		$page = intval( $m[1] );
	}
	else
	{
		$alias = '';
	}
}
$page_title = trim( str_replace( '-', ' ', $alias ) );

$show_no_image = $module_config[$module_name]['show_no_image'];
if(empty($show_no_image))
{
	$show_no_image = 'themes/default/images/' . $module_name . '/' . 'video_placeholder.png';
}


if( ! empty( $page_title ) and $page_title == strip_punctuation( $page_title ) )
{
	$stmt = $db->prepare( 'SELECT tid, image, description, keywords FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tags WHERE alias= :alias' );
	$stmt->bindParam( ':alias', $alias, PDO::PARAM_STR );
	$stmt->execute();
	list( $tid, $image_tag, $description, $key_words ) = $stmt->fetch( 3 );

	if( $tid > 0 )
	{
		$base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=tag/' . $alias;
		if( $page > 1 )
		{
			$page_title .= ' ' . NV_TITLEBAR_DEFIS . ' ' . $lang_global['page'] . ' ' . $page;
		}

		$array_mod_title[] = array(
			'catid' => 0,
			'title' => $page_title,
			'link' => $base_url
		);

		$item_array = array();
		$end_publtime = 0;

		$db->sqlreset()
			->select( 'COUNT(*)' )
			->from( NV_PREFIXLANG . '_' . $module_data . '_rows' )
			->where( 'status=1 AND id IN (SELECT id FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tags_id WHERE tid=' . $tid . ')' );

		$num_items = $db->query( $db->sql() )->fetchColumn();

		$db->select( 'id, catid, admin_id, admin_name, author, sourceid, addtime, edittime, publtime, title, alias, hometext, homeimgfile, homeimgalt, homeimgthumb, allowed_rating, hitstotal, hitscm, total_rating, click_rating' )
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
			$item['title_cut'] = nv_clean60( $item['title'], $module_config[$module_name]['titlecut'], true );

			$end_publtime = $item['publtime'];
			
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
			$item['link'] = $global_array_cat[$item['catid']]['link'] . '/' . $item['alias'] . '-' . $item['id'] . $global_config['rewrite_exturl'];
			$item_array[] = $item;
		}
		$result->closeCursor();
		unset( $query, $row );

		$generate_page = nv_alias_page( $page_title, $base_url, $num_items, $per_page, $page );

		if( ! empty( $image_tag ) )
		{
			$image_tag = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $module_upload . '/' . $image_tag;
		}
		$contents = tag_theme( $item_array, $generate_page, $page_title, $description, $image_tag );

		if( $page > 1 )
		{
			$page_title .= ' ' . NV_TITLEBAR_DEFIS . ' ' . $lang_global['page'] . ' ' . $page;
		}
		include NV_ROOTDIR . '/includes/header.php';
		echo nv_site_theme( $contents );
		include NV_ROOTDIR . '/includes/footer.php';
	}
}

$redirect = '<meta http-equiv="Refresh" content="3;URL=' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name, true ) . '" />';
nv_info_die( $lang_global['error_404_title'], $lang_global['error_404_title'], $lang_global['error_404_content'] . $redirect );
