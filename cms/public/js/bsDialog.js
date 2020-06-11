function bsAjaxDialog(url, title, buttons, width, data, callback_after_load) {
    
	var did = "bs_dialog_"+Math.floor(Math.random() * (1000 - 1) + 1);

	var dialogdiv = document.createElement("DIV");

	dialogdiv.id=did;
	document.getElementsByTagName('body')[0].appendChild(dialogdiv);
	var btn = '';
        
        var btn_objs = new Array();
        
	if (typeof buttons!=='undefined') {
            for (var n in buttons) {
                
                if (typeof buttons[n]==='function') {
                    
                    var b = $('<button class="btn btn-primary">'+n+'</button>');
                    
                    b.on('click',buttons[n]);
                    
                    btn_objs.push(b);
                    
                }else {
                    btn +='<button class="btn btn-primary" onclick="'+buttons[n].replace(/"/g,"'")+'">'+n+'</button>';
                }
            }
	}
	$('#'+did)
            .attr('class',"modal hide")
            .attr('tabindex',"-1")
            .attr("role","dialog")
            .attr("aria-labelledby", "myModalLabel")
            .attr("aria-hidden","true")
            .append('<div class="modal-header"><button type="button" class="close" onclick="bsDialogDestroy(\''+did+'\',this)" aria-hidden="true">×</button><h3 id="myModalLabel">'+title+'</h3></div>')
            .append('<div class="modal-body"></div>')
            .append('<div class="modal-footer">'+btn+'<button class="btn" onclick="bsDialogDestroy(\''+did+'\')">Закрыть</button></div>');
            // data-dismiss="modal"
	
	if (typeof width!=='undefined') {
            $('#'+did).css('width',width+'px');

            var marg = (width/2).toFixed();

            //$('#'+did).css('margin','-250px 0 0 -'+marg+'px');
            $('#'+did).css('margin-left','-'+marg+'px');
	}
	
        
        btn_objs.forEach(function(item,i,arr) {
            $('.modal-footer','#'+did).prepend(item);
        });
        
	$(".modal-body","#"+did).load(url,data,function(){

            $("#"+did).modal('show');		

            $(document).on('hidden.bs.modal', "#"+did, function (e) {
                $("#"+did).remove();
                $('body').css('overflow','auto');
            });

            $(".modal-body script", "#"+did).each(function(k,v) {
                    //eval($(v).text());
            });
        
        
        
            if (typeof callback_after_load!=='undefined')
                callback_after_load();
                
	});


	return did;
}

function bsDialogDestroy(did,btn) {
    if (!did)
        did = cms.dialog.id;
    
    if (btn) {
        var d = $(btn).parent().parent().parent().parent();
        
        $('.modal-backdrop:first').fadeOut(200);
        
        setTimeout(function() {
            $('.modal-backdrop:first').remove();
            $("#"+did).remove();
            //$('body').attr('style','');
            $('body').attr('style','').removeClass('modal-open');
        },200);
        
    }else {
    
	$("#"+did).modal("hide");
        
        $('.modal-backdrop:first').fadeOut(200,function(){
            
            $('.modal-backdrop:first').remove();
            $("#"+did).remove();
            //$('body').css('overflow','auto').removeClass('modal-open').css('padding-right: 0;');
            
            if($('.modal-backdrop').length==0)
                $('body').attr('style','').removeClass('modal-open');
            
        });
        
    }
    
        
        
}


////////// BootStrap3 version

function bsAjaxDialog3(url, title, buttons, width, data, callback_after_load, callback_after_close) {
        
        
	var did = "bs_dialog_"+Math.floor(Math.random() * (1000 - 1) + 1);
        
        cms.dialog.dids.push(did);
        
        cms.dialog.id = did;

	var dialogdiv = document.createElement("DIV");

	dialogdiv.id=did;
	document.getElementsByTagName('body')[0].appendChild(dialogdiv);

	var btn = '';
        
        var btn_objs = new Array();
        
        var n = null;
        
        var has_close = false;
        var is_close = true;
        
	if (buttons!==undefined) {
            for (n in buttons) {
                
                is_close = false;
                
                if (n=='Закрыть') { 
                    has_close = true;
                    is_close = true;
                }
                
                if (typeof buttons[n]==='function') {
                    
                    var b = $('<button class="btn'+((!is_close) ? ' btn-primary" ' : '"')+'>'+n+'</button>');
                    
                    b.on('click',buttons[n]);
                    
                    btn_objs.push(b);
                    
                }else {
                    btn +='<button class="btn'+((!is_close) ? ' btn-primary" ' : '"')+' onclick="'+buttons[n].replace(/"/g,"'")+'">'+n+'</button>';
                }
            }
	}
        
        if (!has_close) {
            var closeName = (typeof translate!=='undefined') ? translate.item('close_btn') : 'Закрыть';
            
            btn = '<button class="btn close_btn" data-dismiss="modal" onclick="bsDialogDestroy(\''+did+'\', this);">'+closeName+'</button>\n'+btn;
        }

	$('#'+did)
                .attr('class',"modal fade")
                .append(
                    '<div class="modal-dialog">\n\
                        <div class="modal-content">\n\
                            <div class="modal-header">\n\
                                <button type="button" class="close" onclick="bsDialogDestroy(\''+did+'\', this)" ><span aria-hidden="true">&times;</span></button>\n\
                                <h4 class="modal-title">'+title+'</h4>\n\
                            </div>\n\
                            <div class="modal-body"></div>\n\
                            <div class="modal-footer">\n\
                                '+btn+'\
                            </div>\n\
                        </div>\n\
                    </div>'
        );

        if (callback_after_close)
            $('.close_btn', '#'+did).click(callback_after_close);

        
        btn_objs.forEach(function(item,i,arr){
            $('.modal-footer','#'+did).prepend(item);
        });
	
	if (width) {
            $('#'+did+' .modal-dialog').css('width', width);
	}
	
        $(".modal-body","#"+did).html('Загрузка...');
        
        $("#"+did).modal('show');
        
        $.ajax({
            type: "GET",
            url: url,
            data: data,
            success: function(msg){
                $(".modal-body","#"+did).html(msg);

                $("#"+did).on('hidden.bs.modal', function () {
                    $(this).remove();
                    $('.modal-backdrop:first').remove();

                    $('body').attr('style','').removeClass('modal-open');
                });

/*
 * Comment for supress double execution
                $(".modal-body script", "#"+did).each(function(k,v) {
                    eval($(v).text());
                });
*/
                if (callback_after_load) {
                    callback_after_load();
                };
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log("DIALOG GET DATA FAILED URL "+url+" "+XMLHttpRequest.responseText);
            }
        });        

	return did;
}



////////// BootStrap4 version
function bsAjaxDialog4(url, title, buttons, width, data, callback_after_load, callback_after_close) {
        
        
	var did = "bs_dialog_"+Math.floor(Math.random() * (1000 - 1) + 1);
        
        cms.dialog.dids.push(did);
        
        cms.dialog.id = did;

	var dialogdiv = document.createElement("DIV");

	dialogdiv.id=did;
	document.getElementsByTagName('body')[0].appendChild(dialogdiv);

	var btn = '';
        
        var btn_objs = new Array();
        
        var n = null;
        
        var has_close = false;
        var is_close = true;
        
	if (buttons!==undefined) {
            for (n in buttons) {
                
                is_close = false;
                
                if (n=='Закрыть') { 
                    has_close = true;
                    is_close = true;
                }
                
                if (typeof buttons[n]==='function') {
                    
                    var b = $('<button class="btn'+((!is_close) ? ' btn-primary" ' : '"')+'>'+n+'</button>');
                    
                    b.on('click',buttons[n]);
                    
                    btn_objs.push(b);
                    
                }else {
                    btn +='<button class="btn'+((!is_close) ? ' btn-primary" ' : '"')+' onclick="'+buttons[n].replace(/"/g,"'")+'">'+n+'</button>';
                }
            }
	}
        
        if (!has_close) {
            var closeName = (typeof translate!=='undefined') ? translate.item('close_btn') : 'Закрыть';
            
            btn = '<button class="btn close_btn" data-dismiss="modal" onclick="bsDialogDestroy(\''+did+'\', this);">'+closeName+'</button>\n'+btn;
        }
        
        
	$('#'+did)
                .attr('class',"modal")
                .append(
                    '<div class="modal-dialog modal-dialog-scrollable">\
                        <div class="modal-content">\
                            <div class="modal-header">\n\
                                \n\<h4 class="modal-title">'+title+'</h4>\n\
                                <button data-dismiss="modal" type="button" class="close" onclick="bsDialogDestroy(\''+did+'\', this)" >&times;</button>\n\
                            </div>\n\
                            <div class="modal-body"></div>\n\
                            <div class="modal-footer">\n\
                                '+btn+'\
                            </div>\n\
                        </div>\n\
                    </div>'
        );

        if (callback_after_close)
            $('.close_btn', '#'+did).click(callback_after_close);

        
        btn_objs.forEach(function(item,i,arr){
            $('.modal-footer','#'+did).prepend(item);
        });
	
	if (width) {
            $('#'+did+' .modal-dialog').css('width', width);
            $('#'+did+' .modal-dialog').css('max-width', 'none');
	}
	
        $(".modal-body","#"+did).html('Загрузка...');
        
        $("#"+did).modal();
        
        $.ajax({
            type: "GET",
            url: url,
            data: data,
            success: function(msg){
                $(".modal-body","#"+did).html(msg);

                $("#"+did).on('hidden.bs.modal', function () {
                    $(this).remove();
                    $('.modal-backdrop:first').remove();

                    $('body').attr('style','').removeClass('modal-open');
                });
/*
                $(".modal-body script", "#"+did).each(function(k,v) {
                    eval($(v).text());
                });
*/
                if (callback_after_load) {
                    callback_after_load();
                };
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log("DIALOG GET DATA FAILED URL "+url+" "+XMLHttpRequest.responseText);
            }
        });        

	return did;
}
