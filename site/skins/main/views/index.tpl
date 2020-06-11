<!DOCTYPE html> 
<html lang="en">
    <head>
        {use file="../../head.tpl"}
        <meta name="yandex-verification" content="4249c15c07bfcc10" />
    </head>
    <body class="bodyRanged metaAdded linesAppend infoShowed backScaled"  id="app">
        {use file="../../header.tpl"}
        <main>

            <style>
                #mlate_index_images {
                    height: 400px;
                }
            </style>
            
            <div id="mlate_index_images" class="carousel slide" data-ride="carousel">
                {$latest=ormModel::getInstance('public','novella')->getAll("","id limit 5")}
                <ol class="carousel-indicators">
                    {foreach $latest as $n}
                        <li data-target="#mlate_index_images" data-slide-to="{$n@index}" {if $n@first}class="active"{/if}></li>
                    {/foreach}
                </ol>
                <div class="carousel-inner" role="listbox">
                    {foreach $latest as $n}
                        <div class="item {if $n@first}active{/if}">
                            <div class="item-background"></div>
                            <div class="carousel-caption">
                                <div class="media">
                                    <div class="media-left cont-m hidden-xs animated slideInDown"> 
                                        <a href="/novellas/{tools_string::translit($n.name)}/{$n.id}"><img class="media-object img-rounded" title="{$n.name}" src="/public/novellas/{$n.image}" /></a>
                                    </div>
                                    <div class="cont-c cont-m animated slideInRight">
                                        <h3 class="media-heading"> <a href="/novellas/{tools_string::translit($n.name)}/{$n.id}">{$n.name}</a></h3>
                                        <blockquote>
                                            {$n.description}
                                        </blockquote>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/foreach}
                </div>
                <a class="left carousel-control" href="#mlate_index_images" role="button" data-slide="prev"> 
                    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> 
                    <span class="sr-only">Пред</span>
                </a> 

                <a class="right carousel-control" href="#mlate_index_images" role="button" data-slide="next"> 
                    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> 
                    <span class="sr-only">След</span> 
                </a> 
            </div>
            <div class="container"> 

            </div>

            <div class="container">
                <div class="row">
                    <div class="col-lg-9 col-md-9 col-sm-9">
                        <h4>Поседние добавленные главы</h4>
<style>
    .latest {
        margin: 15px 5px;
        border-bottom: 1px solid #525252;
        
        position: relative;
        display: flex;
        
        
    }
    .latest a.head {
        font-size: 18px;
        color: #dbdae8;        
        display: block;
    }
    
    .latest .badgetime {
        float: right;
        font-size: 11px;
    }
    .latest img {
        float: left;
        margin: 0 10px 10px 0;
    }
    
    .chapnum {
        margin: 5px;
        font-size: 12px;
        
    }
    
    .latest .name {
        font-size: 17px;
        display: block;
        color: #fff;
    }
