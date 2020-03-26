$(document).ready(function(){
    addEleve();
    addGroup();
    addPeriodes();
    addEvaluation();
    coursEvaluationPeriode;
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

function addPeriodes(){
    $('#addPeriodes').click(function(){
        const index = $('#group_periode_periodes div.form-group').length;
        const form = $('#group_periode_periodes').data('prototype').replace(/__name__/g, index);
        $('#group_periode_periodes').append(form);

        deleteRowPeriodes();
    });
}

function deleteRowPeriodes(){
    $('button[data-action="delete"]').click(function(){
        const target = this.dataset.target;
        $(target).remove();
    });
}

function addEvaluation(){
    var index = 0;
    $('#addEvaluation').click(function(){
        index++;
        const form = $('#add_new_evaluations_evaluations').data('prototype').replace(/__name__/g, index);
        $('#add_new_evaluations_evaluations').append(form);

        deleteRowPeriodes();
    });
}
function presenceEleve(value){
    $.ajax({
        url: '/presences',
        method: 'POST',
        data: $(value)
    });

}

function modifCompetence(value){
    $.ajax({
        url: '/changementCompetence',
        method: 'POST',
        data: $(value)
    });
}

/*function gestionEvaluation(property){
    if(property.checked){
        $('.evalThead').fadeIn('slow');
        $('.evalTbody').fadeIn('slow');
    } 
    else {
        $('.evalThead').fadeOut('slow'); 
        $('.evalTbody').fadeOut('slow');
    }
}

function gestionPeriodes(property, id){
    if(property.checked){
        $('.periode'+id).fadeIn('slow');
        $('.periode'+id).fadeIn('slow');
    } 
    else {
        $('.periode'+id).fadeOut('slow'); 
        $('.periode'+id).fadeOut('slow');
    }
}*/
function coursEvaluation(){
    switch($('input[name=checkboxCours]').is(':checked')){
        case false : 
            $('.periodeCours').fadeOut('slow');
        break;
        case true : 
            $('.periodeCours').fadeIn('slow');
        break;
    }

    switch($('input[name=checkboxEval]').is(':checked')){
        case false : 
            $('.periodeEval').fadeOut('slow');
           
        break;
        case true : 
            $('.periodeEval').fadeIn('slow');
        break;
    }
}

function coursEvaluationPeriode(property= null, idPeriode = null){
    if(!property.checked){
        $('.periodeEval'+idPeriode).fadeOut('slow');
        $('.periodeCours'+idPeriode).fadeOut('slow');
    }
    if(property.checked && $('input[name=checkboxCours]').is(':checked') && $('input[name=checkboxEval]').is(':checked')){
        $('.periodeEval'+idPeriode).fadeIn('slow');
        $('.periodeCours'+idPeriode).fadeIn('slow');
    }
    if(property.checked && !$('input[name=checkboxCours]').is(':checked') && $('input[name=checkboxEval]').is(':checked')){
        $('.periodeEval'+idPeriode).fadeIn('slow');
    }
    if(property.checked && $('input[name=checkboxCours]').is(':checked') && !$('input[name=checkboxEval]').is(':checked')){ 
        $('.periodeCours'+idPeriode).fadeIn('slow');
    }

}