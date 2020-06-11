<!DOCTYPE html>
<!-- saved from url=(0054)/novel/yang-xiaoluo-s-cheap-house-dad -->
<html lang="en">
{if isset($params.id)}    
    {$data=ormModel::getInstance('public','novella')->getRow("id=`$params.id`")}
{/if}
    
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      
      <meta name="yandex-verification" content="4249c15c07bfcc10" />
      
      <meta property="og:title" content="{$data.name}">
      <meta property="og:description" content="{$data.description|truncate:50}">
      <meta property="og:image:url" content="/public/novellas/{$data.image}">
      <meta name="twitter:title" content="{$data.name}">
      <meta name="twitter:description" content="{$data.description|truncate:50}">
      <meta name="twitter:images0" content="/public/novellas/{$data.image}">
      
      <title>{$meta_title|default:meta::get('title')}</title>
      <meta name="description" content="{$meta_description|default:meta::get('description')}">
      <meta name="keywords" content="{$meta_keywords|default:meta::get('keywords')}">
      
      <link rel="icon" href="/favicon.ico">
      <link rel="stylesheet" href="/site/skins/main/public/css/style.css">
      <!--[if lt IE 9]> 
      <script src="https:/oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js" defer async></script> 
      <script src="https:/oss.maxcdn.com/respond/1.4.2/respond.min.js" defer async></script> 
      <![endif]--> 
      <script src="/cms/public/js/jquery/jquery-1.10.2.min.js"></script>
   </head>

