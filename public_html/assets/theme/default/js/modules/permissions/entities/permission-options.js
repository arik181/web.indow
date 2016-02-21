Permissions.module("Entities",function(Entities,Permissions,Backbone,Marionette,$,_){

    Entities.AssocPermission = Backbone.Model.extend({
    });

    Entities.AssocPermissionCollection = Backbone.Collection.extend({
        model:Entities.AssocPermission
    });

    var assocPermissions;

    var initializeAssocPermissions = function(){
        assocPermissions = new Entities.AssocPermissionCollection(indow.permission.permission.permissions);
    };

    var API = {
        getAssocPermissionsEntities:function(){
            if(assocPermissions == undefined){
                initializeAssocPermissions();
            }
            return assocPermissions;
        }
    };

    Permissions.reqres.setHandler("assocPermission:entities",function(){
        return API.getAssocPermissionsEntities();
    });


});