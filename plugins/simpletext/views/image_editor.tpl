<link rel="stylesheet" href="{$config->url->base}plugins/simpletext/public/css/style.css" type="text/css" media="screen"></link>
{*
<div id="siDiv{$id}" class="simpleImage" {if isset($si[$id].params.divAttr)}{$si[$id].params.divAttr}{/if}>
*}
	<div class="simpleImageEditButton simpleImage" id="siEditDiv{$id}">
		<img src="{$config->url->base}plugins/simpletext/public/images/image_edit.png" onclick="editIm_{$id}();return false;" />
	</div>
{*        
</div>
*}

{$data}

<script>
	var si_dialog_{$id} = null;
	function editIm_{$id}() {
		si_dialog_{$id} = cms.dialog.show(
				'/simpletext/image/edit/id/{$id}',
				'Изменение изображения',
				{
					'Сохранить': function() {
						siSave{$id}();
					}
				},
				'90%',
				{
                                    im:'{$si[$id].params.src}'
                                    {if isset($si[$id].params.link)}
                                    ,href:'{$si[$id].params.link}'
                                    {/if}
				},
                                function(){
                                    $('#siDiv{$id}').fadeOut(800);
                                }
		);
	}

    function siInit{$id}() {
        
        var w = $('#{$id}').width();
        var h = $('#{$id}').height();
        
        var l = $('#{$id}').offset().left;
        var t = $('#{$id}').offset().top;
        var el = null;
        
        if ($('#siEditDiv{$id}').parent()[0].tagName!=='BODY') {
            el = $('#siEditDiv{$id}').detach();
            
            $('body').append(el);
        }else el = $('#siEditDiv{$id}');
        
        $('#siEditDiv{$id}').css('left',(l+w-50)+'px')
                    .css('top',(t+20)+'px')
                    .animate({
                        opacity: 1
                    },230);

        
    };
    {*
    $('#{$id}').load(function(){
        //setTimeout(siInit{$id},2000);
    });
*}
    
    $('#{$id}, #siEditDiv{$id}').hover(function(){
        siInit{$id}();
    });

    var mlTo = null;

    $('#{$id}').mouseleave(function(){
        if (mlTo) clearTimeout(mlTo);
        
        mlTo = setTimeout(function(){
            if (!$('#siEditDiv{$id}').is(':hover'))
                $('#siEditDiv{$id}').animate({
                    opacity: 0
                },100);
        }, 300);
    });
    
</script>