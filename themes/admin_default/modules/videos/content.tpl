<!-- BEGIN: main -->
<!-- BEGIN: error -->
<div class="alert alert-danger">{error}</div>
<!-- END: error -->
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/ui/jquery.ui.core.css" rel="stylesheet" />
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/ui/jquery.ui.theme.css" rel="stylesheet" />
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/ui/jquery.ui.menu.css" rel="stylesheet" />
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/ui/jquery.ui.autocomplete.css" rel="stylesheet" />
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/ui/jquery.ui.datepicker.css" rel="stylesheet" />
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css">

<form class="form-inline m-bottom confirm-reload" action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" enctype="multipart/form-data" method="post">
	<ul class="nav nav-tabs">
	  <li class="active"><a data-toggle="tab" href="#main_content">{LANG.main_content}</a></li>
	  <li><a data-toggle="tab" href="#additional_content">{LANG.additional_content}</a></li>
	</ul>
	<div class="row tab-content">
		<div id="main_content" class="col-sm-24 col-md-24 tab-pane fade in active">
			<table class="table table-striped table-bordered">
				<col class="w200" />
				<col />
				<tbody>
					<tr>
						<td><strong>{LANG.name}</strong>: <sup class="required">(∗)</sup></td>
						<td><input type="text" maxlength="255" value="{rowcontent.title}" id="idtitle" name="title" class="form-control"  style="width:350px"/><span class="text-middle"> {GLANG.length_characters}: <span id="titlelength" class="red">0</span>. {GLANG.title_suggest_max} </span></td>
					</tr>
					<tr>
						<td><strong>{LANG.alias}: </strong></td>
						<td><input class="form-control" name="alias" id="idalias" type="text" value="{rowcontent.alias}" maxlength="255"  style="width:350px"/>&nbsp; <em class="fa fa-refresh fa-lg fa-pointer" onclick="get_alias();">&nbsp;</em></td>
					</tr>
					<tr>
						<td class="message_head">
							<cite>{LANG.content_cat}:</cite> <sup class="required">(∗)</sup>
						</td>
						<td>
							<select class="form-control w200" name="catids[]" id="catid">
								<option value="">{LANG.select_category}</option>
								<!-- BEGIN: catid -->
								<option value="{CATS.catid}" {CATS.selected}>{CATS.title}</option>
								<!-- END: catid -->
							</select>
						</td>
					</tr>
					<tr>
						<!-- BEGIN:playlist_cat -->
						<td class="top"><strong>{LANG.content_playlist}</strong></td>
						<td>
							<select name="playlists[]" id="playlists" class="form-control" style="width: 100%" multiple="multiple">
								<!-- BEGIN: loop -->
								<option value="{PLAYLISTS.playlist_id}" {PLAYLISTS.selected}>{PLAYLISTS.title}</option>
								<!-- END: loop -->
							</select>
						</td>
						<!-- END:playlist_cat -->
					</tr>
					<tr>
						<td class="message_head">
							<cite>{LANG.content_tag}:</cite>
						</td>
						<td style="overflow: auto">
							<div class="clearfix uiTokenizer uiInlineTokenizer">
								<div id="keywords" class="tokenarea">
									<!-- BEGIN: keywords -->
									<span class="uiToken removable" title="{KEYWORDS}" ondblclick="$(this).remove();">
										{KEYWORDS}
										<input type="hidden" autocomplete="off" name="keywords[]" value="{KEYWORDS}" />
										<a onclick="$(this).parent().remove();" class="remove uiCloseButton uiCloseButtonSmall" href="javascript:void(0);"></a>
									</span>
									<!-- END: keywords -->
								</div>
								<div class="uiTypeahead">
									<div class="wrap">
										<input type="hidden" class="hiddenInput" autocomplete="off" value="" />
										<div class="innerWrap">
											<input id="keywords-search" type="text" placeholder="{LANG.input_keyword_tags}" class="form-control textInput" style="width: 100%;" />
										</div>
									</div>
								</div>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="table table-striped table-bordered table-hover">
				<col class="w200" />
				<col />
				<tbody>
					<tr>
						<td><strong>{LANG.videos_info}</strong></td>
						<td>
							<input class="form-control" style="width:380px" type="text" name="vid_path" id="vid_path" placeholder="{LANG.videos_sources_placeholder}" value="{rowcontent.vid_path}"/>
							<input id="select-video" type="button" value="{LANG.browse_server}" name="selectvid" class="btn btn-info" />
							<span>{LANG.videos_duration}: </span><input class="form-control" style="width:100px" type="text" name="vid_duration" id="vid_duration" placeholder="00:00:00" value="{rowcontent.vid_duration}"/>&nbsp; <em class="fa fa-refresh fa-lg fa-pointer" onclick="get_duration();">&nbsp;</em>
						</td>
					</tr>
					<tr>
						<td><strong>{LANG.content_homeimg}</strong></td>
						<td><input class="form-control" style="width:380px" type="text" name="homeimg" id="homeimg" value="{rowcontent.homeimgfile}"/> <input id="select-img-post" type="button" value="{LANG.browse_server}" name="selectimg" class="btn btn-info" /></td>
					</tr>
					<tr>
						<td>{LANG.content_homeimgalt}</td>
						<td><input class="form-control" type="text" maxlength="255" value="{rowcontent.homeimgalt}" id="homeimgalt" name="homeimgalt" style="width:100%" /></td>
					</tr>
				</tbody>
			</table>
			<table class="table table-striped table-bordered table-hover">
				<div id="content_hometext" >
				<strong>{LANG.content_hometext}</strong>
					<i>{LANG.content_notehome}.</i> {GLANG.length_characters}: <span id="descriptionlength" class="red">0</span>. 
					<textarea id="description" name="hometext" rows="5" cols="75" style="font-size:14px; width: 100%; height:100px;margin-bottom:15px;" class="form-control">{rowcontent.hometext}</textarea>
				</div>
				<div id="content_bodytext" >
				<strong>{LANG.content_bodytext}</strong>
					<div style="padding:2px; background:#CCCCCC; margin:0; display:block; position:relative">
						{edit_bodytext}
					</div>
					<strong>{LANG.content_sourceid}</strong>
					<input class="form-control" type="text" maxlength="255" value="{rowcontent.sourcetext}" name="sourcetext" id="AjaxSourceText" style="width:100%"/>
				</div>
			</table>
		</div>
		<div id="additional_content" class="col-sm-24 col-md-24 tab-pane fade">
			<div class="row">
				<div class="col-sm-24 col-md-24">
					<div class="col-md-8">
						<div class="col-md-24">
							<p class="message_head">
								<cite>{LANG.content_author}:</cite>
							</p>
							<div class="message_body">
								<input class="form-control" type="text" maxlength="255" value="{rowcontent.author}" name="author" style="width:100%" />
							</div>
						</div>
						
						<div class="col-md-24">
							<p class="message_head">
								<cite>{LANG.content_artist}:</cite>
							</p>
							<div class="message_body">
								<input class="form-control" type="text" maxlength="255" value="{rowcontent.artist}" name="artist" style="width:100%" />
							</div>
						</div>	
						<!-- BEGIN: googleplus -->
						<div class="col-md-24">
							<p class="message_head">
								<cite>{LANG.googleplus}:</cite>
							</p>
							<div class="message_body">
								<select class="form-control" name="gid">
									<!-- BEGIN: gid -->
									<option value="{GOOGLEPLUS.gid}"{GOOGLEPLUS.selected}>{GOOGLEPLUS.title}</option>
									<!-- END: gid -->
								</select>
							</div>
						</div>	
						<!-- END: googleplus -->
						<!-- BEGIN:block_cat -->
						<div class="col-md-24">
							<p class="message_head">
								<cite>{LANG.content_block}:</cite>
							</p>
							<div class="message_body" style="overflow: auto">
                                <!-- BEGIN: loop -->
									<div class="row">
										<label><input type="checkbox" value="{BLOCKS.bid}" name="bids[]" {BLOCKS.checked}>{BLOCKS.title}</label>
									</div>
                                <!-- END: loop -->
							</div>
						</div>
						<!-- END:block_cat -->
					</div>
					<div class="col-md-8">
						<div class="col-md-24">
							<p class="message_head">
								<cite>{LANG.content_publ_date}</cite><span class="timestamp">{LANG.content_notetime}</span>
							</p>
							<div class="message_body">
								<input class="form-control" name="publ_date" id="publ_date" value="{publ_date}" style="width: 90px;" maxlength="10" type="text"/>
								<select class="form-control" name="phour">
									{phour}
								</select>
								:
								<select class="form-control" name="pmin">
									{pmin}
								</select>
							</div>
						</div>
						<div class="col-md-24">
							<p class="message_head">
								<cite>{LANG.content_exp_date}:</cite><span class="timestamp">{LANG.content_notetime}</span>
							</p>
							<div class="message_body">
								<input class="form-control" name="exp_date" id="exp_date" value="{exp_date}" style="width: 90px;" maxlength="10" type="text"/>
								<select class="form-control" name="ehour">
									{ehour}
								</select>
								:
								<select class="form-control" name="emin">
									{emin}
								</select>
								<div style="margin-top: 5px;">
									<input type="checkbox" value="1" name="archive" {archive_checked} />
									<label> {LANG.content_archive} </label>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-8">
						<div class="col-md-24">
							<p class="message_head">
								<cite>{LANG.content_allowed_comm}:</cite>
							</p>
							<div class="message_body">
								<!-- BEGIN: allowed_comm -->
								<div class="row">
									<label><input name="allowed_comm[]" type="checkbox" value="{ALLOWED_COMM.value}" {ALLOWED_COMM.checked} />{ALLOWED_COMM.title}</label>
								</div>
								<!-- END: allowed_comm -->
								<!-- BEGIN: content_note_comm -->
								<div class="alert alert-info">{LANG.content_note_comm}</div>
								<!-- END: content_note_comm -->
							</div>
						</div>
					</div>
					<div class="col-md-24">
						<p class="message_head">
							<cite>{LANG.content_extra}:</cite>
						</p>
						<div class="message_body">
							<div style="margin-bottom: 2px;display: inline-block;">
								<input type="checkbox" value="1" name="inhome" {inhome_checked}/>
								<label> {LANG.content_inhome} </label>
							</div>
							<div style="margin-bottom: 2px;display: inline-block;">
								<input type="checkbox" value="1" name="allowed_rating" {allowed_rating_checked}/>
								<label> {LANG.content_allowed_rating} </label>
							</div>
							<div style="margin-bottom: 2px;display: inline-block;">
								<input type="checkbox" value="1" name="allowed_send" {allowed_send_checked}/>
								<label> {LANG.content_allowed_send} </label>
							</div>
							<div style="margin-bottom: 2px;display: inline-block;">
								<input type="checkbox" value="1" name="allowed_save" {allowed_save_checked} />
								<label> {LANG.content_allowed_save} </label>
							</div>
							<div style="margin-bottom: 2px;display: inline-block;">
							<input type="checkbox" value="1" name="copyright"{checkcop}/>
								<label> {LANG.content_copyright} </label>
							</div>
						</div>
					</div>	
				</div>
			</div>
		</div>
	</div>
	<div class="text-center">
		<br/>
		<input type="hidden" value="1" name="save" />
		<input type="hidden" value="{rowcontent.id}" name="id" />
		<!-- BEGIN:status -->
		<input class="btn btn-primary submit-post" name="statussave" type="submit" value="{LANG.save}" />
		<!-- END:status -->
		<!-- BEGIN:status0 -->
		<input class="btn btn-primary submit-post" name="status4" type="submit" value="{LANG.save_temp}" />
		<input class="btn btn-primary submit-post" name="status1" type="submit" value="{LANG.publtime}" />
		<!-- END:status0 -->
        <!-- BEGIN:status1 -->
		<input class="btn btn-primary submit-post" name="status4" type="submit" value="{LANG.save_temp}" />
		<input class="btn btn-primary submit-post" name="status6" type="submit" value="{LANG.save_send_admin}" />
            <!-- BEGIN:status0 -->
            <input class="btn btn-primary submit-post" name="status0" type="submit" value="{LANG.save_send_spadmin}" />
            <!-- END:status0 -->
		<!-- END:status1 -->
		<br />
	</div>
