{if $user_data->logged}
	<div class="alert alert-success simpleAuthForm">
            Вы успешно авторизованы!
            <br />
            
            {if !isset($redirect)}
                {$redirect='/'}
            {/if}
            Сейчас вы будете перенаправлены на {$redirect}

            <script>

                {if isset($params.nowait)}
                    location.href='{$redirect}';
                {else}
                    setTimeout(function(){
                        location.href='{$redirect}';
                    },1000);
                {/if}
            </script>
        </div>
{else}
{if isset($error)}
    <div class="alert alert-error">{$error}</div>
{/if}
<form method="post" class="simpleAuthForm" style="line-height: 90%;display: block;width: 250px;padding-left:50%;margin: 50px 0 50px -125px;">
    <label>Имя пользователя:</label><br />
    <input class="form-control" type="text" name="user" style="width: 200px;"/><br />
    
    <label>Пароль:</label><br />
   <input class="form-control" type="password" name="password"  style="width: 200px;" /><br />
    <p>
    <button class="btn btn-primary ">авторизация</button>
    </p>
</form>
{/if}
<script> 
    $(document).ready(function(){
        $.scrollTo('.simpleAuthForm', 300);
    });
</script>
