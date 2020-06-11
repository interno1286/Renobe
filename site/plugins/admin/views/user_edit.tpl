{$data=ormModel::init('userModel')->getRow("id=`$params.id`")}

<form id="userData" onclick="return false;">
    <input type="hidden" name="id" value="{$params.id}" />
    <label>Email</label>
    <input type="text" class="form-control" value="{$data.email}" readonly />

    <label>Пароль</label>
    <input type="password" class="form-control" name="password" placeholder="Новый пароль. Если ничего не указать - останется старый" />

    <label>ФИО</label>
    <input type="text" class="form-control" name="db[fio]" placeholder="" value="{$data.fio}" />
</form>