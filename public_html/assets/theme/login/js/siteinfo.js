$(document).ready(function()  
{
    $("#site_info_form").click(function(e){
        e.preventDefault();
        update_site_info();
    });

    function update_site_info()
    {
        customer_id  = $("#customer_id").val();
        first_name   = $("#first_name.large_input").val();
        last_name    = $("#last_name.large_input").val();
        company_name = $("#company_name.large_input").val();
        email_1      = $("#email_1.medium_input").val();
        email_2      = $("#email_2.medium_input").val();
        email_type_1 = $("select#email_type_1 option:selected").text();
        email_type_2 = $("select#email_type_2 option:selected").text();

        updated_json =  {
                    "customer_id" : customer_id,
                    "first_name" : first_name,
                    "last_name" : last_name,
                    "customer_company_name" : company_name,
                    "email_1" : email_1,
                    "email_2" : email_2,
                    "email_type_1" : email_type_1,
                    "email_type_2" : email_type_2,
        };

        //console.log(updated_json);

        $.ajax({
            type : "POST",
            url  : "/api/update_site_info/",
            data : updated_json,
            success: function(data, textStatus) {
                /*
                disp = $("#popover-content").css('display');
                console.log(data);
                console.log(textStatus);
                console.log("WIN");
                console.log(disp);
                $("#popover-content").attr('display', 'gone');
                */
            },
            error: function(data, textStatus) {
                /*
                console.log(data);
                console.log(textStatus);
                console.log("LOSE");
                */
            }
        });

        fetch_updated_site_info();
    }

    function fetch_updated_site_info()
    {
        customer_id     = $("#customer_id").val();

        get_json = {
                "customer_id" : customer_id
        };

        //console.log(get_json);

        $.ajax({
            type : "POST",
            url  : "/api/fetch_updated_site_info",
            data : get_json,
            success: function(data) {
                site_name         = $("#site_name");
                site_company_name = $("#site_company_name");

                console.log(data);
                //console.log(data.first_name);
                site_name.text(data.first_name + ' ' + data.last_name);
                site_company_name.text(data.company);
            },
            error: function(data) {
            }
        });
    }
    
    
    function fetch_existing_customer_info()
    {
        customer_id     = $("#customer_id").val();

        get_json = {
                "customer_id" : customer_id
        };

        //console.log(get_json);

        $.ajax({
            type : "POST",
            url  : "/api/fetch_updated_site_info",
            data : get_json,
            success: function(data) {
                site_name         = $("#site_name");
                site_company_name = $("#site_company_name");

                console.log(data);
                //console.log(data.first_name);
                site_name.text(data.first_name + ' ' + data.last_name);
                site_company_name.text(data.company);
            },
            error: function(data) {
            }
        });
    }
    
});
