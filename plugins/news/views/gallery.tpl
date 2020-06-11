<div style="clear:both;"></div>

{if $gallery_images}

    
    <div class="news_gallery">
	{foreach $gallery_images as $im}
	    <div>
		<a class="news_gallery_image" href="/public/news_gallery/{$event.id}/big_{$im.file}" rel="fancybox-button">
		    <img src="/public/news_gallery/{$event.id}/small_{$im.file}" />
		</a>
		
		{if $edit_allowed}
		    <a href="/news/gallery/remove/id/{$im.id}"><i class="icon-remove"></i></a>
		{/if}
		
	    </div>
	{/foreach}
    </div>    
    

    <script>
	$(document).ready(function(){
	    $('.news_gallery').slick({
		infinite: true,
		slidesToShow: 4,
		slidesToScroll: 1
	    });
	    
	    $(".news_gallery_image").fancybox({
	    });	    
	    
	});
    </script>

{/if}

{if $edit_allowed}
    <button style="margin: 20px 0 20px 0;" class="btn btn-inverse" onclick="seEdit('gallery',{$event.id},'gallery','news');">Загрузить изображения в галерею</button>
{/if}