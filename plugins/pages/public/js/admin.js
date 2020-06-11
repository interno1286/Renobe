function preview(id) {
	$('#content').val(editAreaLoader.getValue('content'));
	
	scroll_save = $('#preview').contents().find('body').scrollTop();
	
	var data = $('#edit_page_form').serializeArray();
	sendPost('/pages/index/save/'+((typeof id!=='undefined') ? 'id/'+id : ''),data,function(){
		try {
			window.opener.location.reload();
		}catch (e) {};
		
                var path = ($('#path').val()!='' && $('#path').val().substring(0,1)=='/') ? $('#path').val() : window.opener.location.href;
                        
                $('#preview').attr('src',path).unbind('load').on('load',function(){
                        $('#preview').contents().find('body').scrollTop(scroll_save);
                });

	});
}

var scroll_save = false;

function save(id) {
	$('#content').val(editAreaLoader.getValue('content'));
	
	scroll_save = $('#preview').contents().find('body').scrollTop();
	
	var data = $('#edit_page_form').serializeArray();
	sendPost('/pages/index/save/'+((typeof id!=='undefined') ? 'id/'+id : ''),data,function(){
            
		try {
			window.opener.location.reload();
		}catch (e) {};
                
                var path = ($('#path').val()!='' && $('#path').val().substring(0,1)=='/') ? $('#path').val() : window.opener.location.href;
                
                $('#preview').attr('src',path).unbind('load').on('load',function(){
                        $('#preview').contents().find('body').scrollTop(scroll_save);
                });
		
		window.close();
	});
	
}


function rollback(id) {
	var data = $('#edit_page_form_source').serializeArray();
	sendPost('/pages/index/save/'+((typeof id!=='undefined') ? 'id/'+id : ''),data,function(){
		window.opener.location.reload();
		window.close();
	});
	
}



function insertST() {
	var text = editAreaLoader.getSelectedText('content');
	
	var default_name = text.replace(new RegExp("[^a-z0-9_-]",'ig'),'').substring(0,30);
	
	editAreaLoader.setSelectedText('content', "{simpleTextEditor name='"+default_name+"' editor=false in_dialog=true}");
}


function insertSI() {
	var text = editAreaLoader.getSelectedText('content');
	
	var src = text.match(/src=[\"|\']([^\"\']+)[\'|\"]/i);
	
	var attr = text.match(/src=[\"|\']([^\"\']+)[\'|\"]([^>]+)>/i);
			
	editAreaLoader.setSelectedText('content', "{simpleImage src='"+src[1]+"'"+((attr!==null) ? " attr='"+attr[2].replace(/'/g,"\'")+"'" : '')+"}");
}


function insertAppendix() {
    editAreaLoader.setSelectedText('content', "<button class='btn btn-primary' onclick='pagesAppendix(\""+$('#path').val()+"\",elem_id_to_copy,destination_element_id);'>добавить элемент</button>");
}

function insertRemoveAppendix() {
    editAreaLoader.setSelectedText('content', "<button class='btn btn-primary' onclick='removeAppendix(\""+$('#path').val()+"\",elem_id);'>удалить элемент</button>");
}

function resizeWindow() {
    dh = $(window).height();
	
    if (!tp)
        tp=$('#frame_content').position().top;
    
    resze();
}

var tp = null;
var dh = null;

function getCoef(current_top) {
    if (!dh)
        dh = $(window).height();
	
    if (!tp)
        tp=$('#frame_content').position().top;
    
	var work_area = dh-tp;

	var pos_in_area = current_top - tp - 10;

	var coef = pos_in_area/work_area;

	return coef;

}

function resze() {

    if (!tp) return false;
    
    var work_area = dh-tp-30;
    var coef = 0;

    $('#preview_container').height(parseInt(work_area*(1-coef)*0.98));

    $('#frame_content').height(parseInt(work_area*coef*1.05)).css('width',$(window).width()+'px');
}


(function($){
    
	$.fn.disableSelection = function() {
		return this
				 .attr('unselectable', 'on')
				 .css('user-select', 'none')
				 .on('selectstart', false);
	};
    
})(jQuery);		



function saveTpl(tpl) {
    var data = {
        content: editAreaLoader.getValue('content')
    };

    $.post('/pages/index/edittpl/tpl/'+tpl,data);
}

function previewTpl(tpl) {

    var data = {
        content: editAreaLoader.getValue('content')
    };


    $.post('/pages/index/edittpl/tpl/'+tpl,data,function(){
        
        var path = $('#path').val();
        
        if (!path) path = '';
        
        var path = (path.substring(0,1)=='/') ? $('#path').val() : window.opener.location.href;

        $('#preview').attr('src',path).unbind('load').on('load',function(){
                $('#preview').contents().find('body').scrollTop(scroll_save);
        });
    });

}
