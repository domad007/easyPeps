$(document).ready(function(){
    addEleve();
    addGroup();
    addPeriodes();
    addEvaluation();
    addAppreciation();
});

//Suppression de l'élève de la liste des élèves
function deleteEleve(idEleve, button, csrf){
   $(button).remove();
   $('#deleteRow_'+idEleve).remove();
   $.ajax({
       url: '/deleteEleve',
       method: 'POST',
       data: 
        {
           eleve: idEleve,
           csrf: csrf
        }
   });
}

/**
 * Ajout des élèves dans le formulaire
 * Copie le formulaire en nombre illimité
 */
function addEleve(){
    
    $('#addEleve').click(function(){
        const index = $('#add_eleve_eleves div.form-group').length;
        const form = $('#add_eleve_eleves').data('prototype').replace(/__name__/g, index);

        $('#add_eleve_eleves').append(form);
        deleteRowEleve(); 
    });  
}


/**
 * Suppression de la ligne de formulaire lorsqu'on en a crée de trop
 */
function deleteRowEleve(){
    $('button[data-action="delete"]').click(function(){
        const target = this.dataset.target;
        $(target).remove();

    });
}

/**
 * Copie du formulaire permettant de créer les groupes
 */
function addGroup(){
    $('#addGroup').click(function(){
        const index = $('#add_group_classes div.form-group').length;
        const form = $('#add_group_classes').data('prototype').replace(/__name__/g, index);
        $('#add_group_classes').append(form);

        deleteRowEleve(); 
    });
}

/**
 * Copie du formulaire permettant de créer les périodes
 */
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

/**
 * Suppression des lignes de formulaire 
 */
function deleteRowPeriodes(){
    $('button[data-action="delete"]').click(function(){
        const target = this.dataset.target;
        $(target).remove();
    });
}

/**
 * Copie du formulaire permettant de créer les évaluations
 */
function addEvaluation(){
    var index = 0;
    $('#addEvaluation').click(function(){
        index++;
        const form = $('#add_new_evaluations_evaluations').data('prototype').replace(/__name__/g, index);
        $('#add_new_evaluations_evaluations').append(form);

        deleteRowPeriodes();
    });
}

/**
 * Copie du formulaire permettant de créer les appréciations
 */
function addAppreciation(){
    var index = 0;
    $('#addAppreciation').click(function(){
        index++;
        const form = $('#new_appreciation_ecole_appreciations').data('prototype').replace(/__name__/g, index);
        $('#new_appreciation_ecole_appreciations').append(form);

        deleteRowPeriodes();
    });
}

/**
 * Modificaiton dynamique des présences des élèves
 * @param {*} value 
 */
function presenceEleve(value, csrf){
    $.ajax({
        url: '/presences',
        method: 'POST',
        data: {
            csrf: csrf,
            presence: $(value).val()
        }
    });

}

/**
 * Modificaiton dynamique par rapport aux  présences customisés par l'utilisateur des élèves
 * @param {*} value 
 */
function presenceEleveCustomized(value, csrf){
    $.ajax({
        url: '/presencesCustomized',
        method: 'POST',
        data: {
            presence: $(value).val(),
            csrf: csrf
        }
    });

}


/**
 * Modification dynamique de la compétence 
 * @param {*} value 
 */
function modifCompetence(value, csrf){
    $.ajax({
        url: '/changementCompetence',
        method: 'POST',
        data: {
            competence: $(value).val(), 
            csrf: csrf
        }
    });
}

/**
 * Affichage ou cachement des évaluations ou cours dans le journal de classe
 */
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

/**
 * Affichage ou cachement des évaluations ou cours dans le journal de classe par rapport à la période
 */
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

/**
 * Affichage d'erreur lorsque l'utilisateur veut créer un cours mais n'as pas crée de période
 */
function periodeError(){
    alert("Aucune période existe, veuillez en créer une avant de créer une évaluation ou un cours!");
}

/**
 * Modificaiton dynamique des appréciations dans les paramètres
 * @param {*} property 
 * @param {*} idType 
 */
function modifAppreciation(property, idType, csrf){
    if(property.checked){
        $.ajax({
            url: '/modifAppreciationCahier',
            method: 'POST',
            data: {
                id: idType,
                appreciation: true,
                csrf: csrf
            }
        });
    }
    else {
        $.ajax({
            url: '/modifAppreciationCahier',
            method: 'POST',
            data: {
                id: idType,
                appreciation: 0,
                csrf: csrf
            }
        });
    }
}

/**
 * Ajout du rôle d'administrateur à l'utilisateur
 * @param {} property 
 * @param {*} idUser 
 */
function addRole(property, idUser, csrf){
    if(property.checked){
        $.ajax({
            url: '/addRoleAdmin',
            method: 'POST',
            data: {
                userId: idUser,
                admin: true,
                csrf: csrf
            }
        });
    }
    else {
        $.ajax({
            url: '/addRoleAdmin',
            method: 'POST',
            data: {
                userId: idUser,
                admin: false,
                csrf: csrf
            }
        });
    }
}

/**
 * Message d'information pour voir si l'utilisateur est sur de désactiver son compte
 * @param {} property 
 */
function desactivate(property){
    if(property.checked){
        $('#desactivate').html('<h6>Êtes vous sur de désactiver votre profil ?</h6>').dialog({
            modal: true, title: 'Confirmation', zIndex: 10000, autoOpen: true,
            width: 'auto', resizable: false,
            buttons: {
              Oui: function () {
                  $(this).dialog("close");
              },
              Non: function () {
                $('#compte_userActif').prop('checked', false);
                $(this).dialog("close");
              }
            },
            close: function (event, ui) {
                $(this).remove();
            }
      });
    }
}

