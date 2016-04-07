<?php

/**
 * @Project VIDEOS 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @Website tradacongnghe.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate Oct 08, 2015 10:47:41 AM
 */

if( ! defined( 'NV_IS_MOD_VIDEOS' ) ) die( 'Stop!!!' );

$contents = '';
$publtime = 0;

if( nv_user_in_groups( $global_array_cat[$catid]['groups_view'] ) )
{

	$query = $db->query( 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_' . $catid . ' WHERE id = ' . $id );
	$news_contents = $query->fetch();
	if( $news_contents['id'] > 0 )
	{
		$body_contents = $db->query( 'SELECT bodyhtml as bodytext, sourcetext, copyright, allowed_send, allowed_save, gid FROM ' . NV_PREFIXLANG . '_' . $module_data . '_bodyhtml_' . ceil( $news_contents['id'] / 2000 ) . ' where id=' . $news_contents['id'] )->fetch();
		$news_contents = array_merge( $news_contents, $body_contents );
		unset( $body_contents );

		$show_no_image = $module_config[$module_name]['show_no_image'];
		if(empty($show_no_image))
		{
			$show_no_image = 'themes/default/images/' . $module_name . '/' . 'video_placeholder.png';
		}

		if( defined( 'NV_IS_MODADMIN' ) or ( $news_contents['status'] == 1 and $news_contents['publtime'] < NV_CURRENTTIME and ( $news_contents['exptime'] == 0 or $news_contents['exptime'] > NV_CURRENTTIME ) ) )
		{
			$time_set = $nv_Request->get_int( $module_data . '_' . $op . '_' . $id, 'session' );
			if( empty( $time_set ) )
			{
				$nv_Request->set_Session( $module_data . '_' . $op . '_' . $id, NV_CURRENTTIME );
				$query = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_rows SET hitstotal=hitstotal+1 WHERE id=' . $id;
				$db->query( $query );

				$array_catid = explode( ',', $news_contents['listcatid'] );
				foreach( $array_catid as $catid_i )
				{
					$query = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_' . $catid_i . ' SET hitstotal=hitstotal+1 WHERE id=' . $id;
					$db->query( $query );
				}
			}
			if( ! empty( $news_contents['homeimgfile'] ) )
			{
				$src = $alt = $note = '';
				$width = $height = 0;
				if( $news_contents['homeimgthumb'] == 1 )
				{
					$src = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $module_upload . '/img/' . $news_contents['homeimgfile'];
					$news_contents['homeimgfile'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/img/' . $news_contents['homeimgfile'];
					$width = $module_config[$module_name]['homewidth'];
				}
				elseif( $news_contents['homeimgthumb'] == 3 )
				{
					$src = $news_contents['homeimgfile'];
				}
				elseif( file_exists( NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/img/' . $news_contents['homeimgfile'] ) )
				{
					$src = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/img/' . $news_contents['homeimgfile'];
					$news_contents['homeimgfile'] = $src;
				}

				if( ! empty( $src ) )
				{
					$meta_property['og:image'] = ( preg_match( '/^(http|https|ftp|gopher)\:\/\//', $src ) ) ? $src : NV_MY_DOMAIN . $src;
				}
				elseif( !empty( $show_no_image ) )
				{
					$meta_property['og:image'] = NV_MY_DOMAIN . NV_BASE_SITEURL . $show_no_image;
				}
			}
			elseif( ! empty( $show_no_image ) )
			{
				$meta_property['og:image'] = NV_MY_DOMAIN . NV_BASE_SITEURL . $show_no_image;
			}
			if( $alias_url == $news_contents['alias'] )
			{
				$publtime = intval( $news_contents['publtime'] );
			}
			
			// Export video link
			$href_vid = array();
			if( ! empty( $news_contents['vid_path'] ) )
			{
				if( $news_contents['vid_type'] == 1 )
				{
					$href_vid['link'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/vid/' . $news_contents['vid_path'];
					$href_vid['quality'] = '';
				}
				elseif( $news_contents['vid_type'] == 2 )
				{
					$href_vid['link'] = $news_contents['vid_path'];
					$href_vid['quality'] = '';
				}
				elseif( $news_contents['vid_type'] == 3 )
				{
					$href_vid = get_link_mp4_picasa($news_contents['vid_path']);
				}
				elseif( $news_contents['vid_type'] == 4 )
				{
					$href_vid = get_facebook_mp4($news_contents['vid_path']);
				}
				elseif( $news_contents['vid_type'] == 5 )
				{
					$href_vid['link'] = $news_contents['vid_path'];
					$href_vid['quality'] = '';
				}
			}
			$link_embed = NV_MY_DOMAIN . NV_BASE_SITEURL . $module_file . '/player/' . rand(1000,9999) . 0 .'-' . md5( $news_contents['id'] . session_id() . $global_config['sitekey'] ) . '-'. rand(1000,9999) . $news_contents['id'] . '-embed/';
			$http_url = NV_MY_DOMAIN . NV_BASE_SITEURL . 'themes/default/modules/' . $module_file . '/jwplayer/jwplayer5.swf?config=' . $link_embed;
			
			$meta_property['og:type'] = 'video';
			if($news_contents['vid_type'] ==3 OR $news_contents['vid_type'] == 4)
			{
				$meta_property['og:video'] = $http_url;
			}
			else
			{
				$https_url = preg_replace('/^http:/i', 'https:', $http_url);
				$meta_property['og:video'] = $https_url;
			}
			
			$meta_property['og:url'] = $client_info['selfurl'];
			$meta_property['og:title'] = $news_contents['title'];
			$meta_property['og:video:type'] = 'application/x-shockwave-flash';
			$meta_property['og:video:width'] = '480';
			$meta_property['og:video:height'] = '360';
			$meta_property['og:published_time'] = date( 'Y-m-dTH:i:s', $news_contents['publtime'] );
			$meta_property['og:updated_time'] = date( 'Y-m-dTH:i:s', $news_contents['edittime'] );
		}

		if( defined( 'NV_IS_MODADMIN' ) and $news_contents['status'] != 1 )
		{
			$alert = sprintf( $lang_module['status_alert'], $lang_module['status_' . $news_contents['status']] );
			$my_head .= "<script type=\"text/javascript\">alert('". $alert ."')</script>";
		}
	}

	if( $publtime == 0 )
	{
		$redirect = '<meta http-equiv="Refresh" content="3;URL=' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name, true ) . '" />';
		nv_info_die( $lang_global['error_404_title'], $lang_global['error_404_title'], $lang_global['error_404_content'] . $redirect );
	}


	$base_url_rewrite = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $global_array_cat[$news_contents['catid']]['alias'] . '/' . $news_contents['alias'] . '-' . $news_contents['id'] . $global_config['rewrite_exturl'], true );
	if( $_SERVER['REQUEST_URI'] == $base_url_rewrite )
	{
		$canonicalUrl = NV_MAIN_DOMAIN . $base_url_rewrite;
	}
	elseif( NV_MAIN_DOMAIN . $_SERVER['REQUEST_URI'] != $base_url_rewrite )
	{
		Header( 'Location: ' . $base_url_rewrite );
		die();
	}
	else
	{
		$canonicalUrl = $base_url_rewrite;
	}

	$news_contents['url_sendmail'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=sendmail/' . $global_array_cat[$catid]['alias'] . '/' . $news_contents['alias'] . '-' . $news_contents['id'] . $global_config['rewrite_exturl'], true );
	$news_contents['url_savefile'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=savefile/' . $global_array_cat[$catid]['alias'] . '/' . $news_contents['alias'] . '-' . $news_contents['id'] . $global_config['rewrite_exturl'], true );

	$sql = 'SELECT title, link, logo FROM ' . NV_PREFIXLANG . '_' . $module_data . '_sources WHERE sourceid = ' . $news_contents['sourceid'];
	$result = $db->query( $sql );

	list( $sourcetext, $source_link, $source_logo ) = $result->fetch( 3 );
	unset( $sql, $result );

	$news_contents['newscheckss'] = md5( $news_contents['id'] . session_id() . $global_config['sitekey'] );
	$news_contents['fake_pl_id'] = 0;
	
	if($news_contents['admin_name'] == $lang_module['guest_post'] )
	{
		unset($news_contents['uploader_link']);
	}
	else
	{
		$news_contents['upload_alias'] = change_alias(  $news_contents['admin_name']  );
		$news_contents['upload_alias'] = strtolower( $news_contents['upload_alias'] );
		$news_contents['uploader_link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=uploader/' . $news_contents['upload_alias'] . '-' . $news_contents['admin_id'];
	}
			
	if( $module_config[$module_name]['config_source'] == 0 ) $news_contents['source'] = $sourcetext;
	elseif( $module_config[$module_name]['config_source'] == 1 ) $news_contents['source'] = $source_link;
	elseif( $module_config[$module_name]['config_source'] == 2 && ! empty( $source_logo ) ) $news_contents['source'] = '<img width="100px" src="' . NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/source/' . $source_logo . '">';

	$related_new_array = array();
	$related_array = array();
	if ( $st_links > 0)
	{
		$db->sqlreset()
			->select( 'id, title, alias, publtime, homeimgfile, homeimgthumb, hometext' )
			->from( NV_PREFIXLANG . '_' . $module_data . '_' . $catid )
			->where( 'status=1 AND publtime > ' . $publtime )
			->order( 'id ASC' )
			->limit( $st_links );

		$related = $db->query( $db->sql() );
		while( $row = $related->fetch() )
		{
			if( $row['homeimgthumb'] == 1 OR $row['homeimgthumb'] == 2 ) //image file
			{
				$row['imghome'] = videos_thumbs($row['id'], $row['homeimgfile'], $module_upload, $module_config[$module_name]['homewidth'], $module_config[$module_name]['homeheight'], 90 );
			}
			elseif( $row['homeimgthumb'] == 3 ) //image url
			{
				$row['imghome'] = $row['homeimgfile'];
			}
			elseif( ! empty( $show_no_image ) ) //no image
			{
				$row['imghome'] = NV_BASE_SITEURL . $show_no_image;
			}
			else
			{
				$row['imghome'] = '';
			}

			$link = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $global_array_cat[$catid]['alias'] . '/' . $row['alias'] . '-' . $row['id'] . $global_config['rewrite_exturl'];
			$related_new_array[] = array(
				'title' => $row['title'],
				'time' => $row['publtime'],
				'link' => $link,
				'newday' => $global_array_cat[$catid]['newday'],
				'hometext' => $row['hometext'],
				'imghome' => $row['imghome']
			);
		}
		$related->closeCursor();

		sort( $related_new_array, SORT_NUMERIC );

		$db->sqlreset()
			->select( 'id, title, alias, publtime, homeimgfile, homeimgthumb, hometext' )
			->from( NV_PREFIXLANG . '_' . $module_data . '_' . $catid )
			->where( 'status=1 AND publtime < ' . $publtime )
			->order( 'id DESC' )
			->limit( $st_links );

		$related = $db->query( $db->sql() );
		while( $row = $related->fetch() )
		{
			if( $row['homeimgthumb'] == 1 OR $row['homeimgthumb'] == 2 ) //image file
			{
				$row['imghome'] = videos_thumbs($row['id'], $row['homeimgfile'], $module_upload, $module_config[$module_name]['homewidth'], $module_config[$module_name]['homeheight'], 90 );
			}
			elseif( $row['homeimgthumb'] == 3 ) //image url
			{
				$row['imghome'] = $row['homeimgfile'];
			}
			elseif( ! empty( $show_no_image ) ) //no image
			{
				$row['imghome'] = NV_BASE_SITEURL . $show_no_image;
			}
			else
			{
				$row['imghome'] = '';
			}

			$link = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $global_array_cat[$catid]['alias'] . '/' . $row['alias'] . '-' . $row['id'] . $global_config['rewrite_exturl'];
			$related_array[] = array(
				'title' => $row['title'],
				'time' => $row['publtime'],
				'link' => $link,
				'newday' => $global_array_cat[$catid]['newday'],
				'hometext' => $row['hometext'],
				'homeimgthumb' => $row['homeimgthumb'],
				'imghome' => $row['imghome']
			);
		}

		$related->closeCursor();
		unset( $related, $row );
	}
	
	if( $news_contents['allowed_rating'] )
	{
		$time_set_rating = $nv_Request->get_int( $module_name . '_' . $op . '_' . $news_contents['id'], 'cookie', 0 );
		if( $time_set_rating > 0 )
		{
			$news_contents['disablerating'] = 1;
		}
		else
		{
			$news_contents['disablerating'] = 0;
		}
		$news_contents['stringrating'] = sprintf( $lang_module['stringrating'], $news_contents['total_rating'], $news_contents['click_rating'] );
		$news_contents['numberrating'] = ( $news_contents['click_rating'] > 0 ) ? round( $news_contents['total_rating'] / $news_contents['click_rating'], 1 ) : 0;
		$news_contents['langstar'] = array(
			'note' => $lang_module['star_note'],
			'verypoor' => $lang_module['star_verypoor'],
			'poor' => $lang_module['star_poor'],
			'ok' => $lang_module['star_ok'],
			'good' => $lang_module['star_good}'],
			'verygood' => $lang_module['star_verygood']
		);
	}

	list( $post_username, $post_first_name, $post_last_name ) = $db->query( 'SELECT username, first_name, last_name FROM ' . NV_USERS_GLOBALTABLE . ' WHERE userid = ' . $news_contents['admin_id'] )->fetch( 3 );
	$news_contents['post_name'] = nv_show_name_user( $post_first_name, $post_last_name, $post_username );

	$array_keyword = array();
	$key_words = array();
	$_query = $db->query( 'SELECT a1.keyword, a2.alias FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tags_id a1 INNER JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_tags a2 ON a1.tid=a2.tid WHERE a1.id=' . $news_contents['id'] );
	while( $row = $_query->fetch() )
	{
		$array_keyword[] = $row;
		$key_words[] = $row['keyword'];
		$meta_property['article:tag'][] = $row['keyword'];
	}

	// comment
	if( isset( $site_mods['comment'] ) and isset( $module_config[$module_name]['activecomm'] ) )
	{
		define( 'NV_COMM_ID', $id );//ID bài viết hoặc
	    define( 'NV_COMM_AREA', $module_info['funcs'][$op]['func_id'] );//để đáp ứng comment ở bất cứ đâu không cứ là bài viết
	    //check allow comemnt
	    $allowed = $module_config[$module_name]['allowed_comm'];//tuy vào module để lấy cấu hình. Nếu là module news thì có cấu hình theo bài viết
	    if( $allowed == '-1' )
	    {
	       $allowed = $news_contents['allowed_comm'];
	    }
	    define( 'NV_PER_PAGE_COMMENT', 5 ); //Số bản ghi hiển thị bình luận
	    require_once NV_ROOTDIR . '/modules/comment/comment.php';
	    $area = ( defined( 'NV_COMM_AREA' ) ) ? NV_COMM_AREA : 0;
	    $checkss = md5( $module_name . '-' . $area . '-' . NV_COMM_ID . '-' . $allowed . '-' . NV_CACHE_PREFIX );

	    $content_comment = nv_comment_module( $module_name, $checkss, $area, NV_COMM_ID, $allowed, 1 );
    }
	else
	{
		$content_comment = '';
	}
	
	$array_user_playlist = array();
	// call user playlist
	if( isset($user_info['userid']) AND $user_info['userid'] > 0)
	{
		$sql = 'SELECT playlist_id, title, status FROM ' . NV_PREFIXLANG . '_' . $module_data . '_playlist_cat WHERE userid=' . $user_info['userid'] . ' AND status > 0 ORDER BY weight ASC';
		$array_user_playlist = $db->query( $sql )->fetchAll();
	}
	
	$contents = detail_theme( $news_contents, $href_vid, $array_keyword, $related_new_array, $related_array, $content_comment, $array_user_playlist );
	$id_profile_googleplus = $news_contents['gid'];

	$page_title = $news_contents['title'];
	$key_words = implode( ', ', $key_words );
	$description = $news_contents['hometext'];
}
else
{
	$contents = no_permission( $global_array_cat[$catid]['groups_view'] );
}

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
