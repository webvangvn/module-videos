/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 1 - 31 - 2010 5 : 12
 */

function sendrating(id, point, newscheckss) {
	if (point == 1 || point == 2 || point == 3 || point == 4 || point == 5) {
		$.post(nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=rating&nocache=' + new Date().getTime(), 'id=' + id + '&checkss=' + newscheckss + '&point=' + point, function(res) {
			$('#stringrating').html(res);
		});
	}
}

function nv_del_content(id, checkss, base_adminurl, detail) {
	if (confirm(nv_is_del_confirm[0])) {
		$.post(base_adminurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=del_content&nocache=' + new Date().getTime(), 'id=' + id + '&checkss=' + checkss, function(res) {
			var r_split = res.split('_');
			if (r_split[0] == 'OK') {
				if( detail ){
					window.location.href = r_split[2];
				}
				else
				{
					window.location.href = strHref;
				}
			} else if (r_split[0] == 'ERR') {
				alert(r_split[1]);
			} else {
				alert(nv_is_del_confirm[2]);
			}
		});
	}
	return false;
}

function get_alias() {
	var title = strip_tags(document.getElementById('idtitle').value);
	if (title != '') {
		$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=user-video&nocache=' + new Date().getTime(), 'get_alias=' + encodeURIComponent(title), function(res) {
			if (res != "") {
				document.getElementById('idalias').value = res;
			} else {
				document.getElementById('idalias').value = '';
			}
		});
	}
	return false;
}

function get_duration(mod) {
	var path = strip_tags(document.getElementById('vid_path').value);
	if (path != '') {
		$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=user-video&nocache=' + new Date().getTime(), 'get_duration=' + path + '&mod=' + mod, function(res) {
			if (res != "") {
				document.getElementById('vid_duration').value = res;
			} else {
				document.getElementById('vid_duration').value = '';
			}
		});
	}
	return false;
}


$(window).load(function(){
	var newsW = $('#news-bodyhtml').innerWidth();
	$.each($('#news-bodyhtml img'), function(){
		var w = $(this).innerWidth();
		var h = $(this).innerHeight();
		
		if( w > newsW ){
			$(this).prop('width', newsW);
			$(this).prop('height', h * newsW / w);
		}
	});
});


function nv_del_playlist_list(oForm, playlist_id) {
	var del_list = '';
	var fa = oForm['idcheck[]'];
	if (fa.length) {
		for (var i = 0; i < fa.length; i++) {
			if (fa[i].checked) {
				del_list = del_list + ',' + fa[i].value;
			}
		}
	} else {
		if (fa.checked) {
			del_list = del_list + ',' + fa.value;
		}
	}

	if (del_list != '') {
		if (confirm(nv_is_del_confirm[0])) {
			$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=user-playlist&nocache=' + new Date().getTime(), 'del_list=' + del_list + '&ajax=1&playlist_id=' + playlist_id, function(res) {
				nv_change_playlist_result(res);
			});
		}
	}
}

function nv_change_playlist_cat(playlist_id, mod) {
	var nv_timer = nv_settimeout_disable('id_' + mod + '_' + playlist_id, 1500);
	var new_vid = $('#id_' + mod + '_' + playlist_id).val();
	$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=user-playlist&nocache=' + new Date().getTime(), 'playlist_id=' + playlist_id + '&ajax=2&mod=' + mod + '&new_vid=' + new_vid, function(res) {
		var r_split = res.split('_');
		if (r_split[0] != 'OK') {
			alert(nv_is_change_act_confirm[2]);
		}
		clearTimeout(nv_timer);
		nv_show_list_playlist_cat();
	});
	return;
}

function nv_del_playlist_cat(playlist_id) {
	if (confirm(nv_is_del_confirm[0])) {
		$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=user-playlist&nocache=' + new Date().getTime(), '&ajax=4&playlist_id=' + playlist_id, function(res) {
			var r_split = res.split('_');
			if (r_split[0] == 'OK') {
				nv_show_list_playlist_cat();
			} else if (r_split[0] == 'ERR') {
				alert(r_split[1]);
			} else {
				alert(nv_is_del_confirm[2]);
			}
		});
	}
	return false;
}

function nv_change_playlist(playlist_id, id, mod) {
	if (mod == 'delete' && !confirm(nv_is_del_confirm[0])) {
		return false;
	}
	var new_vid = $('#id_playlist_sort_' + id).val();
	$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=user-playlist&nocache=' + new Date().getTime(), 'id=' + id + '&playlist_id=' + playlist_id + '&ajax=1&mod=' + mod + '&new_vid=' + new_vid, function(res) {
		nv_change_playlist_result(res);
	});
	return;
}

function nv_change_playlist_result(res) {
	var r_split = res.split('_');
	if (r_split[0] != 'OK') {
		alert(nv_is_change_act_confirm[2]);
	}
	var playlist_id = parseInt(r_split[1]);
	nv_show_list_playlist(playlist_id);
	return;
}

function nv_show_list_playlist(playlist_id) {
	if (document.getElementById('module_show_list')) {
		$('#module_show_list').load(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=list_playlist&playlist_id=' + playlist_id + '&nocache=' + new Date().getTime());
	}
	return;
}

function nv_show_list_playlist_cat() {
	if (document.getElementById('module_show_playlist')) {
		$('#module_show_playlist').load(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=list_playlist_cat&nocache=' + new Date().getTime());
	}
	return;
}

function nv_add_user_playlist(id, mod) {
	if (mod != 'add_user_playlist') {
		return false;
	}
	var user_playlist = $('#add_user_playlist').val();
	$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=user-playlist&nocache=' + new Date().getTime(), 'id=' + id + '&playlist_id=' + user_playlist + '&ajax=3&mod=' + mod, function(res) {
	alert(res);
	});
	return;
}
