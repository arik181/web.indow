var Groups = new Marionette.Application();

Groups.addRegions({
    mainRegion:"#js-subgroups-main-region",
    staticRegion:"#js-subgroups-static-region",
    hiddenRegion:"#js-hidden-inputs-region",
    addNewSubGroupRegion:"#js-addNewSubGroupRegion"
});

Groups.on("start",function(){
    Groups.GroupsApp.List.Controller.listSubGroups();
});