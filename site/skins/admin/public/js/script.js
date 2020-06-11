cms.framework.bootstrap = 4;

site = {
    novella: {
        
        tryGetData: ()=> {
            if (site.novella.gdto) clearTimeout(site.novella.gdto);
            
            site.novella.gdto = setTimeout(site.novella.getData,800);
        },
        
        getData: ()=> {
            $('#url_info').text('Ссылка на список глав (обновление информации...)');
            
            $('.novella_form input, .novella_form textarea').attr('disabled','disabled');
            
            cms.http.post({
                url: '/admin/novella/getinfo',
                params: {
                    url: $('#novela_edit_url').val()
                },
                success: (ret)=> {
                    $('#url_info').text('Ссылка на список глав');
                    
                    if ($('#novela_edit_name').val()=='')
                        $('#novela_edit_name').val(ret.info.name_ru);
                    
                    if ($('#novela_edit_name_original').val()=='')
                        $('#novela_edit_name_original').val(ret.info.name_original);
                    
                    if ($('#novela_edit_author').val()=='')
                        $('#novela_edit_author').val(ret.info.author);
                    
                    if ($('#novela_edit_description').val()=='')
                        $('#novela_edit_description').val(ret.info.description_ru);
                    
                    if ($('#novela_edit_description_original').val()=='')
                        $('#novela_edit_description_original').val(ret.info.description_original);
                    
                    
                    if (ret.info.image) {
                        $('#img_block').html("<img src='"+ret.info.image+"' />");
                        $('#image').val(ret.info.image);
                        
                    }
                    
                    $('.novella_form input, .novella_form textarea').removeAttr('disabled');
                },
                error: ()=> {
                    $('#url_info').text('Ссылка на список глав. (ошибка обновления)');
                    
                    $('.novella_form input, .novella_form textarea').removeAttr('disabled');
                }
            });
        },
        
        add: ()=> {
            cms.dialog.show({
                url: '/admin/novella/edit',
                title: 'Новая новелла',
                width: '70%',
                buttons: {
                    Сохранить: ()=>{
                        showLoadingProcessLayer('Сохраняю');
                        cms.http.post({
                            url: '/admin/novella/save',
                            params: $('form.novella_form').serializeArray(),
                            error: (r)=>{
                                cms.info.show(r.error);
                                hideLoadingProcessLayer();
                            }
                        });
                    }
                }
            });
        },
        
        edit: (id)=>{
            cms.dialog.show({
                url: '/admin/novella/edit/id/'+id,
                title: 'Редактор',
                width: '70%',
                buttons: {
                    Сохранить: ()=>{
                        showLoadingProcessLayer('Сохраняю');
                        cms.http.post({
                            url: '/admin/novella/save',
                            params: $('form.novella_form').serializeArray()
                        });
                    }
                }
            });
        },
        
        del: (id, btn)=> {
            if (confirm('Точно?')) {            
                $(btn).parent().parent().fadeOut(300,()=>{
                    $(this).remove();
                });
                
                cms.http.post({
                    url: '/admin/novella/del/id/'+id,
                    success: ()=>{
                        
                    }
                });
             
            }
        },
        
        translate: {
            currentRow: null,
            
            show: (id, row)=> {
                
                this.currentRow = row;
                
                showLoadingProcessLayer('Загружаю...');

                $('#translate_block').load('/admin/translate/show/id/'+id,()=>{
                    $('#translate_block').fadeIn(200);
                });

            },

            accept: (id)=>{
                
                $(this.currentRow).fadeOut(200);
                cms.info.show('Перевод принят');
                $('#translate_block').hide();
                
                cms.http.post({
                    url: '/admin/translate/accept/id/'+id,
                    success: ()=>{
                        
                        
                    }
                });
            },

            deny: (id)=>{
                $(this.currentRow).fadeOut(200);
                cms.info.show('Перевод принят');
                $('#translate_block').hide();
                
                cms.http.post({
                    url: '/admin/translate/deny/id/'+id,
                    success: ()=>{
                        
                    }
                });
            },
            
            showError: (id, btn) => {
                cms.dialog.show({
                    url: '/admin/translate/parafailed/id/'+id,
                    title: 'Параграф с ошибкой',
                    buttons: {
                        Сохранить: ()=> {
                            $(btn).parent().parent().parent().remove();
                            
                            cms.dialog.hide();
                            
                            cms.http.post({
                                url: '/admin/translate/paraset',
                                params: {
                                    id: id,
                                    translate: $('#translate').val()
                                },
                                success: ()=> { }
                            });
                        }
                    }
                });
            }, 
            
            delErr: (id, btn)=> {
                
                if (confirm('Параграф будет удалён из ошибочных и будет переведен заново.')) {
                    $(btn).parent().parent().parent().remove();
                    cms.http.post({
                        url: '/admin/translate/paradel',
                        params: {
                            id: id
                        },
                        
                        succcess: ()=>{
                            
                        }
                    });
                    
                }
                
            },
            
            delAllErrors: ()=> {
                var ids = [];
                showLoadingProcessLayer('Удаляю');
                
                $('tr.data').each((ind, el)=>{
                    ids.push($(el).attr('paragraph_id'));
                    $(el).remove();
                });
                
                cms.http.post({
                    url: '/admin/translate/delallparaerrors',
                    params: {
                        ids: ids.join(',')
                    },
                    success: ()=>{
                        hideLoadingProcessLayer();
                        cms.info.show('готово');
                    },
                    error: (r)=>{
                        hideLoadingProcessLayer();
                        cms.info.show('Ошибка');
                    },
                    fail: ()=>{
                        hideLoadingProcessLayer();
                        cms.info.show('Ошибка запроса');
                    }                    
                });
            }
            
        },
        showChapters: (nid, btn)=> {
            var cd = $(btn).parent().find('.chapters');

            if ($(cd).is(':visible')) {
                $(cd).fadeOut(300);
            }else {
                $(cd).html('Загрузка...');
                $(cd).fadeIn(700);
                $(cd).load('/admin/novella/loadchapters/id/'+nid);
            }
        },
        
        glossary: {
            edit: (novella_id)=> {
                site.novella.glossary.current_novella = novella_id;
                cms.dialog.show({
                    title: 'Редактор глоссария',
                    url: '/admin/glossary/edit',
                    width: '70%',
                    params: {
                        id: novella_id
                    }
                });
            },
            
            current_novella: null,
            
            save: (btn, id)=> {
                
                if (!id)
                    id=0;
                
                cms.http.post({
                    url: '/admin/glossary/save',
                    params: {
                        original: $(btn).parent().parent().find('input[name=original]').val(),
                        translate: $(btn).parent().parent().find('input[name=translate]').val(),
                        id: id,
                        novella: site.novella.glossary.current_novella
                    },
                    success: (r) => {
                        
                        cms.info.show('Сохранено');
                        if (!id) {
                            var d = "<tr><td><input placeholder='Оригинал' type='text' class='form-control' name='original' value='"+$(btn).parent().parent().find('input[name=original]').val()+"' /></td>";
                            d += "<td><input placeholder='Перевод' type='text' class='form-control' name='translate' value='"+$(btn).parent().parent().find('input[name=translate]').val()+"' /></td>";
                            d += "<td><button class='btn btn-primary' onclick='site.novella.glossary.save(this, "+r.id+");'>сохранить</button>";
                            d += "<button class='btn btn-danger' onclick='site.novella.glossary.del(this, "+r.id+");'>удалить</button>";
                            d += "</td></tr>";

                            $(d).insertBefore('#glossary_table tr:last');
                            
                            $('#glossary_table tr:last input[type=text]').val("");
                        }
                        
                    }
                });
                
                
            },
            
            del: (btn, id)=> {
                
                if (confirm('Точно?')) {
                    $(btn).parent().parent().fadeOut(300,()=>{
                        $(this).remove();
                    });
                    
                    cms.http.post({
                        url: '/admin/glossary/del/id/'+id,
                        success: ()=> {
                            
                        }
                    });
                    
                }
            }
        },
        
        volumes: {
            
            currentNovella: null,
            currentID: null,
            
            edit: (nid, btn) => {
                
                site.novella.volumes.currentNovella = nid;
                
                cms.dialog.show({
                    url: '/admin/novella/volumes/id/'+nid,
                    width: '80%',
                    title: 'Оглавление'
                    
                });
                site.novella.showChapters(nid, btn);
            },
            
            
            sync: (vid, btn)=> {
                $(btn).text('Синхронизация...');
                cms.http.post({
                    url: '/admin/novella/syncvolume/id/'+vid,
                    success: ()=>{
                        $('.modal-body').load('/admin/novella/volumes/id/'+site.novella.volumes.currentNovella,()=>{
                            $('#volume'+vid).trigger('click');
                        });
                    }
                });
            },
            
            clearNames: (volume_id, novella_id, btn)=> {
                $(btn).text('Очистка...');
                site.novella.volumes.currentNovella = novella_id;
                
                cms.http.post({
                    url: '/admin/novella/voldelnames/id/'+volume_id,
                    success: ()=>{
                        $('.modal-body').load('/admin/novella/volumes/id/'+site.novella.volumes.currentNovella,()=>{
                            $('#volume'+volume_id).trigger('click');
                        });
                        
                        
                    }
                });
                
            },
            
            reget: (nid, btn)=> {
                
                if (confirm('Это удалить все текущие тома и главы с переводами и получит их заново с сайта источника. Продолжить?')) {

                    $(btn).text('Синхронизация...');
                    site.novella.volumes.currentNovella = nid;

                    cms.http.post({
                        url: '/admin/novella/regetvolumes/id/'+nid,
                        success: ()=>{
                            $('.modal-body').load('/admin/novella/volumes/id/'+site.novella.volumes.currentNovella);
                        }
                    });
                }
            },
            
            
            refresh: (nid, btn)=>{
                $(btn).text('Обновление...');
                $('.modal-body').load('/admin/novella/volumes/id/'+nid, ()=> {
                    $('#volume'+site.novella.volumes.currentID).trigger('click');
                });
            }
        },
        
        chapters: {
            reloadChapter: (chapter_id)=>{
                showLoadingProcessLayer('Загрузка...');
                cms.http.post({
                    url: '/admin/novella/reloadchapter/id/'+chapter_id,
                    success: (r)=> {
                        hideLoadingProcessLayer();
                        cms.info.show('Содержимое обновлено!');
                        $('#chapars'+chapter_id).text(r.pars);
                    }
                });
            }
            
        }
        
    },
    
    news: {
        add: ()=> {
            cms.dialog.show({
                url:'/admin/news/edit',
                title: 'Добавить новость',
                width: '80%',
                buttons: {
                    Сохранить: site.news.save
                }
            });
        },
        
        edit: (id)=> {
            cms.dialog.show({
                url:'/admin/news/edit/id/'+id,
                title: 'Редактор',
                width: '80%',
                buttons: {
                    Сохранить: site.news.save
                }
            });
            
        },
        
        save: ()=> {
            cms.http.post({
                url: '/admin/news/save',
                params: $('#news_form').serializeArray()
            });
            
        },
        
        del: (id, btn)=> {
            if (confirm('Точно?')) {
                $(btn).parent().fadeOut(200);
                
                cms.http.post({
                    url: '/admin/news/del/id/'+id,
                    success: ()=>{
                        
                    }
                });
            }
        }
    },
    
    
    users: {
        block: (user_id)=> {
            cms.http.post({
                url: '/admin/users/block/id/'+user_id,
                success: ()=> {
                    cms.info.show('Пользователь заблокирован');
                }
            });
        },
        
        edit: (id)=>{
            cms.dialog.show({
                url: '/admin/users/edit/id/'+id,
                title: 'Пользователь',
                buttons: {
                    Сохранить: ()=> {
                        cms.http.post({
                            url: '/admin/users/savedata/id/'+id,
                            params: $('#userData').serializeArray(),
                            success: ()=> {
                                cms.info.show('Сохранено');
                                cms.dialog.hide();
                            }
                        });
                    }
                }
            });
        }
    }
}