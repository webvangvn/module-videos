<?php

/**
 * @Project VIDEOS 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @Website tradacongnghe.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate Oct 08, 2015 10:47:41 AM
 */

if( ! defined( 'NV_IS_MOD_VIDEOS' ) ) die( 'Stop!!!' );

require NV_ROOTDIR . '/modules/' . $module_file . '/site.functions.php'; // Include site lib

// Check if is user
if( isset($user_info) and $user_info['userid'] > 0 )
{
	// Check if is AJAX method
	$ajax = $nv_Request->get_int( 'ajax', 'post', '' );
	if( $ajax == 1 ) // Change order of vids in playlist and del vids in playlist
	{
		$id = $nv_Request->get_int( 'id', 'post', 0 );
		$playlist_id = $nv_Request->get_int( 'playlist_id', 'post', 0 );
		$mod = $nv_Request->get_string( 'mod', 'post', '' );
		$new_vid = $nv_Request->get_int( 'new_vid', 'post', 0 );
		$del_list = $nv_Request->get_string( 'del_list', 'post', '' );
		$content = "NO_" . $playlist_id;
		if( $playlist_id > 0 )
		{
			if( $del_list != '' )
			{
				$array_id = array_map( "intval", explode( ',', $del_list ) );
				foreach( $array_id as $id )
				{
					if( $id > 0 )
					{
						$db->query( "DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_playlist WHERE playlist_id=" . $playlist_id . " AND id=" . $id );
					}
				}
				nv_fix_playlist( $playlist_id );
				$content = "OK_" . $playlist_id;
			}
			elseif( $id > 0 )
			{
				list( $playlist_id, $id ) = $db->query( "SELECT playlist_id, id FROM " . NV_PREFIXLANG . "_" . $module_data . "_playlist WHERE playlist_id=" . intval( $playlist_id ) . " AND id=" . intval( $id ) )->fetch( 3 );
				if( $playlist_id > 0 and $id > 0 )
				{
					if( $mod == "playlist_sort" and $new_vid > 0 )
					{
						$query = "SELECT id FROM " . NV_PREFIXLANG . "_" . $module_data . "_playlist WHERE playlist_id=" . $playlist_id . " AND id!=" . $id . " ORDER BY playlist_sort ASC";
						$result = $db->query( $query );

						$playlist_sort = 0;
						while( $row = $result->fetch() )
						{
							++$playlist_sort;
							if( $playlist_sort == $new_vid ) ++$playlist_sort;
							$sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_playlist SET playlist_sort=" . $playlist_sort . " WHERE playlist_id=" . $playlist_id . " AND id=" . intval( $row['id'] );
							$db->query( $sql );
						}

						$result->closeCursor();
						$sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_playlist SET playlist_sort=" . $new_vid . " WHERE playlist_id=" . $playlist_id . " AND id=" . intval( $id );
						$db->query( $sql );

						$content = "OK_" . $playlist_id;
					}
				}
			}
			nv_fix_playlist( $playlist_id );
			$nv_Cache->delMod( $module_name );
		}
		include NV_ROOTDIR . '/includes/header.php';
		echo $content;
		include NV_ROOTDIR . '/includes/footer.php';
	}
	elseif( $ajax == 2 ) //Change playlist sharing status
	{
		$playlist_id = $nv_Request->get_int( 'playlist_id', 'post', 0 );
		$mod = $nv_Request->get_string( 'mod', 'post', '' );
		$new_vid = $nv_Request->get_int( 'new_vid', 'post', 0 );

		if( empty( $playlist_id ) ) die( 'NO_' . $playlist_id );
		$content = 'NO_' . $playlist_id;

		if( $mod == 'private_mode' and $playlist_id > 0 )
		{
			$new_vid = ( intval( $new_vid ) == 1 ) ? 1 : 0;
			$sql = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_playlist_cat SET private_mode=' . $new_vid . ' WHERE playlist_id=' . $playlist_id;
			$db->query( $sql );
			$content = 'OK_' . $playlist_id;
		}

		$nv_Cache->delMod( $module_name );

		include NV_ROOTDIR . '/includes/header.php';
		echo $content;
		include NV_ROOTDIR . '/includes/footer.php';
	}
	elseif( $ajax == 3 ) // Add video to playlist
	{
		$playlist_id = $nv_Request->get_int( 'playlist_id', 'post', 0 );
		$mod = $nv_Request->get_string( 'mod', 'post', '' );
		$id = $nv_Request->get_int( 'id', 'post', 0 );

		if( empty( $playlist_id ) or empty( $id ) )
		{
			$content = 'NO_LIST_OR_VIDEO';
		}

		$id = ( intval( $id ));
		$playlist_id = ( intval( $playlist_id ));
		if( $mod == 'add_user_playlist' and $playlist_id > 0 and $id >0 )
		{
			$check_video_inlist = $db->query( 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_playlist WHERE playlist_id=' . $playlist_id )->fetchColumn();
			$check_video = $db->query( 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_playlist WHERE id=' . $id . ' AND playlist_id=' . $playlist_id )->fetchColumn();
			if($check_video > 0)
			{
				$content = $lang_module['playlist_added_video'];
			}
			else
			{
				if( $check_video_inlist >= $module_config[$module_name]['playlist_max_items'])
				{
					$content = $lang_module['playlist_full'];
				}
				else
				{
					$sql = 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_playlist (playlist_id, id, playlist_sort) VALUES (' . $playlist_id . ', ' . $id . ', 0)';
					$db->query( $sql );
					nv_fix_playlist( $playlist_id );
					$content = $lang_module['playlist_added_video'];
				}
			}
			$nv_Cache->delMod( $module_name );
		}

		include NV_ROOTDIR . '/includes/header.php';
		echo $content;
		include NV_ROOTDIR . '/includes/footer.php';
	}
	elseif($ajax == 4) //Delete Playlist
	{
		$pid = $nv_Request->get_int( 'playlist_id', 'post', 0 );
		$playlist_id = $db->query( "SELECT playlist_id FROM " . NV_PREFIXLANG . "_" . $module_data . "_playlist_cat WHERE playlist_id=" . intval( $pid ) )->fetchColumn();
		if( $playlist_id > 0 )
		{
			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_del_playlistcat', "playlist_catid " . $playlist_id, $user_info['userid'] );
			$query = "DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_playlist_cat WHERE playlist_id=" . $playlist_id;
			if( $db->exec( $query ) )
			{
				$query = "DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_playlist WHERE playlist_id=" . $playlist_id;
				$db->query( $query );
				nv_fix_playlist_cat();
				$nv_Cache->delMod( $module_name );
				$content = "OK_" . $playlist_id;
			}
		}
		include NV_ROOTDIR . '/includes/header.php';
		echo $content;
		include NV_ROOTDIR . '/includes/footer.php';
	}

	// User playlist site function
	$array_mod_title[] = array(
		'catid' => 0,
		'title' => $lang_module['your_playlist'],
		'link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['user-playlist']
	);

	$page_title = $lang_module['your_playlist'];
	$error = '';
	$savecat = 0;

	// Get info from URL
	$raw_alias = isset( $array_op[1] ) ? trim( $array_op[1] ) : '';
	$arr_alias = explode('-',$raw_alias);
	$playlist_id = intval( end( $arr_alias ) );

	if($playlist_id > 0)
	{
		$sql = 'SELECT playlist_id, title, alias, image, description, keywords FROM ' . NV_PREFIXLANG . '_' . $module_data . '_playlist_cat ORDER BY weight ASC';
		$result = $db->query( $sql );

		$array_playlist = array();
		while( list( $playlist_id_i, $title_i, $alias_i, $image_i, $description_i, $keywords_i ) = $result->fetch( 3 ) )
		{
			$array_playlist[$playlist_id_i]['title'] = $title_i;
			$array_playlist[$playlist_id_i]['alias'] = $alias_i;
		}
		if( empty( $array_playlist ) )
		{
			Header( 'Location: ' . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
		}

		$cookie_playlist_id = $nv_Request->get_int( 'int_playlist_id', 'cookie', 0 );
		if( empty( $cookie_playlist_id ) or ! isset( $array_playlist[$cookie_playlist_id] ) )
		{
			$cookie_playlist_id = 0;
		}

		if( ! in_array( $playlist_id, array_keys( $array_playlist ) ) )
		{
			$playlist_id_array_id = array_keys( $array_playlist );
			$playlist_id = $playlist_id_array_id[0];
		}

		if( $cookie_playlist_id != $playlist_id )
		{
			$nv_Request->set_Cookie( 'int_playlist_id', $playlist_id, NV_LIVE_COOKIE_TIME );
		}
		
		$page_title = $lang_module['your_playlist'] . ' - ' . $array_playlist[$playlist_id]['title'];

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
			Header( 'Location: ' . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&playlist_id=' . $playlist_id );
			die();
		}

		$select_options = array();
		foreach( $array_playlist as $xplaylist_id => $playlistname )
		{
			$select_options[NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;playlist_id=' . $xplaylist_id] = $playlistname;
		}

		$array_mod_title[] = array(
			'catid' => 0,
			'title' => $array_playlist[$playlist_id]['title'],
			'link' => nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' .  NV_OP_VARIABLE . '=' .$module_info['alias']['user-playlist'] . '/' . $array_playlist[$playlist_id]['alias'] .'-'. $playlist_id, true )
		);

		$xtpl = new XTemplate( 'user-playlist.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
		$xtpl->assign( 'LANG', $lang_module );
		$xtpl->assign( 'GLANG', $lang_global );
		$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
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
			$page_title = $lang_module['addtoplaylist'];
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

				$xtpl->parse( 'playlist_single.news.loop' );
			}

			foreach( $array_playlist as $xplaylist_id => $playlistname )
			{
				$xtpl->assign( 'PLAYLIST_ID', array(
					'key' => $xplaylist_id,
					'title' => $playlistname,
					'selected' => $xplaylist_id == $playlist_id ? ' selected="selected"' : ''
				) );
				$xtpl->parse( 'playlist_single.news.playlist_id' );
			}

			$xtpl->assign( 'CHECKSESS', md5( session_id() ) );
			$xtpl->parse( 'playlist_single.news' );
		}

		$xtpl->parse( 'playlist_single' );
		$contents = $xtpl->text( 'playlist_single' );
	}
	else
	{
		list( $playlist_id, $title, $alias, $description, $image, $keywords, $status, $private_mode ) = array( 0, '', '', '', '', '', 1, 1 );
		// Get Alias
		if( $nv_Request->isset_request( 'get_alias', 'post' ) )
		{
			$title = $nv_Request->get_title( 'get_alias', 'post', '' );
			$alias = strtolower(change_alias( $title ));

			include NV_ROOTDIR . '/includes/header.php';
			echo $alias;
			include NV_ROOTDIR . '/includes/footer.php';
		}

		$savecat = $nv_Request->get_int( 'savecat', 'post', 0 );
		if( ! empty( $savecat ) )
		{
			$playlist_id = $nv_Request->get_int( 'playlist_id', 'post', 0 );
			$title = $nv_Request->get_title( 'title', 'post', '', 1 );
			$keywords = $nv_Request->get_title( 'keywords', 'post', '', 1 );
			$alias = $nv_Request->get_title( 'alias', 'post', '' );
			$description = $nv_Request->get_string( 'description', 'post', '' );
			$description = nv_nl2br( nv_htmlspecialchars( strip_tags( $description ) ), '<br/>' );
			$alias = ( $alias == '' ) ? change_alias( $title ) : change_alias( $alias );
			$status = ( $module_config[$module_name]['playlist_moderate'] > 0 )? 1 : 2 ;
			$private_mode = $nv_Request->get_int( 'private_mode', 'post', 0 );

			$image = $nv_Request->get_string( 'image', 'post', '' );
			
			if( !(nv_is_url( $image ) ))
			{
				$image = '';
			}

			if( empty( $title ) )
			{
				$error = $lang_module['error_name'];
			}
			elseif( $playlist_id == 0 )
			{
				$weight = $db->query( "SELECT max(weight) FROM " . NV_PREFIXLANG . "_" . $module_data . "_playlist_cat" )->fetchColumn();
				$weight = intval( $weight ) + 1;

				$sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_playlist_cat ( numbers, title, alias, status, private_mode, userid, description, image, weight, keywords, add_time, edit_time) VALUES (20, :title , :alias, :status, :private_mode, :userid, :description, :image, :weight, :keywords, " . NV_CURRENTTIME . ", " . NV_CURRENTTIME . ")";
				$data_insert = array();
				$data_insert['title'] = $title;
				$data_insert['alias'] = $alias;
				$data_insert['status'] = $status;
				$data_insert['private_mode'] = $private_mode;
				$data_insert['userid'] = $user_info['userid'];
				$data_insert['image'] = $image;
				$data_insert['description'] = $description;
				$data_insert['weight'] = $weight;
				$data_insert['keywords'] = $keywords;

				if( $db->insert_id( $sql, 'playlist_id', $data_insert ) )
				{
					nv_insert_logs( NV_LANG_DATA, $module_name, 'log_add_playlistcat', " ", $user_info['userid'] );
					Header( 'Location: ' . nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op, true ));
					die();
				}
				else
				{
					$error = $lang_module['errorsave'];
				}
			}
			else
			{
				$stmt = $db->prepare( "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_playlist_cat SET title= :title, alias = :alias, status = :status, private_mode = :private_mode, description= :description, image= :image, keywords= :keywords, edit_time=" . NV_CURRENTTIME . " WHERE playlist_id =" . $playlist_id );
				$stmt->bindParam( ':title', $title, PDO::PARAM_STR );
				$stmt->bindParam( ':alias', $alias, PDO::PARAM_STR );
				$stmt->bindParam( ':status', $status, PDO::PARAM_STR );
				$stmt->bindParam( ':private_mode', $private_mode, PDO::PARAM_STR );
				$stmt->bindParam( ':description', $description, PDO::PARAM_STR );
				$stmt->bindParam( ':image', $image, PDO::PARAM_STR );
				$stmt->bindParam( ':keywords', $keywords, PDO::PARAM_STR );
				$stmt->execute();
				if( $stmt->execute() )
				{
					nv_insert_logs( NV_LANG_DATA, $module_name, 'log_edit_playlistcat', "playlist_id " . $playlist_id, $user_info['userid'] );
					Header( 'Location: ' . nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op, true ));
					die();
				}
				else
				{
					$error = $lang_module['errorsave'];
				}
			}
		}

		$array_share_mode = array(
			$lang_module['playlist_private_off'],
			$lang_module['playlist_private_on']
		);

		$playlist_id = $nv_Request->get_int( 'playlist_id', 'get', 0 );
		$mode = $nv_Request->get_string( 'mode', 'get', 0 );
		if( $playlist_id > 0 and $mode == 'edit')
		{
			list( $playlist_id, $title, $alias, $description, $image, $keywords, $status, $private_mode ) = $db->query( "SELECT playlist_id, title, alias, description, image, keywords, status, private_mode FROM " . NV_PREFIXLANG . "_" . $module_data . "_playlist_cat where playlist_id=" . $playlist_id )->fetch( 3 );
			$lang_module['add_playlist_cat'] = $lang_module['edit_playlist_cat'];
		}

		$xtpl = new XTemplate( 'user-playlist.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
		$xtpl->assign( 'LANG', $lang_module );
		$xtpl->assign( 'GLANG', $lang_global );
		$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
		$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
		$xtpl->assign( 'MODULE_NAME', $module_name );
		$xtpl->assign( 'OP', $op );

		$xtpl->assign( 'PLAYLIST_CAT_LIST', nv_show_playlist_cat_list() );

		$xtpl->assign( 'PLAYLIST_ID', $playlist_id );
		$xtpl->assign( 'title', $title );
		$xtpl->assign( 'alias', $alias );
		$xtpl->assign( 'keywords', $keywords );
		$xtpl->assign( 'image', $image );
		
		if( nv_is_url($image) )
		{
			$xtpl->assign( 'disabled', '' );
		}
		else
		{
			$xtpl->assign( 'disabled', 'disabled="disabled"' );
		}
		
		$xtpl->assign( 'description', nv_htmlspecialchars( nv_br2nl( $description ) ) );

		foreach( $array_share_mode as $key => $val )
		{
			$xtpl->assign( 'PRIVATE_MODE', array(
				'key' => $key,
				'title' => $val,
				'selected' => $key == $private_mode ? ' selected="selected"' : ''
			) );
			$xtpl->parse( 'main.edit_playlist.private_mode' );
		}
					
		if( ! empty( $error ) )
		{
			$xtpl->assign( 'ERROR', $error );
			$xtpl->parse( 'main.error' );
		}


		if( empty( $alias ) )
		{
			$xtpl->parse( 'main.getalias' );
		}
		
		if( $module_config[$module_name]['allow_user_plist'] == 1 )
		{
			$xtpl->parse( 'main.edit_playlist' );
		}
		else
		{
			$xtpl->parse( 'main.userpl_disable' );
		}
		$xtpl->parse( 'main' );
		$contents = $xtpl->text( 'main' );
	}
}
else
{
	Header( 'Location: ' . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=users&' . NV_OP_VARIABLE . '=login' );
	die;
}

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';