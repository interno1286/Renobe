<h2>Переводчики</h2>

<style>
    .t_enable {
        padding: 15px;
        background-color: #c1c1c1;
        border: 1px solid #ccc;
        color: #fff;
        border-radius: 5px;
        text-align: center;
        cursor: pointer;
    }
    
    .t_enable.active {
        background-color: #e4e490;
        color: #001;
    }
    .btn-sm {
        text-decoration: underline;
             
    }
</style>

<table class="table table-striped">
        <tr>
            <th>&nbsp;</th>
            <th style="text-align: center;">Дневной лимит</th>
            <th style="text-align: center;">Месячный лимит</th>
            <th>&nbsp;</th>
        </tr>    
    {foreach ormModel::getInstance('public','translators')->getAll("1=1",'id') as $t}
        <tr>
            <td>{$t.name}</td>
            <td style="text-align: center;"><span>{$t.day_used}&nbsp;/&nbsp;{$t.day_limit}</span><br />
                <button class="btn btn-sm" onclick="resetStat(this,'day',{$t.id});">сбросить</button>
                <button class="btn btn-sm" onclick="setLimit(this,{$t.id});">изменить</button>
            </td>
            <td style="text-align: center;"><span>{$t.month_used}&nbsp;/&nbsp;{$t.month_limit}</span>
                <br />
                <button class="btn btn-sm" onclick="resetStat(this,'month',{$t.id});">сбросить</button>
                <button class="btn btn-sm" onclick="setLimit(this,{$t.id});">изменить</button>                
            </td>
            <td>
               <div onclick="parserToggle(this, {$t.id});" class="t_enable {if $t.enabled}active{/if}">{if $t.enabled}включен{else}выключен{/if}</div>
            </td>
        </tr>
    {/foreach}
</table>


<script>
    cms.framework.bootstrap = 4;
    translator_id = false;
    
    function setLimit(btn, id) {
        translator_id = id;
        cms.dialog.show({
            url: '/admin/translators/setlimit/id/'+id,
            title: 'Изменить лимиты',
            buttons: {
                Сохранить: ()=> {
                    cms.http.post({
                        url: '/admin/translators/setlimit',
                        params: {
                            day: $('#day_limit').val(),
                            month: $('#month_limit').val(),
                            id: translator_id
                        },
                        success: ()=>{
                            location.reload();
                        }
                    });
                }
            }
        });
    }
    
    function parserToggle(btn, id) {
        cms.http.post({
            url: '/admin/translators/toggle/id/'+id,
            success: (r)=> {
                $(btn).removeClass('active');
                
                if (r.state) {
                    $(btn).addClass('active');
                    $(btn).text('включен');
                }else {
                    $(btn).text('выключен');
                }
            }
        });
    }
    
    function resetStat(btn, period,id) {
        var used = $(btn).parent().find('span').text().split('/')[0];
        var limit = $(btn).parent().find('span').text().split('/')[0];
        
        $(btn).parent().find('span').html("0&nbsp;/&nbsp;"+limit);
        
        cms.http.post({
            url: '/admin/translators/reset',
            params: {
                id: id,
                period: period
            },
            success:()=> {
                
            }
        });
    }
</script>