siteAdmin = {
    chapter: {
        
        retranslateWindow: null,
        
        retranslate: (id, item)=> {
            
            //this.retranslateWindow = window.open('/admin/translate/retranslate/item/'+item+'/id/'+id, "_blank", "width=1000, height=600" );
            
            cms.http.post({
                url: '/admin/translate/retranslate/item/'+item+'/id/'+id,
                success: ()=>{
                    cms.info.show('Глава очищена и отправлена на повторный перевод. Результат во всплывающем окне');        
                    location.reload();
                }
            });
            
            
        }
    }
}