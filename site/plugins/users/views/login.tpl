<label>Email</label>
<input type="text" class="form-control" id="login" />
<br />
<label>Пароль</label>
<input type="password" class="form-control" id="pass" onkeyup="if (event.keyCode==13) site.login.do();"/>
<div style="text-align: right;">
    <a href="#" onclick="site.user.lostPass();">забыл пароль</a>
</div>
<input type="hidden" id="google_auth" name="google_auth">
<input type="hidden" id="google_auth_email" name="google_auth_email">

<div id="google-auth-button"></div>
<script>
    function onSuccess(googleUser) {
        $('#google_auth').val('true');
        $('#google_auth_email').val(googleUser.getBasicProfile().getEmail());
        site.login.do();
    }

    function onFailure(error) {
        console.log(error);
    }

    function renderButton() {
        gapi.signin2.render('google-auth-button', {
            'scope': 'profile email',
            'width': 240,
            'height': 50,
            'longtitle': true,
            'theme': 'dark',
            'onsuccess': onSuccess,
            'onfailure': onFailure
        });
    }
</script>

<script src="https://apis.google.com/js/platform.js?onload=renderButton" async defer></script>
