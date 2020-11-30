$('.conf').click(function(event){
    event.preventDefault();
    //var dato = $('#idResponsable').val();
    //console.log(dato);
    var id_submit = $(this).parent().attr('id');
    var id_form = '#formConfiguracionProtocolo' + id_submit;
    console.log(id_form);
    
    $.ajax({
        url: './?action=configurarProtocoloBD',
        type: 'POST',
        dataType:'json',
        data: $(id_form).serialize(),
        success: function (respuesta) {
          if(respuesta.valor == 'protocoloActualizado'){
              /*
              $('#cartelError').addClass('hidden');
              $("html, body").animate({ scrollTop: 0 }, 0);
              $('#cartelExito').removeClass('hidden');
              $(':input','#form-historia')
               .not(':button, :submit, :reset, :hidden')
               .val('');
              //$('#apellido').focus();
              $('.form-group').removeClass('has-success');
              */
                alert(respuesta.id_protocolo + 'hola' + respuesta.id_responsable);
            } else{
               //$("html, body").animate({ scrollTop: 0 }, 0);
               //$('#cartelError').removeClass('hidden');
               //$('#textoError').text(respuesta.mensaje);
               alert("Falta pa");
            }
          },


    });
    
    
});