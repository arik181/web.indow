Groups.module("Entities",function(Entities,Groups,Backbone,Marionette,$,_){

    // Models
    Entities.SubGroup = Backbone.Model.extend({
    });
    Entities.Group = Backbone.Model.extend({
    });

    // Collections
    Entities.SubGroupCollection = Backbone.Collection.extend({
        model:Entities.SubGroup
    });
    Entities.GroupCollection = Backbone.Collection.extend({
        model:Entities.Group
    });

    var subGroups;
    var groupsItems;

    var initializeSubGroups = function(){
        subGroups = new Entities.SubGroupCollection(indow.groups.group.subGroups);
    };

    var initializeGroups = function(){
        groupsItems = new Entities.GroupCollection(indow.groups.groups);
    };

    var API = {
        getSubGroupEntities:function(){
            if(subGroups == undefined){
                initializeSubGroups();
            }
            return subGroups;
        },
        getGroupEntities:function(){
            if(groupsItems == undefined){
                initializeGroups();
            }
            return groupsItems;
        }
    };

    Groups.reqres.setHandler("subGroups:entities",function(){
        return API.getSubGroupEntities();
    });

    Groups.reqres.setHandler("groups:entities",function(){
        return API.getGroupEntities();
    });

});