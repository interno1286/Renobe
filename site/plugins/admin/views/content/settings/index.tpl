<style>
    .expandable {
        display: none;
        margin: 15px 0;
        border-bottom: 1px solid #bbb;
        padding-bottom: 15px;
    }
</style>

<h4>Настройки</h4>

<label for='user_tr_allow'>
    <input type='checkbox'{if settings::getVal("user_translate")=='1'}checked="checked"{/if} id='user_tr_allow' name='settings[user_translate]' /> Пользователям разрешен перевод
</label>
<br />

<h5><a href="#" style="text-decoration: underline;" onclick="$('#transet').slideToggle();return false;">Настройки переводчика JS</a></h5>

<div class="expandable" id="transet">
    <h5>Все</h5>
    
    <div class="row">
        <div class="col-4">
            <label>Сколько переводить<br />названий глав за раз</label>
            <input type='text' name='settings[chap_tr_count]' class="form-control" value="{settings::getVal('chap_tr_count')}" />
        </div>
        
        <div class="col-4">
            <label>Количество глав, которые будет переводить<br />парсер и переходить к следующей новэлле</label>
                <input type='text' name='settings[chapters_translate_count]' class="form-control" value="{settings::getVal('chapters_translate_count')}" />
        </div>
        <div class="col-4">
            <label>Пауза для перехода<br />к след. блоку перевода (сек)</label>
                <input type='text' name='settings[next_pause_sec]' class="form-control" value="{settings::getVal('next_pause_sec')}" />
            
        </div>
    </div>
        
    <h5>Deepl</h5>
    
    <div class="row">
        <div class="col-4">
            <label>Сколько работать парсеру<br />перед паузой (минут)</label>
            <input type='text' name='settings[work_time]' class="form-control" value="{settings::getVal('work_time')}" />
        </div>
        <div class="col-4">
            <label>Сколько будет длится<br />пауза (минут)</label>
            <input type='text' name='settings[pause_time]' class="form-control" value="{settings::getVal('pause_time')}"/>
        </div>
        
        <div class="col-4">
            <label>Максимальное количество символов<br />для перевода за раз через DEEPL</label>
                <input type='text' name='settings[deepl_max_sym]' class="form-control" value="{settings::getVal('deepl_max_sym')}" />
            
        </div>
    </div>
</div>


<h5><a href="#" style="text-decoration: underline;" onclick="$('#sysset').slideToggle();return false;">Настройки системы</a></h5>

<div class="expandable" id="sysset">

    <div class="row">
        <div class="col-6">
            <label>Переведенный процент параграфов, после которого<br />глава будет считаться полностью перведенной</label>
                <input placeholder="0-100" type='text' name='settings[chapters_translate_percent]' class="form-control" value="{settings::getVal('chapters_translate_percent')}" />

        </div>
        <div class="col-6">
            <label>E-mail<br />менеджера</label>
                <input type='text' name='settings[manager_email]' class="form-control" value="{settings::getVal('manager_email')}"/>
        </div>
    </div>
</div>
{*
<label>Yandex Translate API Key</label>
    <input type='text' name='settings[yandex_tr_api]' class="form-control" value="{settings::getVal('yandex_tr_api')}" />
*}

<h5><a href="#" style="text-decoration: underline;" onclick="$('#s2s').slideToggle();return false;">Настройки переводчиков API</a></h5>
<div class="expandable" id="s2s">

    <div class="row">
        <div class="col-6">
            <label>Yandex Cloud Translate Token</label>
                <input type='text' name='settings[yandex_cloud_tr_token]' class="form-control" value="{settings::getVal('yandex_cloud_tr_token')}" />
        </div>
        <div class="col-6">
            <label>Yandex Cloud Translate Folder ID</label>
                <input type='text' name='settings[yandex_cloud_folder]' class="form-control" value="{settings::getVal('yandex_cloud_folder')}" />
        </div>
    </div>
<br/>
            <label>DeepL key</label>
                <input type='text' name='settings[deepl_key]' class="form-control" value="{settings::getVal('deepl_key')}" />
</div>
<br />                
{*
<label>Пауза при достижении лимита переводчиком (минут)</label>
    <input type='text' name='settings[parser_sleep]' class="form-control" value="{settings::getVal('parser_sleep')}" />
<br /><br />
*}



<button class="btn btn-primary" onclick="saveSettings();">Сохранить</button>
<br /><br /><br />

<script>
    function saveSettings() {
        
        var out = {
        };
        
        $('input').each((idx,el)=>{
            
            var tn = $(el)[0].type;
            if (tn=='checkbox') {
                if ($(el).prop('checked')) {
                    out[$(el).attr('name')] = 1;
                }else 
                    out[$(el).attr('name')] = 0;
            }else {
                
                out[$(el).attr('name')] = $(el).val();
                
            }
        });
        
        
        cms.http.post({
            url: '/admin/settings/save',
            params: out,
            success: ()=>{
                cms.info.show('Сохранено');
            }
        });
    }
    
</script>

