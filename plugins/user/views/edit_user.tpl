<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <form id="edit_user_form">
                <div class="form-group">

                    <label for="exampleInputEmail1">
                        Email
                    </label>
                    <input type="email" name="db[email]" value="{$data.email|default:''}" class="form-control" id="exampleInputEmail1" />
                </div>
                
                <div class="form-group">

                    <label for="exampleInputEmail1">
                        Имя
                    </label>
                    <input type="email" name="db[fio]" value="{$data.fio|default:''}" class="form-control" id="exampleInputEmail1" />
                </div>
                
                
                <div class="form-group">

                    <label >
                        Пароль
                    </label>
                    <input type="password" name="db[password]" class="form-control"  />
                    
                </div>
                
                
                <div class="form-group">

                    <label for="exampleInputEmail1">
                        Тип
                    </label>
                    
                    <select name="db[user_type]" class="form-control">
                        
                        <option value="user" {if $data.user_type|default:''=='user'}selected="selected" {/if}>Пользователь</option>
                        <option value="admin" {if $data.user_type|default:''=='admin'}selected="selected" {/if}>Администратор</option>
                        
                    </select>
                </div>
                
            </form>
        </div>
    </div>
</div>