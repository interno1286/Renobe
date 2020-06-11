
ml = {
    
    settings: {
        workTime:  false, //minute
        pauseTime: 100, //minute
        oneTimeSymbolsLimit: 5000,
        nextPause: 150000,
        workServer: 'mlate.ru'
    },
    
    items: [],
    translatedItems: [],
    currentItem: false,
    
    checkTranslateTimeout: null,
    translateFailedTimeout: null,
    nextTimeout: null,
    
    runned: false,
    translateMode: 'normal', // normal | kucha
    
    startTS: 0,
    
    
    itemTypes: {
        1: 'paragraph',
        2: 'chapter',
        3: 'volume',
        4: 'novella'
    },
    
    log: (text)=>{
        $('.dl_header_menu_v2__links__item:eq(1)').text(text);
        console.log(text);
    },
    
    getItems: (cb) => {
        ml.log("Получаю новый список для перевода");

        $.get('//'+ml.settings.workServer+'/novella/translate/getpar/t/deepl',(r)=>{
            if (r.list && !r.error)  {
                ml.items = r.list;
                if (cb) cb();
            }else alert('error '+r.error);
            
            if (r.settings && ml.settings.workTime===false) {
                
                if (r.settings.deeplSymCount)
                    ml.settings.oneTimeSymbolsLimit = r.settings.deeplSymCount;
                
                if (r.settings.pauseSec)
                    ml.settings.nextPause = (r.settings.pauseSec*1000);
                    
                if (r.settings.workTime)
                    ml.settings.workTime = r.settings.workTime;
                
                if (r.settings.pauseTime)
                    ml.settings.pauseTime = r.settings.pauseTime;
                
            }
            
            
        }, 'json');
    },
    
    next: ()=> {
        
        ml.runned = true;
        ml.translateMode = 'normal';
        
        ml.log("Следующий...");
        
        $('textarea:eq(0)').val("");
        $('textarea:eq(1)').val("");
        
        if (ml.items.length==0) {
            ml.sendTranslate();
            return false;
        }
            
        var left = [];
        var toTranslate = [];
        var symCount = 0;

        ml.items.forEach((i,ind)=>{
            
            if (symCount<ml.settings.oneTimeSymbolsLimit) {

                let type = 0;
                let text = null;
                ml.log("Обработка "+i.type);
                
                if (i.type==='paragraph') {
                    text = i.text_en;
                    if (!text) text = i.text_original;
                    type = 1;
                }else if (i.type==='chapter') {
                    text = i.name_original;
                    type = 2;
                }else if (i.type==='volume' ) {
                    text = i.title;
                    type = 3;
                }else if (i.type==='novella') {
                    text = i.name_original;
                    type = 4;
                }
                
                ml.log("Тип определён как "+type);


                if (text) {
                    
                    text = text.trim();
                    
                    if (!text || type===0) return true;
                    
                    let str = i.id+"^"+type+"^ "+text;
                    
                    if (symCount+str.length+4<ml.settings.oneTimeSymbolsLimit) {
                        toTranslate.push(str);
                        symCount+=str.length+4;
                    }else left.push(i);
                    
                }else console.log("UNKNOWN NAME PLEASE REPORT TO PAVEL CURRENT ITEM IS "+i.type+" "+i.id);
                
            }else left.push(i);
        });
        

        ml.items = left;
        
        try {

            var text = '';

            if (toTranslate.length>0) {
                ml.log("Режим перевода КУЧА");
                
                ml.translateMode = 'kucha';
                text = toTranslate.join(" || ");
                ml.currentItem = {
                    type: 'kucha'
                };

            }else {
                ml.log("Режим перевода по одному.");
                
                ml.currentItem = ml.items.pop();
                
                ml.log("Элемент "+ml.currentItem);

                if (ml.currentItem.type==='paragraph') {
                    text = ml.currentItem.text_en;
                    if (!text) text = ml.currentItem.text_original;
                }else if (ml.currentItem.type==='chapter') {
                    text = ml.currentItem.name_original;
                }else if (ml.currentItem.type==='volume') {
                    text = ml.currentItem.title;
                }else if (ml.currentItem.type==='novella') {
                    text = ml.currentItem.name_original;
                }
            }

            ml.log("Перевод...");

            setTimeout(()=>{

                $('textarea:eq(0)').val(text);

                if (ml.checkTranslateTimeout) clearTimeout(ml.checkTranslateTimeout);

                setTimeout(ml.doEvent,300);
                ml.checkTranslateTimeout  = setTimeout(ml.checkTranslate, 1200);

                var failedTO = ((text.length>1000) ? 600000 : 12000);
                ml.translateFailedTimeout = setTimeout(ml.translateFailed, failedTO);

            },700);
        }catch (e) {
            ml.log("Ошибка при переводе "+e+" элемента "+ml.items+" переходим к следующему");
            
            if (ml.nextTimeout) clearTimeout(ml.nextTimeout);
            ml.nextTimeout = setTimeout(ml.next, Math.floor((Math.random() * ml.settings.nextPause)+10000));
            
            
        }
    },
    
    doEvent: ()=> {
        var keyboardEvent = document.createEvent("KeyboardEvent");
        var initMethod = typeof keyboardEvent.initKeyboardEvent !== 'undefined' ? "initKeyboardEvent" : "initKeyEvent";

        keyboardEvent[initMethod](
          "keyup", // event type: keydown, keyup, keypress
          true,      // bubbles
          true,      // cancelable
          window,    // view: should be window
          false,     // ctrlKey
          false,     // altKey
          false,     // shiftKey
          false,     // metaKey
          32,        // keyCode: unsigned long - the virtual key code, else 0
          0          // charCode: unsigned long - the Unicode character associated with the depressed key, else 0
        );

        $('textarea:eq(0)')[0].dispatchEvent(keyboardEvent);
        document.dispatchEvent(keyboardEvent);
        
    },
    
    checkTranslate: ()=> {
        if ($('textarea:eq(1)').val()!='' && $('.lmt__language_select__active__title:eq(1) strong').text()=='русский' && !$('.lmt__progress_popup').is(':visible')) {
            
            if (ml.translateFailedTimeout)
                clearTimeout(ml.translateFailedTimeout);
            
            if (ml.translateMode === 'kucha') {
                
                var items = $('textarea:eq(1)').val().split(" || ");
                ml.log("Найден перевод в режиме кучи");

                
                items.forEach((p, index)=>{
                    
                    let parts = p.split("^");   
                    
                    if (parts.length<2) {
                        console.log("Ошибка парсинга элемента '"+p+"' пропуск.");
                        return true;
                    }

                    let id = parts[0].trim();
                    let type = parts[1].trim();

                    var translate = "";
                    
                    for (var x=2;x<parts.length;x++)
                        translate += " "+parts[x];

                    if (translate.trim()=='') {
                        console.log("Какая-то ошибка с элементом "+p);
                        return false;
                    }

                    ml.translatedItems.push({
                        translate: translate.trim(),
                        id: id,
                        type: ml.itemTypes[type]
                    });

                })
            }else {
                ml.log("Найден перевод в обычном режиме");
                
                ml.translatedItems.push({
                    translate: $('textarea:eq(1)').val(),
                    id: ml.currentItem.id,
                    type: ml.currentItem.type
                });
                
            }
            
            
            ml.log("Успешно!");
            
            if (ml.nextTimeout) clearTimeout(ml.nextTimeout);
            ml.nextTimeout = setTimeout(ml.next, Math.floor((Math.random() * ml.settings.nextPause)+10000));
            
            $('.dl_header_menu_v2__links__item:eq(0)').text('Осталось '+ml.items.length);
            
        }else {
            ml.doEvent();
            
            if (ml.checkTranslateTimeout) clearTimeout(ml.checkTranslateTimeout);
            ml.checkTranslateTimeout  = setTimeout(ml.checkTranslate, 1200);
            
        }
    },
    
    sendTranslate: () => {
        
        if (ml.translatedItems.length==0) {
            ml.getItems(ml.next);
        }else {
            ml.log("Отправляем переведеённые элементы");
            $.post('//'+ml.settings.workServer+'/novella/translate/setpar',{
                j: JSON.stringify({
                    list: ml.translatedItems
                }),

                k: '9q298cb982bcy3988vuxfddvwnbevgh'

            }, (r)=>{
                if (r.error) {
                    alert(r.error);
                }else {
                    
                    let currentTS = Math.floor(Date.now() / 1000);

                    if ((currentTS-ml.startTS)>(ml.settings.workTime*60)) {
                        
                        let till_ts = Math.floor((Date.now() / 1000) + (ml.settings.pauseTime*60));
                        ml.startTS = till_ts;
                        let d = new Date(till_ts*1000);
                        ml.log("Перерыв до "+d.getHours()+':'+d.getMinutes());
                        $('.dl_header_menu_v2__links__item:eq(0)').text("Перерыв до "+d.getHours()+':'+d.getMinutes());
                        
                        ml.getItems(()=>{
                            ml.nextTimeout = setTimeout(ml.next, (ml.settings.pauseTime*60*1000));
                        });

                    }else {
                        ml.getItems(()=>{
                            ml.nextTimeout = setTimeout(ml.next, Math.floor((Math.random() * ml.settings.nextPause)+10000));
                        });
                        
                        ml.log("Успешно");
                    }
                    
                }

                ml.translatedItems = [];
                
            }, 'json').fail(function() {
                console.log("Send translate failed try again");
                ml.log("Ошибка пробую снова");
                setTimeout(ml.sendTranslate, 1200);
                ml.translatedItems = [];
            });
        }
    },
    
    
    translateFailed: ()=> {
        
        if (ml.checkTranslateTimeout)
            clearTimeout(ml.checkTranslateTimeout);
        
        if (ml.nextTimeout)
            clearTimeout(ml.nextTimeout);
        
        ml.log("Перевод неудался. Идём дальше.");
        ml.nextTimeout = setTimeout(ml.next, Math.floor((Math.random() * ml.settings.nextPause)+10000));
        /*

        $.post('//'+ml.settings.workServer+'/novella/translate/failed', {
            id: ml.currentItem.id,
            type: ml.currentItem.type
        },(r)=>{
            if (ml.nextTimeout) clearTimeout(ml.nextTimeout);
            ml.nextTimeout = setTimeout(ml.next, Math.floor(Math.random() * 1000));
            ml.log("Успешно");
        },'json')
        .fail(function() {
            ml.log("Ошибка пробую снова");
            console.log("Send TRFAILED failed try again");
            setTimeout(ml.translateFailed, 1200);
        });
        */
    }
    
}

if (!ml.runned) {
    
    try {
        ga = null;
    }catch (e) {
        
    }
    
    ml.getItems(ml.next);
    ml.startTS = Math.floor(Date.now() / 1000);
}

