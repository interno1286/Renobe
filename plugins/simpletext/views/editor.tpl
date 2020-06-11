
<div class="st stett{$id}" id="stcontainer{$id}" title="{if $editEvent=='onclick'}Кликните{else}Двойной клик{/if} для редактирования этого блока {if $is_draft} ЧЕРНОВИК!{/if}" data-container="body">
    
	<div id="steditdiv{$id}" style="display:none;" class="st_edit_div modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">            
                        Редактировать
                    </div>
                    
                    <div class="modal-body">
                        {if $editor==true}
                                <textarea style="width:100%;" name="sttext{$id}" id="text{$id}">{$data}</textarea>
                                <label for="check_draft{$id}">
                                    <input type="checkbox" class="checkBoxNotCustomize" name="draft" id="check_draft{$id}" {if $is_draft}checked="checked"{/if} />
                                    Сохранить как черновик
                                </label>
                        {else}
                                <input style="
                                    width: 100%;
                                    border: 1px solid #e2cece;
                                    padding: 7px 10px;
                                " name="sttext{$id}" id="text{$id}" value="{$data}" onkeyup="kp{$id}(event);" />
                        {/if}
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="doSave{$id}();">Сохранить</button>
                        
                        {if isset($st_params.href) && $st_params.href && $st_params.href!='#'}
                            <button class="btn btn-primary" data-dismiss="modal" onclick="location.href='{$st_params.href}';">{$st_params.href_text|default:'Перейти в раздел'}</button>
                        {/if}
                    </div>
                </div>
            </div>
	</div>

	<span id="stshow{$id}" {$editEvent|default:'onclick'}="doEdit{$id}($('#st_edit_buttton_{$id}')); return false;">
		<span id="stcontenttext{$id}">{$data}</span>
	</span>
	
	{if trim($data)==''}
            <button class="blank_content_button_{$id} btn btn-xs btn-info" id="st_edit_buttton_{$id}" {$editEvent|default:'onclick'}="doEdit{$id}(this); return false;">
                <i class="glyphicon glyphicon-edit icon-edit"></i>
            </button>
	{/if}
        
	{if !$in_dialog}
            <button title="Сохранить изменения в блоке" id="st_save_buttton_{$id}" class="save_a btn btn-mini btn-info" onclick="doSave{$id}(this); return false;" style="display:none;">
                <i class="glyphicon glyphicon-ok-sign icon-ok"></i>
            </button>
	{/if}
</div>

<style>
    #stcontainer{$id} {
            display: {$display};
    }

    #stshow{$id} {
            cursor: pointer;
            position: relative;
            display: {$display};
    }

    #stshow{$id}:hover {
            background-image: url('{$config->url->base}plugins/simpletext/public/images/edit_back.png');
    }

    #stshow{$id}:hover #steditin{$id} {
            display: block;
    }

    #steditin{$id} {
            display: none;
            position: absolute;
            right: 15px;
            top: -6px;		
            width: 10px;
            height: 10px;
    }

    #steditdiv{$id} .modal-dialog {
        width: 70%;
    }
</style>

        
<script>
    {if $notooltip===false}
	$(document).ready(function(){
            $('.stett{$id}').tooltip();
	});
    {/if}
    
	function doEdit{$id}(el) {
	
            {if $notooltip===false}
		try {
                    $('.stett{$id}').tooltip('destroy');
                }catch (e) {
                };
		
                
		$('#st_save_buttton_{$id}').tooltip({
                    placement: 'auto'
                });
            {/if}
		

		{if $in_dialog}
                    
                        $('#steditdiv{$id}').on('shown.bs.modal', function () {
                          $('#text{$id}').focus();
                        });                    
                        
			$('#steditdiv{$id}').modal('show');
		{else}
			$('#stshow{$id}').hide();
			$('#steditdiv{$id}').show();
		{/if}
                    
                {if $editor==true}
                        connect{$id}editor();
                {/if}
                
                if ($('#st_save_buttton_{$id}').is(':visible')) {
                    $('#st_save_buttton_{$id}').show();
                    $('#st_edit_buttton_{$id}').hide();
                }

                if (typeof callbackDoEdit{$id}!='undefined') callbackDoEdit{$id}();

	};

	function doSave{$id}(el) {
            
            $('#st_edit_buttton_{$id}').hide();

            {if $editor==true}
                if ($('#check_draft{$id}').prop('checked')) {
                    alert('Внимание! Вы сохраняете текст как черновик. Сейчас он отобразится на странице для предпросмотра(будет виден только вам). Для того чтобы опубликовать текст - снимите галочку сохранения в черновик.');
                }else 
                    if (!confirm('Текст будет опубликован на странице. Вы уверены?'))
                        return false;


                    var data = {
                            'content': CKEDITOR.instances.text{$id}.getData(),
                            'editor': true,
                            'draft': (($('#check_draft{$id}').prop('checked')) ? '1' : '0')
                    };
            {else}
                    var data = {
                        'content': $('#text{$id}').val(),
                        'editor': false,
                        'draft': (($('#check_draft{$id}').prop('checked')) ? '1' : '0')
                    };
            {/if}

            var save_result = false;

            {if isset($save) && $save}
                    save_result={$save}(data,{$save_params});
            {else}
                    save_result=stSaveData{$id}(data,'{$name}');
            {/if}

		if (save_result) {
                    {if $editor==true}
                            CKEDITOR.instances.text{$id}.destroy();
                    {/if}

                    $('#stcontenttext{$id}').html(data['content']);

                    $('#stshow{$id}').show();

                    if (data['content']!='') {
                            $('.blank_content_button_{$id}').remove();
                    }


                    {if $in_dialog}
                        $('#steditdiv{$id}').modal('hide');
                    {else}
                        $('#steditdiv{$id}').hide();
                    {/if}

                    $('#st_save_buttton_{$id}').hide();
                    $('#st_edit_buttton_{$id}').show();
                    
                    {if $notooltip===false}
                        $('.stett{$id}').tooltip();
                    {/if}
		}
	}


	function stSaveData{$id}(data, name) {
            var d = {
                    'data':data['content'],
                    'draft':data['draft'],
                    'editor': data['editor']
            };
                
            return sendPost('/simpletext/index/savedata/name/'+name,d,function(){
                
            });
	}

	{if $editor}
		function connect{$id}editor() {
			if (typeof CKEDITOR=='undefined') {
				var script = document.createElement( 'script' );
				script.type = 'text/javascript';
				script.src = '{$config->url->base}cms/public/js/ckeditor/ckeditor.js';

				$(script).load(function(){
					CKEDITOR.replace( 'sttext{$id}',{
                                            extraPlugins: 'cyberim,youtube',
                                            defaultLanguage: 'ru',
                                            toolbar: '{$toolbar}'
					});
                                        
                                        CKEDITOR.timestamp=Date.now();
				});

				document.body.appendChild(script);

			}else {
                            CKEDITOR.replace( 'sttext{$id}',{
                                extraPlugins: 'cyberim,youtube',
                                defaultLanguage: 'ru',
                                toolbar: '{$toolbar}'
                            });
                            CKEDITOR.timestamp=Date.now();
                        }
		}
	{else}
		function kp{$id}(e) {
			if (e.keyCode==13) {
				doSave{$id}();
			}
		}
	{/if}


	{if $callback_script}
            {include file=$callback_script id=$id name=$name}
	{/if}



</script>

