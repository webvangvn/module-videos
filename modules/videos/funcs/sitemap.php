<?php

/**
 * @Project VIDEOS 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @Website tradacongnghe.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate Oct 08, 2015 10:47:41 AM
 */

if( ! defined( 'NV_IS_MOD_VIDEOS' ) ) die( 'Stop!!!' );

$url = array();
$cacheFile = NV_LANG_DATA . '_sitemap_' . NV_CACHE_PREFIX . '.cache';
$pa = NV_CURRENTTIME - 7200;

if( ( $cache = $nv_Cache->getItem( $module_name, $cacheFile ) ) != false and filemtime( NV_ROOTDIR . '/' . NV_CACHEDIR . '/' . $module_name . '/' . $cacheFile ) >= $pa )
{
	$url = unserialize( $cache );
}
else
{
	$db->sqlreset()
		->select( 'id, catid, publtime, alias' )
		->from( NV_PREFIXLANG . '_' . $module_data . '_rows' )
		->where( 'status=1' )
		->order( 'publtime DESC' )
		->limit( 1000 );
	$result = $db->query( $db->sql() );

	$url = array();

	while( list( $id, $catid_i, $publtime, $alias ) = $result->fetch( 3 ) )
	{
		$catalias = $global_array_cat[$catid_i]['alias'];
		$url[] = array(
			'link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $catalias . '/' . $alias . '-' . $id . $global_config['rewrite_exturl'],
			'publtime' => $publtime
		);
	}

	$cache = serialize( $url );
	$nv_Cache->setItem( $module_name, $cacheFile, $cache );
}

nv_xmlSitemap_generate( $url );
die();