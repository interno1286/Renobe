<h2>Управление пользователями</h2>

<table class="table table-hover table-condensed table-bordered table-striped">
    <tr>
        <th>Ф.И.О.</th>
        <th>E-mail</th>
        <th>Роль</th>
        <th>Блокировка</th>
        <th>Аккаунт</th>
    </tr>
    
    
    {foreach $users as $u}
        <tr>
            <td>{$u.fio}</td>
            <td>{$u.email}</td>
            <td>
                <select user_id="{$u.id}" onchange="users.role.set(this,{$u.id})" class="form-control">
                    <option value="user" {if $u.user_type=='user'}selected="selected"{/if}>Пользователь</option>
                    <option value="admin" {if $u.user_type=='admin'}selected="selected"{/if}>Администратор</option>
                </select>
            </td>
            <td>
                <button onclick="users.block.toggle({$u.id});" class="btn btn-danger users_block_btn_{$u.id}">{if $u.blocked}Разблокировать{else}Заблокировать{/if}</button>
            </td>
            
            <td>
                <button class="btn btn-success" onclick="location.href='/user/admin/login/id/{$u.id}';">Перейти в аккаунт</button>
            </td>
        </tr>
    {/foreach}
</table>



{if $total_pages>1}
    <div class="pagination">
        <ul>
            {section start=1 loop=$total_pages+1 name=pages step=1}
                {if isset($params.page) && $params.page==$smarty.section.pages.index}
                    <li class="active"><a href="/{$module}/index/list/page/{$smarty.section.pages.index}">{$smarty.section.pages.index}</a></li>
                {else}
                    <li><a href="/{$module}/index/list/page/{$smarty.section.pages.index}">{$smarty.section.pages.index}</a></li>
                {/if}
            {/section}
        </ul>
    </div>
{/if}