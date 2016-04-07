<!-- BEGIN: mainrefresh -->
<div class="text-center">
	{DATA.content}
</div>
<meta http-equiv="refresh" content="5;URL={DATA.urlrefresh}" />
<!-- END: mainrefresh -->

<!-- BEGIN: main -->
<!-- BEGIN: error -->
<div class="alert alert-warning">{ERROR}</div>
<!-- END: error -->

<form action="{CONTENT_URL}" name="fsea" method="post" id="fsea" class="form-horizontal">

	<div class="form-group">
		<label class="col-sm-4 control-label">{LANG.name}<span class="txtrequired">(*)</span></label>
		<div class="col-sm-20">
			<input type="text" class="form-control" name="title" id="idtitle" value="{DATA.title}" />
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">{LANG.alias}</label>
		<div class="col-sm-20">
			<input type="text" class="form-control pull-left" name="alias" id="idalias" value="{DATA.alias}" maxlength="255" style="width: 94%;" />
			<em class="fa fa-refresh pull-right" style="cursor: pointer; vertical-align: middle; margin: 9px 0 0 4px" onclick="get_alias();" alt="Click">&nbsp;</em>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">{LANG.content_cat} <span class="txtrequired">(*)</span></label>
		<div class="col-sm-20">
			<table>
				<tbody>
					<tr>
						<td>
						<select class="form-control w200" name="catids[]" id="catid">
							<option value="">- {LANG.select_category} -</option>
							<!-- BEGIN: catid -->
							<option value="{DATACATID.value}" {DATACATID.selected}>{DATACATID.title}</option>
							<!-- END: catid -->
						</select>
						
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">{LANG.content_link}</label>
		<div class="col-sm-20">
			<input class="form-control" style="width:380px" type="text" name="vid_path" id="vid_path" placeholder="{LANG.videos_sources_placeholder}" value="{DATA.vid_path}"/>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">{LANG.videos_duration}</label>
		<div class="col-sm-20">
			<input class="form-control pull-left" style="width:100px" type="text" name="vid_duration" id="vid_duration" placeholder="00:00:00" value="{DATA.vid_duration}"/>&nbsp; <em class="fa fa-refresh fa-lg fa-pointer pull-left" style="cursor: pointer; vertical-align: middle; margin: 9px 0 0 4px" onclick="get_duration();">&nbsp;</em>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">{LANG.content_homeimg}</label>
		<div class="col-sm-20">
			<input class="form-control" name="homeimgfile" id="homeimg" value="{DATA.homeimgfile}" type="text" />
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">{LANG.search_author}</label>
		<div class="col-sm-20">
			<input maxlength="255" value="{DATA.author}" name="author" type="text" class="form-control" />
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-sm-4 control-label">{LANG.search_artist}</label>
		<div class="col-sm-20">
			<input maxlength="255" value="{DATA.artist}" name="artist" type="text" class="form-control" />
		</div>
	</div>

	<div class="form-group">
		<label>{LANG.content_hometext}</label>
		<textarea class="form-control" rows="6" cols="60" name="hometext"> {DATA.hometext}</textarea>
	</div>

	<div class="form-group">
		<label>{LANG.content_bodytext}</label>
		{HTMLBODYTEXT}
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">{LANG.source}</label>
		<div class="col-sm-20">
			<input maxlength="255" value="{DATA.sourcetext}" name="sourcetext" type="text" class="form-control" />
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">{LANG.content_keywords}</label>
		<div class="col-sm-20">
			<input maxlength="255" value="{DATA.keywords}" name="keywords" type="text" class="form-control" />
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">{LANG.captcha} <span class="txtrequired">(*)</span></label>
		<div class="col-sm-20">
			<input type="text" maxlength="6" value="" id="fcode_iavim" name="fcode" class="form-control pull-left" style="width: 150px;" /><img height="22" src="{NV_BASE_SITEURL}index.php?scaptcha=captcha&t={NV_CURRENTTIME}" alt="{LANG.captcha}" class="captchaImg" /><img alt="{CAPTCHA_REFRESH}" src="{CAPTCHA_REFR_SRC}" width="16" height="16" class="refresh" onclick="change_captcha('#fcode_iavim');" />
		</div>
	</div>

	<br />
	<ul class="list-inline text-center">
		<input type="hidden" name="contentid" value="{DATA.id}" />
		<input type="hidden" name="checkss" value="{CHECKSS}" />
		<li><input type="submit" class="btn btn-primary" value="{LANG.save_draft}" name="status4"></li>
		<!-- BEGIN: save_temp -->
		<li><input type="submit" class="btn btn-primary" value="{LANG.save_temp}" name="status0"></li>
		<!-- END: save_temp -->
		<!-- BEGIN: postcontent -->
		<li><input type="submit" class="btn btn-primary" value="{LANG.save_content}" name="status1"></li>
		<!-- END: postcontent -->
	</ul>
	<br />
</form>
<!-- END: main -->