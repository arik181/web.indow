$(document).ready(function()  
{
    var lastfocused = $();

    notes = $('.notes_textarea');

    notes.focus(function () 
    {
        lastfocused = $(':focus');
        if ( lastfocused.val() == 'Insert notes here.' )
        {
            lastfocused.val('');
        }
        lastfocused.css('color','black');
    });

    notes.blur(function () 
    {
        if ( lastfocused.val() == '' )
        {
            lastfocused.val('Insert notes here.');
            lastfocused.css('color','lightgrey');
        }
    });
});
