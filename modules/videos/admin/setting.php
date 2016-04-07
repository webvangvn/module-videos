<?php

/**
 * @Project VIDEOS 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @Website tradacongnghe.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate Oct 08, 2015 10:47:41 AM
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$page_title = $lang_module['setting'];

if( defined( 'NV_EDITOR' ) )
{
	require_once NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php';
}

$savesetting = $nv_Request->get_int( 'savesetting', 'post', 0 );
if( ! empty( $savesetting ) )
{
	$array_config = array();
	$array_config['indexfile'] = $nv_Request->get_title( 'indexfile', 'post', '', 1 );
	$array_config['per_page'] = $nv_Request->get_int( 'per_page', 'post', 0 );
	$array_config['st_links'] = $nv_Request->get_int( 'st_links', 'post', 0 );
	$array_config['homewidth'] = $nv_Request->get_int( 'homewidth', 'post', 0 );
	$array_config['homeheight'] = $nv_Request->get_int( 'homeheight', 'post', 0 );
	$array_config['blockwidth'] = $nv_Request->get_int( 'blockwidth', 'post', 0 );
	$array_config['blockheight'] = $nv_Request->get_int( 'blockheight', 'post', 0 );
	
	$array_config['titlecut'] = $nv_Request->get_int( 'titlecut', 'post', 0 );

	$array_config['allowed_rating_point'] = $nv_Request->get_int( 'allowed_rating_point', 'post', 0 );
	$array_config['copyright'] = $nv_Request->get_editor( 'copyright', '', NV_ALLOWED_HTML_TAGS );

	$array_config['allow_user_plist'] = $nv_Request->get_title( 'allow_user_plist', 'post', '' );
	$array_config['playlist_moderate'] = $nv_Request->get_title( 'playlist_moderate', 'post', '' );
	$array_config['playlist_allow_detele'] = $nv_Request->get_int( 'playlist_allow_detele', 'post', 0 );
	$array_config['playlist_max_items'] = $nv_Request->get_title( 'playlist_max_items', 'post', '', 0 );
	
	$array_config['youtube_api'] = $nv_Request->get_title( 'youtube_api', 'post', '' );
	$array_config['jwplayer_license'] = $nv_Request->get_title( 'jwplayer_license', 'post', '' );
	$array_config['jwplayer_autoplay'] = $nv_Request->get_title( 'jwplayer_autoplay', 'post', 0 );
	$array_config['jwplayer_loop'] = $nv_Request->get_title( 'jwplayer_loop', 'post', '', 0 );
	$array_config['jwplayer_controlbar'] = $nv_Request->get_title( 'jwplayer_controlbar', 'post', '', 0 );
	$array_config['jwplayer_mute'] = $nv_Request->get_title( 'jwplayer_mute', 'post', 0 );
	$array_config['jwplayer_logo'] = $nv_Request->get_int( 'jwplayer_logo', 'post', 0 );
	$array_config['jwplayer_logo_file'] = $nv_Request->get_title( 'jwplayer_logo_file', 'post', 0 );
	
	$array_config['facebookappid'] = $nv_Request->get_title( 'facebookappid', 'post', '' );
	$array_config['socialbutton'] = $nv_Request->get_int( 'socialbutton', 'post', 0 );
	$array_config['show_no_image'] = $nv_Request->get_title( 'show_no_image', 'post', '', 0 );
	$array_config['structure_upload'] = $nv_Request->get_title( 'structure_upload', 'post', '', 0 );
	$array_config['config_source'] = $nv_Request->get_int( 'config_source', 'post', 0 );
	$array_config['alias_lower'] = $nv_Request->get_int( 'alias_lower', 'post', 0 );
	$array_config['tags_alias'] = $nv_Request->get_int( 'tags_alias', 'post', 0 );
	$array_config['auto_tags'] = $nv_Request->get_int( 'auto_tags', 'post', 0 );
	$array_config['tags_remind'] = $nv_Request->get_int( 'tags_remind', 'post', 0 );

	if( ! nv_is_url( $array_config['show_no_image'] ) and file_exists( NV_DOCUMENT_ROOT . $array_config['show_no_image'] ) )
	{
		$lu = strlen( NV_BASE_SITEURL );
		$array_config['show_no_image'] = substr( $array_config['show_no_image'], $lu );
	}
	else
	{
		$array_config['show_no_image'] = '';
	}
	
	if( ! nv_is_url( $array_config['jwplayer_logo_file'] ) and file_exists( NV_DOCUMENT_ROOT . $array_config['jwplayer_logo_file'] ) )
	{
		$lu = strlen( NV_BASE_SITEURL );
		$array_config['jwplayer_logo_file'] = substr( $array_config['jwplayer_logo_file'], $lu );
	}
	else
	{
		$array_config['jwplayer_logo_file'] = '';
	}

	$sth = $db->prepare( "UPDATE " . NV_CONFIG_GLOBALTABLE . " SET config_value = :config_value WHERE lang = '" . NV_LANG_DATA . "' AND module = :module_name AND config_name = :config_name" );
	$sth->bindParam( ':module_name', $module_name, PDO::PARAM_STR );
	foreach( $array_config as $config_name => $config_value )
	{
		$sth->bindParam( ':config_name', $config_name, PDO::PARAM_STR );
		$sth->bindParam( ':config_value', $config_value, PDO::PARAM_STR );
		$sth->execute();
	}

	$nv_Cache->delMod( 'settings' );
	$nv_Cache->delMod( $module_name );
	Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&rand=' . nv_genpass() );
	die();
}

$xtpl = new XTemplate( 'settings.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'GLANG', $lang_global );
$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'OP', $op );
$xtpl->assign( 'DATA', $module_config[$module_name] );

$array_jw_js = array(
	'true' =>$lang_global['yes'],
	'false' => $lang_global['no']
	);
	
$array_jw_logo = array(
	$lang_global['no'],
	$lang_global['yes']
	);


// Cach hien thi tren trang chu
foreach( $array_viewcat_full as $key => $val )
{
	$xtpl->assign( 'INDEXFILE', array(
		'key' => $key,
		'title' => $val,
		'selected' => $key == $module_config[$module_name]['indexfile'] ? ' selected="selected"' : ''
	) );
	$xtpl->parse( 'main.indexfile' );
}

// JW player
foreach( $array_jw_js as $key => $val )
{
	$xtpl->assign( 'AUTO_PLAY', array(
		'key' => $key,
		'title' => $val,
		'selected' => $key == $module_config[$module_name]['jwplayer_autoplay'] ? ' selected="selected"' : ''
	) );
	$xtpl->parse( 'main.jwplayer_autoplay' );
}

foreach( $array_jw_js as $key => $val )
{
	$xtpl->assign( 'LOOP_PLAY', array(
		'key' => $key,
		'title' => $val,
		'selected' => $key == $module_config[$module_name]['jwplayer_loop'] ? ' selected="selected"' : ''
	) );
	$xtpl->parse( 'main.jwplayer_loop' );
}

foreach( $array_jw_js as $key => $val )
{
	$xtpl->assign( 'CONTROL_BAR', array(
		'key' => $key,
		'title' => $val,
		'selected' => $key == $module_config[$module_name]['jwplayer_controlbar'] ? ' selected="selected"' : ''
	) );
	$xtpl->parse( 'main.jwplayer_controlbar' );
}

foreach( $array_jw_js as $key => $val )
{
	$xtpl->assign( 'JW_MUTE', array(
		'key' => $key,
		'title' => $val,
		'selected' => $key == $module_config[$module_name]['jwplayer_mute'] ? ' selected="selected"' : ''
	) );
	$xtpl->parse( 'main.jwplayer_mute' );
}

foreach( $array_jw_logo as $key => $val )
{
	$xtpl->assign( 'JW_LOGO', array(
		'key' => $key,
		'title' => $val,
		'selected' => $key == $module_config[$module_name]['jwplayer_logo'] ? ' selected="selected"' : ''
	) );
	$xtpl->parse( 'main.jwplayer_logo' );
}

// So bai viet tren mot trang
for( $i = 5; $i <= 30; ++$i )
{
	$xtpl->assign( 'PER_PAGE', array(
		'key' => $i,
		'title' => $i,
		'selected' => $i == $module_config[$module_name]['per_page'] ? ' selected="selected"' : ''
	) );
	$xtpl->parse( 'main.per_page' );
}

// Bai viet chi hien thi link
for( $i = 0; $i <= 20; ++$i )
{
	$xtpl->assign( 'ST_LINKS', array(
		'key' => $i,
		'title' => $i,
		'selected' => $i == $module_config[$module_name]['st_links'] ? ' selected="selected"' : ''
	) );
	$xtpl->parse( 'main.st_links' );
}

// Show points rating article on google
for( $i = 0; $i <= 6; ++$i )
{
	$xtpl->assign( 'RATING_POINT', array(
		'key' => $i,
		'title' => ($i == 6) ? $lang_module['no_allowed_rating'] : $i,
		"selected" => $i == $module_config[$module_name]['allowed_rating_point'] ? " selected=\"selected\"" : ""
	) );
	$xtpl->parse( 'main.allowed_rating_point' );
}

// So bai viet tren mot trang
for( $i = 0; $i <= 30; ++$i )
{
	$xtpl->assign( 'MAX_PLISTS', array(
		'key' => $i,
		'title' => $i,
		'selected' => $i == $module_config[$module_name]['playlist_max_items'] ? ' selected="selected"' : ''
	) );
	$xtpl->parse( 'main.playlist_max_items' );
}

$xtpl->assign( 'SOCIALBUTTON', $module_config[$module_name]['socialbutton'] ? ' checked="checked"' : '' );

$xtpl->assign( 'ALLOW_USER_PLIST', $module_config[$module_name]['allow_user_plist'] ? ' checked="checked"' : '' );
$xtpl->assign( 'PLAYLIST_MODERATE', $module_config[$module_name]['playlist_moderate'] ? ' checked="checked"' : '' );
$xtpl->assign( 'PLAYLIST_ALLOW_DETELE', $module_config[$module_name]['playlist_allow_detele'] ? ' checked="checked"' : '' );

$xtpl->assign( 'TAGS_ALIAS', $module_config[$module_name]['tags_alias'] ? ' checked="checked"' : '' );
$xtpl->assign( 'ALIAS_LOWER', $module_config[$module_name]['alias_lower'] ? ' checked="checked"' : '' );
$xtpl->assign( 'AUTO_TAGS', $module_config[$module_name]['auto_tags'] ? ' checked="checked"' : '' );
$xtpl->assign( 'TAGS_REMIND', $module_config[$module_name]['tags_remind'] ? ' checked="checked"' : '' );
$xtpl->assign( 'SHOW_NO_IMAGE', ( !empty( $module_config[$module_name]['show_no_image'] ) ) ? NV_BASE_SITEURL . $module_config[$module_name]['show_no_image'] : '' );
$xtpl->assign( 'JWPLAYER_LOGO_FILE', ( !empty( $module_config[$module_name]['jwplayer_logo_file'] ) ) ? NV_BASE_SITEURL . $module_config[$module_name]['jwplayer_logo_file'] : '' );

$array_structure_image = array();
$array_structure_image[''] = NV_UPLOADS_DIR . '/' . $module_upload;
$array_structure_image['Y'] = NV_UPLOADS_DIR . '/' . $module_upload . '/' . date( 'Y' );
$array_structure_image['Ym'] = NV_UPLOADS_DIR . '/' . $module_upload . '/' . date( 'Y_m' );
$array_structure_image['Y_m'] = NV_UPLOADS_DIR . '/' . $module_upload . '/' . date( 'Y/m' );
$array_structure_image['Ym_d'] = NV_UPLOADS_DIR . '/' . $module_upload . '/' . date( 'Y_m/d' );
$array_structure_image['Y_m_d'] = NV_UPLOADS_DIR . '/' . $module_upload . '/' . date( 'Y/m/d' );
$array_structure_image['username'] = NV_UPLOADS_DIR . '/' . $module_upload . '/username_admin';

$array_structure_image['username_Y'] = NV_UPLOADS_DIR . '/' . $module_upload . '/username_admin/' . date( 'Y' );
$array_structure_image['username_Ym'] = NV_UPLOADS_DIR . '/' . $module_upload . '/username_admin/' . date( 'Y_m' );
$array_structure_image['username_Y_m'] = NV_UPLOADS_DIR . '/' . $module_upload . '/username_admin/' . date( 'Y/m' );
$array_structure_image['username_Ym_d'] = NV_UPLOADS_DIR . '/' . $module_upload . '/username_admin/' . date( 'Y_m/d' );
$array_structure_image['username_Y_m_d'] = NV_UPLOADS_DIR . '/' . $module_upload . '/username_admin/' . date( 'Y/m/d' );

$structure_image_upload = isset( $module_config[$module_name]['structure_upload'] ) ? $module_config[$module_name]['structure_upload'] : "Ym";

// Thu muc uploads
foreach( $array_structure_image as $type => $dir )
{
	$xtpl->assign( 'STRUCTURE_UPLOAD', array(
		'key' => $type,
		'title' => $dir,
		'selected' => $type == $structure_image_upload ? ' selected="selected"' : ''
	) );
	$xtpl->parse( 'main.structure_upload' );
}

// Cau hinh hien thi nguon tin
$array_config_source = array( $lang_module['config_source_title'], $lang_module['config_source_link'], $lang_module['config_source_logo'] );
foreach( $array_config_source as $key => $val )
{
	$xtpl->assign( 'CONFIG_SOURCE', array(
		'key' => $key,
		'title' => $val,
		'selected' => $key == $module_config[$module_name]['config_source'] ? ' selected="selected"' : ''
	) );
	$xtpl->parse( 'main.config_source' );
}


$copyright = nv_htmlspecialchars( nv_editor_br2nl( $module_config[$module_name]['copyright'] ) );
if( defined( 'NV_EDITOR' ) and nv_function_exists( 'nv_aleditor' ) )
{
	$_uploads_dir = NV_UPLOADS_DIR . '/' . $module_upload;
	$copyright = nv_aleditor( 'copyright', '100%', '100px', $copyright, 'Basic', $_uploads_dir, $_uploads_dir );
}
else
{
	$copyright = "<textarea style=\"width: 100%\" name=\"copyright\" id=\"copyright\" cols=\"20\" rows=\"15\">" . $copyright . "</textarea>";
}
$xtpl->assign( 'COPYRIGHTHTML', $copyright );

$xtpl->assign( 'PATH', defined( 'NV_IS_SPADMIN' ) ? "" : NV_UPLOADS_DIR . '/' . $module_upload );
$xtpl->assign( 'CURRENTPATH', defined( 'NV_IS_SPADMIN' ) ? "images" : NV_UPLOADS_DIR . '/' . $module_upload );

if( defined( 'NV_IS_ADMIN_FULL_MODULE' ) or ! in_array( 'admins', $allow_func ) )
{
	$groups_list = nv_groups_list();
	unset($groups_list[6]);

	$savepost = $nv_Request->get_int( 'savepost', 'post', 0 );
	if( ! empty( $savepost ) )
	{
		$array_config = array();
		$array_group_id = $nv_Request->get_typed_array( 'array_group_id', 'post' );
		$array_addcontent = $nv_Request->get_typed_array( 'array_addcontent', 'post' );
		$array_postcontent = $nv_Request->get_typed_array( 'array_postcontent', 'post' );
		$array_editcontent = $nv_Request->get_typed_array( 'array_editcontent', 'post' );
		$array_delcontent = $nv_Request->get_typed_array( 'array_delcontent', 'post' );

		foreach( $array_group_id as $group_id )
		{
			if( isset( $groups_list[$group_id] ) )
			{
				$addcontent = ( isset( $array_addcontent[$group_id] ) and intval( $array_addcontent[$group_id] ) == 1 ) ? 1 : 0;
				$postcontent = ( isset( $array_postcontent[$group_id] ) and intval( $array_postcontent[$group_id] ) == 1 ) ? 1 : 0;
				$editcontent = ( isset( $array_editcontent[$group_id] ) and intval( $array_editcontent[$group_id] ) == 1 ) ? 1 : 0;
				$delcontent = ( isset( $array_delcontent[$group_id] ) and intval( $array_delcontent[$group_id] ) == 1 ) ? 1 : 0;
				$addcontent = ( $postcontent == 1 ) ? 1 : $addcontent;
				$db->query( "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_config_post SET addcontent = '" . $addcontent . "', postcontent = '" . $postcontent . "', editcontent = '" . $editcontent . "', delcontent = '" . $delcontent . "' WHERE group_id =" . $group_id );
			}
		}

		$nv_Cache->delMod( 'settings' );
		$nv_Cache->delMod( $module_name );
		Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&rand=' . nv_genpass() );
		die();
	}

	$array_post_data = array();

	$sql = "SELECT group_id, addcontent, postcontent, editcontent, delcontent FROM " . NV_PREFIXLANG . "_" . $module_data . "_config_post ORDER BY group_id ASC";
	$result = $db->query( $sql );
	while( list( $group_id, $addcontent, $postcontent, $editcontent, $delcontent ) = $result->fetch( 3 ) )
	{
		if( isset( $groups_list[$group_id] ) )
		{
			$array_post_data[$group_id] = array(
				'group_id' => $group_id,
				'addcontent' => $addcontent,
				'postcontent' => $postcontent,
				'editcontent' => $editcontent,
				'delcontent' => $delcontent
			);
		}
		else
		{
			$db->query( 'DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_config_post WHERE group_id = ' . $group_id );
		}
	}

	$xtpl->assign( 'FORM_ACTION', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op );

	foreach( $groups_list as $group_id => $group_title )
	{
		if( ( isset( $array_post_data[$group_id] ) ) )
		{
			$addcontent = $array_post_data[$group_id]['addcontent'];
			$postcontent = $array_post_data[$group_id]['postcontent'];
			$editcontent = $array_post_data[$group_id]['editcontent'];
			$delcontent = $array_post_data[$group_id]['delcontent'];
		}
		else
		{
			$addcontent = $postcontent = $editcontent = $delcontent = 0;
			$db->query( "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_config_post (group_id,addcontent,postcontent,editcontent,delcontent) VALUES ( '" . $group_id . "', '" . $addcontent . "', '" . $postcontent . "', '" . $editcontent . "', '" . $delcontent . "' )" );
		}

		$xtpl->assign( 'ROW', array(
			'group_id' => $group_id,
			'group_title' => $group_title,
			'addcontent' => $addcontent ? ' checked="checked"' : '',
			'postcontent' => $postcontent ? ' checked="checked"' : '',
			'editcontent' => $editcontent ? ' checked="checked"' : '',
			'delcontent' => $delcontent ? ' checked="checked"' : ''
		) );

		$xtpl->parse( 'main.admin_config_post.loop' );
	}

	$xtpl->parse( 'main.admin_config_post' );
}

$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';