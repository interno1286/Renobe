<form id="profile_form" onsubmit="site.user.data.save();return false;">
    <label>Email</label>
    <input type="text" class="form-control" name="email" value="{$user_data->email}" />
    <br />
    <label>Ник</label>
    <input type="text" class="form-control" name="fio" value="{$user_data->fio}" />
    <br />
    <label>Новый пароль</label>
    <input type="password" class="form-control" name="password" placeholder="Если ничего не указать - останется старый" value="" />

</form>