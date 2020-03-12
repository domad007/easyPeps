$(document).ready(function(){
    addEleve();
    addGroup();
});

function deleteEleve(idEleve, button){
   $(button).remove();
   $('#deleteRow_'+idEleve).remove();
   $.ajax({
       url: '/deleteEleve',
       method: 'POST',
       data: 'eleve='+idEleve
   });
}

function addEleve(){
    $('#addEleve').click(function(){
        const index = $('#add_eleve_eleves div.form-group').length;
        const form = $('#add_eleve_eleves').data('prototype').replace(/__name__/g, index);

        $('#add_eleve_eleves').append(form);
        deleteRowEleve(); 
    });  
}


function deleteRowEleve(){
    $('button[data-action="delete"]').click(function(){
        const target = this.dataset.target;
        $(target).remove();

    });
}

function addGroup(){
    $('#addGroup').click(function(){
        const index = $('#add_group_classes div.form-group').length;
        const form = $('#add_group_classes').data('prototype').replace(/__name__/g, index);
        $('#add_group_classes').append(form);

        deleteRowEleve(); 
    });
}

function presenceEleve(){
    $.ajax({
        url: '/presences',
        method: 'POST',
        data: $('#presence').serialize()
    });

}