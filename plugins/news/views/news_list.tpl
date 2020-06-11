<div class="heading-top heading-top-inner">
	<h2><a href="/{$controller}/{$action}">Новости</a></h2>
	{include file="news/add_link.tpl"}
</div>
<div class="news-holder">
	<ul class="news-menu">

	{foreach from=$news item=n}
		<li>
			<div class="visual">
				<a href="/{$controller}/{$action}/act/show/id/{$n.id}"><img src="{if $n.image}{$n.image}{else}/public/images/img-visual03.jpg{/if}" alt="" width="218" height="110" /></a>
			</div>
			<div class="text">
				<div class="heading">
					<h3>
						<a href="/{$controller}/{$action}/act/show/id/{$n.id}">{$n.name|strip_tags}</a><span>({$newsTypes[$n.type]|default:'Новость'})</span>
					</h3>
					{include file="news/edit_item_link.tpl"}
				</div>
				<div class="date">
					{$n.date|date_format:'%d.%m.%Y'}
					{if $n.type=='exhibition'}
						 <strong>{$n.exhibition_place}(<a href="http://maps.yandex.ru/?text={$n.exhibition_address}" target="_blank" class="link-color">{$n.exhibition_address}</a></strong>)
					{/if}

				</div>

				<div class="heading">
					<p>{$n.text|strip_tags|truncate:300:'...':true:false}</p>

					<div class="comment-box">
						<a class="link" href="/{$controller}/{$action}/act/show/id/{$n.id}#comments">комментировать</a>
						<span class="number">{$n.cnt}</span>
						<a class="link02" href="/{$controller}/{$action}/act/show/id/{$n.id}#comments">высказалось</a>
					</div>
				</div>
			</div>
		</li>
	{/foreach}



	</ul>
</div>







{*


<h1>Новости</h1>
<p>
{include file="news/add_link.tpl"}
</p>
{foreach from=$news item=n}
	{*<img src="{$n.image}" align="left" />*}{*
	<h4>
		{$n.name|strip_tags}
		</h4>
		<p>
		{$n.description|strip_tags} <a href="/{$controller}/{$action}/act/show/id/{$n.id}">Подробнее...</a>
		<br/><span class="date">{$n.datef}</span>
		{include file="news/edit_item_link.tpl" id=$n.id}
		</p>




{/foreach}


{include file="news/pages.tpl"}

*}