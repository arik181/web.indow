Permissions.module("PermissionsApp.List",function(List,Permissions,Backbone,Mariontte,$,_){

    List.Controller = {
        listAssocPermissions: function(){
            var assocPermission = Permissions.request("assocPermission:entities");
            var assocPermissionListView = new List.AssocPermissionsCol({collection:assocPermission});
            var assocPermissionInputListView = new List.AssocPermissionInputCol({collection:assocPermission});
            var addNewPermissionButtonView = new List.AddNewPermissionButtonItem;
            addNewPermissionButtonView.render();
            Permissions.mainRegion.show(assocPermissionListView);
            Permissions.hiddenRegion.show(assocPermissionInputListView);
        }
    }

});