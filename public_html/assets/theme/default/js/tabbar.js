// Move the speech bubble on a tabbar
$(document).ready(function()  
{
    var tab_list = $('.addtab-cell');

    tab_list.click(hider);

    function hider(e) 
    {
        balloon_list = $('.speech-balloon');

        id = $(this).attr('id');
        balloon_id = '#' + id + '_speech_balloon';
        balloon = $(balloon_id);
    
        $(balloon_list).each(function ()
        {
            $(this).hide();
        });

        balloon.show();
    }
});
