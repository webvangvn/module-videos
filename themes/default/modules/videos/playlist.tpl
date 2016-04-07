<!-- BEGIN: main -->
<!-- BEGIN: playlist_info -->
<div class="col-xs-24 col-md-24 col-lg-24">
	<div class="page-header pd10_0 mg0_10_10">
		<h3>{PLAYLIST_INFO.title}</h3>
		<!-- BEGIN: description -->
		<p class="text-justify">{PLAYLIST_INFO.description}</p>
		<!-- END: description -->
		<!-- BEGIN: time -->
		<span class="text-muted clearfix">{PLAYLIST_INFO.add_time}</span>
		<!-- END: time -->
		<!-- BEGIN: viewed -->
		<span class="text-muted">{PLAYLIST_INFO.hitstotal}{LANG.hits_view}</span>
		<!-- END: viewed -->
	</div>
</div>
<!-- END: playlist_info -->

<!-- BEGIN: no_jwp_lic_admin -->
<div class="clear alert alert-warning"><a href="{SETTING_LINKS}" title="{LANG.no_jwp_lic_admin}"><strong>{LANG.no_jwp_lic_admin}</strong>&nbsp;<em class="fa fa-external-link"></em></a> </div>
<!-- END: no_jwp_lic_admin -->

<!-- BEGIN: no_jwp_lic -->
<div class="clear alert alert-warning"><strong>{LANG.no_jwp_lic}</strong></div>
<!-- END: no_jwp_lic -->

<!-- BEGIN: player -->
<div class="detail_video margin-bottom-lg margin-top-lg col-xs-24 col-md-24 col-lg-24">
	<div class="videoplayer">
		<div id="videoCont">
			<img src="{NV_BASE_SITEURL}themes/default/images/{MODULE_FILE}/loading.gif" class="center-block" alt="Loading player" />
		</div>
		<div class="PlaylistCont margin-top-lg">
			<div id="show-list"></div>
		</div>
	</div>
</div>
<script type="text/javascript" src="{NV_BASE_SITEURL}themes/default/modules/{MODULE_FILE}/jwplayer/jwplayer.js"></script>
<script type="text/javascript">jwplayer.key="{VIDEO_CONFIG.jwplayer_license}";</script>
<script type="text/javascript">
var playerInstance = jwplayer("videoCont");
playerInstance.setup({
	image: "{PLAYLIST_IMAGE}",
	autostart: {VIDEO_CONFIG.jwplayer_autoplay},
	aspectratio: "16:9",
	playlist : "{NV_BASE_SITEURL}{MODULE_NAME}/player/{RAND_SS}{PLAYLIST_ID}-{PLIST_CHECKSS}-{RAND_SS}{FAKE_ID}{EXT_URL}",
	controls: {VIDEO_CONFIG.jwplayer_controlbar},
	displaydescription: true,
	displaytitle: true,
	flashplayer: "{NV_BASE_SITEURL}themes/default/modules/{MODULE_FILE}/jwplayer/jwplayer.flash.swf",
	primary: "html5",
	repeat: {VIDEO_CONFIG.jwplayer_loop},
	mute: {VIDEO_CONFIG.jwplayer_mute},
	<!-- BEGIN: player_logo -->
	logo: {
		file: '{VIDEO_CONFIG.jwplayer_logo_file}',
		link: '{NV_MY_DOMAIN}'
	},
	<!-- END: player_logo -->
	skin: {"name": "stormtrooper"},
	stagevideo: false,
	stretching: "uniform",
	visualplaylist: true,
	width: "100%"
  });
  
var lang_play = "{LANG.play}";
var lang_new_window = "{LANG.open_new_window}";
var list = document.getElementById("show-list");
var html = list.innerHTML;
html +="<ul class='list-group'>"
playerInstance.on('ready',function(){
var playlist = playerInstance.getPlaylist();
for (var index=0;index<playlist.length;index++){
	var playindex = index +1;
	html += "<li id='play-items-"+index+"' class='list-group-item'><span>"+playlist[index].title+"</span><span class='pull-right'><label onclick='javascript:playThis("+index+")' title='"+lang_play+" "+playlist[index].title+"' class='btn btn-primary btn-xs mgr_10'><i class='fa fa-play'></i></label><a href='"+playlist[index].link+"' title='"+lang_new_window+"' target='_blank'><label class='btn btn-default btn-xs'><i class='fa fa-external-link-square'></i></label></a></span></li>"
	list.innerHTML = html;
}
html +="</ul>"
});

