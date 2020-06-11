function sendPost(url,data,success,fail) {
    return cms.http.post(url,data,success,fail);
}

function sendPostAjax(url, elementId, success, fail) {
    
    var formElement = (typeof elementId==='object') ? elementId : document.getElementById(elementId);
    
    $.ajax({
        url: url,
        type: 'POST',
        data: new FormData(formElement),
        processData: false,
        contentType: false,
        dataType: 'json'
    }).done(function (ret) {
        
        if (ret['error'] != '') {
            if (fail !== undefined)
                fail(ret);
            else cms.info.show(ret['error'], 15000);
            return false;
        } else {
            if (success !== undefined)
                success(ret);
            
            return true;
        }
    }).fail(function(){
        cms.info.show("Произошла ошибка запроса "+url);
    });
}



function hideLoadingProcessLayer(){
    $("#progress_layer").parent().fadeOut(200,function(){
        $(this).remove();
    });
    
    $('.ui-widget-overlay').remove();
}

function showLoadingProcessLayer(message) {
	var did = "loader_"+Math.floor(Math.random() * (1000 - 1) + 1);
	var lpdiv= document.createElement("DIV");

	lpdiv.id=did;

	var h = $(window).height();
	var w = $(window).width();

	var st = $(document).scrollTop();

	lpdiv.className     = 'ui-widget-overlay';
	lpdiv.style.width   = '100%';
	lpdiv.style.height  = '100vh';
        
	lpdiv.style.opacity = "0";
        lpdiv.style.display = "flex";
        lpdiv.style.position = "fixed";

	lpdiv.style.zIndex  = '15000';

	document.getElementsByTagName('body')[0].appendChild(lpdiv);
        
        $('.ui-widget-overlay').animate({
            opacity: 0.5
        },600);

	var left = parseInt( $(window).width()/2-50 );
	//var top = parseInt( ($(window).height()/2)-10+st );
        var top = parseInt( ($(window).height()/2)-10);

	var mess = (message!=undefined) ? message : 'Загрузка...';

	var b_u = '/';

	if (typeof base_url!='undefined') {
            b_u = base_url;
	}

	$(lpdiv).html('<div class="loadingprocesslayer" style="margin: auto;" id="progress_layer">'+((typeof message!='undefined' && message!='') ? '<span>'+mess+'</span><br /><img src="'+b_u+'cms/public/images/loading.gif" align="center" />' : '')+'</div>');

	//$("div.loadingprocesslayer",lpdiv).css("left",parseInt($(window).width()/2-parseInt($("div.loadingprocesslayer",lpdiv).width()/2)));

	return did;
}

var po_update_timer=undefined;

function in_array(needle, haystack, strict) {
	var found = false, key, strict = !!strict;
	for (key in haystack) {
		if ((strict && haystack[key] === needle) || (!strict && haystack[key] == needle)) {
			found = true;
			break;
		}
	}

	return found;
}

var sedialog=false;

function seEdit(object,object_id,controller,plugin,dialog_width,modal) {

	var add_params = '';

	if (typeof params!='undefined') {
		for (key in params)
			add_params += '/'+key+'/'+params[key];
	}

	var cntrl = (controller==undefined) ? current_controller : controller;
	var plgn = (plugin==undefined) ? current_plugin : plugin;
	var obj_params = '';

	if (typeof object_id == 'object') {
		for (key in object_id)
			obj_params  += '/'+key+'/'+object_id[key];
	}else obj_params = '/objectid/'+object_id;

	if (typeof dialog_type!='undefined' && dialog_type=='bootstrap') {
		sedialog = bsAjaxDialog('/'+plgn+'/'+cntrl+'/seeditobject/object/'+object+obj_params+add_params, 'Редактирование', {
			'Сохранить': 'seSaveData("'+object+'","'+object_id+'", "'+cntrl+'", "'+plgn+'")'
		});
	}else {

		var w = (dialog_width!==undefined) ? dialog_width : 600;

		sedialog = ajaxDialog('/'+plgn+'/'+cntrl+'/seeditobject/object/'+object+obj_params+add_params, 'Редактирование', w, 'auto', {
			'Сохранить': function(){seSaveData(object,object_id, cntrl, plgn);}
		},null,modal);
	}
}

