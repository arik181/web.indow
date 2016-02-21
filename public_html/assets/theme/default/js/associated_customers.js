// Add default text to search field
$(document).ready(function()  
{
    var lastfocused = $();

    search = $('#existing-customer-search');

    search.focus(function () 
    {
        lastfocused = $(':focus');
        if ( lastfocused.val() == 'Search by Customer Email/Name to Add' )
        {
            lastfocused.val('');
        }
        lastfocused.css('color','black');
    });

    search.blur(function () 
    {
        if ( lastfocused.val() == '' )
        {
            lastfocused.val('Search by Customer Email/Name to Add');
            lastfocused.css('color','lightgrey');
        }
    });
});