playerInstance.on('playlistItem', function() {
    var playlist = playerInstance.getPlaylist();
    var index = playerInstance.getPlaylistIndex();
	var current_li = document.getElementById("play-items-"+index);
    for(var i = 0; i < playlist.length; i++) {
            $('li[id^=play-items-]').removeClass( "active" )
    }
	current_li.classList.add('active');
});

function playThis(index) {
	playerInstance.playlistItem(index);
}
</script>
<!-- END: player -->

<!-- BEGIN: playlist_is_private -->
<div class="col-xs-24 col-md-24 col-lg-24">
	<div class="alert alert-info" role="alert">{LANG.playlist_is_private}</div>
</div>
<!-- END: playlist_is_private -->

<!-- BEGIN: no_playlist_inlist -->
<div class="col-xs-24 col-md-24 col-lg-24">
	<div class="alert alert-info" role="alert">{LANG.playlist_empty_playlist}</div>
</div>
<!-- END: no_playlist_inlist -->

<!-- BEGIN: no_video_inlist -->
<div class="col-xs-24 col-md-24 col-lg-24">
	<div class="alert alert-info" role="alert">{LANG.playlist_empty_video}</div>
</div>
<!-- END: no_video_inlist -->

<!-- BEGIN: pending_playlist -->
<div class="col-xs-24 col-md-24 col-lg-24">
	<div class="alert alert-warning" role="alert">{LANG.playlist_pending}</div>
</div>
<!-- END: pending_playlist -->

<div class="clearfix"></div>
<!-- BEGIN: playlist_loop -->
<div class="news_column panel panel-default">
	<div class="panel-body">
		<!-- BEGIN: homethumb -->
		<div class="videos-thumbnail pull-left col-md-6 col-lg-6 col-xs-6">
			<a href="{PLAYLIST_LOOP.link}" title="{PLAYLIST_LOOP.title}"><img alt="{PLAYLIST_LOOP.alt}" src="{PLAYLIST_LOOP.src}" width="{PLAYLIST_LOOP.width}" class="img-thumbnail pull-left imghome" /></a>
		</div>
		<!-- END: homethumb -->
		<div class="videos-thumbnail pull-left col-md-18 col-lg-18 col-xs-18">
			<h3><a href="{PLAYLIST_LOOP.link}" title="{PLAYLIST_LOOP.title}">{PLAYLIST_LOOP.title}</a></h3>
			<!-- BEGIN: num_items -->
			<p>
				{LANG.playlist_num_videos}&nbsp;:&nbsp;{PLAYLIST_LOOP.num_items}&nbsp;{LANG.video}
			</p>
			<!-- END: num_items -->
			<!-- BEGIN: publtime -->
			<p>
				{LANG.create_date}&nbsp;:&nbsp;{DATE}
			</p>
			<!-- END: publtime -->
			<p class="text-justify">
				{PLAYLIST_LOOP.hometext}
			</p>
			<!-- BEGIN: adminlink -->
			<p class="text-right">
				{ADMINLINK}
			</p>
			<!-- END: adminlink -->
		</div>
	</div>
</div>
<!-- END: playlist_loop -->

<!-- BEGIN: socialbutton -->
<div class="socialicon clearfix margin-bottom-lg margin-top-lg">
	<div class="fb-like" data-href="{SELFURL}" data-layout="button_count" data-action="like" data-show-faces="false" data-share="true">&nbsp;</div>
	<div class="g-plusone" data-size="medium"></div>
	<a href="http://twitter.com/share" class="twitter-share-button">Tweet</a>
</div>
<!-- END: socialbutton -->

<!-- BEGIN: other -->
<ul class="related">
	<!-- BEGIN: loop -->
	<li>
		<a title="{PLAYLIST_OTHER.title}" href="{PLAYLIST_OTHER.link}">{PLAYLIST_OTHER.title}</a>
		<em>({PLAYLIST_OTHER.publtime})</em>
	</li>
	<!-- END: loop -->
</ul>
<!-- END: other -->

<!-- BEGIN: generate_page -->
<div class="text-center">
	{GENERATE_PAGE}
</div>
<!-- END: generate_page -->
<!-- END: main -->