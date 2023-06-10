function fnSelectAtoB(){
    var $select = $('.form-origen');
    var $selectB = $('.form-destino');
    $('option:selected',$select).appendTo('.form-destino');
    $('option:selected',$select).remove();
    $('option:selected',$selectB).removeAttr('selected');
    //$('#cliente-seleccionado').append($('#cliente-lista').selectedOptions);
}
function fnSelectBtoA(){
    var $select = $('.form-destino');
    var $selectA = $('.form-origen');
    $('option:selected',$select).appendTo('.form-origen');
    $('option:selected',$select).remove();
    $('option:selected',$selectA).removeAttr('selected');
    //$('#cliente-seleccionado').append($('#cliente-lista').selectedOptions);
}

function fnSelectAllAtoB(){
    var $select = $('.form-origen');
    $('option',$select).appendTo('.form-destino');
    $('option',$select).remove();
    //$('#cliente-seleccionado').append($('#cliente-lista').selectedOptions);
}

function fnSelectAllBtoA(){
    var $select = $('.form-destino');
    $('option',$select).appendTo('.form-origen');
    $('option',$select).remove();
    //$('#cliente-seleccionado').append($('#cliente-lista').selectedOptions);
}

function showContainer() {
    $('#container').show();
}

function hideContainer() {
    $('#container').hide();
}