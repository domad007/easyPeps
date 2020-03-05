$(document).ready(function(){
    $('#table p').editable();
    //deleteEleve();
});

function deleteEleve(idEleve, button){
   $(button).remove();
   $('#'+idEleve).remove();
   $.ajax({
       url: '/deleteEleve',
       method: 'POST',
       data: 'eleve='+idEleve
   });
}