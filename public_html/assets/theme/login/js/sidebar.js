$(document).ready(function()  
{
    active_link = $('.sidebar_active');

    //console.log(active_link.attr('id'));
    switch(active_link.attr('id'))
    {
    case "sidebar_estimates":
    case "sidebar_quotes":
        $(".estimates_quotes_child").show();
        break;
    case "sidebar_production":
    case "sidebar_billing":
    case "sidebar_shipping":
        $(".fulfillment_child").show();
        break;
    case "sidebar_users":
    case "sidebar_groups":
    case "sidebar_permissions":
        $(".users_groups_child").show();
        break;
    default:
        break;
    }
    
    $('.sidebar_parent').click(function() 
    {
        switch(this.id)
        {
        case "sidebar_estimates_quotes":
            $(".estimates_quotes_child").toggle();
            break;
        case "sidebar_fulfillment":
            $(".fulfillment_child").toggle();
            break;
        case "sidebar_users_groups":
            $(".users_groups_child").toggle();
            break;
        default:
            break;
        }
    });
});
