$(document).ready(function() {


    $('#find-start-end-date').click(function () {
        var fecha_inicio = $('#find-start-end-date-form [name="fecha_inicio"]').val();
        var fecha_fin = $('#find-start-end-date-form [name="fecha_fin"]').val();
        if(!fecha_inicio && !fecha_fin){
            showToastMessage('error', 'No puede hacer una búsqueda sin fecha(s)');
            return false;
        }
        if(fecha_inicio > fecha_fin && fecha_fin){
            showToastMessage('error', 'La fecha de fin no puede ser menor que la fecha de inicio');
            return false;
        }
    });

    $('#find-date').click(function () {
        console.log('click date');
    });

    $('#qrModal').on('shown.bs.modal', function () {
        $('#qrName').focus();
    });

    //Boton Buscar en ORDEN DE SERVICIO
    $('#buscarOrdenFecha').click(function () {
        $('#buscarOrdenFecha-wrapper').css('display','block');
    });

    $('#closeBuscarOrdenFecha').click(function () {
        $('#buscarOrdenFecha-wrapper').css('display','none');
    });

    // Index Delete Button and Modal
    $('#indexDelete').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var action = button.data('form-action') // Extract info from data-* attributes
        // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
        // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other
        var modal = $(this)
        modal.find('.modal-footer form').attr('action', action);
    })
    // Select MDB
    $('.mdb-select').materialSelect({
        multiple: true,
        searchable: "Departamento",
        labels: {
            noSearchResults: 'No hay resultados',
            selectAll: 'Seleccionar todos'
        },
        visibleOptions: 10,
        copyClassesOption: true
    });
    //Alerts
    $( "div.alert" ).fadeIn( 300 ).delay( 5000 ).fadeOut( 300 );

   // SideNav Default Options
    $('.button-collapse').sideNav({
        edge: 'left', // Choose the horizontal origin
        closeOnClick: false, // Closes side-nav on &lt;a&gt; clicks, useful for Angular/Meteor
        breakpoint: 1440, // Breakpoint for button collapse
        menuWidth: 240, // Width for sidenav
        timeDurationOpen: 300, // Time duration open menu
        timeDurationClose: 200, // Time duration open menu
        timeDurationOverlayOpen: 50, // Time duration open overlay
        timeDurationOverlayClose: 200, // Time duration close overlay
        easingOpen: 'easeOutQuad', // Open animation
        easingClose: 'easeOutCubic', // Close animation
        showOverlay: true, // Display overflay
        showCloseButton: false // Append close button into siednav
    });
    var el = document.querySelector('.custom-scrollbar');
    var ps = new PerfectScrollbar(el);

    $(".image-input").change(function(){
        readURL(this);
    });

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

    $('.spinner-button').click(function () {
        $('body').prepend(html2);
    });
    // $('.act-spinner').click(function () {
    //     $('body').prepend(html2);
    // });

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

    // Inicializar reloj del sistema
    startTime();

    $('.mp-btn-remove').click(function () {
        $(this).parent().parent().remove();
    });

    $('.select-area').change(function(){
        $('form[name="form"]').submit();
    });

    $(".select-options-trabajador_provincia").change(function(){
        // var data = {
        //     provincia_id: $(this).val()
        // };
        //
        // $('body').prepend(html2);
        // $.ajax({
        //     type: 'POST',
        //     url: "{{ path('listar_municipios') }}",
        //     data: data,
        //     success: function(data) {
        //
        //         var $muni_selector = $('.select-municipio');
        //
        //         for (var i=0, total = data.length; i < total; i++) {
        //             $muni_selector.append('<option value="' + data[i].id + '">' + data[i].nombre + '</option>');
        //         }
        //     }
        // });
        // $('#container').remove();
        alert('select-options-trabajador_provincia');
    });
    /******** DATA TABLES ***************/
    $('#datos-multi-select-desc-0').dataTable(
        {
            "order" : [0, 'desc'],
            "language": {
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
        },
        {
            aoColumnDefs: [{
                bSortable: false,
                aTargets: [-1]
            }]
        },
        {
            columnDefs: [{
                orderable: false,
                className: 'select-checkbox select-checkbox-all',
                targets: 0
            }],
            select: {
                style: 'multi',
                selector: 'td:first-child'
            }
        });

    $('#datos-multi-select').dataTable(
        {
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
        },
        {
            aoColumnDefs: [{
                bSortable: false,
                aTargets: [-1]
            }]
        },
        {
        columnDefs: [{
            orderable: false,
            className: 'select-checkbox select-checkbox-all',
            targets: 0
        }],
        select: {
            style: 'multi',
            selector: 'td:first-child'
        }
    });
    $('#datos-multi-select-all').dataTable(
        {
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
        },
        {
        columnDefs: [{
            orderable: false,
            className: 'select-checkbox',
            targets: 0
        }],
        select: {
            style: 'multi',
            selector: 'td:first-child'
        }
    });

    $('#datos').dataTable(
        {
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
        },
        {
            initComplete: function () {
                this.api().columns().every(function () {
                    var column = this;
                    var select = $('<select  class="browser-default custom-select form-control-sm"><option value="" selected>Buscar</option></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );

                            column
                                .search(val ? '^' + val + '$' : '', true, false)
                                .draw();
                        });

                    column.data().unique().sort().each(function (d, j) {
                        select.append('<option value="' + d + '">' + d + '</option>')
                    });
                });
            }
        }
    );

    $('#datos_wrapper').find('label').each(function () {
        $(this).parent().append($(this).children());
    });
    $('#datos_wrapper .dataTables_filter').find('input').each(function () {
        const $this = $(this);
        $this.attr("placeholder", "Buscar");
        $this.removeClass('form-control-sm');
    });
    $('#datos_wrapper .dataTables_length').addClass('d-flex flex-row');
    $('#datos_wrapper .dataTables_filter').addClass('md-form');
    $('#datos_wrapper select').removeClass('custom-select custom-select-sm form-control form-control-sm');
    $('#datos_wrapper select').addClass('mdb-select');
    $('#datos_wrapper .mdb-select').materialSelect();
    $('#datos_wrapper .dataTables_filter').find('label').remove();

    $('.datepicker').pickadate({
        // Escape any “rule” characters with an exclamation mark (!).

        // Strings and translations
        monthsFull: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre',
            'Noviembre', 'Diciembre'],
        monthsShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        weekdaysFull: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        weekdaysShort: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
        showMonthsShort: true,
        showWeekdaysFull: undefined,
        firstDay: 1,

// Buttons
        today: 'Hoy',
        clear: 'Limpiar',
        close: 'Cerrar',

// Accessibility labels
        labelMonthNext: 'Mes siguiente',
        labelMonthPrev: 'Mes anterior',
        labelMonthSelect: 'Seleccione un mes',
        labelYearSelect: 'Seleccione un año',

//Formatf
        format: 'dd-mm-yyyy',
        formatSubmit: 'dd-mm-yyyy',
        // hiddenPrefix: 'prefix__',
        // hiddenSuffix: '__suffix'

//Other
        max: true,
        // disable: [1]
    })

/////
    // Data tables
    $('#datos-multi-select-all').parent().css('overflow-x', 'scroll');
    $('#datos-multi-select').parent().css('overflow-x', 'scroll');
    $('#datos-multi-select-all-desc-0').parent().css('overflow-x', 'scroll');
    $('#datos').parent().css('overflow-x', 'scroll');

});