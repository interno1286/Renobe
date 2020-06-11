{*

Это пример файла news_block.tpl отображаемый через плагин Smarty {newsBlockLast}

Рабочий актуальный файл, должен находиться в папке со скином /skins/default/views/news_block.tpl

*}
{$add_button}

<div class="news-block">
	<h2>Наши новости</h2>
	<ul class="naw-list">
		{foreach $news as $item}
			<li>
				<div class="info"><a href="#" onclick="showNews({$item.id});return false;">
					{$item.description}
				</a></div>
				<div class="date"><span>{$item.create_date|date_format:'%d %h. %Y'}</span></div>

				{if $edit_allowed}
					<div class="btn-holder news_edit">
                        <button class="btn btn-xs btn-info" onclick="seEdit('news', {$item.id},'index','news'); return false;">редактировать</button>
                        <button class="btn btn-xs btn-danger" onclick="document.location.href='/news/index/do/act/del/id/{$item.id}';">удалить</button>
					</div>
				{/if}
			</li>
		{/foreach}
	</ul>
</div>
