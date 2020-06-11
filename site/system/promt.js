
ml = {
    workServer: 'mlate.ru',
    items: [],
    
    maxTextSize: 3000,
    
    manyParas: false,
    
    translatedItems: [],
    
    currentItem: false,
    
    checkTranslateTimeout: null,
    
    translateFailedTimeout: null,
    
    nextTimeout: null,
    
    runned: false,
    
    log: (text)=>{
        $('#userName').text(text);
        console.log(text);
    },
    
    getItems: (cb) => {
        ml.log("Получаю новый список для перевода");
        $.get('//'+ml.workServer+'/novella/translate/getpar/t/promt',(r)=>{
            
            if (r.list && !r.error)  {
                ml.items = r.list;
                
                if (cb) cb();
                
            }else alert('error '+r.error);
        }, 'json');
    },
    
    next: ()=> {
        ml.log("Следующий...");
        ml.runned = true;
        ml.translateMode = 'normal';
        
        $('textarea:eq(1)').val("");
        
        if (ml.items.length==0)  {
            ml.sendTranslate();
            return false;
        }
        
        
        setTimeout(()=>{
            
            var left = [];
            var paragraph = [];
            var textSize = 0;
            
            if (ml.manyParas) {
                
                
                ml.items.forEach((i,ind)=>{
                    if (i.type=='paragraph' && textSize<ml.maxTextSize) {
                        var text = i.text_en;
                        if (!text) text = i.text_original;

                        textSize  += text.length;

                        if (textSize>=ml.maxTextSize) {
                            left.push(i);
                        }else paragraph.push(i.id+"^ "+text);


                    }else left.push(i);
                });


                ml.items = left;
            }
            
            if (paragraph.length>0) {
                ml.log("Режим много параграфов");
                text = paragraph.join(" || ");
                
                ml.translateMode = 'paragraph';
                ml.currentItem = {
                    type: 'paragraph'
                };
                
            }else {
                var obj = ml.items.pop();
                ml.log("Обычный режим переводим "+obj.id);
                ml.currentItem = obj;

                var text = '';

                if (ml.currentItem.type=='paragraph') {
                    text = ml.currentItem.text_en;
                    if (!text) text = ml.currentItem.text_original;
                }else if (ml.currentItem.type=='chapter') {
                    text = ml.currentItem.name_original;
                }else if (ml.currentItem.type=='volume') {
                    text = ml.currentItem.title;
                }else if (ml.currentItem.type=='novella') {
                    text = ml.currentItem.name_original;
                }
            }
            
            
            try {
                text = text.trim();
            }catch (e) {
                console.log("UNKNOWN NAME PLEASE REPORT TO PAVEL CURRENT ITEM IS "+ml.currentItem.type+" "+ml.currentItem.id);
                ml.next();
                return false;
            }
            
            if (text=='' || text.length>ml.maxTextSize) {
                ml.next();
                return false;
            }
            
            ml.log("Перевод...");
            
            $('textarea:eq(0)').val(text);

            

            if (ml.checkTranslateTimeout) clearTimeout(ml.checkTranslateTimeout);

            ml.checkTranslateTimeout  = setTimeout(ml.checkTranslate, 1200);

            var failedTO = ((ml.currentItem.type=='paragraph') ? 600000 : 12000);

            ml.translateFailedTimeout = setTimeout(ml.translateFailed, failedTO);
            
            setTimeout(ml.doEvent,700);
            
            
        }, 300);
    },
    
    doEvent: ()=> {
       $('#bTranslation').trigger('click');
    },
    
    checkTranslate: ()=> {
        
        ml.log("Проверяю есть ли перевод...");
        
        if ($('.result-sets button span').text()!='Русский') {
            
            ml.log("Внимание язык перевода отличается от Русского выставите русский язык!");
            
            if (ml.checkTranslateTimeout) clearTimeout(ml.checkTranslateTimeout);
            
            ml.checkTranslateTimeout  = setTimeout(ml.checkTranslate, 800);
            
            if ($('#mlaudio').length==0) {
                $('body').append("<audio id='mlaudio' src='https://mlate.ru/site/system/alarm.mp3' autoplay=true loop=true></audio>");
            }else {
                $('#mlaudio')[0].play();
            }
            
            //return false;
        }else {
            if ($('#mlaudio').length>0) {
                $('#mlaudio')[0].pause();
            }
        }
        
        if ($('textarea:eq(1)').val()!='') {
            
            ml.log("Перевод есть");
            
            if (ml.translateFailedTimeout)
                clearTimeout(ml.translateFailedTimeout);
            
            
            
            if (ml.translateMode == 'paragraph' && ml.manyParas) {
                ml.log("Много параграфов делаю парсинг.");
                var pars = $('textarea:eq(1)').val().split(" || ");
                pars.forEach((p, index)=>{
                    var items = p.split("^ ");
                    
                    var id = items[0];
                    var translate = "";
                    for (var x=1;x<items.length;x++)
                        translate += " "+items[x];
                    
                    
                    ml.translatedItems.push({
                        translate: translate.trim(),
                        id: id,
                        type: 'paragraph'
                    });
                    
                })
                
            }else {
                var obj = {
                    translate: $('textarea:eq(1)').val(),
                    id: ml.currentItem.id,
                    type: ml.currentItem.type
                };
                ml.log("Сохраняем переведенный фрагмент. "+obj.id);
                
                ml.translatedItems.push(obj);
            }
            
            ml.log("Успешно!");
            
            if (ml.nextTimeout) clearTimeout(ml.nextTimeout);
            ml.nextTimeout = setTimeout(ml.next, Math.floor(Math.random() * 1200));
            
            $('.crown-white').text('Осталось '+ml.items.length);
            
        }else {
            //ml.doEvent();
            
            if (ml.checkTranslateTimeout) clearTimeout(ml.checkTranslateTimeout);
            ml.checkTranslateTimeout  = setTimeout(ml.checkTranslate, 800);
            
        }
    },
    
    
    sendTranslate: () => {
        
        if (ml.translatedItems.length==0) {
            ml.getItems(ml.next);
        }else {
            ml.log("Отправляем переведеённые элементы");
            $.post('//'+ml.workServer+'/novella/translate/setpar',{
                j: JSON.stringify({
                    list: ml.translatedItems
                }),

                k: '9q298cb982bcy3988vuxfddvwnbevgh'

            }, (r)=>{
                if (r.error) {
                    alert(r.error);
                }else {
                    ml.translatedItems = [];
                    ml.getItems(ml.next);
                    ml.log("Успешно");
                }

            }, 'json').fail(function() {
                console.log("Send translate failed try again");
                ml.log("Ошибка пробую снова");
                setTimeout(ml.sendTranslate, 1200);
            });
        }
    },
    
    
    translateFailed: ()=> {
        
        if (ml.checkTranslateTimeout)
            clearTimeout(ml.checkTranslateTimeout);
        
        if (ml.nextTimeout)
            clearTimeout(ml.nextTimeout);
        
        ml.log("Неудачный перевод. Отправляю отметку");
        
        $.post('//'+ml.workServer+'/novella/translate/failed', {
            id: ml.currentItem.id,
            type: ml.currentItem.type
        },(r)=>{
            if (ml.nextTimeout) clearTimeout(ml.nextTimeout);
            ml.nextTimeout = setTimeout(ml.next, Math.floor(Math.random() * 1000));
            ml.log("Успешно");
        },'json').fail(function() {
                ml.log("Ошибка пробую снова");
                console.log("Send TRFAILED failed try again");
                setTimeout(ml.translateFailed, 1200);
            });
    }
    
}

if (!ml.runned) {
    
    try {
        ga = null;
    }catch (e) {
        
    }
    
    ml.getItems(ml.next);
}
