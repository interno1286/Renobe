function ajaxDialog( url, title, width, height, buttons, callback, modal, data, close_callback ) {
	var did = "ajax_dialog_"+Math.floor(Math.random() * (1000 - 1) + 1);

	if (buttons==undefined) buttons = new Object();

	buttons['Закрыть'] = function(){$(this).dialog('close');};

	var dialogdiv = document.createElement("DIV");

	var m=(modal) ? modal : false;

	dialogdiv.id=did;

	$('body').append(dialogdiv);
	//document.getElementsByTagName('body')[0].appendChild(dialogdiv);

	$("#"+did).load(url,data,function(){

		$("#"+did).dialog({
			title:title,
			width: width,
			height: height,
			buttons: buttons,
			modal: m,
			closeOnEscape: false,
			close: function() {
				if (typeof close_callback!='undefined')
					close_callback(did);
				else ajaxDialogDestroy(did);
			}
		});


		$('.ui-dialog-buttonset button').addClass('btn');

		if ( callback!=undefined && callback!==false && callback!==null) callback();

		try {
			//$.each($("#"+did+" script"),function(k,v) {eval($(v).text());}); /// запускаем скрипты из контента
			// load и так сам выоплняет все скрипты на странице
		}catch (e) {
			alert('При вызове сценария, на странице диалога произошла ошибка! '+e);
		}
	});

	return did;
}

function ajaxDialogDestroy(did) {
    if ($("#"+did).length>0)
        $("#"+did).dialog('destroy').remove();
}


function submitAjaxDialogForm(dialogid,formid,url,action) {
	var data = $("#"+formid).serializeArray();

	$.post(url,data,function(ret){
		if (ret['error']!='')
			alert(ret['error']);
		else {
			if (action!=undefined)
				action();
			else document.location.reload();
		}
	},'json');
}


function bootstrapityDialog(id) {
    $('.ui-dialog-buttonset button').addClass('btn btn-default btn-l');
}