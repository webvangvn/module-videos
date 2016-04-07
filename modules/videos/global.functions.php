<?php

/**
 * @Project VIDEOS 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @Website tradacongnghe.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate Oct 08, 2015 10:47:41 AM
 */

if( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

$timecheckstatus = $module_config[$module_name]['timecheckstatus'];
if( $timecheckstatus > 0 and $timecheckstatus < NV_CURRENTTIME )
{
	nv_set_status_module();
}

/**
 * nv_set_status_module()
 *
 * @return
 */
function nv_set_status_module()
{
	global $db, $nv_Cache, $module_name, $module_data, $global_config;

	$check_run_cronjobs = NV_ROOTDIR . '/' . NV_LOGS_DIR . '/data_logs/cronjobs_' . md5( $module_data . 'nv_set_status_module' . $global_config['sitekey'] ) . '.txt';
	$p = NV_CURRENTTIME - 300;
	if( file_exists( $check_run_cronjobs ) and @filemtime( $check_run_cronjobs ) > $p )
	{
		return;
	}
	file_put_contents( $check_run_cronjobs, '' );

	//status_0 = "Cho duyet";
	//status_1 = "Xuat ban";
	//status_2 = "Hen gio dang";
	//status_3= "Het han";

	// Dang cai bai cho kich hoat theo thoi gian
	$query = $db->query( 'SELECT id, listcatid FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE status=2 AND publtime < ' . NV_CURRENTTIME . ' ORDER BY publtime ASC' );
	while( list( $id, $listcatid ) = $query->fetch( 3 ) )
	{
		$array_catid = explode( ',', $listcatid );
		foreach( $array_catid as $catid_i )
		{
			$catid_i = intval( $catid_i );
			if( $catid_i > 0 )
			{
				$db->query( 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_' . $catid_i . ' SET status=1 WHERE id=' . $id );
			}
		}
		$db->query( 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_rows SET status=1 WHERE id=' . $id );
	}

	// Ngung hieu luc cac bai da het han
	$query = $db->query( 'SELECT id, listcatid, archive FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE status=1 AND exptime > 0 AND exptime <= ' . NV_CURRENTTIME . ' ORDER BY exptime ASC' );
	while( list( $id, $listcatid, $archive ) = $query->fetch( 3 ) )
	{
		if( intval( $archive ) == 0 )
		{
			nv_del_content_module( $id );
		}
		else
		{
			nv_archive_content_module( $id, $listcatid );
		}
	}

	// Tim kiem thoi gian chay lan ke tiep
	$time_publtime = $db->query( 'SELECT min(publtime) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE status=2 AND publtime > ' . NV_CURRENTTIME )->fetchColumn();
	$time_exptime = $db->query( 'SELECT min(exptime) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE status=1 AND exptime > ' . NV_CURRENTTIME )->fetchColumn();

	$timecheckstatus = min( $time_publtime, $time_exptime );
	if( ! $timecheckstatus ) $timecheckstatus = max( $time_publtime, $time_exptime );

	$sth = $db->prepare( "UPDATE " . NV_CONFIG_GLOBALTABLE . " SET config_value = :config_value WHERE lang = '" . NV_LANG_DATA . "' AND module = :module_name AND config_name = 'timecheckstatus'" );
	$sth->bindValue( ':module_name', $module_name, PDO::PARAM_STR );
	$sth->bindValue( ':config_value', intval( $timecheckstatus ), PDO::PARAM_STR );
	$sth->execute();

	$nv_Cache->delMod( 'settings' );
	$nv_Cache->delMod( $module_name );

	unlink( $check_run_cronjobs );
	clearstatcache();
}

/**
 * nv_del_content_module()
 *
 * @param mixed $id
 * @return
 */
function nv_del_content_module( $id )
{
	global $db, $module_name, $module_data, $title, $lang_module;
	$content_del = 'NO_' . $id;
	$title = '';
	list( $id, $listcatid, $title, $homeimgfile ) = $db->query( 'SELECT id, listcatid, title, homeimgfile FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE id=' . intval( $id ) )->fetch( 3 );
	if( $id > 0 )
	{
		$number_no_del = 0;
		$array_catid = explode( ',', $listcatid );
		foreach( $array_catid as $catid_i )
		{
			$catid_i = intval( $catid_i );
			if( $catid_i > 0 )
			{
				$_sql = 'DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_' . $catid_i . ' WHERE id=' . $id;
				if( ! $db->exec( $_sql ) )
				{
					++$number_no_del;
				}
			}
		}
		
		$_sql = 'DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE id=' . $id;
		if( ! $db->exec( $_sql ) )
		{
			++$number_no_del;
		}

		$_sql = 'DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_bodyhtml_' . ceil( $id / 2000 ) . ' WHERE id = ' . $id;
		if( ! $db->exec( $_sql ) )
		{
			++$number_no_del;
		}
		
		$_sql = 'DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_bodytext WHERE id = ' . $id;
		if( ! $db->exec( $_sql ) )
		{
			++$number_no_del;
		}
		
		$db->query( 'DELETE FROM ' . NV_PREFIXLANG . '_comment WHERE module=' . $db->quote( $module_name ) . ' AND id = ' . $id );
		$db->query( 'DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_block WHERE id = ' . $id );
		
		$db->query( 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_tags SET numnews = numnews-1 WHERE tid IN (SELECT tid FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tags_id WHERE id=' . $id . ')' );
		$db->query( 'DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tags_id WHERE id = ' . $id );
		
		if( $number_no_del == 0 )
		{
			$content_del = 'OK_' . $id .'_' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name, true );
		}
		else
		{
			$content_del = 'ERR_' . $lang_module['error_del_content'];
		}
	}
	return $content_del;
}

/**
 * nv_archive_content_module()
 *
 * @param mixed $id
 * @param mixed $listcatid
 * @return
 */
function nv_archive_content_module( $id, $listcatid )
{
	global $db, $module_data;
	$array_catid = explode( ',', $listcatid );
	foreach( $array_catid as $catid_i )
	{
		$catid_i = intval( $catid_i );
		if( $catid_i > 0 )
		{
			$db->query( 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_' . $catid_i . ' SET status=3 WHERE id=' . $id );
		}
	}
	$db->query( 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_rows SET status=3 WHERE id=' . $id );
}

/**
 * nv_link_edit_page()
 *
 * @param mixed $id
 * @return
 */
function nv_link_edit_page( $id )
{
	global $lang_global, $module_name;
	$link = "<a class=\"btn btn-primary btn-xs\" href=\"" . NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=content&amp;id=" . $id . "\"><em class=\"fa fa-edit margin-right\"></em> " . $lang_global['edit'] . "</a>";
	return $link;
}

/**
 * nv_link_delete_page()
 *
 * @param mixed $id
 * @return
 */
function nv_link_delete_page( $id, $detail = 0)
{
	global $lang_global, $module_name;
	$link = "<a class=\"btn btn-danger btn-xs\" href=\"javascript:void(0);\" onclick=\"nv_del_content(" . $id . ", '" . md5( $id . session_id() ) . "','" . NV_BASE_ADMINURL . "', " . $detail . ")\"><em class=\"fa fa-trash-o margin-right\"></em> " . $lang_global['delete'] . "</a>";
	return $link;
}

/**
 * nv_link_edit_playlist()
 *
 * @param mixed $id
 * @return
 */
function nv_link_edit_playlist( $id )
{
	global $lang_global, $module_name;
	$link = "<a class=\"btn btn-primary btn-xs\" href=\"" . NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=playlists&amp;playlist_id=" . $id . "#edit\"><em class=\"fa fa-edit margin-right\"></em> " . $lang_global['edit'] . "</a>";
	return $link;
}


/**
 * nv_news_get_bodytext()
 *
 * @param mixed $bodytext
 * @return
 */
function nv_news_get_bodytext( $bodytext )
{
	// Get image tags
	if( preg_match_all( "/\<img[^\>]*src=\"([^\"]*)\"[^\>]*\>/is", $bodytext, $match ) )
	{
		foreach( $match[0] as $key => $_m )
		{
			$textimg = '';
			if( strpos( $match[1][$key], 'data:image/png;base64' ) === false )
			{
				$textimg = " " . $match[1][$key];
			}
			if( preg_match_all( "/\<img[^\>]*alt=\"([^\"]+)\"[^\>]*\>/is", $_m, $m_alt ) )
			{
				$textimg .= " " . $m_alt[1][0];
			}
			$bodytext = str_replace( $_m, $textimg, $bodytext );
		}
	}
	// Get link tags
	if( preg_match_all( "/\<a[^\>]*href=\"([^\"]+)\"[^\>]*\>(.*)\<\/a\>/isU", $bodytext, $match ) )
	{
		foreach( $match[0] as $key => $_m )
		{
			$bodytext = str_replace( $_m, $match[1][$key] . " " . $match[2][$key], $bodytext );
		}
	}

	$bodytext = str_replace( '&nbsp;', ' ', strip_tags( $bodytext ) );
	return preg_replace( '/[ ]+/', ' ', $bodytext );
}

/**
 * get_youtube_id()
 *
 * @param mixed $url
 * @return
 */
 
function get_youtube_id($url){
	preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $matches);
	return $matches[1];
}
/**
 * curl()
 *
 */
 function curl($url) {
	$ch = @curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	$head[] = "Connection: keep-alive";
	$head[] = "Keep-Alive: 300";
	$head[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
	$head[] = "Accept-Language: en-us,en;q=0.5";
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36');
	curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
	$page = curl_exec($ch);
	curl_close($ch);
	return $page;
}

// Check URL is Youtube
function is_youtube($url){
	$valid = preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url);
	if ($valid) {
		return true;
	} else {
		return false;
	}
}

// Check URL is FaceBook
function is_facebook($url){
	$pattern = '/facebook.com/';
	if( preg_match( $pattern, $url ) == 1 ) {
	   return true;
	}
	else
	{
		return false;
	}
}
// Get Facebook video id
function get_facebook_id($fb_url)
{
	preg_match("~/videos/(?:(t|vb)\.\d+/)?(\d+)~i", $fb_url, $fb_vid);
	return $fb_vid[2];
}

// Get direct link MP$ Facebook
function get_facebook_mp4($fb_url)
{
	$id = get_facebook_id($fb_url);
	$embed = 'https://www.facebook.com/video/embed?video_id='.$id;
	$get = curl($embed);
	$data = explode('[["params","', $get);
	$data = explode('"],["', $data[1]);
	$data = str_replace(
		array('\u00257B', '\u002522', '\u00253A', '\u00252C', '\u00255B', '\u00255C\u00252F', '\u00252F', '\u00253F', '\u00253D', '\u002526'),
		array('{', '"', ':', ',', '[', '\/', '/', '?', '=', '&'),
		$data[0]
	);
	$fbvid_mp4 = array();
	$mp4 = array(
		'link_mp4'=>'',
		'quality'=>''
	 );
	//Link HD
	$HD = explode('[{"hd_src":"', $data);
	if(!empty($HD[1]))
	{
		$HD = explode('","', $HD[1]);
		$HD = str_replace('\/', '/', $HD[0]);
		$mp4 = array(
			'link_mp4'=>$HD,
			'quality'=>'720p HD'
		 );  
		$fbvid_mp4[] = $mp4;
	}
	
	 //Link SD
	$SD = explode('"sd_src":"', $data);
	$SD = explode('","', $SD[1]);
	$SD = str_replace('\/', '/', $SD[0]);
	$mp4 = array(
		'link_mp4'=>$SD,
		'quality'=>'360p'
	 );  
	$fbvid_mp4[] = $mp4;
	
	return $fbvid_mp4;  
}


// Check URL is Picasa
function is_picasa($url){
	$pattern = '/picasaweb.google.com/';
	if( preg_match( $pattern, $url ) == 1 ) {
	   return true;
	}
	else
	{
		return false;
	}
}

// Get link Picasa
function get_link_redirector_picasa($link_picasa){
	$data = file_get_contents($link_picasa);
	$a = explode('"media":{"content":[', $data);
	$a = explode('],"', $a[1]);
	$datar = explode('},', $a[0]);
	$links = array();
	foreach ($datar as $key => $value)
	{
		$value = str_replace("}}", "}", $value . "}");  
		$links[] = json_decode($value, true);  
	}  
	return $links;
}

// Get link Picasa
function getDirectLink($url){
	$urlInfo = parse_url($url);  
	$out  = "GET  {$url} HTTP/1.1\r\n";  
	$out .= "Host: {$urlInfo['host']}\r\n";  
	$out .= "User-Agent: {$_SERVER['HTTP_USER_AGENT']}\r\n";  
	$out .= "Connection: Close\r\n\r\n";      
	$con = @fsockopen('ssl://'. $urlInfo['host'], 443, $errno, $errstr, 10);  
	if (!$con)
	{
		return $errstr." ".$errno;   
	}  
	fwrite($con,$out);  
	$data = '';  
	while (!feof($con))
	{  
		$data .= fgets($con, 512);  
	}  
	fclose($con);  
	preg_match("!\r\n(?:Location|URI): *(.*?) *\r\n!", $data, $matches);  
	$url = $matches[1];  
	return trim($url);  
}

// Output Picasa MP4 direct link
function get_link_mp4_picasa($link_picasa){
	$links= get_link_redirector_picasa($link_picasa);  
	$links_mp4= array();  
	for ($i = 1; $i < count($links); $i++){  
		$mp4 = array('link_mp4'=>'', 'quality'=>'');  
		$mp4['link_mp4']= getDirectLink($links[$i]['url']).'&format=getlink/video.mp4'; //get link mp4, must have &format... to embed in JWPlayer 
		$mp4['quality'] = $links[$i]['height']; //get quality  
		$links_mp4[] = $mp4;
	}  
	return $links_mp4;  
}


function humanTiming($time)
{
	global $lang_module;
	
    $time = time() - $time; // to get the time since that moment
    $time = ($time<1)? 1 : $time;
    $tokens = array (
        31536000 => $lang_module['year'],
        2592000 => $lang_module['month'],
        604800 => $lang_module['week'],
        86400 => $lang_module['day'],
        3600 => $lang_module['hour'],
        60 => $lang_module['minute'],
        1 => $lang_module['second']
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits.' '.$text;
    }

}

/**
 * videos_thumbs()
 * front-end thumbs create
 *
 */
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

/**
* Return video duration in seconds.
* 
* @param $video_url
* @param $api_key
* @return integer|null
*/
function youtubeVideoDuration($video_url) {
	global $module_config, $module_name;
    // video id from url
    $video_id = get_youtube_id($video_url);
	$api_key =  $module_config[$module_name]['youtube_api'];
	if(empty($api_key))
	{
		$seconds = '';
		return $seconds;
	}
	else
	{
		// video json data
		$json_result = file_get_contents("https://www.googleapis.com/youtube/v3/videos?part=contentDetails&id=$video_id&key=$api_key");
		$result = json_decode($json_result, true);

		// video duration data
		if (!count($result['items'])) {
			return null;
		}
		$duration_encoded = $result['items'][0]['contentDetails']['duration'];

		// duration
		$interval = new DateInterval($duration_encoded);
		$seconds = $interval->days * 86400 + $interval->h * 3600 + $interval->i * 60 + $interval->s;

		return $seconds;
	}
}

/**
* Return video duration to hours.
* 
* @param $sec
* @param $padHours (true/false)
* @return $hms
*
*/
function sec2hms ($sec, $padHours = false) {
	$hms = "";
	$hours = intval(intval($sec) / 3600); 
	$hms .= ($padHours) 
		  ? str_pad($hours, 2, "0", STR_PAD_LEFT). ":"
		  : $hours. ":";
	$minutes = intval(($sec / 60) % 60); 
	$hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ":";
	$seconds = intval($sec % 60); 
	$hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
	return $hms;
}

/**
* Add http to URL
* 
* @param $url
* @return $url
*
*/
function addhttp($url) {
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
}