</form>
<div id="message"></div>
<script type="text/javascript">
//<![CDATA[
var LANG = [];
var CFG = [];
CFG.uploads_dir_user = '{UPLOADS_DIR_USER}';
CFG.uploads_dir_file_user = '{UPLOADS_DIR_FILE_USER}';
CFG.upload_current = '{UPLOAD_CURRENT}';
CFG.upload_file = '{UPLOAD_FILE_PATH}';
LANG.content_tags_empty = '{LANG.content_tags_empty}.<!-- BEGIN: auto_tags --> {LANG.content_tags_empty_auto}.<!-- END: auto_tags -->';
LANG.alias_empty_notice = '{LANG.alias_empty_notice}';
var content_checkcatmsg = "{LANG.content_checkcatmsg}";
$(document).ready(function() {
	$("#playlists").select2({
	placeholder: "{LANG.content_playlist}"
	});
	$("#catid").select2();
});
<!-- BEGIN: getalias -->
$("#idtitle").change(function() {
	get_alias();
});
<!-- END: getalias -->

<!-- BEGIN: get_duration -->
$("#vid_path").change(function() {
	get_duration();
});
<!-- END: get_duration -->

//]]>
</script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/ui/jquery.ui.core.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/ui/jquery.ui.menu.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/ui/jquery.ui.autocomplete.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/ui/jquery.ui.datepicker.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}themes/admin_default/js/videos_content.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.js"></script>

<!-- END:main -->