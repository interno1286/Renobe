
var users = {
    
    current_edit_user: null,
    
    addNew: function(){
        
        users.editDialog = cms.dialog.show('/'+cms.zend.plugin+'/index/edit', 'Редактирование', {
            'Создать': users.doAdd
        });
    },
    
    editDialog: null,
    
    editUser: function(user_id) {
        
        users.current_edit_user = user_id;
        
        users.editDialog = cms.dialog.show('/'+cms.zend.plugin+'/index/edit/id/'+user_id, (user_id ? 'Редактирование' : 'Создание'), {
            'Сохранить': users.saveUserData
        });
        
    },
    
    doAdd: function() {
        var data = $('#edit_user_form').serializeArray();
        
        showLoadingProcessLayer('Создаю');
        
        cms.http.post('/'+cms.zend.plugin+'/index/edit',data,function(){
            document.location.reload();
        });
    },
    
    saveUserData: function(){
        var data = $('#edit_user_form').serializeArray();
        
        showLoadingProcessLayer('Сохраняю');
        
        cms.http.post('/'+cms.zend.plugin+'/index/edit/id/'+users.current_edit_user,data,function(){
            document.location.reload();
        });
    },
    
    block: {
        toggle: function(userId){
            var data = {
                state: 'toggle',
                user_id: userId
            };
            
            cms.http.post('/user/admin/block',data,function(ret){
                if (ret.blocked) {
                    $('.users_block_btn_'+ret.id).text('Разблокировать');
                }else
                    $('.users_block_btn_'+ret.id).text('Заблокировать');
            });
        }
    },
    
    del: function(uid,elem){
        if (confirm('Удалить пользователя?')) {
            showLoadingProcessLayer('Удаляю...');
            cms.http.post('/'+cms.zend.plugin+'/admin/del/id/'+uid,null,function(){
                $(elem).parent().parent().fadeOut(300);
                $('.ui-widget-overlay').remove();
            });
        }
    },
    
    role: {
        set: function(sel, userID){
            
            var data = {
                user_id: userID,
                role:   $(sel).val()
            };
            
            $(sel).attr('disabled','disabled');
            
            cms.http.post('/user/admin/roleset',data,function(){
                cms.info.show('Роль изменена');
                
                $(sel).removeAttr('disabled');
            })
        }
    },
    
    register: {
        
        regModule: 'user',
        
        showForm: function(module) {
            
            users.register.regModule = module;
            
            cms.dialog.show('/'+module+'/register/ajaxform','Регистрация',{
                'Зарегистрироваться': users.register.doRegister
            });
        },
        
        
        
        doRegister: function() {
            var data = $('#register_form').serializeArray();
            
            cms.http.post('/'+users.register.regModule+'/register/ajaxregister',data,function(){
                cms.info.show(translate.item('register_success'));
                cms.dialog.hide();
                setTimeout(function(){
                    document.location.reload();
                },1500);
            },function(ret){
                cms.info.show(ret.error);
            });
        }
    },
    
    
    forgetPass: function() {
        
        
        var obj = {
            btns: {
                
            }
	 };
         
         if (translate) {
             obj.btns[translate.item('lp_send_instr')] = users.sendPass;
         }else {
             obj.btns['Выслать новый пароль'] = users.sendPass;
         }
         
         
        
	 fgp=bsAjaxDialog3('/users/index/lostpass',translate.item('password_restore'),obj.btns);

	 return false;
    },
    
    
    sendPass: function() {
	var data=$('#lost_pass_form').serializeArray();

	$.post('/users/index/lostpass',data,function(ret){
		if (ret['error']!='') {
			cms.info.show(ret['error'],4000);
		}else {
			cms.info.show('На ваш email высланы инструкции для смены пароля');
			bsDialogDestroy(fgp);
		}
	},'json');

	return false;
    }

}



function checkEmailExists(elem) {
    
    var data = {
        'email': $(elem).val()
    };
    
    $.post('/user/register/checkloginexists',data,function(ret){
        if (ret['exist']==true) {
            $('#register_button').attr('disabled','disabled');
            $('#alert_div').html('Ошибка! Пользователь с таким email уже зарегистрирован').fadeIn();
        }else {
            $('#alert_div').fadeOut();
            $('#register_button').removeAttr('disabled');
        }
    },'json');
    
}


function checkPasswords() {
    if ($('#password1').val()!='' && $('#password2').val()!='') {
        if ($('#password1').val()!=$('#password2').val()) {
            $('#register_button').attr('disabled','disabled');
            $('#alert_div').html('Ошибка! Введённые вами пароли не совпадают.').fadeIn();
        }else {
            $('#alert_div').fadeOut();
            $('#register_button').removeAttr('disabled');
        }
    }
}
