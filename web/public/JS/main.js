$(document).ready(function(){
    addEleve();
    addGroup();
    addPeriodes();
    addEvaluation();
    addAppreciation();
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
    var index = 0;
    $('#addPeriodes').click(function(){
        index++;
        //const index = $('#group_periode_periodes div.form-group').length;
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

function addAppreciation(){
    var index = 0;
    $('#addAppreciation').click(function(){
        index++;
        const form = $('#new_appreciation_ecole_appreciations').data('prototype').replace(/__name__/g, index);
        $('#new_appreciation_ecole_appreciations').append(form);

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

function presenceEleveCustomized(value){
    $.ajax({
        url: '/presencesCustomized',
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

function periodeError(){
    alert("Aucune période existe, veuillez en créer une avant de créer une évaluation ou un cours!");
}

function modifAppreciation(property, idType){
    if(property.checked){
        $.ajax({
            url: '/modifAppreciationCahier',
            method: 'POST',
            data: {
                id: idType,
                appreciation: true
            }
        });
    }
    else {
        $.ajax({
            url: '/modifAppreciationCahier',
            method: 'POST',
            data: {
                id: idType,
                appreciation: 0
            }
        });
    }
}

function addRole(property, idUser){
    if(property.checked){
        $.ajax({
            url: '/addRoleAdmin',
            method: 'POST',
            data: {
                userId: idUser,
                admin: true
            }
        });
    }
    else {
        $.ajax({
            url: '/addRoleAdmin',
            method: 'POST',
            data: {
                userId: idUser,
                admin: false
            }
        });
    }
}
