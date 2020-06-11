<style>
.tab-pane {
    max-height: none;
}

</style>
<ul class="nav nav-tabs" id="imEdit">
    
	<li><a href="#cut">Обрезать</a></li>
        
	<li class="active">
            <a href="#upload">Загрузить новое</a>
        </li>
        
	{if $href}
            <li><a href="#href">Ссылка</a></li>
	{/if}
</ul>

<div class="tab-content image_editor">
	<div class="tab-pane" id="cut">
		<form action="/simpletext/image/crop" method="post" enctype="multipart/form-data">
			<div class="alert">
                            {if $ext!='png'}
                                Выделите область изображения мышкой и нажмите кнопку сохранить чтобы обрезать его
                            {else}
                                Изоражения такого типа подрезать нельзя, вы можете только обновить изоражение, загрузив
                                другое, точно таких же размеров ({$w}x{$h} px).
                            {/if}
                        </div>
                        <div id="jcropHolder" style="padding: 20px;width: 100%;overflow: auto;background-image: url(/plugins/skineditor/public/images/transparent.png);">
                            {if file_exists("`$config->path->root``$full_version`")}
                                <img id="si_full_im" />
                            {else}
                                Исходное изображение не найдено {$full_version}
                            {/if}
                        </div>
			<input type="hidden" name="x" id="x" value="0" />
			<input type="hidden" name="y" id="y" value="0" />
			<input type="hidden" name="w" id="w" value="0" />
			<input type="hidden" name="h" id="h" value="0" />

			<input type="hidden" value="" name="new_im" id="new_im" />
		</form>
                        
                <div style="clear: both;"></div>
	</div>

	<div class="tab-pane active" id="upload">
		<form action="/simpletext/image/upload" method="post" enctype="multipart/form-data" target="simpleImUploadframe" id="upload_{$params.id}_form">
			<div class="alert">Выберите файл который вы хотите загрузить вместо текущего изображения.<br />Размеры изображения: {$w}x{$h} px</div>
			<input type="file" name="image" onchange="setTimeout(function(){
                            doUpload{$params.id}();
                        },800);" />

			<input type="hidden" value="{$params.im}" name="old_im" />
                        <br /><br />
                        
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <label>Максимальная ширина</label>
                                <input type="text" style="width:100px;" class="form-control" id="newImMaxW" name="max_width" value="{$w}" />
                            </div>
                            
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <label>Обрезать края для сохранения пропорций</label>
                                    <input style="width: 40px;" type="checkbox" name="cut" checked="checked" value="1" />
                                </div>
                            
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <label>Привести к исходному размеру</label>
                                    <input style="width: 40px;" type="checkbox" name="force_size" value="1" />
                                </div>
                            
                        </div>
		</form>
                        
		<iframe name="simpleImUploadframe" id="simpleImUploadframe" style="display:none;"></iframe>

	</div>
		
	{if $href}
		<div class="tab-pane" id="href">
			<div class="alert">
				Укажите здесь адрес страницы, на которую будет перенаправлен пользователь при клике на изображение. 
				Это может быть как абсолютная ссылка вида http://site.com/123.html.<br />Так и относительная вида /shop.html
			</div>
			<input type="text" name="href" id="link" value="{$data.link|default:''}" style="width: 95%;"/>
		</div>
	{/if}
</div>


<link rel="stylesheet" href="{$config->url->base}plugins/simpletext/public/js/jcrop/css/jquery.Jcrop.min.css" type="text/css" media="screen" />

