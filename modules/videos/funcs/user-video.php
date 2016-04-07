<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 12-11-2010 20:40
 */

if( ! defined( 'NV_IS_MOD_VIDEOS' ) ) die( 'Stop!!!' );

if( defined( 'NV_EDITOR' ) )
{
	require_once NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php';
}
elseif( ! nv_function_exists( 'nv_aleditor' ) and file_exists( NV_ROOTDIR . '/' . NV_EDITORSDIR . '/ckeditor/ckeditor.js' ) )
{
	define( 'NV_EDITOR', true );
	define( 'NV_IS_CKEDITOR', true );
	$my_head .= '<script type="text/javascript" src="' . NV_BASE_SITEURL . NV_EDITORSDIR . '/ckeditor/ckeditor.js"></script>';

	function nv_aleditor( $textareaname, $width = '100%', $height = '450px', $val = '', $customtoolbar = '' )
	{
		global  $module_data;
		$return = '<textarea style="width: ' . $width . '; height:' . $height . ';" id="' . $module_data . '_' . $textareaname . '" name="' . $textareaname . '">' . $val . '</textarea>';
		$return .= "<script type=\"text/javascript\">
		CKEDITOR.replace( '" . $module_data . "_" . $textareaname . "', {" . ( ! empty( $customtoolbar ) ? 'toolbar : "' . $customtoolbar . '",' : '' ) . " width: '" . $width . "',height: '" . $height . "',});
		</script>";
		return $return;
	}
}

$page_title = $lang_module['content'];
$key_words = $module_info['keywords'];

// check user post content
$array_post_config = array();
$sql = 'SELECT group_id, addcontent, postcontent, editcontent, delcontent FROM ' . NV_PREFIXLANG . '_' . $module_data . '_config_post';
$result = $db->query( $sql );
while( list( $group_id, $addcontent, $postcontent, $editcontent, $delcontent ) = $result->fetch( 3 ) )
{
	$array_post_config[$group_id] = array(
		'addcontent' => $addcontent,
		'postcontent' => $postcontent,
		'editcontent' => $editcontent,
		'delcontent' => $delcontent
	);
}

$array_post_user = array(
	'addcontent' => isset( $array_post_config[5]['addcontent'] ) ? $array_post_config[5]['addcontent'] : 0,
	'postcontent' => isset( $array_post_config[5]['postcontent'] ) ? $array_post_config[5]['postcontent'] : 0,
	'editcontent' => isset( $array_post_config[5]['editcontent'] ) ? $array_post_config[5]['editcontent'] : 0,
	'delcontent' => isset( $array_post_config[5]['delcontent'] ) ? $array_post_config[5]['delcontent'] : 0
);

if( defined( 'NV_IS_USER' ) and isset( $array_post_config[4] ) )
{
	if( $array_post_config[4]['addcontent'] )
	{
		$array_post_user['addcontent'] = 1;
	}

	if( $array_post_config[4]['postcontent'] )
	{
		$array_post_user['postcontent'] = 1;
	}

	if( $array_post_config[4]['editcontent'] )
	{
		$array_post_user['editcontent'] = 1;
	}

	if( $array_post_config[4]['delcontent'] )
	{
		$array_post_user['delcontent'] = 1;
	}

	foreach( $user_info['in_groups'] as $group_id_i )
	{
		if( $group_id_i > 0 and isset( $array_post_config[$group_id_i] ) )
		{
			if( $array_post_config[$group_id_i]['addcontent'] )
			{
				$array_post_user['addcontent'] = 1;
			}

			if( $array_post_config[$group_id_i]['postcontent'] )
			{
				$array_post_user['postcontent'] = 1;
			}

			if( $array_post_config[$group_id_i]['editcontent'] )
			{
				$array_post_user['editcontent'] = 1;
			}

			if( $array_post_config[$group_id_i]['delcontent'] )
			{
				$array_post_user['delcontent'] = 1;
			}
		}
	}
}

if( $array_post_user['postcontent'] )
{
	$array_post_user['addcontent'] = 1;
}
// check user post content

$base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;