<body class="bodyRanged metaAdded linesAppend infoShowed backScaled  pace-done" id="app">
    
    <div class="pace  pace-inactive">
        <div class="pace-progress" data-progress-text="100%" data-progress="99" style="width: 100%;">
            <div class="pace-progress-inner"></div>
        </div>
        <div class="pace-activity"></div>
    </div>
    <style>
        .trs {
            opacity: 0;
            transition: all 0.6s;
        }
        h2:hover .trs, .description:hover .trs {
            opacity: 1;
        }
        
        .readContinue {
            width: 100%;
            margin-top: 20px;            
        }
    </style>

    {use file="../../header.tpl"}
    <main>
        <div class="jumbotron novel">
            <div class="container">
                <div class="media">
                    <div class="media-left cont-m hidden-xs">
                        <img class="media-object img-rounded" title="{$data.name}" src="/public/novellas/{$data.image}" style="width: 200px;"> 
                        {$first_chapter=ormModel::init('novellasModel')->getFirstChapter($data.id)}
                        <a href="/chapter/{tools_string::translit($first_chapter.name_ru)}/{$first_chapter.id}" class="readContinue btn btn-default">начать чтение</a>
                        
                        <script>
                            var cnt=localStorage.getItem('readContinue_{$data.id}');
                            if (cnt) {
                                $('.readContinue').text('продолжить чтение');
                                $('.readContinue').attr('href', cnt);
                            }
                        </script>
                    </div>
                    <div class="cont-c cont-m">
                        <h1 class="media-heading">
                            <span class="novel-name" data-content="{$data.name}" data-original-title="" title="" aria-describedby="popover899572">Ранобэ {$data.name}<small>&nbsp;/&nbsp;{$data.name_original}</small></span>
                            {if $user_data->id}
                                <a class="trs" style="
                                    font-size: 13px;
                                    color:  #988383;                                   
                                " href="#" onclick="site.translate.suggest({$params.id},'novella');return false;">предложить перевод</a>
                            {/if}                            
                            {*
                            <div class="popover fade bottom in" role="tooltip" id="popover899572" style="top: 216px; left: 610.898px; display: block;">
                                <div class="arrow" style="left: 50%;"></div>
                                <h3 class="popover-title" style="display: none;"></h3>
                                <div class="popover-content">{$data.name}</div>
                            </div>
                            *}
                        </h1>
                        <div class="description">
                            <p>{$data.description}</p>
                            {if $user_data->id}
                                <a class="trs" style="
                                    font-size: 13px;
                                    color: #988383;                                   
                                " href="#" onclick="site.translate.suggest({$params.id},'description');return false;">предложить перевод</a>
                            {/if}                        
                            
                        </div>
                        {$total_likes=$data.likes_plus|default:0+$data.likes_minus|default:0+$data.likes_neutral|default:0}
                        {if $total_likes>0}
                        <div class="progress">
                            <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="33.33" aria-valuemin="0" aria-valuemax="100" style="width: {round(($data.likes_minus/$total_likes*100),2)}%;">{$data.likes_minus} Негативных</div>
                            <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="33.33" aria-valuemin="0" aria-valuemax="100" style="width: {round(($data.likes_neutral/$total_likes*100),2)}%;">{$data.likes_neutral} Нейтральных</div>
                            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="33.3" aria-valuemin="0" aria-valuemax="100" style="width: {round(($data.likes_plus/$total_likes*100),2)-0.01}%;">{$data.likes_plus} Позитивных</div>
                        </div>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
                        
        <div class="container">
            <div class="row">
                <div class="col-lg-9 col-md-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Последние главы</h3> </div>
                        <table class="table">
                            <tbody>
                                {foreach ormModel::getInstance('chaptersModel')->getLatest($data.id) as $c}
                                
                                <tr>
                                    <td>
                                        <a href="/chapter/{tools_string::translit($c.name_ru)}/{$c.id}" class="chapter-link"> 
                                            {if trim($c.name_ru," -\r\n")}
                                                {$c.name_ru}
                                            {else}
                                        <span class="badge chapter-badge">Глава {$c.number_parsed|default:$c.number}</span> {$c.name_ru} </a>
                                            {/if}
                                        
                                    </td>
                                    <td class="text-right"> 
                                        <span class="label label-default">{$c.created|date_format:'%d.%m.%Y'}</span> 
                                    </td>
                                </tr>
                                
                                {/foreach}
                                
                            </tbody>
                        </table>
                    </div>
                    <div id="volumes-container">
                        <div>
                            <div class="alert alert-info v-cloak--block">
                                <p>Загрузка</p>
                            </div>
                            
                            <div class="wrap">
                                <h2>Тома</h2>
                                <div class="scrollbar">
                                    <div class="handle" style="transform: translateZ(0px) translateX(0px); width: 476px;">
                                        <div class="mousearea"></div>
                                    </div>
                                </div>
                                {$volumes=ormModel::getInstance('public','volumes')->getAll("novella_id=`$data.id`", "number,id")}
                                
                                
                                    <div class="frame" id="basic" style="overflow-x: auto;height: 140px;">
                                        <ul class="clearfix" style="display: flex;flex-wrap: nowrap;"{*style="transform: translateZ(0px); width: 1512px;"*}>
                                            {foreach $volumes as $v}
                                                <li style="min-width: 167px; padding: 0 5px;"
                                                    class="volume volume_{$v.id}
                                                        {if (!$params.v|default:false && $v@first) || ($params.v|default:false && $params.v|default:false==$v.id)}
                                                           active
                                                        {/if}"
                                                    onclick="site.novella.changeVolume({$v.id}); {$volume_id=$v.id}">
                                                    {if $v.number && false}
                                                        Том #{$v.number}
                                                        <br>
                                                    {/if}
                                                    <small>{$v.title_ru}</small>
                                                </li>
                                            {/foreach}
                                        </ul>
                                    </div>
                                        {*
                                    <ul class="pages">
                                        <li class="active">1</li>
                                        <li>2</li>
                                    </ul>
                                    *}
                                </div>
                                
                                {if $volumes}
                                {$volume_id=$params.v|default:$volumes[0].id}
                                
                                <div id="chaptersTable">
                                    {use file="chapters_table.tpl" data=ormModel::getInstance('chaptersModel')->getChapters($volume_id, 1) volume_id=$volume_id}
                                </div>

                            {/if}
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-heading"> Рэйтинг <span class="badge pull-right">{$total_likes}</span> </div>
                        <div class="panel-body text-center">
                            <div class="btn-group btn-group-justified">
                                <a href="/novella/index/rating/r/-1/n/{$data.id}" class="btn btn-danger">Плохо</a>
                                <a href="/novella/index/rating/r/0/n/{$data.id}" class="btn btn-warning">Норм</a>
                                <a href="/novella/index/rating/r/1/n/{$data.id}" class="btn btn-success">Отлично</a>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info text-center">
                        <h5 style="margin-top: 0; margin-bottom: 20px;">{simpleText name="auto_tr_text" editor=true}</h5>
                    </div>
                    <div class="a2a_kit a2a_kit_size_32 a2a_default_style" style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                        <a class="a2a_dd" href="https://www.addtoany.com/share"></a>
                        <a class="a2a_button_facebook"></a>
                        <a class="a2a_button_twitter"></a>
                        <a class="a2a_button_whatsapp"></a>
                        <a class="a2a_button_copy_link"></a>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading"> Информация </div>
                        <div class="panel-body">
                            <dl> <dt>Автор</dt>
                                <dd> <span class="label label-default">{$data.author}</span> </dd>
                            </dl>
                            <dl> <dt>Статус</dt>
                                <dd>
                                    {if $data.status=='finished'} 
                                        Перевод завершен
                                    {else}
                                        В процессе 
                                    {/if}
                                </dd>
                            </dl>
                        </div>
                    </div>
                            {*
                    <div class="panel panel-default">
                        <div class="panel-heading"> Статистика  </div>
                        <div class="panel-body">
                            <dl> <dt>Retranslations count</dt>
                                <dd><span class="label label-default">2 times</span></dd>
                            </dl>
                            <dl> <dt>Latest retranslation at</dt>
                                <dd><span class="label label-primary">2019-04-21 10:27:25</span></dd>
                            </dl>
                            <dl> <dt>Glossary changes till next retranslation</dt>
                                <dd>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" aria-valuenow="41.8" aria-valuemin="0" aria-valuemax="100" style="min-width: 5em; width: 41.8%;"> 121 / 289 </div>
                                    </div>
                                </dd>
                            </dl>
                            <div class="btn-group btn-group-vertical" style="width: 100%;"> <a href="/termProposition?novel_id=357" class="btn btn-primary">Check New Term Propositions</a> <a href="/termProposition/create?novel_id=357&amp;type=create" class="btn btn-success">Add Glossary Term</a> </div>
                        </div>
                    </div>
                            *}
                            
                    <div class="panel panel-default">
                        <div class="panel-heading"> В избранном 
                            <span class="badge pull-right">{ormModel::getInstance('public','favorites')->get("count(id)","novella_id=`$data.id`")}</span> 
                        </div>
                        <div class="panel-body text-center"> 
                            <a href="#" onclick="site.novella.favorite({$data.id});return false;" class="btn form-control btn-success">В избранное</a> 
                        </div>
                    </div>
                    {*
                    <div class="panel panel-default">
                        <div class="panel-heading"> Social Media </div>
                        <div class="panel-body text-center">
                            <div class="ssk-group ssk-rounded ssk-count ssk-lg">
                                <a href="/novel/yang-xiaoluo-s-cheap-house-dad#" class="ssk ssk-facebook">
                                    <div class="ssk-num">0</div>
                                </a>
                                <a href="/novel/yang-xiaoluo-s-cheap-house-dad#" class="ssk ssk-twitter"></a>
                                <a href="/novel/yang-xiaoluo-s-cheap-house-dad#" class="ssk ssk-google-plus">
                                    <div class="ssk-num">0</div>
                                </a>
                            </div>
                        </div>
                    </div>
                    *}
                </div>
            </div>
        </div>
    </main>

    {use file="../../footter.tpl"}
    <script type="text/javascript">
        (function($) {
            $(function() {
                $('.novel-name').popover({
                    trigger: 'click',
                    placement: 'bottom'
                });
            });
        })(jQuery);
    </script>
    <script async src="https://static.addtoany.com/menu/page.js"></script>
</body>

</html>