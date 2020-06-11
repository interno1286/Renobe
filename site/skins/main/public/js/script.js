site = {
    login: {
        modal: ()=> {
            cms.dialog.show({
                url: '/users/index/login',
                title: 'Авторизация',
                buttons: {
                    Вход: site.login.do
                }
            });
        },
        
        do: ()=>{
            var params = {
                login: $('#login').val(),
                password: $('#pass').val()
            };
            if ($('#google_auth').val() == 'true') {
                params = {
                    google_auth: true,
                    google_auth_email: $('#google_auth_email').val()
                };
            }

            cms.http.post({
                url: '/users/index/ajaxlogin',
                params: params,
                success: ()=> {
                    cms.info.show('Вы успешно авторизованы!');
                    location.reload();
                }
            });
        },
        
        logout: ()=> {
            var auth2 = gapi.auth2.getAuthInstance();
            auth2.signOut().then(function () {
                auth2.disconnect();
            });

            location.href = '/users/index/logout';
        }
    },
    
    register: {
        modal: ()=> {
            cms.dialog.show({
                url: '/users/index/register',
                title: 'Регистрация',
                buttons: {
                    Регистрация: ()=> {
                        cms.http.post({
                            url: '/users/register/ajaxregister',
                            params: {
                                email: $('#login').val(),
                                password1: $('#pass').val(),
                                password2: $('#pass').val()
                            },
                            success: ()=> {
                                cms.info.show('Вы успешно зарегистрированы!');
                                location.reload();
                            }
                        });
                    }
                }
            });
        }
    },
    
    translate: {
        suggest: (p_id, type)=>{
            cms.dialog.show({
                url: '/novella/translate/suggest/id/'+p_id+'/t/'+type,
                title: 'Предложить перевод',
                width: '80%',
                buttons: {
                    'Отправить на рассмотрение': ()=> {
                        cms.http.post({
                            url: '/novella/translate/send',
                            params: {
                                id: p_id,
                                type: type,
                                translate: $('#user_translate').val()
                            },
                            success: (r)=> {
                                if (r.approved) {
                                    cms.info.show('Спасибо! Ваш перевод принят и опубликован.');
                                }else cms.info.show('Спасибо! Модератор рассмотрит ваш вариант перевода и примет решение о публикации.');
                                
                                cms.dialog.hide();
                            }
                        });
                    }
                }
            });
        }
    },
    
    novella: {
        changeVolume: (volume_id) => {
            $('#chaptersTable').animate({
                opacity: 0.2
            }, 400);

            $('#chaptersTable').load('/novella/chapter/loadlist/page/' + 1 + '/v/' + volume_id, () => {
                $('#chaptersTable').animate({
                    opacity: 1
                }, 200);
            });
            $('li.pages').removeClass('active');
            $('li.pages.p' + 1).addClass('active');
            $('.volume').removeClass('active');
            $('.volume_' + volume_id).addClass('active');
        },

        loadChapters: (volume_id, page, li) => {
            $('#chaptersTable').animate({
                opacity: 0.2
            }, 400);

            $('#chaptersTable').load('/novella/chapter/loadlist/page/' + page + '/v/' + volume_id, () => {
                $('#chaptersTable').animate({
                    opacity: 1
                }, 200);

            });
            $('li.pages').removeClass('active');
            $('li.pages.p' + page).addClass('active');

        },

        chapters: {
            lost: (volume_id)=> {
                var m = prompt('Какая глава утеряна?');
                
                cms.http.post({
                    url: '/novella/chapter/lost/v/'+volume_id,
                    params: {
                        message: m
                    },
                    success:()=>{
                        cms.info.show('Спасибо за сообщение');
                    }
                });
            },
            
            comments: {
                show: (chapter_id)=> {
                    cms.dialog.show({
                        url: '/novella/chapter/comments/id/'+chapter_id,
                        title: 'Комментарии'
                    });
                }
            }
            
            
        },
        
        favorite: (id)=> {
            cms.http.post({
                url: '/novella/index/favorite',
                params: {
                    id: id
                },
                success:(r)=> {
                    cms.info.show(r.message);
                }
            });
        }
    },
    
    settings: {
        
        dialog: ()=> {
            cms.dialog.show({
                url: '/users/settings/dialog',
                title: 'Настройки'
            });
        },
        
        change: (e)=> {
            
            var name = $(e.target).attr('name');
            var val = $(e.target).val();
            
            $('.settings.'+name).remove();
            $('body').append('<link class="settings '+name+'" href="/site/skins/main/public/css/settings/'+name+'/'+val+'.css" rel="stylesheet" />');
            
            cms.http.post({
                url: '/users/settings/save',
                params: {
                    name: name,
                    value: val
                },
                success: ()=> {}
            });
        }
        
    },
    
    user: {
        avatar: {
            change: ()=> {
                $('#avatarFile').click();
            }
        },
        
        data: {
            change: ()=> {
                cms.dialog.show({
                    url: '/users/profile/edit',
                    title: 'Редактор',
                    buttons: {
                        Сохранить: site.user.data.save
                    }
                });
            },
            
            save: ()=> {
                cms.http.post({
                    url: '/users/profile/save',
                    params: $('#profile_form').serializeArray()
                });
            }
        },
        
        lostPass: ()=> {
            
            cms.dialog.hide();
            
            setTimeout(()=>{
                cms.dialog.show({
                    url: '/users/index/lostpass',
                    title: 'Восстановление пароля',
                    buttons: {
                        'Выслать новый пароль': ()=> {
                            cms.http.post({
                                url: '/users/index/lostpass',
                                params: {
                                    email: $('#lost_pass_form input').val()
                                },
                                success: ()=> {
                                    cms.info.show('Инструкции по смене пароля отправлены вам на email');
                                    cms.dialog.hide();
                                }
                            })
                        }
                    }
                });
            },1000);
            
        }
    }
    
}