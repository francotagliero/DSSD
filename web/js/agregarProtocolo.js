$('.agregarProtocolo').hide();
$('.botonAgregarProtocolo').click(function(){
    $('.agregarProtocolo').show("slow");
    $('.botonAgregarProtocolo').hide('slow');
});

$(".agregarActividad").click(function(){

    var div = $('.actividad');

    var borrar = $('.borrar');

    var campoId = $(".actividad input").length + 1; //Asigno un id dinamico a cada input

    var campoCantidad = $("<input type=\"text\" class=\"form-control actividad_"+campoId+"\" name=\"actividad[" + campoId + "]\" id=\"actividad_" + campoId + "\" placeholder=\"Ingrese una actividad\" style=margin-top:10px required>")

    var eliminarCampo = $("<a type=\"button\" data-id="+campoId+"  class=\"btn btn-danger actividad_"+campoId+" eliminarActividad\" style=margin-top:10px><i class=\"fas fa-minus fa-xs\"></i></a>")

    eliminarCampo.click(function() {
        $(this).closest('.form-group').find('.actividad_'+campoId).remove();
    });

    div.append(campoCantidad);
    borrar.append(eliminarCampo);
});
