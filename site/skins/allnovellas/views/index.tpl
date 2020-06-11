<!DOCTYPE html>
<html lang="en">

    <head>
        {use file="../../head.tpl"}
        <meta name="yandex-verification" content="4249c15c07bfcc10" />
    </head>

    <body class="bodyRanged metaAdded linesAppend infoShowed backScaled  pace-done" id="app">
        <div class="pace  pace-inactive">
            <div class="pace-progress" data-progress-text="100%" data-progress="99" style="width: 100%;">
                <div class="pace-progress-inner"></div>
            </div>
            <div class="pace-activity"></div>
        </div>
        
        {use file="../../header.tpl"}
        <main>
            <div class="container">
                <div class="row">
                    <div class="col-md-10">
                        <h1>Все новеллы</h1>
                        <div class="btn-toolbar">
                            <div class="btn-group btn-group-sm">
                                <a href="/novellas?tag={$params.tag|default:''}&genre={$params.genre|default:''}&orderBy=favorites&order={$params.order|default:'desc'}&filter={$params.filter|default:'all'}"
                                   class="btn btn-default{if $params.orderBy|default:'favorites'=='favorites'} active{/if}">Рейтинг</a>
                                <a href="/novellas?tag={$params.tag|default:''}&genre={$params.genre|default:''}&orderBy=name&order={$params.order|default:'desc'}&filter={$params.filter|default:'all'}"
                                   class="btn btn-default {if $params.orderBy|default:'favorites'=='name'} active{/if}">Название</a>
                                <a href="/novellas?tag={$params.tag|default:''}&genre={$params.genre|default:''}&orderBy=date_add&order={$params.order|default:'desc'}&filter={$params.filter|default:'all'}"
                                   class="btn btn-default {if $params.orderBy|default:'favorites'=='date_add'} active{/if}">Дата</a>
                            </div>
                            <div class="btn-group btn-group-sm">
                                <a href="/novellas?tag={$params.tag|default:''}&genre={$params.genre|default:''}&orderBy={$params.orderBy|default:'favorites'}&order=asc&filter={$params.filter|default:'all'}"
                                   class="btn btn-default {if $params.order|default:'desc'=='asc'} active{/if}">Вверх</a>
                                <a href="/novellas?tag={$params.tag|default:''}&genre={$params.genre|default:''}&orderBy={$params.orderBy|default:'favorites'}&order=desc&filter={$params.filter|default:'all'}"
                                   class="btn btn-default {if $params.order|default:'desc'=='desc'} active{/if}">Вниз</a>
                            </div>
                            <div class="btn-group btn-group-sm">
                                <a href="/novellas?tag={$params.tag|default:''}&genre={$params.genre|default:''}&orderBy={$params.orderBy|default:'favorites'}&order={$params.order|default:'desc'}&filter=all"
                                   class="btn btn-default {if $params.filter|default:'all'=='all'} active{/if}">Все</a>
                                <a href="/novellas?tag={$params.tag|default:''}&genre={$params.genre|default:''}&orderBy={$params.orderBy|default:'favorites'}&order={$params.order|default:'desc'}&filter=inprogress"
                                   class="btn btn-default {if $params.filter|default:'all'=='inprogress'} active{/if}">В
                                    процессе</a>
                                <a href="/novellas?tag={$params.tag|default:''}&genre={$params.genre|default:''}&orderBy={$params.orderBy|default:'favorites'}&order={$params.order|default:'desc'}&filter=finished"
                                   class="btn btn-default {if $params.filter|default:'all'=='finished'} active{/if}">Перевод
                                    завершен</a>
                            </div>
                        </div>
                        <hr>

                        {foreach $data as $d}
                            <div class="media" style="margin-bottom: 30px;">
                                <div class="media-left">
                                    <a href="/novellas/{tools_string::translit($d.name)}/{$d.id}"><img
                                                src="/public/novellas/{$d.image}" class="img-rounded"
                                                style="width: 140px;" alt="{$d.name}"></a>
                                </div>
                                <div class="cont-c">
                                    <h4 class="titlesz" style="margin-top: 0;"><a
                                                href="/novellas/{tools_string::translit($d.name)}/{$d.id}">{$d.name}</a>
                                    </h4>
                                    <p><span class="label label-primary">{$d.author}</span>
                                        <span class="label label-default">
                                <span class="glyphicon glyphicon-heart"></span> 0
                            </span>
                                        <span class="label label-default">
                                <span class="glyphicon glyphicon-calendar"></span> {$d.date_add|date_format:'%d.%m.%Y'}
                            </span>
                                        <span class="label label-default">
                                <span class="glyphicon glyphicon-book"></span>
                                    {ormModel::getInstance('chaptersModel')->getChaptersCountByNovellaId($d.id)}
                            </span>
                                        <span class="label label-default">
                                <span class="glyphicon glyphicon-eye-open"></span>
                                    {$view_data=ormModel::getInstance('public', 'novella_views')->getRow("novella_id=`$d.id`")}
                                            {$view_data.count|default:0}
                            </span>
                                    </p>

                                    <div class="progress">
                                        {$total_likes=$d.likes_plus|default:0+$d.likes_minus|default:0+$d.likes_neutral|default:0}
                                        <div class="progress-bar progress-bar-danger" role="progressbar"
                                             aria-valuenow="33.33" aria-valuemin="0" aria-valuemax="100"
                                             style="width: {if $d.likes_minus>0}{round(($d.likes_minus|default:0/$total_likes*100),2)}{else}0{/if}%;">{$d.likes_minus|default:0}
                                            Негативных
                                        </div>
                                        <div class="progress-bar progress-bar-warning" role="progressbar"
                                             aria-valuenow="33.33" aria-valuemin="0" aria-valuemax="100"
                                             style="width: {if $d.likes_neutral>0}{round(($d.likes_neutral|default:0/$total_likes*100),2)}{else}0{/if}%;">{$d.likes_neutral|default:0}
                                            Нейтральных
                                        </div>
                                        <div class="progress-bar progress-bar-success" role="progressbar"
                                             aria-valuenow="33.3" aria-valuemin="0" aria-valuemax="100"
                                             style="width: {if $d.likes_plus>0}{round(($d.likes_plus|default:0/$total_likes*100),2)-0.01}{else}0{/if}%;">{$d.likes_plus|default:0}
                                            Позитивных
                                        </div>

                                    </div>
                                    <div style="font-size: 11px !important;">
                                        {$d.description}
                                    </div>
                                    {if $d.genres}
                                        <hr>
                                        <ul class="list-inline text-center" style="font-size: 10px;">
                                            {foreach $d.genres as $g}
                                                <li><a href="/novellas?genre={$g.id}&gn={$g.name}">{$g.name}</a></li>
                                            {/foreach}
                                        </ul>
                                    {/if}

                                    {if $d.tags}
                                        <hr>
                                        <ul class="list-inline text-center" style="font-size: 10px;">
                                            {foreach $d.tags as $t}
                                                <li><a href="/novellas?tag={$t.id}&t={$t.name}">{$t.name}</a></li>
                                            {/foreach}
                                        </ul>
                                    {/if}
                                </div>
                            </div>
                        {/foreach}

                        {if $total_pages>1}
                            <nav class="text-center">
                                <ul class="pagination">
                                    {if $params.page|default:1==1}
                                        <li class="disabled"><span>«</span></li>
                                    {else}
                                        <li>
                                            <a href="/novellas?tag={$params.tag|default:''}&genre={$params.genre|default:''}&orderBy={$params.orderBy|default:'favorites'}&order={$params.order|default:'desc'}&filter={$params.filter|default:''}&page={$params.page-1}"
                                               rel="next">«</a></li>
                                    {/if}

                                    {section start=1 loop=$total_pages name=p step=1}
                                        {if $smarty.section.p.index==$params.page}
                                            <li class="active"><span>{$smarty.section.p.index}</span></li>
                                        {else}
                                            <li>
                                                <a href="/novellas?tag={$params.tag|default:''}&genre={$params.genre|default:''}&orderBy={$params.orderBy|default:'favorites'}&order={$params.order|default:'desc'}&filter={$params.filter|default:''}&page={$smarty.section.p.index}">{$smarty.section.p.index}</a>
                                            </li>
                                        {/if}
                                    {/section}

                                    {if $data}
                                        <li>
                                            <a href="/novellas?tag={$params.tag|default:''}&genre={$params.genre|default:''}&orderBy={$params.orderBy|default:'favorites'}&order={$params.order|default:'desc'}&filter={$params.filter|default:''}&page={$params.page+1}"
                                               rel="next">»</a></li>
                                    {/if}
                                </ul>
                            </nav>
                        {/if}
                    </div>

                    <div class="col-md-2">
                        {$tags=ormModel::init('tagsModel')->getTagsList()}

                        {foreach $tags as $tag}
                            <p>
                                <span data-tag-id="{$tag.id}" data-action="plus" class="filter-tag glyphicon glyphicon-plus" style="cursor:pointer;"></span>
                                <span data-tag-id="{$tag.id}" data-action="minus" class="filter-tag glyphicon glyphicon-minus" style="cursor:pointer;"></span>
                                {$tag.name}
                            </p>
                        {/foreach}
                    </div>
                </div>
            </div>
        </main>

        {use file="../../footter.tpl"}
        <script>
            $(function () {
                hideCheckedTags();

                $('.filter-tag').click(function () {
                    let tag_id = $(this).data('tag-id');
                    let action = $(this).data('action');

                    let tag_query_string = (action === 'plus') ? 'usedTags[]' : 'unUsedTags[]';
                    tag_query_string += '=' + tag_id + '&';
                    location.href = '/novellas?' + tag_query_string + getFilteredTagQuery(action, tag_id);
                });
            });

            function getFilteredTagQuery(action, tag_id) {
                let params = window.location.search.substr(1);
                let splitted_params = params.split('&');

                let search_param = (action === 'plus') ? 'unUsedTags[]=' + tag_id : 'usedTags[]=' + tag_id;
                if (splitted_params.includes(search_param)) {
                    splitted_params = splitted_params.filter(e => e !== search_param).join('&');
                } else {
                    splitted_params = undefined;
                }

                return typeof splitted_params !== 'undefined' ? splitted_params : params;
            }

            function hideCheckedTags() {
                let url = new URL(window.location.href);
                let usedOptions = url.searchParams.getAll("usedTags[]");
                let unUsedOptions = url.searchParams.getAll("unUsedTags[]");
                usedOptions.forEach(function (element) {
                    let span = $("span[data-tag-id="+element+"][data-action='plus']");
                    if (span) span.css('display', 'none');
                });
                unUsedOptions.forEach(function (element) {
                    let span = $("span[data-tag-id="+element+"][data-action='minus']");
                    if (span) span.css('display', 'none');
                });
            }
        </script>
    </body>

</html>