</style>
                        <div class="panel panel-default panel-chapter latest_chapters">
                            
                            
                            <script>
                                function loadLast(p, a) {
                                    $('.pagination li.active').removeClass('active');
                                    $('.pagination li[p='+p+']').addClass('active');
                                    
                                    $('#latestChaps').animate({
                                        opacity: 0.3
                                    },300, ()=>{
                                        $('#latestChaps').load('/default/index/loadlatest/p/'+p,()=>{
                                            $('#latestChaps').animate({
                                                opacity: 1
                                            },300);
                                            
                                            var state = { 
                                                'page_id': {$param.p|default:1}, 'user_id': 5 
                                            };
                                            var title = document.title;
                                            var url = '/?p='+p;

                                            history.pushState(state, title, url);                                            
                                            
                                            
                                        });
                                    })
                                }
                            </script>
                            
                            <div id="latestChaps">
                                {use file="latest_chaps.tpl"}
                            </div>
                        </div>

                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3">
                        {*
                        <h4>Donations</h4>
                            <div class="panel panel-default">
                                <div class="panel-body">
                                <h5>To the next novel outside queue <small>0$ / 100$</small></h5>
                                <div class="progress">
                                <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%; min-width: 2em;"> 0% </div>
                                </div>
                                <form action="https:/www.paypal.com/cgi-bin/webscr" method="post" target="_top" style="width: 100%; text-align: center;">
                                <input type="hidden" name="cmd" value="_s-xclick"> <input type="hidden" name="hosted_button_id" value="LWTLFXXTEWWZE"> 
                                <div class="btn-group"> <button type="submit" name="submit" class="btn btn-primary" alt="PayPal – The safer, easier way to pay online.">Donate through Paypal</button> <a href="/about" class="btn btn-primary btn-info">About</a> </div>
                                <img alt="" border="0" src="https:/www.paypalobjects.com/pl_PL/i/scr/pixel.gif" width="1" height="1"> 
                                </form>
                                <hr />
                                <ul class="list-group">
                                <li class="list-group-item">No donations in last 2 weeks</li>
                                </ul>
                            </div>
                        </div>
                        *}
{*
                        <h4>Время на сервере</h4>
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <h3 class="text-center" style="margin: 0;">{$smarty.now|date_format:'%H:%M:%S'} <small>(GMT +3)</small></h3>
                            </div>
                        </div>
*}
                        <h4><a href="/news">Новости</a></h4>
                        
                        <ul class="list-group">
                            {foreach ormModel::getInstance('newsModel')->getLastNews(3) as $n}
                                <li class="list-group-item">
                                    <span class="badge">{$n.create_date|date_format:'%d.%m.%Y'}</span>
                                    <a href="/news/{tools_string::translit({$n.header})}/{$n.id}">{$n.header}</a>
                                </li>
                            {/foreach}
                            <li class="list-group-item text-center">
                                <a href="/news" class="btn btn-link btn-sm">Все новости</a>
                            </li>
                        </ul>
                            
                        <h4>Теги</h4>
                        <script src="/site/skins/main/public/js/jquery.tagcloud.js"></script>
                        <div class="panel panel-default">
                            <div class="panel-body">
                                
                                {$tags=ormModel::init('tagsModel')->getTagsForCloud()}
                                
                                <div id="tagcloud">
                                    {foreach $tags as $t}
                                        <a href="/novellas?tag={$t.id}&t={$t.name_en}" rel="{$t.cnt}">{$t.name}</a>
                                    {/foreach}
                                </div>
                                
                                <script>
                                    $("#tagcloud a").tagcloud({
                                        size: {
                                            start: 12, end: 22, unit:"px"
                                        }
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-9 col-md-9">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <a href="/novellas?orderBy=date_add&order=desc" class="btn btn-xs btn-primary pull-right">ещё</a>
                                <h4 class="panel-title">Недавно добавленные новеллы</h4>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    {foreach ormModel::getInstance('public','novella')->getAll("name is not null and name!=''","date_add desc limit 6") as $n}
                                    <div class="col-lg-6 text-center">
                                        <div class="media" style="margin-bottom: 20px;">
                                            <div class="media-left cont-m"> 
                                                <a href="/novellas/{tools_string::translit($n.name)}/{$n.id}"><img src="/public/novellas/{$n.image}" class="img-rounded" style="width: 80px;" alt="{$n.name}" /></a> 
                                            </div>
                                            <div class="cont-c cont-m">
                                                <h4 class="titlesz" style="margin-top: 0;"><a href="/novellas/{tools_string::translit($n.name)}/{$n.id}">{$n.name}</a></h4>
                                                <p style="font-size: 12px;">
                                                    <span class="label label-primary">{$n.author}</span>
                                                    <span class="label label-default"> 
                                                        <span class="glyphicon glyphicon-heart"></span> {ormModel::getInstance('public','favorites')->get("count(id)","novella_id=`$n.id`")}
                                                    </span> 
                                                    <span class="label label-default"> 
                                                        <span class="glyphicon glyphicon-calendar"></span> {$n.date_add|date_format:'%d.%m.%Y'} </span> 
                                                </p>
                                                {$total_likes=$n.likes_plus|default:0+$n.likes_minus|default:0+$n.likes_neutral|default:0}
                                                {if $total_likes>0}
                                                <div class="progress progress-sm" style="margin-bottom: 0;">
                                                    <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: {round(($n.likes_minus/$total_likes*100),2)}%;">{$n.likes_minus} негативных</div>
                                                    <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: {round(($n.likes_neutral/$total_likes*100),2)}%;">{$n.likes_neutral} нейтральных</div>
                                                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: {round(($n.likes_plus/$total_likes*100),2)}%;">{$n.likes_plus} положительных</div>
                                                </div>
                                                {/if}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {if $n@index%2==0}
                                </div>
                                <div class="row">
                                    {/if}
                                       
                                    {/foreach}
                                </div>

                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <a href="/novellas" class="btn btn-xs btn-primary pull-right">ещё</a> 
                                <h4 class="panel-title">Популярны новеллы</h4>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    {foreach ormModel::getInstance('public','novella')->getAll("1=1","likes_plus limit 4") as $n}
                                    <div class="col-lg-6">
                                        <div class="media" style="margin-bottom: 20px;">
                                            <div class="media-left"> 
                                                <a href="/novellas/{tools_string::translit($n.name)}/{$n.id}"><img src="/public/novellas/{$n.image}" class="img-rounded" style="width: 80px;" alt="{$n.name}" /></a> 
                                            </div>
                                            <div class="cont-c cont-m">
                                                <h4 class="titlesz" style="margin-top: 0;"><a href="/novellas/{tools_string::translit($n.name)}/{$n.id}">{$n.name}</a></h4>
                                                <div style="font-size: 11px !important;"> 
                                                    {$n.description|truncate:500}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {if $n@index%2==0}
                                </div>
                                <div class="row">
                                    {/if}
                                    
                                    {/foreach}
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3">
                    <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">Топ пользователей</h3>
                            </div>
                            <ul class="list-group">
                                {$users=ormModel::getInstance('usersModel')->getTopUsers()}
                                {foreach $users as $user}
                                    <li class="list-group-item">
                                        <div class="media">
                                            <div class="media-left cont-m">
                                                {if !$user.avatar}
                                                    <img style="width: 50px" src="https://secure.gravatar.com/avatar/e02e90a2288b054ad92ff8ab500b102a?s=100&amp;r=pg&amp;d=identicon" alt="avatar" class="img-rounded">
                                                {else}
                                                    <img style="width: 50px;"
                                                         src="/public/avatar/{$user.avatar}"
                                                         alt="avatar" class="img-rounded"/>
                                                {/if}
                                            </div>
                                            <div class="cont-c cont-m">
                                                <h5 class="titlesz" style="margin-bottom: 5px; margin-top: 0;">{$user.fio}</h5>
                                                <div>
                                                    <span class="label label-primary">{$user.rating} кирпичей</span>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                {/foreach}
                            </ul>
                        </div>
                        </div>
                    {*
                    <div class="col-lg-4 col-md-4">
                        
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">Top Users</h3>
                            </div>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <div class="media">
                                        <div class="media-left cont-m"> <img src="/user/avatar/748" class="img-rounded" alt="avatar" /> </div>
                                        <div class="cont-c cont-m">
                                            <h5 class="titlesz" style="margin-bottom: 5px; margin-top: 0;">ArgosYesu</h5>
                                            <div> <span class="label label-primary">511439 Points</span> <span class="label label-default">Registered at 2016-01-27</span> </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="media">
                                        <div class="media-left cont-m"> <img src="/user/avatar/1091" class="img-rounded" alt="avatar" /> </div>
                                        <div class="cont-c cont-m">
                                            <h5 class="titlesz" style="margin-bottom: 5px; margin-top: 0;">Capt. Carrot</h5>
                                            <div> <span class="label label-primary">480801 Points</span> <span class="label label-default">Registered at 2016-02-26</span> </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="media">
                                        <div class="media-left cont-m"> <img src="/user/avatar/6747" class="img-rounded" alt="avatar" /> </div>
                                        <div class="cont-c cont-m">
                                            <h5 class="titlesz" style="margin-bottom: 5px; margin-top: 0;">Ophis</h5>
                                            <div> <span class="label label-primary">324874 Points</span> <span class="label label-default">Registered at 2016-08-31</span> </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="media">
                                        <div class="media-left cont-m"> <img src="/user/avatar/1019" class="img-rounded" alt="avatar" /> </div>
                                        <div class="cont-c cont-m">
                                            <h5 class="titlesz" style="margin-bottom: 5px; margin-top: 0;">EdinDan</h5>
                                            <div> <span class="label label-primary">248995 Points</span> <span class="label label-default">Registered at 2016-02-18</span> </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="media">
                                        <div class="media-left cont-m"> <img src="/user/avatar/68" class="img-rounded" alt="avatar" /> </div>
                                        <div class="cont-c cont-m">
                                            <h5 class="titlesz" style="margin-bottom: 5px; margin-top: 0;">marijan14</h5>
                                            <div> <span class="label label-primary">231191 Points</span> <span class="label label-default">Registered at 2015-11-04</span> </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        
                        
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">Latest Comments</h3>
                            </div>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <div class="media">
                                        <div class="media-left cont-m"> <img src="/user/avatar/31977" class="img-rounded" alt="avatar" /> </div>
                                        <div class="cont-c">
                                            <h5 class="titlesz" style="margin-bottom: 5px; margin-top: 0;"> Nutcracker  <small>(Commented at Today 06:57)</small> </h5>
                                            <a href="/chapter/douluo-dalu-4-final-douluo-chapter-542"> <span class="label label-success">DD4</span> <span class="label label-default">#6</span> <span class="label label-primary"> Chapter #542 </span> </a>  
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="media">
                                        <div class="media-left cont-m"> <img src="/user/avatar/171995" class="img-rounded" alt="avatar" /> </div>
                                        <div class="cont-c">
                                            <h5 class="titlesz" style="margin-bottom: 5px; margin-top: 0;"> Ozshalev <small>(Commented at Today 06:50)</small> </h5>
                                            <a href="/chapter/against-the-gods-book-15-chapter-1554"> <span class="label label-success">ATG</span> <span class="label label-default">#15</span> <span class="label label-primary"> Chapter #1554 </span> </a>  
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="media">
                                        <div class="media-left cont-m"> <img src="/user/avatar/3655" class="img-rounded" alt="avatar" /> </div>
                                        <div class="cont-c">
                                            <h5 class="titlesz" style="margin-bottom: 5px; margin-top: 0;"> kosiu <small>(Commented at Today 06:49)</small> </h5>
                                            <a href="/chapter/against-the-gods-book-15-chapter-1554"> <span class="label label-success">ATG</span> <span class="label label-default">#15</span> <span class="label label-primary"> Chapter #1554 </span> </a>  
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="media">
                                        <div class="media-left cont-m"> <img src="/user/avatar/107408" class="img-rounded" alt="avatar" /> </div>
                                        <div class="cont-c">
                                            <h5 class="titlesz" style="margin-bottom: 5px; margin-top: 0;"> Pendragon <small>(Commented at Today 06:47)</small> </h5>
                                            <a href="/chapter/against-the-gods-book-15-chapter-1554"> <span class="label label-success">ATG</span> <span class="label label-default">#15</span> <span class="label label-primary"> Chapter #1554 </span> </a>  
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="media">
                                        <div class="media-left cont-m"> <img src="/user/avatar/107408" class="img-rounded" alt="avatar" /> </div>
                                        <div class="cont-c">
                                            <h5 class="titlesz" style="margin-bottom: 5px; margin-top: 0;"> Pendragon <small>(Commented at Today 06:47)</small> </h5>
                                            <a href="/chapter/against-the-gods-book-15-chapter-1554"> <span class="label label-success">ATG</span> <span class="label label-default">#15</span> <span class="label label-primary"> Chapter #1554 </span> </a>  
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item text-center"> <a href="/comment" class="btn btn-link btn-sm">More Comments</a> </li>
                            </ul>
                        </div>
                        
                    </div>
                    *}
                </div>
            </div>
        </main>
                    {*
        <div class="modal fade" id="chapter-display-options-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> 
                        <h4 class="modal-title">Настройки</h4>
                    </div>
                    <div class="modal-body">
                        <h3>Chat</h3>
                        <div class="row">
                            <div class="col-xs-12">
                                <label class="control-label">Locations</label> 
                                <select class="form-control chapter-options-select" data-type="chatLocation">
                                    <option value="floating" selected="selected">floating</option>
                                    <option value="hidden">hidden</option>
                                </select>
                                <p> Page refresh is required for change to chat to occur </p>
                            </div>
                        </div>
                        <h3>Colorization</h3>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-6">
                                    <label class="control-label">Font Face</label> 
                                    <select class="form-control chapter-options-select" data-type="fontFace">
                                        <option value="roboto" selected="selected">roboto</option>
                                        <option value="droidSerif">droidSerif</option>
                                    </select>
                                </div>
                                <div class="col-xs-6">
                                    <label class="control-label">Font Size</label> 
                                    <select class="form-control chapter-options-select" data-type="fontSize">
                                        <option value="automatic" selected="selected">automatic</option>
                                        <option value="normal">normal</option>
                                        <option value="small">small</option>
                                        <option value="medium">medium</option>
                                        <option value="big">big</option>
                                        <option value="ultra">ultra</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6">
                                <label class="control-label">Background Color</label> 
                                <select class="form-control chapter-options-select" data-type="backgroundColor">
                                    <option value="dark" selected="selected">dark</option>
                                    <option value="black">black</option>
                                    <option value="bright">bright</option>
                                </select>
                            </div>
                            <div class="col-xs-6">
                                <label class="control-label">Chapter Font Color</label> 
                                <select class="form-control chapter-options-select" data-type="fontColor">
                                    <option value="white" selected="selected">white</option>
                                    <option value="gray">gray</option>
                                    <option value="black">black</option>
                                    <option value="green">green</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"> <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> </div>
                </div>
            </div>
        </div>
                    *}
                    
        {use file="../../footter.tpl"}
        
        
        {use file="../../discord.tpl"}
    </body>
</html>