<script>
	$(document).ready(function() {

		$('#imEdit a').click(function (e) {
			e.preventDefault();
			$(this).tab('show');
		});
                
                
            {if file_exists("`$config->path->root``$full_version`")}

                {if $ext!='png'}
                    $('#si_full_im').load(function(){
                        connect{$params.id}Jcrop();
                    });

                {/if}

                setTimeout(function(){
                    $('#si_full_im').attr('src','/simpletext/image/crop/w/'+($('#imEdit').width()-60)+'/f/{base64_encode($full_version)}');
                }, 1500);
            {/if}
                
                
                
	});

	function saveCords{$params.id}(c) {
            $('#x').val(c.x);
            $('#y').val(c.y);
            $('#w').val(c.w);
            $('#h').val(c.h);
            return false;
	}


	function connect{$params.id}Jcrop() {
            
		if (typeof window.jcrop=='undefined') {
                        window.jcrop = true;
			var script = document.createElement( 'script' );
			script.type = 'text/javascript';
			script.src = '{$config->url->base}plugins/simpletext/public/js/jcrop/jquery.Jcrop.min.js';
                        
			$(script).load(function(){
                            
                            $('#jcropHolder').height(3000);
                            
                                $('#si_full_im').css('width',window.imWidth+'px');
                                $('#si_full_im').css('height',window.imHeight+'px');
                            
                            
				window.jcrop = $.Jcrop('#si_full_im', {
                                    aspectRatio: {if intval($prop)}{$prop}{else}1{/if},
                                    onSelect: saveCords{$params.id}
				});
                                
                            $('#jcropHolder').height("");
			});

			document.body.appendChild(script);

		}else {
                    
                    if (window.jcrop===true) return true;
                    
                    if (window.jcrop.destroy)
                        window.jcrop.destroy();
                    
                    $('#jcropHolder').height(3000);
                    
                    $('#si_full_im').css('width',window.imWidth+'px');
                    $('#si_full_im').css('height',window.imHeight+'px');
                    
                    window.jcrop = $.Jcrop('#si_full_im', {
                        aspectRatio: {if intval($prop)}{$prop}{else}1{/if},
                        onSelect: saveCords{$params.id}
                    });
                    
                    $('#jcropHolder').height("");
                }
	}


        var lp = null;
        
	function doUpload{$params.id}() {
                //$('#newImMaxW').val($('#imEdit').width()-60);
                
		$('#simpleImUploadframe').load(false);
		$('#simpleImUploadframe').load(replaceImage{$params.id});
                
                lp = showLoadingProcessLayer('Загружаю изображение');
                
		$('#upload_{$params.id}_form').submit();
                
	}


	function replaceImage{$params.id}() {
                $('#'+lp).remove();
                
		var response = $.parseJSON($('#simpleImUploadframe').contents().text());

		if (response['error']==undefined){
                    alert(response);
                    return;
		}

		if (response['error']!='') {
			alert(response['error']);
			return;
		}
                {if $ext!='png'}
                    window.jcrop.destroy();
                    $('.jcrop-holder').remove();
                {/if}
                
                
                
                                    
                $('#si_full_im')
                        .attr('src',response['im'])
                        .attr('style','width: '+response.width+'px;height: '+response.height+'px;');
                
                window.imWidth = response.width;
                window.imHeight = response.height;
                
                var s = $('#si_full_im').attr('style');
                
                $('#new_im').val(response['im']);

		$('#imEdit a:first').tab('show');
	}



	function siSave{$params.id}() {

		/*
		if (parseInt($('#w').val(),10)===0) {
			alert('Вы не выбрали область изображения!');
			return false;
		}
		*/

		var imw = parseInt($('#si_full_im').width(),10);
		var imh = parseInt($('#si_full_im').height(),10);

		var data = {
			'crop_data[x]':parseInt($('#x').val(),10)/imw*100,
			'crop_data[y]':parseInt($('#y').val(),10)/imh*100,
			'crop_data[w]':parseInt($('#w').val(),10)/imw*100,
			'crop_data[h]':parseInt($('#h').val(),10)/imh*100,
			'image': '{$params.im}',
			'new_im': $('#new_im').val(),
			'href': $('#link').val(),
                        'full_im': '{$full_version}'
		};

		$.post('/simpletext/image/save',data,function(ret){
			if (ret['error']) {
                            alert(ret['error']);
			}else {
                            cms.dialog.hide(si_dialog_{$params.id});
                            
                            $('#si{$params.id}').attr('src','/cms/public/images/loading.gif')
                            $('#si{$params.id}').attr('src',ret['img_url']+'?t='+Math.random());
                            
                            
                            simpleImage = {
                                src: ret['img_url']+'?t='+Math.random()
                            };
			}
		},'json');


	}

</script>