if( ! $array_post_user['addcontent'] )
{
	if( defined( 'NV_IS_USER' ) )
	{
		$array_temp['urlrefresh'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA;
	}
	else
	{
		$array_temp['urlrefresh'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=users&amp;' . NV_OP_VARIABLE . '=login&nv_redirect=' . nv_redirect_encrypt( $client_info['selfurl'] );
	}

	$array_temp['content'] = $lang_module['error_addcontent'];
	$template = $module_info['template'];

	if( ! file_exists( NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file . '/user-video.tpl' ) )
	{
		$template = 'default';
	}

	$array_temp['urlrefresh'] = nv_url_rewrite( $array_temp['urlrefresh'], true );

	$xtpl = new XTemplate( 'user-video.tpl', NV_ROOTDIR . '/themes/' . $template . '/modules/' . $module_file );
	$xtpl->assign( 'DATA', $array_temp );
	$xtpl->parse( 'mainrefresh' );
	$contents = $xtpl->text( 'mainrefresh' );

	include NV_ROOTDIR . '/includes/header.php';
	echo nv_site_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
}

if( $nv_Request->isset_request( 'get_alias', 'post' ) )
{
	$title = $nv_Request->get_title( 'get_alias', 'post', '' );
	$alias = change_alias( $title );
	$alias = strtolower( $alias );

	include NV_ROOTDIR . '/includes/header.php';
	echo $alias;
	include NV_ROOTDIR . '/includes/footer.php';
}

if( $nv_Request->isset_request( 'get_duration', 'post' ) )
{
	$path = $nv_Request->get_string( 'get_duration', 'post', '' );
	$mod = $nv_Request->get_string( 'mod', 'post', '' );
	$path = urldecode($path);
	if( !empty($path) AND is_youtube($path) )
	{
		$_vid_duration = youtubeVideoDuration($path);
		$duration = sec2hms($_vid_duration);
	}
	else
	{
		$duration = '';
	}

	include NV_ROOTDIR . '/includes/header.php';
	echo $duration;
	include NV_ROOTDIR . '/includes/footer.php';
}

$contentid = $nv_Request->get_int( 'contentid', 'get,post', 0 );
$fcheckss = $nv_Request->get_title( 'checkss', 'get,post', '' );
$checkss = md5( $contentid . $client_info['session_id'] . $global_config['sitekey'] );

if( $nv_Request->isset_request( 'contentid', 'get,post' ) and $fcheckss == $checkss )
{
	if( $contentid > 0 )
	{
		$rowcontent_old = $db->query( 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows where id=' . $contentid . ' and admin_id= ' . $user_info['userid'] )->fetch();
		$contentid = ( isset( $rowcontent_old['id'] ) ) ? intval( $rowcontent_old['id'] ) : 0;

		if( empty( $contentid ) )
		{
			Header( 'Location: ' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op, true ) );
			die();
		}

		if( $nv_Request->get_int( 'delcontent', 'get' ) and ( empty( $rowcontent_old['status'] ) or $array_post_user['delcontent'] ) )
		{
			nv_del_content_module( $contentid );

			$user_content = defined( 'NV_IS_USER' ) ? ' | ' . $user_info['username'] : '';
			nv_insert_logs( NV_LANG_DATA, $module_name, $lang_module['del_content'], $contentid . ' | ' . $client_info['ip'] . $user_content, 0 );

			if( $rowcontent_old['status'] == 1 )
			{
				$nv_Cache->delMod( $module_name );
			}

			Header( 'Location: ' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op, true ) );
			die();
		}
		elseif( ! ( empty( $rowcontent_old['status'] ) or $array_post_user['editcontent'] ) )
		{
			Header( 'Location: ' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op, true ) );
			die();
		}

		$page_title = $lang_module['update_content'];
	}
	else
	{
		$page_title = $lang_module['add_content'];
	}

	$array_mod_title[] = array(
		'catid' => 0,
		'title' => $lang_module['add_content'],
		'link' => $base_url
	);
	
	$admin_name = '';
	if(defined( 'NV_IS_USER' ))
	{
		if( !empty($user_info['first_name']) OR !empty($user_info['first_name'] ) )
		{
			$admin_name = $user_info['first_name'] . ' ' . $user_info['last_name'];
		}
		else
		{
			$admin_name = $user_info['username'];
		}
	}
	
	$rowcontent = array(
		'id' => '',
		'listcatid' => '',
		'catid' => ( $contentid > 0 ) ? $rowcontent_old['catid'] : 0,
		'admin_id' => ( defined( 'NV_IS_USER' ) ) ? $user_info['userid'] : 1, // Anonymous videos will be moderated by Admin.
		'admin_name' => ( defined( 'NV_IS_USER' ) ) ? $admin_name : $lang_module['guest_post'],
		'author' => '',
		'artist' => '',
		'sourceid' => 0,
		'addtime' => NV_CURRENTTIME,
		'edittime' => NV_CURRENTTIME,
		'status' => 0,
		'publtime' => NV_CURRENTTIME,
		'exptime' => 0,
		'archive' => 1,
		'title' => '',
		'alias' => '',
		'vid_path' => '',
		'vid_duration' => '',
		'vid_type' => '',
		'hometext' => '',
		'homeimgfile' => '',
		'homeimgalt' => '',
		'homeimgthumb' => 0,
		'bodyhtml' => '',
		'copyright' => 0,
		'inhome' => 1,
		'allowed_comm' => 4,
		'allowed_rating' => 1,
		'allowed_send' => 1,
		'allowed_save' => 1,
		'hitstotal' => 0,
		'hitscm' => 0,
		'total_rating' => 0,
		'click_rating' => 0,
		'keywords' => ''
	);

	$array_catid_module = array();
	$sql = 'SELECT catid, title, lev FROM ' . NV_PREFIXLANG . '_' . $module_data . '_cat ORDER BY sort ASC';
	$result_cat = $db->query( $sql );

	while( list( $catid_i, $title_i, $lev_i ) = $result_cat->fetch( 3 ) )
	{
		$array_catid_module[] = array(
			'catid' => $catid_i,
			'title' => $title_i,
			'lev' => $lev_i
		);
	}

	$error = '';

	if( $nv_Request->isset_request( 'contentid', 'post' ) )
	{
		$rowcontent['id'] = $contentid;
		$fcode = $nv_Request->get_title( 'fcode', 'post', '' );
		$catids = array_unique( $nv_Request->get_typed_array( 'catids', 'post', 'int', array() ) );

		$rowcontent['listcatid'] = implode( ',', $catids );
		$rowcontent['author'] = $nv_Request->get_title( 'author', 'post', '', 1 );
		$rowcontent['artist'] = $nv_Request->get_title( 'artist', 'post', '', 1 );

		$rowcontent['title'] = $nv_Request->get_title( 'title', 'post', '', 1 );
		$alias = $nv_Request->get_title( 'alias', 'post', '' );
		$rowcontent['alias'] = ( $alias == '' ) ? change_alias( $rowcontent['title'] ) : change_alias( $alias );

		$rowcontent['hometext'] = $nv_Request->get_title( 'hometext', 'post', '' );
		$rowcontent['vid_path'] = $nv_Request->get_title( 'vid_path', 'post', '' );
		$rowcontent['vid_duration'] = $nv_Request->get_title( 'vid_duration', 'post', '' );
		$rowcontent['homeimgfile'] = $nv_Request->get_title( 'homeimgfile', 'post', '' );
		$rowcontent['homeimgalt'] = $nv_Request->get_title( 'homeimgalt', 'post', '', 1 );
		$rowcontent['sourcetext'] = $nv_Request->get_title( 'sourcetext', 'post', '' );

		// Xu ly Video link
		$rowcontent['vid_type'] = 0;
		if(is_youtube($rowcontent['vid_path']))
		{
			$rowcontent['vid_type'] = 2; //is Youtube
		}
		elseif( is_picasa($rowcontent['vid_path']) )
		{
			$rowcontent['vid_type'] = 3; //is Picasa
		}
		else
		{
			$rowcontent['vid_path'] = ''; //return blank
			$rowcontent['vid_type'] = ''; //return blank
		}

		// Xu ly anh minh hoa
		$rowcontent['homeimgthumb'] = 0;
		if( nv_is_url( $rowcontent['homeimgfile'] ) )
		{
			$rowcontent['homeimgthumb'] = 3;
		}
		else
		{
			$rowcontent['homeimgfile'] = '';
			$rowcontent['homeimgthumb'] = 0;
		}
		
		// Auto-Thumb from Youtube - if empty Image
		if( ($rowcontent['vid_type'] == 2) AND ( empty($rowcontent['homeimgfile']) ) )
		{
			$rowcontent['homeimgfile'] = 'http://img.youtube.com/vi/' . get_youtube_id($rowcontent['vid_path']) . '/0.jpg';
			$rowcontent['homeimgthumb'] = 3;
		}
		
		// Auto-duration from Youtube - if empty 
		if( ($rowcontent['vid_type'] == 2) AND ( empty($rowcontent['vid_duration']) ) )
		{
			$_vid_duration = youtubeVideoDuration($rowcontent['vid_path']);
			$rowcontent['vid_duration'] = sec2hms($_vid_duration);
		}
		
		$bodyhtml = $nv_Request->get_string( 'bodyhtml', 'post', '' );
		$rowcontent['bodyhtml'] = defined( 'NV_EDITOR' ) ? nv_nl2br( $bodyhtml, '' ) : nv_nl2br( nv_htmlspecialchars( strip_tags( $bodyhtml ) ), '<br />' );

		$rowcontent['keywords'] = $nv_Request->get_title( 'keywords', 'post', '', 1 );

		if( empty( $rowcontent['title'] ) )
		{
			$error = $lang_module['error_title'];
		}
		elseif( empty( $rowcontent['listcatid'] ) )
		{
			$error = $lang_module['error_cat'];
		}
		elseif( ! nv_capcha_txt( $fcode ) )
		{
			$error = $lang_module['error_captcha'];
		}
		elseif( empty( $rowcontent['vid_path'] ) )
		{
			$error = $lang_module['error_vid_path'];
		}
		else
		{
			if( ( $array_post_user['postcontent'] ) && $nv_Request->isset_request( 'status1', 'post' ) ) $rowcontent['status'] = 1;
			elseif( $nv_Request->isset_request( 'status0', 'post' ) ) $rowcontent['status'] = 0;
			elseif( $nv_Request->isset_request( 'status4', 'post' ) ) $rowcontent['status'] = 4;
			$rowcontent['catid'] = in_array( $rowcontent['catid'], $catids ) ? $rowcontent['catid'] : $catids[0];
			$rowcontent['bodytext'] = nv_news_get_bodytext( $rowcontent['bodyhtml'] );

			$rowcontent['sourceid'] = 0;
			if( ! empty( $rowcontent['sourcetext'] ) )
			{
				$url_info = @parse_url( $rowcontent['sourcetext'] );

				if( isset( $url_info['scheme'] ) and isset( $url_info['host'] ) )
				{
					$sourceid_link = $url_info['scheme'] . '://' . $url_info['host'];
					$rowcontent['sourceid'] = $db->query( 'SELECT sourceid FROM ' . NV_PREFIXLANG . '_' . $module_data . '_sources WHERE link=' . $db->quote( $sourceid_link ) )->fetchColumn();

					if( empty( $rowcontent['sourceid'] ) )
					{
						$weight = $db->query( 'SELECT max(weight) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_sources' )->fetchColumn();
						$weight = intval( $weight ) + 1;
						$_sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_sources (title, link, logo, weight, add_time, edit_time) VALUES (" . $db->quote( $url_info['host'] ) . ", " . $db->quote( $sourceid_link ) . ", '', " . $db->quote( $weight ) . ", " . NV_CURRENTTIME . ", " . NV_CURRENTTIME . ")";
						$rowcontent['sourceid'] = $db->insert_id( $_sql, 'sourceid' );
					}
				}
			}
			if( $rowcontent['id'] == 0 )
			{
				$_sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_rows
						(catid, listcatid, admin_id, admin_name, author, artist, sourceid, addtime, edittime, status, publtime, exptime, archive, title, alias, hometext, vid_path, vid_duration, vid_type, homeimgfile, homeimgalt, homeimgthumb, inhome, allowed_comm, allowed_rating, hitstotal, hitscm, total_rating, click_rating) VALUES
						 (" . intval( $rowcontent['catid'] ) . ",
						 " . $db->quote( $rowcontent['listcatid'] ) . ",
						 " . intval( $rowcontent['admin_id'] ) . ",
						 " . $db->quote( $rowcontent['admin_name'] ) . ",
						 " . $db->quote( $rowcontent['author'] ) . ",
						 " . $db->quote( $rowcontent['artist'] ) . ",
						 " . intval( $rowcontent['sourceid'] ) . ",
						 " . intval( $rowcontent['addtime'] ) . ",
						 " . intval( $rowcontent['edittime'] ) . ",
						 " . intval( $rowcontent['status'] ) . ",
						 " . intval( $rowcontent['publtime'] ) . ",
						 " . intval( $rowcontent['exptime'] ) . ",
						 " . intval( $rowcontent['archive'] ) . ",
						 " . $db->quote( $rowcontent['title'] ) . ",
						 " . $db->quote( $rowcontent['alias'] ) . ",
						 " . $db->quote( $rowcontent['hometext'] ) . ",
						 " . $db->quote( $rowcontent['vid_path'] ) . ",
						 " . $db->quote( $rowcontent['vid_duration'] ) . ",
						 " . intval( $rowcontent['vid_type'] ) . ",
						 " . $db->quote( $rowcontent['homeimgfile'] ) . ",
						 " . $db->quote( $rowcontent['homeimgalt'] ) . ",
						 " . intval( $rowcontent['homeimgthumb'] ) . ",
						 " . intval( $rowcontent['inhome'] ) . ",
						 " . intval( $rowcontent['allowed_comm'] ) . ",
						 " . intval( $rowcontent['allowed_rating'] ) . ",
						 " . intval( $rowcontent['hitstotal'] ) . ",
						 " . intval( $rowcontent['hitscm'] ) . ",
						 " . intval( $rowcontent['total_rating'] ) . ",
						 " . intval( $rowcontent['click_rating'] ) . ")";

				$rowcontent['id'] = $db->insert_id( $_sql, 'id' );
				if( $rowcontent['id'] > 0 )
				{
					foreach( $catids as $catid )
					{
						$db->query( "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_" . $catid . " SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_rows WHERE id=" . $rowcontent['id'] );
					}

					$tbhtml = NV_PREFIXLANG . "_" . $module_data . "_bodyhtml_" . ceil( $rowcontent['id'] / 2000 );
					$db->query( "CREATE TABLE IF NOT EXISTS " . $tbhtml . " (id int(11) unsigned NOT NULL, bodyhtml longtext NOT NULL, sourcetext varchar(255) NOT NULL default '', copyright tinyint(1) NOT NULL default '0', allowed_send tinyint(1) NOT NULL default '0', allowed_save tinyint(1) NOT NULL default '0', PRIMARY KEY (id)) ENGINE=MyISAM" );
					$db->query( "INSERT INTO " . $tbhtml . " (id, bodyhtml, sourcetext, copyright, allowed_send, allowed_save, gid) VALUES (
							" . $rowcontent['id'] . ",
							" . $db->quote( $rowcontent['bodyhtml'] ) . ",
							" . $db->quote( $rowcontent['sourcetext'] ) . ",
			 				" . intval( $rowcontent['copyright'] ) . ",
			 				" . intval( $rowcontent['allowed_send'] ) . ",
			 				" . intval( $rowcontent['allowed_save'] ) . ", 0
						)" );

					$db->query( 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_bodytext VALUES (' . $rowcontent['id'] . ', ' . $db->quote( $rowcontent['bodytext'] ) . ')' );
					$user_content = defined( 'NV_IS_USER' ) ? ' | ' . $user_info['username'] : '';

					nv_insert_logs( NV_LANG_DATA, $module_name, $lang_module['add_content'], $rowcontent['title'] . ' | ' . $client_info['ip'] . $user_content, 0 );
				}
				else
				{
					$error = $lang_module['errorsave'];
				}
			}
			else
			{
				if( $rowcontent_old['status'] == 1 )
				{
					$rowcontent['status'] = 1;
				}

				$_sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_rows SET
						 catid=" . intval( $rowcontent['catid'] ) . ",
						 listcatid=" . $db->quote( $rowcontent['listcatid'] ) . ",
						 author=" . $db->quote( $rowcontent['author'] ) . ",
						 artist=" . $db->quote( $rowcontent['artist'] ) . ",
						 sourceid=" . intval( $rowcontent['sourceid'] ) . ",
						 status=" . intval( $rowcontent['status'] ) . ",
						 publtime=" . intval( $rowcontent['publtime'] ) . ",
						 exptime=" . intval( $rowcontent['exptime'] ) . ",
						 archive=" . intval( $rowcontent['archive'] ) . ",
						 title=" . $db->quote( $rowcontent['title'] ) . ",
						 alias=" . $db->quote( $rowcontent['alias'] ) . ",
						 vid_path=" . $db->quote( $rowcontent['vid_path'] ) . ",
						 vid_duration=" . $db->quote( $rowcontent['vid_duration'] ) . ",
 						 vid_type=" . intval( $rowcontent['vid_type'] ) . ",
						 hometext=" . $db->quote( $rowcontent['hometext'] ) . ",
						 homeimgfile=" . $db->quote( $rowcontent['homeimgfile'] ) . ",
						 homeimgalt=" . $db->quote( $rowcontent['homeimgalt'] ) . ",
						 homeimgthumb=" . intval( $rowcontent['homeimgthumb'] ) . ",
						 inhome=" . intval( $rowcontent['inhome'] ) . ",
						 allowed_comm=" . intval( $rowcontent['allowed_comm'] ) . ",
						 allowed_rating=" . intval( $rowcontent['allowed_rating'] ) . ",
						 edittime=" . NV_CURRENTTIME . "
						WHERE id =" . $rowcontent['id'];

				if( $db->exec( $_sql ) )
				{
					$array_cat_old = explode( ',', $rowcontent_old['listcatid'] );

					foreach( $array_cat_old as $catid )
					{
						$db->query( 'DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_' . $catid . ' WHERE id = ' . $rowcontent['id'] );
					}

					$array_cat_new = explode( ',', $rowcontent['listcatid'] );

					foreach( $array_cat_new as $catid )
					{
						$db->query( 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_' . $catid . ' SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE id=' . $rowcontent['id'] );
					}

					$db->query( "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_bodyhtml_" . ceil( $rowcontent['id'] / 2000 ) . " SET
							bodyhtml=" . $db->quote( $rowcontent['bodyhtml'] ) . ",
							 sourcetext=" . $db->quote( $rowcontent['sourcetext'] ) . ",
							 copyright=" . intval( $rowcontent['copyright'] ) . ",
							 allowed_send=" . intval( $rowcontent['allowed_send'] ) . ",
							 allowed_save=" . intval( $rowcontent['allowed_save'] ) . "
							WHERE id =" . $rowcontent['id'] );

					$db->query( 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_bodytext SET bodytext=' . $db->quote( $rowcontent['bodytext'] ) . ' WHERE id =' . $rowcontent['id'] );

					$user_content = defined( 'NV_IS_USER' ) ? ' | ' . $user_info['username'] : '';

					nv_insert_logs( NV_LANG_DATA, $module_name, $lang_module['update_content'], $rowcontent['title'] . ' | ' . $client_info['ip'] . $user_content, 0 );
				}
				else
				{
					$error = $lang_module['errorsave'];
				}
			}

			if( empty( $error ) )
			{
				$array_temp = array();

				if( defined( 'NV_IS_USER' ) )
				{
					$array_temp['urlrefresh'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op;

					if( $rowcontent['status'] )
					{
						$array_temp['content'] = $lang_module['save_content_ok'];
						$nv_Cache->delMod( $module_name );
					}
					else
					{
						$array_temp['content'] = $lang_module['save_content_waite'];
					}
				}
				elseif( $rowcontent['status'] == 1 and sizeof( $catids ) )
				{
					$catid = $catids[0];
					$array_temp['urlrefresh'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $global_array_cat[$catid]['alias'] . '/' . $rowcontent['alias'] . '-' . $rowcontent['id'];
					$array_temp['content'] = $lang_module['save_content_view_page'];
					$nv_Cache->delMod( $module_name );
				}
				else
				{
					$array_temp['urlrefresh'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA;
					$array_temp['content'] = $lang_module['save_content_waite_home'];
				}

				$template = $module_info['template'];

				if( ! file_exists( NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file . '/user-video.tpl' ) )
				{
					$template = 'default';
				}

				$array_temp['urlrefresh'] = nv_url_rewrite( $array_temp['urlrefresh'], true );

				$xtpl = new XTemplate( 'user-video.tpl', NV_ROOTDIR . '/themes/' . $template . '/modules/' . $module_file );
				$xtpl->assign( 'DATA', $array_temp );
				$xtpl->parse( 'mainrefresh' );
				$contents = $xtpl->text( 'mainrefresh' );

				include NV_ROOTDIR . '/includes/header.php';
				echo nv_site_theme( $contents );
				include NV_ROOTDIR . '/includes/footer.php';
			}
		}
	}
	elseif( $contentid > 0 )
	{
		$rowcontent = $db->query( 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows where id=' . $contentid )->fetch();

		if( empty( $rowcontent['id'] ) )
		{
			Header( 'Location: ' . nv_url_rewrite( NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true ) );
			die();
		}

		$body_contents = $db->query( 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_bodyhtml_' . ceil( $rowcontent['id'] / 2000 ) . ' where id=' . $rowcontent['id'] )->fetch();
		$rowcontent = array_merge( $rowcontent, $body_contents );
		unset( $body_contents );
	}

	if( ! empty( $rowcontent['homeimgfile'] ) and file_exists( NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/' . $rowcontent['homeimgfile'] ) )
	{
		$rowcontent['homeimgfile'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $rowcontent['homeimgfile'];
	}

	$rowcontent['bodyhtml'] = htmlspecialchars( nv_editor_br2nl( $rowcontent['bodyhtml'] ) );
	if( defined( 'NV_EDITOR' ) and nv_function_exists( 'nv_aleditor' ) )
	{
		$htmlbodyhtml = nv_aleditor( 'bodyhtml', '100%', '300px', $rowcontent['bodyhtml'], 'Basic' );
	}
	else
	{
		$htmlbodyhtml .= "<textarea class=\"textareaform\" name=\"bodyhtml\" id=\"bodyhtml\" cols=\"60\" rows=\"15\">" . $rowcontent['bodyhtml'] . "</textarea>";
	}

	if( ! empty( $error ) )
	{
		$my_head .= "<script type=\"text/javascript\">\n";
		$my_head .= "	alert('" . $error . "')\n";
		$my_head .= "</script>\n";
	}

	$template = $module_info['template'];

	if( ! file_exists( NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file . '/user-video.tpl' ) )
	{
		$template = 'default';
	}

	$xtpl = new XTemplate( 'user-video.tpl', NV_ROOTDIR . '/themes/' . $template . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'DATA', $rowcontent );
	$xtpl->assign( 'HTMLBODYTEXT', $htmlbodyhtml );

	$xtpl->assign( 'GFX_WIDTH', NV_GFX_WIDTH );
	$xtpl->assign( 'GFX_HEIGHT', NV_GFX_HEIGHT );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'CAPTCHA_REFRESH', $lang_global['captcharefresh'] );
	$xtpl->assign( 'CAPTCHA_REFR_SRC', NV_BASE_SITEURL . NV_FILES_DIR . '/images/refresh.png' );
	$xtpl->assign( 'NV_GFX_NUM', NV_GFX_NUM );
	$xtpl->assign( 'CHECKSS', $checkss );

	$xtpl->assign( 'CONTENT_URL', $base_url . '&contentid=' . $rowcontent['id'] . '&checkss=' . $checkss );
	$array_catid_in_row = explode( ',', $rowcontent['listcatid'] );

	foreach( $array_catid_module as $value )
	{
		$xtitle_i = '';

		if( $value['lev'] > 0 )
		{
			for( $i = 1; $i <= $value['lev']; ++$i )
			{
				$xtitle_i .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			}
		}

		$array_temp = array();
		$array_temp['value'] = $value['catid'];
		$array_temp['title'] = $xtitle_i . $value['title'];
		$array_temp['selected'] = ( in_array( $value['catid'], $array_catid_in_row ) ) ? ' selected="selected"' : '';

		$xtpl->assign( 'DATACATID', $array_temp );
		$xtpl->parse( 'main.catid' );
	}


	if( ! ( $rowcontent['status'] and $rowcontent['id'] ) )
	{
		$xtpl->parse( 'main.save_temp' );
	}

	if( $array_post_user['postcontent'] or ( $rowcontent['status'] and $rowcontent['id'] and $array_post_user['editcontent'] ) )
	{
		$xtpl->parse( 'main.postcontent' );
	}

	$xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );

	if( empty( $rowcontent['alias'] ) )
	{
		$contents .= "<script type=\"text/javascript\">\n";
		$contents .= '$("#idtitle").change(function () {
 		get_alias();
		});';
		$contents .= "</script>\n";
	}
	
	if( empty( $rowcontent['vid_duration'] ) )
	{
		$contents .= "<script type=\"text/javascript\">\n";
		$contents .= '$("#vid_path").change(function () {
 		get_duration();
		});';
		$contents .= "</script>\n";
	}
}
elseif( defined( 'NV_IS_USER' ) )
{
	$page = 1;

	if( isset( $array_op[1] ) and substr( $array_op[1], 0, 5 ) == 'page-' )
	{
		$page = intval( substr( $array_op[1], 5 ) );
	}

	$contents = "<div style=\"border: 1px solid #ccc;margin: 10px; font-size: 15px; font-weight: bold; text-align: center;\"><a href=\"" . $base_url . "&amp;contentid=0&checkss=" . md5( "0" . $client_info['session_id'] . $global_config['sitekey'] ) . "\">" . $lang_module['add_content'] . "</a></h1></div>";

	$array_catpage = array();

	$db->sqlreset()
		->select( 'COUNT(*)' )
		->from( NV_PREFIXLANG . '_' . $module_data . '_rows' )
		->where( 'admin_id= ' . $user_info['userid'] );

	$num_items = $db->query( $db->sql() )->fetchColumn();
	if( $num_items )
	{
		$db->select( 'id, catid, listcatid, admin_id, admin_name, author, artist, sourceid, addtime, edittime, status, publtime, title, alias, hometext, vid_path, vid_duration, homeimgfile, homeimgalt, homeimgthumb, allowed_rating, hitstotal, hitscm, total_rating, click_rating' )
			->order( 'id DESC' )
			->limit( $per_page )
			->offset( ( $page - 1 ) * $per_page );

		$result = $db->query( $db->sql() );
		while( $item = $result->fetch() )
		{
			if( $item['homeimgthumb'] == 1 OR $item['homeimgthumb'] == 2 ) //image file
			{
				$item['imghome'] = videos_thumbs($item['id'], $item['homeimgfile'], $module_upload, $module_config[$module_name]['homewidth'], $module_config[$module_name]['homeheight'], 90 );
			}
			elseif( $item['homeimgthumb'] == 3 ) // image url
			{
				$item['imghome'] = $item['homeimgfile'];
			}
			else // no image
			{
				$item['imghome'] = NV_BASE_SITEURL .  'themes/default/images/' . $module_name . '/' . 'video_placeholder.png';
			}

			$item['is_edit_content'] = ( empty( $item['status'] ) or $array_post_user['editcontent'] ) ? 1 : 0;
			$item['is_del_content'] = ( empty( $item['status'] ) or $array_post_user['delcontent'] ) ? 1 : 0;

			$catid = $item['catid'];
			$item['link'] = $global_array_cat[$catid]['link'] . '/' . $item['alias'] . '-' . $item['id'] . $global_config['rewrite_exturl'];
			$item['title_cut'] = nv_clean60( $item['title'], $module_config[$module_name]['titlecut'], true );
			$array_catpage[] = $item;
		}


		// parse content
		$xtpl = new XTemplate( 'viewcat_page.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
		$xtpl->assign( 'LANG', $lang_module );
		$xtpl->assign( 'IMGWIDTH1', $module_config[$module_name]['homewidth'] );

		$a = 0;
		foreach( $array_catpage as $array_row_i )
		{
			$array_row_i['publtime'] = nv_date( 'd/m/Y h:i:s A', $array_row_i['publtime'] );
			$xtpl->assign( 'CONTENT', $array_row_i );
			$id = $array_row_i['id'];
			$array_link_content = array();

			if( $array_row_i['is_edit_content'] )
			{
				$array_link_content[] = "<em class=\"fa fa-edit fa-lg\">&nbsp;</em> <a href=\"" . $base_url . "&amp;contentid=" . $id . "&amp;checkss=" . md5( $id . $client_info['session_id'] . $global_config['sitekey'] ) . "\">" . $lang_global['edit'] . "</a>";
			}

			if( $array_row_i['is_del_content'] )
			{
				$array_link_content[] = "<em class=\"fa fa-trash-o fa-lg\">&nbsp;</em> <a onclick=\"return confirm(nv_is_del_confirm[0]);\" href=\"" . $base_url . "&amp;contentid=" . $id . "&amp;delcontent=1&amp;checkss=" . md5( $id . $client_info['session_id'] . $global_config['sitekey'] ) . "\">" . $lang_global['delete'] . "</a>";
			}

			if( ! empty( $array_link_content ) )
			{
				$xtpl->assign( 'ADMINLINK', implode( '&nbsp;-&nbsp;', $array_link_content ) );
				$xtpl->parse( 'main.viewcatloop.news.adminlink' );
			}

			if( $array_row_i['imghome'] != '' )
			{
				$xtpl->assign( 'HOMEIMG1', $array_row_i['imghome'] );
				$xtpl->assign( 'HOMEIMGALT1', ! empty( $array_row_i['homeimgalt'] ) ? $array_row_i['homeimgalt'] : $array_row_i['title'] );
				$xtpl->parse( 'main.viewcatloop.news.image' );
			}

			$xtpl->parse( 'main.viewcatloop.news' );
			++$a;
		}
		$xtpl->parse( 'main.viewcatloop' );

		$generate_page = nv_alias_page( $page_title, $base_url, $num_items, $per_page, $page );

		if( ! empty( $generate_page ) )
		{
			$xtpl->assign( 'GENERATE_PAGE', $generate_page );
			$xtpl->parse( 'main.generate_page' );
		}

		$xtpl->parse( 'main' );
		$contents .= $xtpl->text( 'main' );


		if( $page > 1 )
		{
			$page_title .= ' ' . NV_TITLEBAR_DEFIS . ' ' . $lang_global['page'] . ' ' . $page;
		}
	}
}
elseif( $array_post_user['addcontent'] )
{
	Header( 'Location: ' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&contentid=0&checkss=' . md5( '0' . $client_info['session_id'] . $global_config['sitekey'] ), true ) );
	die();
}

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';