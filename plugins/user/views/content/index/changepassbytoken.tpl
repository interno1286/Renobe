{if $token_data}

        {if isset($new_pass)}

                <h2 align="center">Пароль успешно изменён и отправлен вам на e-mail</h2>

        {else}
                <div align="center">
                        <h2>Для смены пароля, введите код с картинки</h2>
                        <form method="post" id="chpf">
                                <span id="captcha"></span>
                                <img style="cursor: pointer" title="обновить картинку" src="/plugins/user/public/images/reload.png" onclick="$('#captcha').load('/user/index/captcha');" /><br />
                                <br />
                                <input type="text" name="captcha[input]" placeholder="код" value="" />
                                <br /><br />
                                <button class="btn btn-inverse" onclick="$('#chpf').submit();">сменить пароль</button>
                        </form>

                        <script>
                        $(document).ready(function(){
                                $('#captcha').load('/user/index/captcha');
                        });
                        </script>
                </div>
        {/if}
{else}
        <h2>Ссылка устарела :(</h2>
{/if}

