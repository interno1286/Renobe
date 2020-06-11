function newsUp(item_id,owner) {
    
    var data = {
	item: item_id,
	news_owner: owner
    }
    
    sendPost('/news/index/moveup',data,function(){
	document.location.reload();
    });
}

function newsDown(item_id,owner) {
    
    var data = {
	item: item_id,
	news_owner: owner
    }
    
    sendPost('/news/index/movedown',data,function(){
	document.location.reload();
    });
}