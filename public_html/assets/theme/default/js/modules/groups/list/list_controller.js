Groups.module("GroupsApp.List",function(List,Groups,Backbone,Mariontte,$,_){

    List.Controller = {

        listSubGroups: function(){

            var subGroups = Groups.request("subGroups:entities");
            var subGroupsListView = new List.subGroupsCol({collection:subGroups});
            var subGroupsInputListView = new  List.SubGroupInputCol({collection:subGroups});
            var groups = Groups.request("groups:entities");
            var subGroupSelectListView = new List.GroupSelectListCol({collection:groups});
            var addNewSubGroupButton = new List.AddNewSubGroupButtonItem;
            addNewSubGroupButton.render();
            Groups.mainRegion.show(subGroupsListView);
            Groups.hiddenRegion.show(subGroupsInputListView);
            Groups.staticRegion.show(subGroupSelectListView);

        }
    }

});