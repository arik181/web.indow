var Permissions = new Marionette.Application();

Permissions.addRegions({
    mainRegion:"#permissions_options_values_region",
    addNewPermissionRegion:"#addNewPermissionRegion",
    hiddenRegion:"#hidden_values_region",
    addNewPermissionInputsRegion:"#addNewPermissionInputsRegion"
});

Permissions.on("start",function(){
    Permissions.PermissionsApp.List.Controller.listAssocPermissions();
});

$('#edit_perm_form').submit(function (e) {
    var options = [];
    $('.permoption').each(function () {
        if ($.inArray($(this).val(), options) !== -1) {
            alert('You may only have one permission level per tool.');
            e.preventDefault();
            return false;
        } else {
            options.push($(this).val());
        }
    });
});