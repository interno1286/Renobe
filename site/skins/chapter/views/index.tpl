<!DOCTYPE html>
<html lang="en">
{$data=ormModel::getInstance('chaptersModel')->getData($params.id)}

{if $data}
    {$pars=ormModel::getInstance('public','paragraph')->getAll("chapter_id=`$data.id`"," index ")}
{else}
    {$pars=[]}
{/if}
<head>
    {use file="../../head.tpl"}
    <meta name="yandex-verification" content="4249c15c07bfcc10" />
    <script src="/cms/public/js/jquery/jquery-1.10.2.min.js"></script>
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
        {if $data}
        <div id="chapter-container">
            <div class="jumbotron chapter-head">
                <div class="container">
                    <div class="dashhead">
                        <div class="dashhead-titles">
                            <h6 class="dashhead-subtitle"> 
                                <a title="{$data.novella_name}" href="/novellas/{tools_string::translit($data.novella_name)}/{$data.novella_id}">{$data.novella_name}</a>{if $data.volume_number} :: Том #{$data.volume_number}{/if}
                            </h6>
                            <h1 class="dashhead-title"> 
                                <span class="chapter-title" data-content="{$data.name_ru}" data-original-title="" title="">{* Глава {$data.number}: *}{$data.name_ru} </span> 
                            </h1> 
                            {if $user_data->id}
                                <div><a href="#" onclick="site.translate.suggest({$params.id},'chapter');return false;">предложить перевод</a></div>
                            {/if}
                            
                            {if $user_data->role=='admin'}
                                <div><a href="#" onclick="siteAdmin.chapter.retranslate({$params.id},'chapter');return false;">перевести всю главу заново</a></div>
                                <script src="/site/skins/admin/public/js/retranslate.js"></script>
                            {/if}
                        </div>
                        <div class="dashhead-toolbar hidden-xs">
                            {*
                            <div class="dashhead-toolbar-item">
                                <div class="ssk-group">
                                    <a href="https://lnmtl.com/chapter/this-is-definitely-not-dragon-ball-chapter-129#" class="ssk ssk-icon ssk-facebook"></a>
                                    <a href="https://lnmtl.com/chapter/this-is-definitely-not-dragon-ball-chapter-129#" class="ssk ssk-icon ssk-twitter"></a>
                                    <a href="https://lnmtl.com/chapter/this-is-definitely-not-dragon-ball-chapter-129#" class="ssk ssk-icon ssk-google-plus"></a>
                                </div>
                            </div> 
                            *}
                            <span class="dashhead-toolbar-divider hidden-xs"></span>
                            <div class="btn-group btn-group-sm dashhead-toolbar-item">
                                {*
                                <button type="button" class="btn btn-warning" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="You can report only if you are logged in and have over 300 positive points"> Report </button>
                                *}
                                <button class="btn btn-primary js-toggle-translated" onclick="$('.ru').toggle();">RU</button>
                                <button class="btn btn-default js-toggle-original" onclick="$('.cn').toggle();">CN</button>
                                <button class="btn btn-default js-toggle-original" onclick="$('.en').toggle();">EN</button>
                            </div>
                        </div>
                        <div class="dashhead-toolbar visible-xs">
                            <hr>
                            {*
                            <div class="dashhead-toolbar-item text-center">
                                <div class="ssk-group ssk-sm" style="margin-bottom: 10px;">
                                    <a href="https://lnmtl.com/chapter/this-is-definitely-not-dragon-ball-chapter-129#" class="ssk ssk-icon ssk-facebook"></a>
                                    <a href="https://lnmtl.com/chapter/this-is-definitely-not-dragon-ball-chapter-129#" class="ssk ssk-icon ssk-twitter"></a>
                                    <a href="https://lnmtl.com/chapter/this-is-definitely-not-dragon-ball-chapter-129#" class="ssk ssk-icon ssk-google-plus"></a>
                                    <a href="https://lnmtl.com/chapter/this-is-definitely-not-dragon-ball-chapter-129#" class="ssk ssk-icon ssk-pinterest"></a>
                                    <a href="https://lnmtl.com/chapter/this-is-definitely-not-dragon-ball-chapter-129#" class="ssk ssk-icon ssk-linkedin"></a>
                                </div>
                            </div>
                            *}
                            <div class="btn-group btn-group-vertical btn-group-xs dashhead-toolbar-item">
                                <button class="btn btn-default btn-lg" onclick="site.novella.chapters.comments.show({$data.id});"> 
                                    <span class="glyphicon glyphicon-comment"></span> Комментарии <span class="badge">{ormModel::getInstance('commentsModel')->getCommentsTotal("chapter`$data.id`")}</span> 
                                </button>
                                {*
                                <button type="button" class="btn btn-warning" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="You can report only if you are logged in and have over 300 positive points"> Report </button>
                                *}
                                <button class="btn btn-primary js-toggle-translated" onclick="$('.ru').toggle();">Русский</button>
                                <button class="btn btn-default js-toggle-original" onclick="$('.cn').toggle();">Китайский</button>
                                <button class="btn btn-default js-toggle-original" onclick="$('.en').toggle();">Английский</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div>
                    <div id="topNav">
                        <nav>
                            <ul class="pager">
                            {if $data.prev_chapter}
                                {$prev_chapter=ormModel::getInstance('chaptersModel')->getRow("id=`$data.prev_chapter`")}
                                    {if $prev_chapter}
                                    <li class="previous"> 
                                        <a href="/chapter/{tools_string::translit($prev_chapter.name_ru)}/{$prev_chapter.id}">
                                            <span class="glyphicon glyphicon-backward"></span> 
                                            <span class="hidden-xs">Предыдущая</span>
                                        </a> 
                                    </li>
                                    {/if}
                            {/if}
                                <li>
                                    <a href="/novellas/{tools_string::translit($data.novella_name)}/{$data.novella_id}"> <span class="glyphicon glyphicon-list"></span> 
                                        <span class="hidden-xs">Содержание</span> 
                                    </a>
                                </li>
                                {if $data.next_chapter}
                                    {$next_chapter=ormModel::getInstance('chaptersModel')->getRow("id=`$data.next_chapter`")}
                                    {if $next_chapter}
                                    <li class="next "> 

                                        <a href="/chapter/{tools_string::translit($next_chapter.name_ru)}/{$next_chapter.id}"><span class="hidden-xs">Следующая</span> 
                                            <span class="glyphicon glyphicon-forward"></span>
                                        </a> 
                                    </li>
                                    {/if}
                                {/if}
                            </ul>
                        </nav>
                    </div>
                    <style>
                        .hyphenate div.en, 
                        .hyphenate div.cn {
                            
                            display: none;
                        }
                        
                        .hyphenate div.cn {
                            color: #91cc82;
                        }
                        
                        .hyphenate div.en {
                            color: #c1ff28;
                        }
                        
                        .ru, .cn, .en {
                            padding: 5px;
                        }
                        
                        .ru {
                            position: relative;
                            
                            border: 1px solid transparent;
                        }
                        
                        .translateit {
                            display: block;
                            position: absolute;
                            width: auto;
                            height: auto;
                            background-color: #000;
                            border-right: 1px solid #4e4c4c;
                            border-left: 1px solid #4e4c4c;
                            border-bottom: 1px solid #4e4c4c;
                            bottom: -31px;
                            right: -1px;
                            padding: 5px;
                            border-radius: 0;
                            z-index: 4;
                            opacity: 0;
                            font-size: 14px;
                            cursor: pointer;
                            transition: 0.6s ease-out;                            
                        }
                        
                        .translateit:hover {
                            opacity: 1;
                        }
                        
                        {if $user_data->id}
                        div.ru:hover {
                            border: 1px solid #4e4c4c;
                            background-color: #000;                            
                        }
                        
                        div.ru:hover .translateit {
                            opacity: 0.8;
                        }
                        {/if}
                    </style>
                    <div class="chapter-body hyphenate">
                        {foreach $pars as $p}
                            <p style="margin-bottom: 25px;" >
                                <div class="cn">{$p.text_original}</div>
                                <div class="en">{$p.text_en}</div>
                                <div class="ru">
                                    {$p.text_ru}
                                    {if $user_data->id}
                                        <div class="translateit" onclick="site.translate.suggest({$p.id}, 'paragraph');">Предложить свой перевод</div>
                                    {/if}
                                </div>
                            </p>
                        {/foreach}
                       
                    </div>
                    <nav>
                            <ul class="pager">
                                {if isset($prev_chapter)}
                                <li class="previous"> 
                                    <a href="/chapter/{tools_string::translit($prev_chapter.name_ru)}/{$prev_chapter.id}">
                                        <span class="glyphicon glyphicon-backward"></span> 
                                        <span class="hidden-xs">Предыдущая</span>
                                    </a> 
                                </li>
                                {/if}
                                <li>
                                    <a href="/novellas/{tools_string::translit($data.novella_name)}/{$data.novella_id}"> <span class="glyphicon glyphicon-list"></span> 
                                        <span class="hidden-xs">Содержание</span> 
                                    </a>
                                </li>
                                {if isset($next_chapter)}
                                <li class="next "> 
                                    <a href="/chapter/{tools_string::translit($next_chapter.name_ru)}/{$next_chapter.id}"><span class="hidden-xs">Следующая</span> 
                                        <span class="glyphicon glyphicon-forward"></span>
                                    </a> 
                                </li>
                                {/if}
                            </ul>
                        
                    </nav>
                     {*
                    <div class="alert alert-info">
                        <button class="btn btn-primary active btn-xs"> <span class="glyphicon glyphicon-comment"></span> <span class="hidden-xs hidden-sm">Comments</span> <span class="badge">0</span> </button> To display comments and comment, click at the button </div>
                    <div class="modal fade" id="comments-modal" tabindex="-1" role="dialog">
                        
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                    <h4 class="modal-title"> Comments for <span class="chapter-title" data-content="章 第二次许愿" data-original-title="" title=""> Chapter #129: Chapter's second wishing </span> </h4> </div>
                                <div class="modal-body">
                                    <div>
                                        <div class="alert alert-info v-cloak--block">
                                            <p>Loading</p>
                                        </div>
                                        <div class="well well-lg v-cloak--hidden">
                                            <p class="lead">No comments at the moment!</p>
                                        </div>
                                    </div>
                                    <p class="lead">Login to post comment</p>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    *}
                </div>
                {include file='../../../plugins/novella/views/chapter_comments.tpl'}
            </div>
        </div>
        {else}
        <div id="chapter-container">
            <div class="jumbotron chapter-head">
                <div class="container">
                    <div class="dashhead">
                        {header("HTTP/1.0 404 Not Found")}
                        Глава не найдена    
                    </div>
                </div>
            </div>
        </div>
        {/if}
    </main>
    
    {use file="../../footter.tpl"}
   
    <script>
        localStorage.setItem('readContinue_{$data.novella_id}', location.href);
        
        
        if ($('.ru:visible').length==0) {
            $('.cn, .en').toggle();
        }
    </script>
</body>

</html>