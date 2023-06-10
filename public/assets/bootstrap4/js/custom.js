function checkTime(i) {
    if (i < 10){ i = "0" + i ;}
    return i;
}

$(document).ready(function() {
    $('body').onload(function () {
        startTime();
    })

    $('#container').addClass('hidde');

    $('.del-btn').click(function () {
        $('#all-checked').prop("checked", false);
        // $(this).closest('.row-checkbox').prop('checked', true);
        $('.row-checkbox').prop("checked", false);
        $(this).parents('tr').children('td').children('input').prop('checked', true);
    });

    var remove_button = '<button class="btn btn-danger pull-right multiple-delete" type="button" data-toggle="modal" href="#multiple-delete-modal">Eliminar varios <span class="glyphicon glyphicon-trash"></span> </button>';
    $('#all-checked').click(function () {


        if($('#all-checked').is(':checked')){
            $('.row-checkbox').prop("checked", true);
        } else {
            $('.row-checkbox').prop("checked", false);
        }
        var cant = $('#datos').find('.row-checkbox:checked').length;
        if(cant > 1){
            $('.btn-load').parent().children('.multiple-delete').remove();
            $('.btn-load').parent().prepend(remove_button);
        } else {
            $('.btn-load').parent().children('.multiple-delete').remove();
        }
    });

    $('.row-checkbox').click(function () {
        var cant = $('#datos').find('.row-checkbox:checked').length;
        var all = $('#datos').find('.row-checkbox').length;
        if(cant != all){
            $('#all-checked').prop("checked", false);
        } else {
            $('#all-checked').prop("checked", true);
        }
    });

    $('.switch-fin-contrato').click(function () {

        if($('#fecha-fin-contrato').is(':checked')){
            $('.fecha-fin-contrato').prop('disabled', true);
        } else {
            $('.fecha-fin-contrato').prop('disabled', false);
        }


    });

    var html2 = '<div id="container"><div class="lds-ellipsis"><div></div><div></div><div></div></div></div>';

    $('form').submit(function () {
        $('body').prepend(html2);
    });

    $('.btn-load').click(function () {
        $('body').prepend(html2);
    });

    $('ul li a:not([href="#"])').click(function () {
        $('body').prepend(html2);
    });

    $('.row-checkbox').click(function () {
        var cant = $('#datos').find('.row-checkbox:checked').length;
        $('#counter').html(cant);
        if(cant > 1){
            $('#page-title').children('.multiple-delete').remove();
            $('#page-title').append(remove_button);
        } else {
            $('#page-title').children('.multiple-delete').remove();
        }
    });

    $('.mp-btn-remove').click(function () {
        $(this).parent().parent().remove();
    });

    $('.select-area').change(function(){
        $('form[name="form"]').submit();
    });

    $('#datos').DataTable({
        language: {
            "decimal": "",
            "emptyTable": "No hay información",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ resultados",
            "infoEmpty": "Mostrando 0 a 0 de 0 entradas",
            "infoFiltered": "(Filtrado de _MAX_ total entradas)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ resultados",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar: ",
            "zeroRecords": "No se encontraron resultados",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        }
    });
} );