function seSaveData(object, object_id, controller, plugin) {

	if (typeof CKEDITOR!=="undefined") {
		$('#'+sedialog+' form textarea').each(function() {
			var textareaId = $(this).attr('id');
			if (textareaId && CKEDITOR.instances[textareaId]) {
				$('#' + textareaId).val(CKEDITOR.instances[textareaId].getData());
			}
		});
	}
	
	
	//////check all fields for require////////
	var ret = false;
	$('#'+sedialog+' form input[required=true]').each(function(){
		if ($(this).val()=='') {
			alert('Не заполнено одно из обязательных полей!');
			
			$(this).delay(100).fadeOut().fadeIn('slow').delay(100).fadeOut().fadeIn('slow');
			
			ret = true;
			return false;
		};
	});
	
	if (ret) return false;
	//////////////////////////////////////////
	

	var data = $('#'+sedialog+' form').serializeArray();

	var add_params = '';

	if (typeof params!='undefined') {
		for (key in params)
			add_params += '/'+key+'/'+params[key];
	}

	var cntrl = (controller==undefined) ? current_controller : controller;
	var plgn = (plugin==undefined) ? current_plugin : plugin;
	var obj_params = '';

	if (typeof object_id == 'object') {
		for (key in object_id)
			obj_params  += '/'+key+'/'+object_id[key];
	}else obj_params = '/objectid/'+object_id;

	var target_url = '/'+plgn+'/'+cntrl+'/seeditobject/object/'+object+obj_params+add_params;

	var file_input_count = $('#'+sedialog+' form input[type=file]').length;

	showLoadingProcessLayer('Сохраняю');

	if (file_input_count>0 && $('#'+sedialog+' form input[type=file]').attr('seIgnore')!="true") {

		$('#'+sedialog+' form').after('<iframe onload="seEditFilishSave(this)" id="ifr'+sedialog+'" name="ifr'+sedialog+'" style="display:none;"></iframe>');

		$('#'+sedialog+' form').attr('enctype','multipart/form-data');
		$('#'+sedialog+' form').attr('method','post');
		$('#'+sedialog+' form').attr('action',target_url);
		$('#'+sedialog+' form').attr('target','ifr'+sedialog);

		$('#'+sedialog+' form').submit();
	}else {
		sendPost(target_url, data,
			function() {
				document.location.reload();
			},
                        function(){
                            hideLoadingProcessLayer();
                        }
		);
	}
}

function seEditFilishSave(frame) {
	try {
		var content = $(frame).contents().find('body').text();

		var data = JSON.parse(content);
		if (data['finish']) {
			if (data['error']!=''){
				alert(data['error']);
                hideLoadingProcessLayer();
            }else document.location.reload();
		}
	}catch (e) {}
}

function rand(min, max) {
  return Math.floor(Math.random() * (max - min + 1)) + min;
}


function ckeditorInit(callbackFunction) {
    if (typeof CKEDITOR=='undefined') {
            var script = document.createElement( 'script' );
            script.type = 'text/javascript';
            script.src = '/cms/public/js/ckeditor/ckeditor.js';

            $(script).load(callbackFunction);

            document.body.appendChild(script);
    }else callbackFunction();
    
}


function checkFormElement(el,type) {
    var val = $(el).val();
    
    switch (type) {
        case 'email':
            return /[a-zа-я0-9_.-]+\@[a-zа-я0-9_.-]+\.[a-zа-я0-9_.-]+/i.test(val);
            break;
    }
    return false;
}

function checkValidForm(form_el) {
    $('input',form_el).each(function(index,elem){
        if ($(elem).attr('checkfor')) {
            if (!checkFormElement(elem,$(elem).attr('checkfor'))) {
                $(elem).addClass('form_incorrect');
                return false;
            }
        }
    });
}


var Base64 = {
 
	// private property
	_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
 
	// public method for encoding
	encode : function (input) {
		var output = "";
		var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
		var i = 0;
 
		input = Base64._utf8_encode(input);
 
		while (i < input.length) {
 
			chr1 = input.charCodeAt(i++);
			chr2 = input.charCodeAt(i++);
			chr3 = input.charCodeAt(i++);
 
			enc1 = chr1 >> 2;
			enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
			enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
			enc4 = chr3 & 63;
 
			if (isNaN(chr2)) {
				enc3 = enc4 = 64;
			} else if (isNaN(chr3)) {
				enc4 = 64;
			}
 
			output = output +
			this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
			this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
 
		}
 
		return output;
	},
 
	// public method for decoding
	decode : function (input) {
		var output = "";
		var chr1, chr2, chr3;
		var enc1, enc2, enc3, enc4;
		var i = 0;
 
		input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
 
		while (i < input.length) {
 
			enc1 = this._keyStr.indexOf(input.charAt(i++));
			enc2 = this._keyStr.indexOf(input.charAt(i++));
			enc3 = this._keyStr.indexOf(input.charAt(i++));
			enc4 = this._keyStr.indexOf(input.charAt(i++));
 
			chr1 = (enc1 << 2) | (enc2 >> 4);
			chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
			chr3 = ((enc3 & 3) << 6) | enc4;
 
			output = output + String.fromCharCode(chr1);
 
			if (enc3 != 64) {
				output = output + String.fromCharCode(chr2);
			}
			if (enc4 != 64) {
				output = output + String.fromCharCode(chr3);
			}
 
		}
 
		output = Base64._utf8_decode(output);
 
		return output;
 
	},
 
	// private method for UTF-8 encoding
	_utf8_encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";
 
		for (var n = 0; n < string.length; n++) {
 
			var c = string.charCodeAt(n);
 
			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}
 
		}
 
		return utftext;
	},
 
	// private method for UTF-8 decoding
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;
 
		while ( i < utftext.length ) {
 
			c = utftext.charCodeAt(i);
 
			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}
 
		}
 
		return string;
	}
 
}


function showMessage(m,h) {
    
    if (typeof h === 'undefined') {
        h = 'Внимание!';
    }
    
    var mb = $('<div id="message_box"><x onclick="$(\'#message_box\').animate({top: -2000},300,function(){$(this).remove();});"></x><h>'+h+'</h><t>'+m+'</t></div>');
    
    $('body').append(mb);    
    
    $('#message_box').css('opacity',0).css('top','-1250px').animate({
        top: 120,
        opacity: 1
    },1000);
    
    
}
