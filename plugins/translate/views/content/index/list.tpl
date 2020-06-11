<div class='top_right'>
<ul class="nav nav_menu">
    <li class="dropdown">
        <a class="dropdown-toggle" role="button" data-toggle="dropdown" data-target="#" href="../../page.html">
            <span class="title">{$languages[$lng]|default:$languages['ru']}</span></a>
        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
            {foreach from=$languages key=k item=c}
                <li><a href="/translate/index/list/lng/{$k}">{$c}</a></li>
            {/foreach}
            <li><a onclick="seEdit('language',0,'index','translate');">Добавить язык</a></li>
        </ul>
    </li>
</ul>
</div>

<div class="pull-right">
    <div class="btn-group">
        <button class="btn btn-info btn-large" onclick="seEdit('item','{$lng|default:'ru'}','index','translate');" >
            <i class="icon-plus-sign"></i>
            Добавить новый элемент
        </button>
    </div>
</div>

{$mess}
<table class="responsive table table-striped table-bordered" style="width:100%;margin-bottom:0; " id="translates">
    <thead>
    <tr>
        <th class="jv" style="width:20px">
            Код
        </th>

        <th class="jv">Перевод</th>
        
        <th class="jv" style="width:5%">Действия</th>
    </tr>
    </thead>
    <tbody>
    {foreach from=$items item=c}
        <tr>
            <td><input class="edit_items_code" value="{$c.code}" data-code="{$c.code}" /></td>
            <td><textarea class="edit_items_txt" data-code="{$c.code}">{$c.txt}</textarea></td>
            <td class='ms' style="width:5%">
                <button class="btn btn-danger btn-small" rel="tooltip" data-placement="top" data-original-title="Удалить" onclick="deleteItem('{$c.code}','{$lng|default:'ru'}'); return false;"><i class="gicon-remove icon-white"></i></button>
            </td>
        </tr>
    {/foreach}
    </tbody>
</table>
{literal}
<script>
    $(document).ready(function(){
        $(".edit_items_code").blur(function(){
            $.ajax({
                url:'/translate/index/edit',
                data:{
                    lng:'{/literal}{$lng}{literal}',
                    code:$(this).attr("data-code"),
                    new_code:$(this).val()
                },
                error:function(err){
                    alert(err.responseText)
                }
            });
        });
        $(".edit_items_txt").blur(function(){
            $.ajax({
                url:'/translate/index/edit',
                data:{
                    lng:'{/literal}{$lng}{literal}',
                    code:$(this).attr("data-code"),
                    txt:$(this).val()
                },
                error:function(err){
                    alert(err.responseText)
                }
            });
        });
    });
    function deleteItem(item,lng){
        $.ajax({
                url:'/translate/index/delete',
                data:{
                    lng:lng,
                    item: item
                },
                success: function(){
                    location.reload();
                }
            });
    };
</script>
{/literal}
{*
{include file="`$config->path->skin`views/controls/datatables.tpl" element="translates"}
*}