<h2>Выберите изображения для загрузки</h2>

<form action="/news/gallery/upload/id/{$params.objectid}" class="dropzone" id="my-awesome-dropzone" onsubmit="document.location.reload();return false;">
	  
</form>


<script>
    $(document).ready(function(){
	$("form#my-awesome-dropzone").dropzone({ 
	    url: "/news/gallery/upload/id/{$params.objectid}",
	    dictDefaultMessage: "Перетащите изображения сюда или кликните чтобы выбрать."
    {*    
    
	    addRemoveLinks: true,
	    dictRemoveFile: 'удалить'
*}	    
	});
    });
</script>

{*
<input type="file" name="images[]" id="source" style="display:none;" />

<form enctype="multipart/form-data" method="post" id="news_gallery_form">
</form>

<br />
<button onclick="newsGalleryAddFile();" class="btn btn-info">Добавить изображение</button>


<script>
    function newsGalleryAddFile() {
	var i = $('#source').clone();
	
	$(i).attr('id','').attr('style','');
	
	$('#news_gallery_form').append(i);
	
	$('#news_gallery_form input[type=file]:last-child').click();
    }
</script>
*}