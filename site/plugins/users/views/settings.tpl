<h2 style="margin-top: 0px;margin-bottom: 0px;">Оформление</h2>
<style>
    #settingsRow label {
        margin-top: 22px;
    }
</style>

<div class="row" id="settingsRow">
    
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <label>Шрифт</label>
        <select class="form-control" name="font">
            <option {if $smarty.session.font|default:'roboto'=='roboto'}selected="selected"{/if} value="roboto">roboto</option>
            <option {if $smarty.session.font|default:'roboto'=='droidSerif'}selected="selected"{/if} value="droidSerif">droidSerif</option>
        </select>
        
        
        <label>Цветовая схема</label>
        <select class="form-control" name="colorSchema">
            <option {if $smarty.session.colorSchema|default:'dark'=='light'}selected="selected"{/if} value="light">Светлая</option>
            <option {if $smarty.session.colorSchema|default:'dark'=='dark'}selected="selected"{/if} value="dark">Тёмная</option>
            <option {if $smarty.session.colorSchema|default:'dark'=='black'}selected="selected"{/if} value="black">Чёрная</option>
        </select>
        
    </div>
    
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <label>Размер шрифта</label>
        <select class="form-control" name="fontSize">
            <option value="auto" {if $smarty.session.fontSize|default:'auto'=='auto'}selected="selected"{/if}>Автоматически</option>
            <option value="normal" {if $smarty.session.fontSize|default:'auto'=='normal'}selected="selected"{/if}>Нормальный</option>
            <option value="small" {if $smarty.session.fontSize|default:'auto'=='small'}selected="selected"{/if}>Маленький</option>
            <option value="medium" {if $smarty.session.fontSize|default:'auto'=='medium'}selected="selected"{/if}>Средний</option>
            <option value="big" {if $smarty.session.fontSize|default:'auto'=='big'}selected="selected"{/if}>Большой</option>
            <option value="ultra" {if $smarty.session.fontSize|default:'auto'=='ultra'}selected="selected"{/if}>Гигантский</option>
        </select>
        
        
        <label>Цвет шрифта главы</label>
        <select class="form-control" name="chapterFont">
            <option value="white" {if $smarty.session.chapterFont|default:'white'=='white'}selected="selected"{/if}>Белый</option>
            <option value="gray" {if $smarty.session.chapterFont|default:'white'=='gray'}selected="selected"{/if}>Серый</option>
            <option value="black" {if $smarty.session.chapterFont|default:'white'=='black'}selected="selected"{/if}>Чёрный</option>
            <option value="green" {if $smarty.session.chapterFont|default:'white'=='green'}selected="selected"{/if}>Зелёный</option>
        </select>
        
    </div>
</div>


<script>
    $('#settingsRow select').on('change',site.settings.change);
</script>