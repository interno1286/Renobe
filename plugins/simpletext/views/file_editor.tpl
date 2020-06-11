<link rel="stylesheet" href="{$config->url->base}plugins/simpletext/public/css/style.css" type="text/css" media="screen" />

<div class="sfedit{$id}" title="Двойной клик для редактирования этого блока" onclick="clearTimeout(sfto{$id});sfto{$id} = setTimeout(click{$id},1000);return false;" ondblclick="clearTimeout(sfto{$id});editFile{$id}();" data-container="body">
	{$data}
</div>

<script>
        
        var sfto{$id} = false;
        
        function click{$id}() {
            var href = $('.sfedit{$id} a').prop('href');
            document.location.href=href;
        }
    
        $(document).ready(function(){
            $('.sfedit{$id}').tooltip();
        });
    
	var sf_dialog_{$id} = null;
        
	function editFile{$id}() {
            
		sf_dialog_{$id} = ajaxDialog(
				'/simpletext/file/edit/id/{$id}',
				'Загрузка нового файла',
				650,
				'auto',
				{
					'Загрузить': function() {
						sfSave{$id}();
					}
				},
				undefined,
				true,
                                {
                                    name: '{$sf[$id].params.name}'
                                }
		);
	}
        
        
        function sfSave{$id}() {
            $('#editFileForm').submit();
        }
</script>


