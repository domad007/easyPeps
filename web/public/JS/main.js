$(document).ready(function(){
    $('#table a').editable();
    //deleteEleve();
});

function deleteEleve(data, buton){
    //console.log(data);
   $(buton).remove();
   $('#'+data).remove();
}