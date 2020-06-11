
<div class="heading-top heading-top-inner">
	<h2><a href="#">Новости</a></h2>
</div>
<div class="news-holder">
	<div class="visual">
		<img style="margin-bottom: 5px;" src="{if $event.image}{$event.image}{else}/public/images/img-visual05.png{/if}" alt="" width="362" height="341" />
		<br />

		<div style="height: 40px;clear:both;width: {$config->shop->images->big->width}px;">
			<div style="float: left;width: 90px;">
				<div id="vk_like"></div>
			</div>

			<g:plusone size="medium" annotation="none"></g:plusone>
			{literal}
			<a href="https://twitter.com/share" class="twitter-share-button" data-lang="ru">Твитнуть</a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
			{/literal}
		</div>

		<iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2F{$smarty.server.SERVER_NAME}%2F{$smarty.server.REQUEST_URI|urlencode}&amp;send=false&amp;layout=standard&amp;width=450&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=35" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:{$config->shop->images->big->width}px; height:35px;" allowTransparency="true"></iframe>



	</div>
	<div class="text">
		<h3>{$event.name}</h3>
		{$event.text}
        
        
        {if $event.video}
            <br /><br />
                {use file="video_player.tpl" video_file=$event.video}
        {/if}

        {if $event.audio}
            <div style="clear:both;">
                {use file="audio_player.tpl" audio_file=$event.audio}
            </div>
        {/if}
        
        
		{if $edit_allowed}
            
		{/if}
	</div>
</div>


<div class="comment-block">
	<h3>Комментарии<span>Всего ({$comments|@count})</span></h3>
	<div class="holder">
		{if $user_data->id}
		<form action="/{$controller}/{$action}/act/addcomment/id/{$event.id}" method="post" class="form-comment02">
			<fieldset>
				<span class="title">Добавить комментарий:</span>
				<div class="box">
					<div class="visual">
						<a href="#"><img src="{$user_data->medium_photo}" alt="" width="80" height="75" /></a>
					</div>
					<div class="text">
						{*
						<div class="row">
							<span class="input"><input type="text" class="text-input" value="Ваше имя" /></span>
						</div>
						*}
						<div class="row">
							<span class="textarea"><textarea cols="30" name="comment" rows="10">Ваш комментарий</textarea></span>
						</div>
						<div class="row">
							<input type="submit" class="btn-add" value="Добавить " />
						</div>
					</div>
				</div>
			</fieldset>
		</form>
		{/if}
		<div class="table-content">
			<table>
				<tbody>

					{foreach from=$comments item=comment name=comments}
						<tr{if $smarty.foreach.comments.iteration%2!=0} class="odd"{/if}>
							<td class="angle-left-bottom" style="width: {$config->user_photo->medium->width+10}px;">
								<div>
									<a onclick="return false;">
										<img src="{if file_exists("`$config->path->imgs`user_photo/`$comment.author`/medium.jpg")}{$config->url->imgs}user_photo/{$comment.author}/medium.jpg{else}/public/images/img-visual03.png{/if}" alt="{$comment.user_fio}" width="{$config->user_photo->medium->width}" height="{$config->user_photo->medium->height}" />
									</a>
								</div>
							</td>
							<td class="angle-right-bottom">
								<div>
									<span class="heading">
										<a href="#" class="name">{$comment.first_name}</a>
										<span class="date">{$comment.datetime|date_format:'%d.%m.%Y, %H:%M'}</span>
									</span>
									<p>{$comment.comment|htmlentities:0:'UTF-8'|nl2br}</p>
								</div>
							</td>
						</tr>
					{/foreach}

				</tbody>
			</table>
		</div>
	</div>
</div>


<script type="text/javascript">
	var page_title = 'Новости miss-florence.ru';
	var page_descr = '{$event.name}';
	{literal}
	$(document).ready(function() {
		VK.init({
			apiId: 2815268,
			onlyWidgets: true

		});

		VK.Widgets.Like('vk_like',{
			type: 'mini',
			pageTitle: page_title,
			pageDescription: page_descr,
			text: 'Мне нравится новость о '+page_descr,
			height: 20
		});



		(function() {
			var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
			po.src = 'https://apis.google.com/js/plusone.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
		})();



		(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/ru_RU/all.js#xfbml=1";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));

	});
</script>

{/literal}

{*
<div class="newsinn">
<h1>Новости</h1>
<h2>{$event.name}<br/>
	<span class="date">{$event.datef}</span>
</h2>
	{if $event.image!=''}
		<div class="image">
			<p><img src="{$event.image}" alt="" /></p>
		</div>
	{/if}

	{$event.text}
</div>

<br />

<div align="center">
<p>
	<input type="button"  onclick="document.location.href='/{$controller}/{$action}'" value="К списку новостей">
</p>
</div>
*}