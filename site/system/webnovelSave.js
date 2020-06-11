

ml = {
    
    novella: {
        name: null,
        author: null,
        img: null,
        url: null,
        description: null,
        tags: [],
        volumes: []
    },
    
    
    save: ()=> {
        
        $('h2 small').remove();
        
        ml.novella.name = $('h2:eq(0)').text();
        ml.novella.url = location.href;
        ml.novella.author = $('address:eq(0) p span').text();
        $('#about>div:eq(0)>div:eq(0) a').remove();
        
        ml.novella.description = $('#about>div:eq(0)>div:eq(0)').text().trim();
        ml.novella.img = 'https:'+$('i.g_thumb img:eq(0)').attr('src');
        
        $('.m-tags p').each((ind, el) => {
            ml.novella.tags.push($('a',el).text().replace('# ','').trim());
        });
        $('a.j_show_contents:eq(0)').trigger('hover');
        $('a.j_show_contents:eq(0)').trigger('mousemove');
        $('a.j_show_contents:eq(0)').trigger('mouseover');
        $('a.j_show_contents:eq(0)').trigger('mouseout');
        
        setTimeout(()=>{
            $('a.j_show_contents:eq(0)').trigger('click');
            setTimeout(ml.saveVolumes, 3200);
        },600);
        
    },
    
    
    saveVolumes: ()=> {
        var v_number = 1;
        $('.volume-item').each((ind, el)=>{
            var volume = {
                name: $('h4',el).text().trim(),
                number: v_number,
                chapters: []
            };
            
            chap_num = 1;
            
            $('ol li',el).each((ind, el)=>{
                var chapter_data = {
                    numberParsed: $('a i:eq(0)',el).text(),
                    number: chap_num,
                    name: $('.oh strong',el).text(),
                    url: 'https:'+$('a:eq(0)',el).attr('href')
                }
                
                volume.chapters.push(chapter_data);
                
                chap_num++;
            });
            
            ml.novella.volumes.push(volume);
            
            v_number++;
        });
        
        ml.sendDataToServer();
    },
    
    
    sendDataToServer: ()=> {
        $.ajax({
            method: "POST",
            url: '//mlate.ru/novella/webnovella/save',
            data: {
                data: btoa(unescape(encodeURIComponent(JSON.stringify(ml.novella)))),
                k: 'xh73g8',
                dataType: "json"
            }
        }).done((r)=>{
            if (r.error)
                alert(r.error);
            else alert('Готово! Сохранена как '+r.name);
            
        }).fail(()=>{
            alert('Ошибка!');
        });
    }
    
    
    
};



setTimeout(ml.save, 1200);