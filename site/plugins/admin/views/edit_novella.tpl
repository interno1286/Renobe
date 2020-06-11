
<form class="novella_form" onsubmit="site.novella.save();">
    {if isset($params.id)}
        {$data=ormModel::getInstance('public','novella')->getRow("id=`$params.id`")}
        {$tags=ormModel::getInstance('novellasModel')->getTags($data.id)}
        {$genres=ormModel::getInstance('novellasModel')->getGenres($data.id)}
        {$volumes=ormModel::getInstance('public','volumes')->getAll("novella_id=`$data.id`", "number,id")}
        <input type="hidden" name="id" value="{$params.id}" />
    {else}
        {$tags=[]}
        {$genres=[]}
    {/if}
    
    <label id="url_info">Ссылка на список глав</label>
    <input type="text" id="novela_edit_url" class="form-control" name="db[url]" value="{$data.url|default:''}" onkeyup="site.novella.tryGetData(this);" />

    <label id="url_info">Статус</label>
    <select name="db[status]" class="form-control">
        <option value="inprogress" {if $data.status|default:'inprogress'=='inprogress'}selected="selecrted"{/if}>в процессе</option>
        <option value="finished" {if $data.status|default:''=='finished'}selected="selecrted"{/if}>закончена</option>
    </select>

    <label>Название на русском</label>
    <input type="text" class="form-control" name="db[name]" id="novela_edit_name" value="{$data.name|default:''}"/>

    <label>Оригинальное название</label>
    <input type="text" class="form-control" name="db[name_original]" id="novela_edit_name_original" value="{$data.name_original|default:''}" />

    <label>Автор</label>
    <input type="text" class="form-control" name="db[author]" id="novela_edit_author" value="{$data.author|default:''}" />

{*
    <label>Источник</label>
    <select class="form-control" name="db[source]">
        <option value="1" {if $data.source|default:false==1}selected="selected"{/if}>lnmtl.com</option>
        <option value="2" {if $data.source|default:false==2}selected="selected"{/if}>wuxiaworld.co</option>
    </select>
*}

    <label>Описание оригинал</label>
    <textarea class="form-control" style="height: 155px;" id="novela_edit_description_original" name="db[description_original]">{$data.description_original|default:''}</textarea>
    
    <label>Описание перевод</label>
    <textarea class="form-control" style="height: 155px;" id="novela_edit_description" name="db[description]">{$data.description|default:''}</textarea>

    <div class="nntags">
        <label>Тэги</label><br/>
        <input type="text" name="tags" value="{foreach $tags as $t}{$t.name}{if !$t@last},{/if}{/foreach}" data-role="tagsinput" />
    </div>

    <div class="nngenres">
        <label>Жанры</label><br/>
        <input type="text" name="genres"  value="{foreach $genres as $g}{$g.name}{if !$g@last},{/if}{/foreach}" data-role="tagsinput" />
    </div>

    <div class="volumes">
        <label>Тома</label><br/>
        {foreach $volumes as $v}
            <div class="row">
                <div class="col">
                    <input name="volume_title[{$v.id}]" value="{$v.title_ru}" style="width: 100%">
                </div>
            </div>
        {/foreach}
    </div>

    <input type="hidden" name="image" id="image" value="{$data.image|default:''}" />
    
    <div id="img_block" style="margin: 20px 0 20px 0"></div>
</form>
    
<style>
    .suggests {
        display: flex;
        flex-wrap: wrap;
        flex-direction: row;
        justify-content: flex-start;
        padding-top: 5px;
    }
    .sug {
        display: inline;
        padding: 3px 5px;
        cursor: pointer;
        color: #2220b7;
    }
    .sug:hover, .sug.active {
        background-color: #a0c2ff;
    }
</style>
    
<script>
    
    ac = {
        keyUp: (e)=> {
            
            var container = $(e.path[2]);
            
            if (e.keyCode==38 || e.keyCode==37) {
                ac.prevResult(container);
                e.stopPropagation();
                return false;
            }
            
            if (e.keyCode==40 || e.keyCode==39) {
                ac.nextResult(container);
                e.stopPropagation();
                return false;
            }
            
            if (e.keyCode==13 && $('.sug.active',container).length>0) {
                ac.useResult(container);
                return false;
            }
            
            var source = ($(container).hasClass('nntags')) ? allTags : allGenres;
            
            var input = e.target;
            var value = ($(e.target).val()+e.key).toLowerCase();
            
            if ($('.suggests',container).length==0) {
                $(container).append("<div class='suggests'></div>");
            }
            
            $('.suggests',container).html('');
            
            var added = 0;
            
            source.forEach((data)=>{
                if (added<10) {
                    if (data.name.toLowerCase().indexOf(value)>-1) {
                        $('.suggests',container).append("<div onclick='ac.useIt(this);' class='sug'>"+data.name+"</div>");
                        added++;
                    }
                }
            });
            
        },
        
        
        prevResult: (container)=> {
            if ($('.active',container).length==0) {
                $('.sug:last',container).addClass('active');
                return false;
            }
            
            if ($('.active',container).prev('.sug').length>0) {
                var c = $('.active',container);
                var n = $('.active',container).prev('.sug');
                
                $(c).removeClass('active');
                $(n).addClass('active');
            }
            
        },
        
        nextResult: (container)=> {
            if ($('.active',container).length==0) {
                $('.sug:eq(0)',container).addClass('active');
                return false;
            }
            
            if ($('.active',container).next('.sug').length>0) {
                
                var c = $('.active',container);
                var n = $('.active',container).next('.sug');
                
                $(c).removeClass('active');
                $(n).addClass('active');
                
            }
        
            
        },
        
        useResult: (container)=> {
            
            var selected = $('.sug.active',container);
            if (selected.length>0) {
                $('input[data-role=tagsinput]',container).tagsinput('add', $(selected).text());
                $('.suggests',container).html('');
                $('input[type=text]:visible',container).val('');
            }else {
                
                var itm = $('.bootstrap-tagsinput input',container).text();
                
                $('input[data-role=tagsinput]',container).tagsinput('add', itm);
            }
        },
        
        useIt: (div)=>{
            var selected = $(div);
            var container = $(selected).parent().parent();
            
            $('input[data-role=tagsinput]',container).tagsinput('add', $(selected).text());
            $('.suggests',container).html('');
            $('input[type=text]:visible',container).val('');
            
        }
    }
    
    
    var allTags = JSON.parse(atob('{base64_encode(json_encode(ormModel::getInstance('public','tags')->getAll("1=1",'name')))}'));
    var allGenres = JSON.parse(atob('{base64_encode(json_encode(ormModel::getInstance('public','genres')->getAll("1=1",'name')))}'));
    
    $('input[data-role=tagsinput]').tagsinput();
    
    setTimeout(()=>{
        $('.nntags input[type=text]:visible')[0].onkeydown=ac.keyUp;
        $('.nngenres input[type=text]:visible')[0].onkeydown=ac.keyUp;
    }, 600);
    
    
</script>
