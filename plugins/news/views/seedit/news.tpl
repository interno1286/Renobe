<form id="data_form" class="news_add_form" action="/news/index/act/{if isset($id) && $id!=''}edit/id/{$id}{else}add{/if}" enctype="multipart/form-data" method="post">

		<div class="add_n_descr">Дата:</div>
		<input type="text" class="dt form-control" name="create_date" value="{$current_data.create_date|default:$smarty.now|date_format:'%d.%m.%Y'}" />


		<div class="add_n_descr">Заголовок:</div>
			<input type="text" class="form-control" name="header" value="{$current_data.header|default:''}" />

		<div onclick="$('#add_n_description,#add_n_text').slideToggle();return false;" class="add_n_descr add_n_more_button">Анонс для ленты:</div>
        
        <div id="add_n_description">
            <div class="closer"></div>
            <textarea class="edited" class="form-control add_n_editor" id="news_description" name="description">{$current_data.description|default:''}</textarea>
        </div>
            
            
		<div onclick="$('#add_n_description,#add_n_text').slideToggle();return false;" class="add_n_descr add_n_more_button">Текст новости:</div>
        
        <div id="add_n_text" style="display: none;">
            <div class="closer"></div>
            <textarea class="edited" class="form-control add_n_editor" id="news_text" name="text">{$current_data.text|default:''}</textarea>
        </div>

		<br />
		<div class="add_n_descr">Сопроводительное изображение:</div>
		<input type="file" name="photo" />
        <br />
        <a href="#" class="add_n_more_button" onclick="$('#add_n_addon').slideToggle();return false;">Дополнительно</a>
        <div id="add_n_addon" style="display: none">
            <br />
            <div class="add_n_descr">Видео в формате FLV:</div>
            <input type="file" name="video" />

            <br />
            <div class="add_n_descr">Аудио материал в формате MP3:</div>
            <input type="file" name="audio" />
        </div>

</form>

<script>
	$(document).ready(function(){
		CKEDITOR.replace( 'news_description',{
						extraPlugins: 'cyberim', defaultLanguage: 'ru',
						toolbar: 'Simple'
		});
        
		CKEDITOR.replace( 'news_text',{
						extraPlugins: 'cyberim', defaultLanguage: 'ru',
						toolbar: 'Advanced'
		});
        
        
        $('.dt').datepicker();
	})
</script>

