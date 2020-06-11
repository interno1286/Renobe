<div class="container">
    <h1> {$user_data->email} </h1>
    <div class="row">
        <div class="col-lg-2 col-md-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Профиль</h3> 
                </div>
                <div class="panel-body">
                    {if isset($error)}
                        <div class="alert alert-success">
                            {$error}
                        </div>
                    {/if}
                    <p class="text-center"> 
                        {if !$user_data->avatar}
                        <img src="https://secure.gravatar.com/avatar/e02e90a2288b054ad92ff8ab500b102a?s=100&amp;r=pg&amp;d=identicon" alt="avatar" class="img-rounded"> 
                        {else}
                            <img style="max-width: 100%;" src="/public/avatar/{$user_data->avatar}" alt="avatar" class="img-rounded" /> 
                        {/if}
                    </p>
                    <p class="text-center"> 
                        <a href="#" onclick="site.user.avatar.change();return false;" class="btn btn-primary">Сменить</a> 
                        
                        <form id="avatarForm" action="/users/index/avatar" method="post" enctype="multipart/form-data" style="opacity:0;height:0px;">
                            <input type="file" name="image" id="avatarFile" onchange="$('#avatarForm').submit();" />
                        </form>
                    </p>
                    <dl> 
                        <dt>Логин</dt>
                        <dd>{$user_data->fio}</dd> 

                        <dt>Email</dt>
                        <dd>{$user_data->email}</dd> 
                        
                        <dt>Дата регистрации</dt>
                        <dd>{$user_data->created|date_format:'%d.%m.%Y %H:%M'}</dd> 
                        
                        <dt>Кирпичи</dt>
                        <dd>
                            <span class="label label-info">{ormModel::getInstance('userModel')->get('rating',"id=`$user_data->id`")} всего</span> 
                            <span class="label label-danger">{ormModel::getInstance('userTranslateModel')->get('sum(user_rating)',"user_id=`$user_data->id` and user_rating<0")} красных</span> 
                        </dd>
                    </dl>
                        
                        <a href="#" onclick="site.user.data.change();return false;" class="btn btn-primary">Изменить инфо</a> 
                </div>
            </div>
        </div>
        <div class="col-lg-10 col-md-10">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Любимые новеллы</h3> </div>
                <ul class="list-group">
                    {$favs=ormModel::getInstance('novellasModel')->getMyFavs()}
                    {if !$favs}
                        <li class="list-group-item"> нет </li>
                    {else}
                        {foreach $favs as $f}
                            <li class="list-group-item"><a href="/novellas/{tools_string::translit({$f.name})}/{$f.id}">{$f.name}</a></li>
                        {/foreach}
                    {/if}
                </ul>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Рэйтинг</h3> 
                </div>
                <ul class="list-group">
                    {$ratings=ormModel::getInstance('userTranslateModel')->getAll("user_id=`$user_data->id` and approved is not null","created desc limit 20")}
                    
                    {if !$ratings}
                        <li class="list-group-item">нет</li>
                    {else}
                        {foreach $ratings as $r}
                            <li class="list-group-item">
                                Перевод 
                                {if $r.paragraph_id}
                                    параграфа
                                {/if}
                                
                                {if $r.chapter_id}
                                    названия главы
                                {/if}
                                
                                {if $r.novella_id}
                                    названия новеллы
                                {/if}
                                
                                {if $r.description_id}
                                    описание новеллы
                                {/if}
                                
                                {if $r.approved}
                                    принято
                                {else}
                                    отклонено
                                {/if}
                                
                                {if $r.user_rating>0}+{/if}{$r.user_rating}
                                <br />
                                <span style="font-size: 11px;color: #999;">{$r.translate}</span>
                            </li>
                        {/foreach}
                    {/if}
                </ul>
            </div>
        </div>
    </div>
</div>