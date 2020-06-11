
{if isset($error)}
    <div class="alert-error alert">
        {$error}
    </div>
{/if}


<div align="center">
    Авторизация
    <form method="POST">
        <input type="text" name="login" value="{$params.login|default:''}" placeholder="Логин" /><br />
        <input type="password" name="password" placeholder="пароль" /><br />
        <button type="submit" class="btn btn-large">вход</button>
    </form>

    <a href="#" onclick="users.register.showForm();return false;">регистрация</a>
</div>