var indow_delete_permissions = [];
Permissions.module("PermissionsApp.List",function(List,Permissions,Backbone,Mariontte,$,_){

    List.AddNewPermissionButtonItem = Marionette.ItemView.extend({
        el: '#addNewPermissionRegion',
        template: false,
        ui: {
            button: 'button#addperm.btn.btn-default'
        },
        events: {
            'click @ui.button': 'clickedButton'
        },
        clickedButton: function() {
            $( "button#addpermreal.btn.btn-default" ).trigger( "click" );
        }
    });

    List.AssocPermissionItem = Marionette.ItemView.extend({
        tagName:"tr",
        className:"permrow",
        template:"#associated-permissions-rows",
        ui:{
            xmark:"i.delperm.icon.fa.fa-times",
            toolname:"select.permoption",
            permissionLevel:"select.permvalue"
        },
        events:{
            "click @ui.xmark":"removeAssocPermission",
            "change @ui.toolname":"setToolNameValue",
            "change @ui.permissionLevel":"setPermissionLevel"
        },
        removeAssocPermission:function(){
            this.model.trigger('destroy', this.model, this.model.collection);
        },
        setToolNameValue:function(e){
            this.model.set('feature_id',$(e.currentTarget).val());
        },
        setPermissionLevel:function(e){
            this.model.set('permission_level_id',$(e.currentTarget).val());
        }
    });


    List.AssocPermissionsCol = Marionette.CompositeView.extend({
        tagName:"table",
        className:"display table table-hover dataTable no-footer",
        template:"#associated-permission-list",
        childView:List.AssocPermissionItem,
        ui:{
            button: 'button#addpermreal.btn.btn-default'
        },
        events:{
            'click @ui.button': 'clickedButton'
        },
        clickedButton: function() {

            var models = this.collection.models;
            var ids = [];
            var permission_preset_id;

            _.each(models,function(model){
                ids.push(model.attributes.id);
            });

            if(typeof indow.permission.permission.id == 'undefined'){
                permission_preset_id = 1;
            }
            else
            {
                permission_preset_id = indow.permission.permission.id;
            }

            var permissionOption = {
                id:this.getRandomNewPermissionId(ids),
                permission_preset_id:permission_preset_id,
                feature_id:1,
                permission_level_id:1
            };

            this.collection.add(permissionOption);

        },
        getRandomNewPermissionId:function(ids){
            console.log(ids);
            console.log(ids.length);
            if(ids.length == 0)
            {
                return 1;
            }
            else
            {
                var gotRand = false;
                var rand;
                while(!gotRand){
                    rand = Math.floor(Math.random() * 100);
                    if ($.inArray(rand, ids) === -1) {
                        gotRand = true;
                    }
                }
                return rand;
            }
        }
    });

    List.AssocPermissionInputItem = Marionette.ItemView.extend({
        initialize:function(){
          this.listenTo(this.model,"change",this.modelChanged);
        },
        tagName:"div",
        className:"col-md-6",
        template:"#associated-permission-inputs",
        modelEvens:{
            "change":"modelChanged"
        },
        modelChanged:function(){
            this.render();
        }
    });

    List.AssocPermissionInputCol = Marionette.CollectionView.extend({
    tagName:"div",
    className:"col-md-6",
    childView:List.AssocPermissionInputItem
    });

});
