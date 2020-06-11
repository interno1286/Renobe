
ml = {
    workServer: 'mlate.ru',
    items: [],
    
    translatedItems: [],
    
    currentItem: false,
    
    checkTranslateTimeout: null,
    
    translateFailedTimeout: null,
    
    nextTimeout: null,
    
    runned: false,
    
    log: (text)=>{
        $('.dl_header_menu_v2__links__item:eq(1)').text(text);
    },
    
    getItems: (cb) => {
        ml.log("Получаю новый список для перевода");
        $.get('//'+ml.workServer+'/novella/translate/getpar/t/deepl',(r)=>{
            
            if (r.list && !r.error)  {
                ml.items = r.list;
                
                if (cb) cb();
                
            }else alert('error '+r.error);
        }, 'json');
    },
    
    next: ()=> {
        
        ml.runned = true;
        $('textarea:eq(1)').val("");
        
        if (ml.items.length==0)  {
            ml.sendTranslate();
            return false;
        }
        
        setTimeout(()=>{
            ml.currentItem = ml.items.pop();

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
            
            try {
                
                text = text.trim();
                
            }catch (e) {
                console.log("UNKNOWN NAME PLEASE REPORT TO PAVEL CURRENT ITEM IS "+ml.currentItem.type+" "+ml.currentItem.id);
                ml.next();
                return false;
            }
            
            if (text=='') {
                ml.next();
                return false;
            }
            
            ml.log("Перевод...");
            
            $('textarea:eq(0)').val(text);

            setTimeout(ml.doEvent,200);

            if (ml.checkTranslateTimeout) clearTimeout(ml.checkTranslateTimeout);

            ml.checkTranslateTimeout  = setTimeout(ml.checkTranslate, 800);

            ml.translateFailedTimeout = setTimeout(ml.translateFailed, 12000);
            
        }, 700);
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
        
        
        
        if ($('textarea:eq(1)').val()!='' && $('.lmt__language_select__active__title:eq(1) strong').text()=='русский') {
            
            if (ml.translateFailedTimeout)
                clearTimeout(ml.translateFailedTimeout);
            
            if (ml.checkTranslateTimeout)
                clearTimeout(ml.checkTranslateTimeout);
            
            ml.translatedItems.push({
                translate: $('textarea:eq(1)').val(),
                id: ml.currentItem.id,
                type: ml.currentItem.type
            });
            ml.log("Успешно!");
            
            if (ml.nextTimeout) clearTimeout(ml.nextTimeout);
            ml.nextTimeout = setTimeout(ml.next, Math.floor(Math.random() * 1000));
            
            $('.dl_header_menu_v2__links__item:eq(0)').text('Осталось '+ml.items.length);
            
        }else {
            ml.doEvent();
            
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
                    alert(r.error)
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
