function leaveComment(fo,el) {
    $('.leave_comment').slideDown();
    
    $(el).val('Готово').css('margin-top', '20px');
    
    $(el).removeAttr('onclick').click(function(){
        doComment(fo);
    });

}

function doComment(fo) {
    var pass = true;

    $('#comment_form input[type=text], #comment_form textarea').each(function(){
        if ($(this).val()=='') {
            alert('Необходимо заполнить все поля!');
            $(this).focus().addClass('error');
            pass=false;
            return false;
        }
    });

    if (pass) {

        $('#comments_block_'+fo).animate({
            opacity: 0.5
        });


        var data = $('#comment_form').serializeArray();

        sendPost('/comments/index/new',data,function(){
            commentsReload(fo);
        },function(){
            $('#comments_block_'+fo).animate({
                opacity: 1
            },200);
        });

    }
    
}

function commentsReload(fo) {
    $('#comments_block_'+fo).load('/comments/index/reload/for/'+fo,null,function(){
        $('#comments_block_'+fo).animate({
            opacity: 1
        },200);
    });
}


function answer(comment_id, el, fo) {
    $('#ansfor').val(comment_id);
    
    $('#af_text').html($(el).parent().parent().find('.text').html());
    
    $('#af_author').text($(el).parent().parent().find('.author').html()+', '+$(el).parent().parent().find('.city').html());
    
    $('.ansForText').show();
    
    $('#comment_button').val('ответить').removeAttr('onclick').click(function(){
        doComment(fo);
    });
    
    $('#comment_form textarea').attr('placeholder','Напишите здесь ваш ответ');
    
    $('.leave_comment').slideDown(100,function(){
        $.scrollTo('#comment_form',800);
    });
}