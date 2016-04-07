<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 3/9/2010 23:25
 */

if( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

if( ! nv_function_exists( 'videos_thumbs' ) )
{
	function videos_thumbs( $id, $file, $module_upload, $width = 200, $height = 150, $quality = 90 )
	{
		if( $width >= $height ) $rate = $width / $height;
		else  $rate = $height / $width;

		$image = NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/img/' . $file;
 
		if( $file != '' and file_exists( $image ) )
		{
			$imgsource = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/img/' . $file;
			$imginfo = nv_is_image( $image );

			$basename = $module_upload . '_' . $width . 'x' . $height . '-' . $id . '-' . md5_file( $image ) . '.' . $imginfo['ext'];

			if( file_exists( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_upload. '/thumbs/' . $basename ) )
			{
				$imgsource = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload. '/thumbs/' . $basename;
			}
			else
			{

				$_image = new NukeViet\Files\Image( $image, NV_MAX_WIDTH, NV_MAX_HEIGHT );

				if( $imginfo['width'] <= $imginfo['height'] )
				{
					$_image->resizeXY( $width, 0 );

				}
				elseif( ( $imginfo['width'] / $imginfo['height'] ) < $rate )
				{
					$_image->resizeXY( $width, 0 );
				}
				elseif( ( $imginfo['width'] / $imginfo['height'] ) >= $rate )
				{
					$_image->resizeXY( 0, $height );
				}

				$_image->cropFromCenter( $width, $height );

				$_image->save( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_upload . '/thumbs/', $basename, $quality );

				if( file_exists( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_upload. '/thumbs/' . $basename ) )
				{
					$imgsource = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload. '/thumbs/' . $basename;
				}
			}
		}
		elseif( nv_is_url( $file ) )
		{
			$imgsource = $file;
		}
		else
		{
			$imgsource = '';
		}
		return $imgsource;
	}
}

if( ! nv_function_exists( 'nv_news_block_tophits' ) )
{
	function nv_block_config_tophits_blocks( $module, $data_block, $lang_block )
	{
		global $site_mods, $nv_Cache;
		$html = '';
		$html .= '<tr>';
		$html .= '	<td>' . $lang_block['number_day'] . '</td>';
		$html .= '	<td><input type="text" name="config_number_day" class="form-control w100" size="5" value="' . $data_block['number_day'] . '"/></td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '	<td>' . $lang_block['numrow'] . '</td>';
		$html .= '	<td><input type="text" name="config_numrow" class="form-control w100" size="5" value="' . $data_block['numrow'] . '"/></td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td>' . $lang_block['showtooltip'] . '</td>';
		$html .= '<td>';
		$html .= '<input type="checkbox" value="1" name="config_showtooltip" ' . ( $data_block['showtooltip'] == 1 ? 'checked="checked"' : '' ) . ' /><br /><br />';
		$tooltip_position = array( 'top' => $lang_block['tooltip_position_top'], 'bottom' => $lang_block['tooltip_position_bottom'], 'left' => $lang_block['tooltip_position_left'], 'right' => $lang_block['tooltip_position_right'] );
		$html .= '<span class="text-middle pull-left">' . $lang_block['tooltip_position'] . '&nbsp;</span><select name="config_tooltip_position" class="form-control w100 pull-left">';
		foreach( $tooltip_position as $key => $value )
		{
			$html .= '<option value="' . $key . '" ' . ( $data_block['tooltip_position'] == $key ? 'selected="selected"' : '' ) . '>' . $value . '</option>';
		}
		$html .= '</select>';
		$html .= '&nbsp;<span class="text-middle pull-left">' . $lang_block['tooltip_length'] . '&nbsp;</span><input type="text" class="form-control w100 pull-left" name="config_tooltip_length" size="5" value="' . $data_block['tooltip_length'] . '"/>';
		$html .= '</td>';
		$html .= '</tr>';

		$html .= '<tr>';
		$html .= '<td>' . $lang_block['nocatid'] . '</td>';
		$sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_cat ORDER BY sort ASC';
		$list = $nv_Cache->db( $sql, '', $module );
		$html .= '<td>';
		$html .= '<div style="height: 200px; overflow: auto">';
		foreach( $list as $l )
		{
			$xtitle_i = '';

			if( $l['lev'] > 0 )
			{
				for( $i = 1; $i <= $l['lev']; ++$i )
				{
					$xtitle_i .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				}
			}
			$html .= $xtitle_i . '<label><input type="checkbox" name="config_nocatid[]" value="' . $l['catid'] . '" ' . ( ( in_array( $l['catid'], $data_block['nocatid'] ) ) ? ' checked="checked"' : '' ) . '</input>' . $l['title'] . '</label><br />';
		}
		$html .= '</div>';
		$html .= '</td>';
		$html .= '</tr>';

		return $html;
	}

	function nv_block_config_tophits_blocks_submit( $module, $lang_block )
	{
		global $nv_Request;
		$return = array();
		$return['error'] = array();
		$return['config'] = array();
		$return['config']['number_day'] = $nv_Request->get_int( 'config_number_day', 'post', 0 );
		$return['config']['numrow'] = $nv_Request->get_int( 'config_numrow', 'post', 0 );
		$return['config']['showtooltip'] = $nv_Request->get_int( 'config_showtooltip', 'post', 0 );
		$return['config']['tooltip_position'] = $nv_Request->get_string( 'config_tooltip_position', 'post', 0 );
		$return['config']['tooltip_length'] = $nv_Request->get_string( 'config_tooltip_length', 'post', 0 );
		$return['config']['nocatid'] = $nv_Request->get_typed_array( 'config_nocatid', 'post', 'int', array() );
		return $return;
	}

	function nv_news_block_tophits( $block_config, $mod_data )
	{
		global $module_array_cat, $site_mods, $module_info, $db, $module_config, $global_config;

		$module = $block_config['module'];
		$mod_file = $site_mods[$module]['module_file'];

		$blockwidth = $module_config[$module]['blockwidth'];
		$show_no_image = $module_config[$module]['show_no_image'];
		if(empty($show_no_image))
		{
			$show_no_image = 'themes/default/images/' . $module . '/' . 'video_placeholder.png';
		}
		$publtime = NV_CURRENTTIME - $block_config['number_day'] * 86400;

		$array_block_news = array();
		$db->sqlreset()
			->select( 'id, catid, publtime, exptime, title, alias, homeimgthumb, homeimgfile, hometext' )
			->from( NV_PREFIXLANG . '_' . $mod_data . '_rows' )
			->order( 'hitstotal DESC' )
			->limit( $block_config['numrow'] );
		if( empty( $block_config['nocatid'] ) )
		{
			$db->where( 'status= 1 AND publtime > ' . $publtime );
		}
		else
		{
			$db->where( 'status= 1 AND publtime > ' . $publtime .' AND catid NOT IN ('.implode( ',', $block_config['nocatid'] ) . ')' );
		}

		$result = $db->query( $db->sql() );
		while( list( $id, $catid, $publtime, $exptime, $title, $alias, $homeimgthumb, $homeimgfile, $hometext ) = $result->fetch( 3 ) )
		{
			if( $homeimgthumb == 1 OR $homeimgthumb == 2 ) //image file
			{
				$imgurl = videos_thumbs($id, $homeimgfile, $module, $module_config[$module]['homewidth'], $module_config[$module]['homeheight'], 90 );
			}
			elseif( $homeimgthumb == 3 ) //image url
			{
				$imgurl = $homeimgfile;
			}
			elseif( ! empty( $show_no_image ) ) //no image
			{
				$imgurl = NV_BASE_SITEURL . $show_no_image;
			}
			else
			{
				$imgurl = '';
			}
			
			$link = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $module_array_cat[$catid]['alias'] . '/' . $alias . '-' . $id . $global_config['rewrite_exturl'];

			$array_block_news[] = array(
				'id' => $id,
				'title' => $title,
				'link' => $link,
				'imgurl' => $imgurl,
				'width' => $blockwidth,
				'hometext' => $hometext
			);
		}

		if( file_exists( NV_ROOTDIR . '/themes/' . $global_config['module_theme']  . '/modules/' . $mod_file . '/block_tophits.tpl' ) )
		{
			$block_theme = $global_config['module_theme'] ;
		}
		else
		{
			$block_theme = 'default';
		}

		$xtpl = new XTemplate( 'block_tophits.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/'. $mod_file );

		foreach( $array_block_news as $array_news )
		{
			$array_news['hometext'] = nv_clean60( $array_news['hometext'], $block_config['tooltip_length'], true );
			$xtpl->assign( 'blocknews', $array_news );
			if( ! empty( $array_news['imgurl'] ) )
			{
				$xtpl->parse( 'main.newloop.imgblock' );
			}

			if( ! $block_config['showtooltip'] )
			{
				$xtpl->assign( 'TITLE', 'title="' . $array_news['title'] . '"' );
			}

			$xtpl->parse( 'main.newloop' );
		}

		if( $block_config['showtooltip'] )
		{
			$xtpl->assign( 'TOOLTIP_POSITION', $block_config['tooltip_position'] );
			$xtpl->parse( 'main.tooltip' );
		}

		$xtpl->parse( 'main' );
		return $xtpl->text( 'main' );
	}
}

if( defined( 'NV_SYSTEM' ) )
{
	global $site_mods, $module_name, $global_array_cat, $module_array_cat, $nv_Cache;
	$module = $block_config['module'];
	if( isset( $site_mods[$module] ) )
	{
		$mod_data = $site_mods[$module]['module_data'];
		if( $module == $module_name )
		{
			$module_array_cat = $global_array_cat;
			unset( $module_array_cat[0] );
		}
		else
		{
			$module_array_cat = array();
			$sql = 'SELECT catid, parentid, title, alias, viewcat, subcatid, numlinks, description, inhome, keywords, groups_view FROM ' . NV_PREFIXLANG . '_' . $mod_data . '_cat ORDER BY sort ASC';
			$list = $nv_Cache->db( $sql, 'catid', $module );
			foreach( $list as $l )
			{
				$module_array_cat[$l['catid']] = $l;
				$module_array_cat[$l['catid']]['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $l['alias'];
			}
		}
		$content = nv_news_block_tophits( $block_config, $mod_data );
	}
}