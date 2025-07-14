Vue.component('newsubcategory', {
     template: '#rowTemplate',
     data: function() {
          return {
               savingSubcategory: false
          }
     },
     methods: {
          saveSubcategory: function(event) {
               event.preventDefault();
               var form = $(event.target).closest('form');
               // console.log(form);
               // form.find('input').filter(':visible:first').focus();
               this.savingSubcategory = true;
               $.ajax({
                    type: 'POST',
                    url: form.data('url'),
                    data: form.serialize(),
                    success: function(data){
                         if(data.location_matters == 1) {location_matters = 'Yes'; } else { location_matters = 'No'; }
                         $('#activetable'+data.category+' tr:last')
                         .after('<tr id="activetr'+data.subcategory_id+'"><td>'+data.name+'</td><td>'+data.tags+'</td><td>'+location_matters+'</td><td>'+data.created_by+'</td><td>'+data.created_at+'</td><td></td></tr>');
                         form.trigger('reset');
                         this.savingSubcategory = false;
                         form.find('input').filter(':visible:first').focus();
                    }.bind(this),
                    error: function(data) {
                         var response = JSON.parse(data.responseText); 
                         this.savingSubcategory = false;
                         $('#errors').append(response.name);
                         $('#errors').show();
                    }.bind(this)
               });
          }
     },
});
new Vue({
     el: '#addSubcategory'
});
$( document ).ready(function() {
     
     $('.inactive').removeClass('active');
     

});

function inactivateSubcategory(event) {
     event.preventDefault();
     
     var form = $(event.target).closest('form');
      $.ajax({
                    type: 'POST',
                    url: form.data('url'),
                    data: form.serialize(),
                    success: function(data){
                         $('#activetr'+data.subcategory_id).remove();  
                         $('#inactivetable'+data.category+' tr:last')
                         .after('<tr id="inactivetr'+data.subcategory_id+'"><td>'+data.name+'</td><td>'+data.tags+'</td><td>'+data.created_by+'</td><td>'+data.created_at+'</td><td><strong>'+data.ticket_count+'</strong></td><td> <form data-url="/admin/category-management/activate-subcategory/'+data.subcategory_id+'" method="POST"><button type="submit" class="btn btn-primary btn-sm" onclick="activateSubcategory(event)"><i class="fa fa-plus"></i></button></form></td></tr>');                       
                         var activeBadge = $('#activeBadge'+data.category).html();
                         var inactiveBadge = $('#inactiveBadge'+data.category).html();
                         $('#activeBadge'+data.category).html(Number(activeBadge)-1);
                         $('#inactiveBadge'+data.category).html(Number(inactiveBadge)+1)
                    },
                    error: function(data) {
                    }
               });
}
function activateSubcategory(event) {
     event.preventDefault();
     var form = $(event.target).closest('form');
      $.ajax({
                    type: 'POST',
                    url: form.data('url'),
                    data: form.serialize(),
                    success: function(data){
                         $('#inactivetr'+data.subcategory_id).remove();  
                         $('#activetable'+data.category+' tr:last')
                         .after('<tr id="activetr'+data.subcategory_id+'"><td>'+data.name+'</td><td>'+data.tags+'</td><td>'+data.created_by+'</td><td>'+data.created_at+'</td><td><strong>'+data.ticket_count+'</strong></td><td><form data-url="/admin/category-management/inactivate-subcategory/'+data.subcategory_id+'" method="POST"><button type="submit" class="btn btn-danger btn-sm" onclick="inactivateSubcategory(event)"><i class="fa fa-minus"></i></button></td></tr>');                       
                           var activeBadge = $('#activeBadge'+data.category).html();
                         var inactiveBadge = $('#inactiveBadge'+data.category).html();
                         $('#activeBadge'+data.category).html(Number(activeBadge)+1);
                         $('#inactiveBadge'+data.category).html(Number(inactiveBadge)-1)
                    },
                    error: function(data) {
                    }
               });
}

function inactivateCategory(event) {
     event.preventDefault();
     var form = $(event.target).closest('form');
      $.ajax({
                    type: 'POST',
                    url: form.data('url'),
                    data: form.serialize(),
                    success: function(data){
                         var activeButton = $('#activeButton'+data.category_id);
                         var inactiveButton = $('#inactiveButton'+data.category_id);
                        activeButton.hide();
                        inactiveButton.show();
                    },
                    error: function(data) {
                    }
               });
}

function activateCategory(event) {
     event.preventDefault();
     
     var form = $(event.target).closest('form');
      $.ajax({
                    type: 'POST',
                    url: form.data('url'),
                    data: form.serialize(),
                    success: function(data){
                        var activeButton = $('#activeButton'+data.category_id);
                         var inactiveButton = $('#inactiveButton'+data.category_id);
                        inactiveButton.hide();
                        activeButton.show();
                    },
                    error: function(data) {
                    }
               });
}

function editMode(id) {
     var name = $('#scname'+id);
     var tags = $('#sctags'+id);
     var locm = $('#scloc'+id);
     console.log(locm.html());
     var selected = "";
     if(locm.html() == 'Yes') { selected = "checked"; }
     var form1 = $("<input id='editscname"+id+"' type='text' name='name' value='"+name.html()+"'' class='form-control'>");
     var form2 = $("<input id='editsctags"+id+"' name='tags' type='text' value='"+tags.html()+"' class='form-control'>")
     var form3 = $("<div class='form-inline'><input id='editscloc"+id+"' name='loc_matters' type='checkbox' class='form-control' "+selected+">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='btn btn-success' onclick='saveEdit("+id+")'><i class='fa fa-floppy-o'></i></button>")
     name.html('');
     tags.html('');
     locm.html('');
     form1.appendTo('#scname'+id);
     form2.appendTo('#sctags'+id);
     form3.appendTo('#scloc'+id);
     $('#editMode'+id).attr('onclick', 'viewMode('+id+')');
     $('#editMode'+id).attr('id', 'viewMode'+id);
}

function viewMode(id) {
     var name=$('#editscname'+id).val();
     var tags=$('#editsctags'+id).val();
     var locm=$('#editscloc'+id).prop("checked");
     if(locm == true) { locm = 'Yes'; } else { locm = 'No'; } 
     $('#editscname'+id).remove();
     $('#editsctags'+id).remove();
     $('#editscloc'+id).remove();
     $('#scname'+id).html(name);
     $('#sctags'+id).html(tags);
     $('#scloc'+id).html(locm);
     $('#viewMode'+id).attr('onclick', 'editMode('+id+')');
     $('#viewMode'+id).attr('id', 'editMode'+id);
}

function saveEdit(id) {
     var name=$('#editscname'+id).val();
     var tags=$('#editsctags'+id).val();
     var locm=$('#editscloc'+id).prop("checked");
     
     var values = {
          'name':name,
          'tags':tags,
          'location_matters':locm,
     };
     $.ajax({
                    type: 'POST',
                    url: '/admin/category-management/edit-subcategory/'+id,
                    data: values,
                    success: function(data){
                        $('#editscname'+id).remove();
                        $('#editsctags'+id).remove();
                        $('#editscloc'+id).remove();
                         $('#scname'+id).html(data.name);
                         $('#sctags'+id).html(data.tags);
                         if(data.location_matters == 1) { location_matters = 'Yes'; } else { location_matters = 'No'; }
                         $('#scloc'+id).html(location_matters);
                         $('#viewMode'+id).attr('onclick', 'editMode('+id+')');
                         $('#viewMode'+id).attr('id', 'editMode'+id);
                    },
                    error: function(data) {
                    }
               });

}

//# sourceMappingURL=add-subcategory.js.map
