
ml = {
    settings: {
        workTime: false, //minute
        pauseTime: 100, //minute
        workServer: 'mlate.ru',
        nextPause: 150000
    },
    
    items: [],
    
    translatedItems: [],
    
    currentItem: false,
    
    checkTranslateTimeout: null,
    translateFailedTimeout: null,
    nextTimeout: null,
    runned: false,
    
    translateMode: 'normal', // normal | paragraph
    
    paraIDS: [],
    startTS: 0,
    
    log: (text)=>{
        $('.dl_header_menu_v2__links__item:eq(1)').text(text);
        console.log(text);
    },
    
    getItems: (cb) => {
        ml.log("Получаю новый список для перевода");
        //var d = JSON.parse(atob('eyJzdGF0ZSI6Im9rIiwibGlzdCI6W3siaWQiOjUyNTMwOTIsInRleHRfb3JpZ2luYWwiOm51bGwsInRleHRfcnUiOm51bGwsInRleHRfZW4iOiJcIiBzaWdoLCBhbnl0aGluZyBlbHNlIHlvdSB3YW5uYSBzYXksIFwic2FpZCBoaXMgZmF0aGVyLCBieSB0aGlzIHBvaW50IGhlIGp1c3Qgd2FudGVkIHRvIGdldCBvdXQgb2YgaGVyZSBhbmQgZ28gc2xlZXAgZm9yIHRoZSBuZXh0IGRlY2FkZSB3aXRob3V0IHdha2luZyB1cC48YnI+Iiwibm92ZWxsYV9pZCI6MTMyLCJjaGFwdGVyX2lkIjoxNzU5MzUsInZvbHVtZV9pZCI6OTcyLCJoIjoiYzBhZGM1NThjZjVjNmIwZTM1NTAxNDVkOTUyZjU0ZTgwOTFmMWU5NiIsInR5cGUiOiJwYXJhZ3JhcGgifSx7ImlkIjo1MjUzMDY1LCJ0ZXh0X29yaWdpbmFsIjpudWxsLCJ0ZXh0X3J1IjpudWxsLCJ0ZXh0X2VuIjoiXCJtbSwgSSBkb24ndCBrbm93IHdoYXQncyB5b3VyIHJlYXNvbiBmb3IgdGhhdCBidXQgdGhhdCdzIGZpbmUsIGFjdHVhbGx5IHdpdGggdGhlIGV2ZXItZXhwYW5kaW5nIHNlcGFyYXRlIGRpbWVuc2lvbiB0aGF0IHlvdSBjcmVhdGVkIHdlIGNhbiBleHBhbmQgb3VyIGNpdHkgaG93ZXZlciB3ZSB3YW50IHNvIHdlIHdpbGwgbmV2ZXIgcnVuIG91dCBvZiByb29tLCBwbHVzIHdpdGggdGhlIGFydGlmaWNpYWwgZW52aXJvbm1lbnQgd2UgZG9uJ3QgcmVhbGx5IGhhdmUgYSBwcm9ibGVtIGxpdmluZyBoZXJlLCBtYXR0ZXIgb2YgZmFjdCBpdCBpcyBiZXR0ZXIgdGhhbiB0aGUgc3VyZmFjZSwgXCJzYWlkIGhpcyBmYXRoZXIgaW4gb25lIGJyZWF0aCBhZ3JlZWluZyB3aXRoIGhpcyBwbGFuLiIsIm5vdmVsbGFfaWQiOjEzMiwiY2hhcHRlcl9pZCI6MTc1OTM1LCJ2b2x1bWVfaWQiOjk3MiwiaCI6ImE3YmZkOGQzN2U1N2Y1Yjg3YTNmNjdiNTBiM2NlMWVlMjJmNTE3NGQiLCJ0eXBlIjoicGFyYWdyYXBoIn0seyJpZCI6MTc3NDE0LCJuYW1lX29yaWdpbmFsIjoiQ2hhcHRlciAzNjogRGFuY2luZyBvbiB0aGUgVGlwIG9mIGEgU3dvcmQiLCJuYW1lX3J1IjpudWxsLCJudW1iZXIiOjM2LCJ0eXBlIjoiY2hhcHRlciJ9LHsiaWQiOjE3OTEyOCwibmFtZV9vcmlnaW5hbCI6IkNoYXB0ZXIgMzYgOiBWZXRlcmFuXHUyMDE5cyBwcm92b2NhdGlvbiIsIm5hbWVfcnUiOm51bGwsIm51bWJlciI6MzYsInR5cGUiOiJjaGFwdGVyIn0seyJpZCI6MTgxNDcyLCJuYW1lX29yaWdpbmFsIjoiMzYgT3ZlcnR1cm5lZCIsIm5hbWVfcnUiOm51bGwsIm51bWJlciI6MzYsInR5cGUiOiJjaGFwdGVyIn0seyJpZCI6MTc3NDM3LCJuYW1lX29yaWdpbmFsIjoiQ2hhcHRlciAzNjogRGFuY2luZyBvbiB0aGUgVGlwIG9mIGEgU3dvcmQiLCJuYW1lX3J1IjpudWxsLCJudW1iZXIiOjM2LCJ0eXBlIjoiY2hhcHRlciJ9LHsiaWQiOjE3OTEyOSwibmFtZV9vcmlnaW5hbCI6IkNoYXB0ZXIgMzcgOiAzIFR5cGVzIG9mIEhha2kiLCJuYW1lX3J1IjpudWxsLCJudW1iZXIiOjM3LCJ0eXBlIjoiY2hhcHRlciJ9LHsiaWQiOjE3NzQzOSwibmFtZV9vcmlnaW5hbCI6IkNoYXB0ZXIgMzc6IFRocmVlIFllYXJzIFN0YXJ0IiwibmFtZV9ydSI6bnVsbCwibnVtYmVyIjozNywidHlwZSI6ImNoYXB0ZXIifSx7ImlkIjoxNzc0MTYsIm5hbWVfb3JpZ2luYWwiOiJDaGFwdGVyIDM3OiBUaHJlZSBZZWFycyBTdGFydCIsIm5hbWVfcnUiOm51bGwsIm51bWJlciI6MzcsInR5cGUiOiJjaGFwdGVyIn0seyJpZCI6MTgxNDczLCJuYW1lX29yaWdpbmFsIjoiMzcgU3ViZHVpbmcgdGhlIENpdHkgb2YgUWluZyIsIm5hbWVfcnUiOm51bGwsIm51bWJlciI6MzcsInR5cGUiOiJjaGFwdGVyIn0seyJpZCI6MTc5MTMwLCJuYW1lX29yaWdpbmFsIjoiQ2hhcHRlciAzOCA6IFN0cmVuZ3RoIERpdmlzaW9uIiwibmFtZV9ydSI6bnVsbCwibnVtYmVyIjozOCwidHlwZSI6ImNoYXB0ZXIifSx7ImlkIjoxNzc0NDEsIm5hbWVfb3JpZ2luYWwiOiJDaGFwdGVyIDM4OiBGaXZlLVN0YXIgRm9ybXVsYSEiLCJuYW1lX3J1IjpudWxsLCJudW1iZXIiOjM4LCJ0eXBlIjoiY2hhcHRlciJ9LHsiaWQiOjE3NzQxOCwibmFtZV9vcmlnaW5hbCI6IkNoYXB0ZXIgMzg6IEZpdmUtU3RhciBGb3JtdWxhISIsIm5hbWVfcnUiOm51bGwsIm51bWJlciI6MzgsInR5cGUiOiJjaGFwdGVyIn0seyJpZCI6MTgxNDc0LCJuYW1lX29yaWdpbmFsIjoiMzggQXVjdGlvbiIsIm5hbWVfcnUiOm51bGwsIm51bWJlciI6MzgsInR5cGUiOiJjaGFwdGVyIn0seyJpZCI6MTc3NDIwLCJuYW1lX29yaWdpbmFsIjoiQ2hhcHRlciAzOTogQSBEYXkgRnVsbCBvZiBTdXJwcmlzZXMiLCJuYW1lX3J1IjpudWxsLCJudW1iZXIiOjM5LCJ0eXBlIjoiY2hhcHRlciJ9LHsiaWQiOjE3NzQ0MywibmFtZV9vcmlnaW5hbCI6IkNoYXB0ZXIgMzk6IEEgRGF5IEZ1bGwgb2YgU3VycHJpc2VzIiwibmFtZV9ydSI6bnVsbCwibnVtYmVyIjozOSwidHlwZSI6ImNoYXB0ZXIifSx7ImlkIjoxODE0NzUsIm5hbWVfb3JpZ2luYWwiOiIzOSBCb2lzdGVyb3VzIiwibmFtZV9ydSI6bnVsbCwibnVtYmVyIjozOSwidHlwZSI6ImNoYXB0ZXIifSx7ImlkIjoxNzkxMzEsIm5hbWVfb3JpZ2luYWwiOiJDaGFwdGVyIDM5IDogU29ycnksIEkgYW0gaW4gYSBodXJyeSIsIm5hbWVfcnUiOm51bGwsIm51bWJlciI6MzksInR5cGUiOiJjaGFwdGVyIn0seyJpZCI6MTc5MTMyLCJuYW1lX29yaWdpbmFsIjoiQ2hhcHRlciA0MCA6IFNvcnUgYW5kIEdlcHBvIiwibmFtZV9ydSI6bnVsbCwibnVtYmVyIjo0MCwidHlwZSI6ImNoYXB0ZXIifSx7ImlkIjoxNzc0NDUsIm5hbWVfb3JpZ2luYWwiOiJDaGFwdGVyIDQwOiBUaGUgU3Ryb25nZXN0IEdlbmUhIiwibmFtZV9ydSI6bnVsbCwibnVtYmVyIjo0MCwidHlwZSI6ImNoYXB0ZXIifSx7ImlkIjoxNzc0MjIsIm5hbWVfb3JpZ2luYWwiOiJDaGFwdGVyIDQwOiBUaGUgU3Ryb25nZXN0IEdlbmUhIiwibmFtZV9ydSI6bnVsbCwibnVtYmVyIjo0MCwidHlwZSI6ImNoYXB0ZXIifSx7ImlkIjoxODE0NzYsIm5hbWVfb3JpZ2luYWwiOiI0MCBJbmNvbnRlc3RhYmxlIiwibmFtZV9ydSI6bnVsbCwibnVtYmVyIjo0MCwidHlwZSI6ImNoYXB0ZXIifV0sImVycm9yIjoiIn0gCg=='));
        //ml.items = d.list;
        //if (cb) cb();
        $.get('//'+ml.settings.workServer+'/novella/translate/getpar/t/deepl1',(r)=>{
            if (r.list && !r.error)  {
                ml.items = r.list;
                
                if (cb) cb();
                
            }else alert('error '+r.error);
            
            if (r.settings && ml.settings.workTime===false) {
                if (r.settings.pauseSec)
                    ml.settings.nextPause = (r.settings.pauseSec*1000);
                
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
        
        ml.paraIDS = [];
        ml.runned = true;
        ml.translateMode = 'normal';
        
        ml.log("Следующий...");
        
        $('textarea:eq(0)').val("");
        $('textarea:eq(1)').val("");
        
        if (ml.items.length==0)  {
            ml.sendTranslate();
            return false;
        }
        
        setTimeout(()=>{
            
            var left = [];
            var paragraph = [];
            
            ml.items.forEach((i,ind)=>{
                if (i.type=='paragraph') {
                    var text = i.text_en;
                    if (!text) text = i.text_original;
                    
                    paragraph.push(i.id+"^ "+text);
                    //ml.paraIDS.push(i.id);
                }else left.push(i);
            });
            
            ml.items = left;
            
            if (paragraph.length>0) {
                ml.log("переводим в режиме целой главы");
                text = paragraph.join(" || ");
                ml.translateMode = 'paragraph';
                ml.currentItem = {
                    type: 'paragraph'
                };
                
                
            }else {
                ml.log("переводим в обычном режиме");
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

            

            if (ml.checkTranslateTimeout) clearTimeout(ml.checkTranslateTimeout);

            ml.checkTranslateTimeout  = setTimeout(ml.checkTranslate, 1200);

            var failedTO = ((ml.currentItem.type=='paragraph') ? 600000 : 12000);

            ml.translateFailedTimeout = setTimeout(ml.translateFailed, failedTO);
            
            setTimeout(ml.doEvent,600);
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
        
        
               /////////////                                                /////////////////                                 ////////////
        if ($('textarea:eq(1)').val()!='' && $('.lmt__language_select__active__title:eq(1) strong').text()=='русский' && !$('.lmt__progress_popup').is(':visible')) {
            if (ml.translateFailedTimeout)
                clearTimeout(ml.translateFailedTimeout);
            
            
            if (ml.translateMode == 'paragraph') {
                var pars = $('textarea:eq(1)').val().split(" || ");
                ml.log("Найден перевод в режиме главы кол-во параграфов: "+pars.length);
                
                pars.forEach((p, index)=>{
                    //var id = ml.paraIDS[index];
                    
                    var items = p.split("^");
                    
                    var id = items[0].trim();
                    
                    var translate = "";
                    for (var x=1;x<items.length;x++)
                        translate += " "+items[x];
                    
                    if (translate.trim()=='') {
                        console.log("Какая-то ошибка с параграфом "+p);
                        //alert("Какая-то ошибка с параграфом "+p);
                        return true;
                    }
                    
                    ml.translatedItems.push({
                        translate: translate.trim(),
                        id: id,
                        type: 'paragraph'
                    });
                    
                })
                
            }else {
                ml.log("Найден перевод в нормальном режиме");
                ml.translatedItems.push({
                    translate: $('textarea:eq(1)').val(),
                    id: ml.currentItem.id,
                    type: ml.currentItem.type
                });
                
            }
            ml.log("Успешно!");
            
            if (ml.nextTimeout) clearTimeout(ml.nextTimeout);
            ml.nextTimeout = setTimeout(ml.next,    Math.floor(Math.random() * ml.settings.nextPause) + 10000);
            
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
                    alert(r.error)
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
                    
                    
                    
                    ml.log("Успешно");
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
        
        ml.log("Неудачный перевод. Отправляю отметку");
/*        
        sid = 0;
        
        if (ml.translateMode == 'paragraph') {
            var pars = $('textarea:eq(1)').val().split("||");

            var items = [];

            pars.forEach((p, index)=>{
                var id = ml.paraIDS[index];

                items.push(id);

            });
            
            sid = items.join(',');
            ml.currentItem.type = 'paragraph';
        }else 
            sid = ml.currentItem.id;
*/        
        
        $.post('//'+ml.settings.workServer+'/novella/translate/failed', {
            id: ml.currentItem.id,
            type: ml.currentItem.type
        },(r)=>{
            if (ml.nextTimeout) clearTimeout(ml.nextTimeout);
            ml.nextTimeout = setTimeout(ml.next, Math.floor((Math.random() * ml.settings.nextPause)+10000));
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
    
    ml.startTS = Math.floor(Date.now()/1000);
}
