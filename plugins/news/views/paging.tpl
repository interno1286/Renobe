{if isset($total_pages) && $total_pages>1}
<div class="pageCounter">
	<div>
		{section start=1 loop=$total_pages+1 step=1 name=p}
			<a href="{$plugin_params.page_link|replace:'#':$smarty.section.p.index}" {if $current_page==$smarty.section.p.index}class="select"{/if}>{$smarty.section.p.index}</a>

		{/section}
	</div>
</div>
{/if}