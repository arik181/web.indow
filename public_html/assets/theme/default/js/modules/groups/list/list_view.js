Groups.module("GroupsApp.List",function(List,Groups,Backbone,Mariontte,$,_){

    List.GroupOptionItem = Marionette.ItemView.extend({
        template: '#js-subgroup-select-list-item',
        tagName:"option",
        onRender:function(){
            this.$el.attr('value',this.model.get('id'));
        }
    });

    List.GroupSelectListCol = Marionette.CompositeView.extend({
        tagName:"select",
        id:"js-select-list-for-sub-groups",
        className:"form-control input-sm",
        template:"#js-subgroup-select-list",
        childView:List.GroupOptionItem
    });

    List.AddNewSubGroupButtonItem = Marionette.ItemView.extend({
        el:'#js-addNewSubGroupRegion',
        template: false,
        ui: {
            button: 'button#addnewsub.btn.btn-default'
        },
        events: {
            'click @ui.button': 'clickedButton'
        },
        clickedButton: function()
        {
            $('#addnewsubgroup').trigger('click');
        }
    });

    List.subGroupItem = Marionette.ItemView.extend({
        tagName:"li",
        className:"sub-group-item",
        template:"#js-subgroup-items",
        ui:{
            xmark:"i.delperm.icon.fa.fa-times"
        },
        events:{
            "click @ui.xmark":"removeSubGroup"
        },
        removeSubGroup:function(){
            this.model.destroy();
        },
        onRender:function(){
            this.$el.attr('rel',this.model.get('id'));
        }
    });

    List.subGroupsCol = Marionette.CompositeView.extend({
        tagName:"ul",
        id:"js-subgroup-sortable-list",
        className:"list-unstyled",
        template:"#js-subgroup-list",
        childView:List.subGroupItem,ui:{
            button: 'button#addnewsubgroup',
            sort: 'button#sortsubgroups'
        },
        events:{
            'click @ui.button': 'addNewSubGroup',
            'click @ui.sort': 'sortSubGroups'
        },
        addNewSubGroup: function(e) {
            e.preventDefault();
            var models = this.collection.models;
            var selected_sub_group_id = $('select#js-select-list-for-sub-groups option:selected').val();
            var groups = Groups.request("groups:entities");
            var sub_group = groups.get(selected_sub_group_id);

            var ids = [];
            _.each(models,function(model){
                ids.push(model.attributes.id);
            });

            var duplicate_check = _.find(ids,function(id){
                return id == selected_sub_group_id;
            });

            if(typeof duplicate_check  == 'undefined'){

                this.collection.add({
                    id:sub_group.attributes.id,
                    name:sub_group.attributes.name,
                    rank:this.collection.models.length + 1
                });
            }
            else
            {
                alert('The group selected is already a sub group.');
            }

        },
        sortSubGroups:function(e){
            e.preventDefault();
            var sub_groups = $('li.sub-group-item');
            console.log('sortSubGroups');
            var collection = this.collection;

            var order = 1;
            _.each(sub_groups,function(group){
                var group_id = $(group).attr('rel');
                var group_item = collection.get(group_id);
                group_item.set('rank',order);
                order++;
            });

        },
        onRender:function(){
            $( this.$el ).sortable({
                stop: function() {
                    $('button#sortsubgroups').trigger('click');
                }
            });
        }


});

    List.SubGroupInputItem = Marionette.ItemView.extend({
        initialize:function(){
          this.listenTo(this.model,"change",this.modelChanged);
        },
        template:"#js-subgroup-inputs",
        modelEvens:{
            "change":"modelChanged"
        },
        modelChanged:function(){
            this.render();
        }
    });

    List.SubGroupInputCol = Marionette.CollectionView.extend({
    childView:List.SubGroupInputItem
    });

});