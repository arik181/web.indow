$(document).ready(function()  
{
    $("#site_info_form").keydown(function(e){
        update_site_info();
        update_site_display();
    });

    $("#site_info_form").keyup(function(e){
        update_site_info();
        update_site_display();
    });

    function update_site_info()
    {
        var site_id  = $("#site_id").val();
        var address   = $("#site_address").val();
        var address_ext    = $("#site_address_ext").val();
        var address_type = $("select#address_type option:selected").text();

        updated_json =  {
                    "site_id" : site_id,
                    "address" : address,
                    "last_name" : address_ext,
                    "address_type" : address_type,
        };

        $.ajax({
            type : "POST",
            url  : "/api/update_site_info/",
            data : updated_json,
            success: function(data, textStatus) {
                /*
                console.log("WIN");
                */
            },
            error: function(data, textStatus) {
                /*
                console.log(textStatus);
                */
            }
        });
    }

    function fetch_updated_site_info()
    {
        var site_id      = $("#site_id").val();
        var address   = $("#site_address").val();
        var address_ext    = $("#site_address_ext").val();
        var address_type = $("select#address_type option:selected").text();

        get_json = {
                "site_id" : site_id
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
