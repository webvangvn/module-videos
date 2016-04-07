<?php

/**
 * @Project VIDEOS 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @Website tradacongnghe.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate Oct 08, 2015 10:47:41 AM
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$path = $nv_Request->get_string( 'vid_path', 'post', '' );
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