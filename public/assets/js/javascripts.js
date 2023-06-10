/**
 * Created by SONY on 29/07/2017.
 */
/* Global variable for the DataTables object */
 var cont = 1;

/**/
var oTable;
var asInitVals = new Array();

$(document).ready(function() {

    /**************************/
    $('#datos').dataTable({
        "sPaginationType": "full_numbers"
    });
    $('#otros-datos').dataTable({
        "sPaginationType": "full_numbers"
    });
    $('.dataTables-paginate span:nth-child(3)').addClass('btn-group');
    /*
     * Support functions to provide a little bit of 'user friendlyness' to the textboxes in
     * the footer
     */

} );
/**/

function fnClickAddRow() {
    if(cont != 10){
    cont++;
    var final = '"></td>'
    var tr_open = '<tr>';

    var nombre = '<td><input type="text" class="form-control input-sm" name="nombre_pr_' + cont.toString() + final;

    var ci = '<td><input type="text" maxlength="11" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"  pattern="[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]|[]" title="El CI tiene solo n&uacute;meros y una longitud de 11 dig&iacute;tos"  class="form-control input-sm" name="ci_pr_' + cont.toString() + final;
    var cargo = '<td><input type="text" class="form-control input-sm" name="cargo_pr_' + cont.toString() + final;
    var telefono = '<td><input type="text" class="form-control input-sm" name="telefono_pr_' + cont.toString() + final;
    var email = '<td><input multiple type="email" class="form-control input-sm" name="email_pr_' + cont.toString() + final;
    var facturador = '<td><input type="checkbox" class="checkbox-ficha" name="facturador_pr_' + cont.toString() + final;
    var directivo = '<td><input type="checkbox" class="checkbox-ficha" name="directivo_pr_' + cont.toString() + final;
    var contacto = '<td><input type="checkbox"  class="checkbox-ficha" name="contacto_pr_' + cont.toString() + final;
    var vacio = '<td></td>';
    var tr_close = '</tr>';

    var total = tr_open + nombre + ci + cargo + telefono + email + facturador + directivo + contacto + vacio + tr_close;
    $(total).appendTo('#pr');
    }
    $('#cant-pr').attr('value',cont);
}
function fnClickAddRowContrato() {
    if(contar != 20){
        contar++;
        var final = '"></td>'
        var final2 = '">'
        var tr_open = '<tr>';
        var inciso = '<td><input type="text" class="form-control input-sm"placeholder="a" list="inciso" pattern="[a-z]" title="Es una letra del abecedario(a..z) en m&iacute;nuscula" name="inciso_h_' + contar.toString() + final;
        var nopgespro = '<td><input type="number" class="form-control input-sm"placeholder="12345"  name="nopgespro_h_' + contar.toString() + final;
        var entregable = '<td><input type="text" class="form-control input-sm"placeholder="a la entrega del informe de incidencia" name="entregable_h_' + contar.toString() + final;
        var ingresocuc = '<td><input type="text" class="form-control input-sm" value="0.00" name="ingresocuc_h_' + contar.toString() + final;
        var ingresocup = '<td><input type="text" class="form-control input-sm"value="0.00"  name="ingresocup_h_' + contar.toString() + final;
        var ingresomlc = '<td><input type="text" class="form-control input-sm" value="0.00" name="ingresomlc_h_' + contar.toString() + final;
        var estado = '<td><span class="form-control input-sm" readonly="readonly">Planificado</span></td>';
        var centro = '<td><span class="form-control input-sm" readonly="readonly">Igual al contrato</span></td>';

        var vacio = '<td></td>';
        var tr_close = '</tr>';

        var total = tr_open + inciso+ nopgespro + entregable + ingresocuc + ingresocup + ingresomlc  +estado +centro+vacio + tr_close;
            $(total).appendTo('#pr');

    }
    $('#cant-hito').attr('value',contar);
}

function fnClickAddRowDocumento() {
    if(contdoc != 10){
        contdoc++;
        var finaldocumento = '"></td>'
        var tr_open = '<tr>';

        var no = '<td><span class="form-control input-sm">'+contdoc.toString()+'</span></td>';
        var nombredoc = '<td><input type="text" class="form-control input-sm" name="nombre_doc_' + contdoc.toString() + finaldocumento;
        var observaciones = '<td><input type="text" class="form-control input-sm" name="observaciones_' + contdoc.toString() + finaldocumento;
        var cantejemplares = '<td><input type="number" class="form-control input-sm" name="cant_ejemplares_' + contdoc.toString() + finaldocumento;
        var vacio = '<td></td>';
        var tr_close = '</tr>';

        var totaldocumento = tr_open+no+nombredoc + observaciones+cantejemplares+ vacio + tr_close;
        $(totaldocumento).appendTo('#doc');
    }
    $('#cant-doc').attr('value',contdoc);
}

function fnSelectAtoB(){
    alert("as");
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

function fnSelectAllBeforeSubmit(){
    var $select = $('#cliente-seleccionado');
    $('option',$select).attr('selected','selected');
}

function fnSelectAllAtoB(){
    var $select = $('#cliente-lista');
    $('option',$select).appendTo('#cliente-seleccionado');
    $('option',$select).remove();
    //$('#cliente-seleccionado').append($('#cliente-lista').selectedOptions);
}

function fnSelectAllBtoA(){
    var $select = $('#cliente-seleccionado');
    $('option',$select).appendTo('#cliente-lista');
    $('option',$select).remove();
    //$('#cliente-seleccionado').append($('#cliente-lista').selectedOptions);
}


function fnForm1MAction(){
    $('#procesar').attr('action',$('#form1').attr('action'));
}

function fnForm2MAction(){
    $('#procesar').attr('action',$('#form2').attr('action'));
}

function fnForm3CAction(){
    $('#procesar').attr('action',$('#form1_crear').attr('action'));
}

function fnForm4CAction(){
    $('#procesar').attr('action',$('#form2_crear').attr('action'));
}

function fnAgregarInput(hito) {
    var cantejemplares = '<tr id="display-none"><td><input type="number" class="form-control input-sm" name="id_hito" value="' + hito.toString() + '"></td></tr>';
    $(cantejemplares).appendTo('#pr');
    $('#procesar').attr('action',$('#form2').attr('action'));
}

function fnUpdateDireccion(){
    $cliente = $('select[name=id_cliente]').val();
    if($cliente == "Ninguno"){
        document.getElementById('direccion').style.display = '';
        document.getElementById('direccion_input').required= true ;
    }else if($cliente == ""){
        document.getElementById('direccion').style.display='None';
        document.getElementById('direccion_input').required= false ;
    }else{
        document.getElementById('direccion').style.display='None';
        document.getElementById('direccion_input').required= false ;
    }

}

//funcion onclick
function fnViexAction(valor){
    if (valor=='encuentro-solicitado'){
    $('encuentro-solicitado').class( style="background-color: #0000F0; display: none!important;");
}

}
function fnUpdateEstadoSolicitud(){
    if ($('select[name=estado]').val()=="Cancelada" || $('select[name=estado]').val()=="Cumplida"){
        document.getElementById('descripcion').required= true ;
    }else{
        document.getElementById('descripcion').required= false ;
    }
}

function fnUpdateEstadoActaOcupacion(){
   if ($('select[name=estado_acta]').val()=="Cancelada"){
        document.getElementById('nota').required= true ;
        document.getElementById('textbox').required= false ;
        document.getElementById('recibido_por_abogado').required= false ;
        document.getElementById('nombre_recibe').required= false ;
        document.getElementById('cliente-seleccionado').required= false ;
        document.getElementById('contratos-acta').style.display = 'None' ;
        document.getElementById('datos-entrega').style.display = 'None';
    }else if($('select[name=estado_acta]').val()=="Entregada para archivar"){
        document.getElementById('textbox').required= true ;
        document.getElementById('recibido_por_abogado').required= true ;
        document.getElementById('nota').required= false ;
        document.getElementById('cliente-seleccionado').required= true ;
        document.getElementById('nombre_recibe').required= true ;
        document.getElementById('contratos-acta').style.display = 'None' ;
        document.getElementById('datos-entrega').style.display = '';
    }else if($('select[name=estado_acta]').val()=="Entregada"){
        document.getElementById('nota').required= false ;
        document.getElementById('textbox').required= false ;
        document.getElementById('recibido_por_abogado').required= false ;
        document.getElementById('cliente-seleccionado').required= false ;
        document.getElementById('nombre_recibe').required= true ;
        document.getElementById('contratos-acta').style.display = 'None' ;
        document.getElementById('datos-entrega').style.display = 'None';
    }else{
        document.getElementById('nota').required= false ;
        document.getElementById('textbox').required= false ;
        document.getElementById('recibido_por_abogado').required= false ;
        document.getElementById('nombre_recibe').required= false ;
        document.getElementById('cliente-seleccionado').required= true ;
        document.getElementById('contratos-acta').style.display = '' ;
        document.getElementById('datos-entrega').style.display = 'None';
    }
}


function fnUpdate(){
    $tipo = $('select[name=tipo_solicitud]').val();
    if($tipo == "Encuentro"){
        document.getElementById('tipo-encuentro').style.display = '';
        document.getElementById('fecha-encuentro').style.display = '';
        document.getElementById('documento').style.display       = 'None';
        document.getElementById('fecha').style.display       = 'None';
        document.getElementById('tipo_encuentro').required= true ;
        document.getElementById('fecha_encuentro').required= true ;
        document.getElementById('fecha_input').required= false ;
        document.getElementById('documento_input').required= false ;


    }else if($tipo == ""){
        document.getElementById('tipo-encuentro').style.display='None';
        document.getElementById('documento').style.display='None';
        document.getElementById('fecha-encuentro').style.display = 'None';
        document.getElementById('fecha').style.display = 'None';
        document.getElementById('tipo_encuentro').required= false ;
        document.getElementById('fecha_encuentro').required= false ;
        document.getElementById('fecha_input').required= false ;
        document.getElementById('documento_input').required= false ;



    }else{
        document.getElementById('tipo-encuentro').style.display='None';
        document.getElementById('fecha-encuentro').style.display = 'None';
        document.getElementById('fecha').style.display = '';
        document.getElementById('documento').style.display='';
        document.getElementById('tipo_encuentro').required= false ;
        document.getElementById('fecha_encuentro').required= false ;
        document.getElementById('fecha_input').required= true ;
        document.getElementById('documento_input').required= true ;
    }
}


function fnUpdateEstadoContratoFirmado() {
    $tipo = $('select[name=estado_contrato]').val();
    if ($tipo == "Cancelado") {
        document.getElementById('descripcion').style.display = '';
        document.getElementById("descripcion_cancelacion").required=true;
    } else {
        document.getElementById('descripcion').style.display = 'None';
        document.getElementById("descripcion_cancelacion").required=false;
    }
}
function fnUpdateEstadoContratoARevision() {
    $tipo = $('select[name=estado]').val();
    if ($tipo == "Cancelado") {
        document.getElementById('descripcion').style.display = '';
        document.getElementById('botones').style.display = '';
        document.getElementById("descripcion_cancelacion").required=true;
    } else {
        document.getElementById('botones').style.display = 'None';
        document.getElementById('descripcion').style.display = 'None';
        document.getElementById("descripcion_cancelacion").required=false;
    }
}


function fnUpdateCancelacionContrato(){
    $estado = $('select[name=estado]').val();
    if($estado == "Cancelado"){
        document.getElementById("descripcion").required=true;
    }else{
        document.getElementById("descripcion").required=false;
    }
}

function fnUpdateTipoContrato(){
    $t = $('select[name=tipo]').val();
    if($t == "Proforma"){
        document.getElementById("idcliente").required=false;
        document.getElementById("proveedor").required=false;
        document.getElementById("no_c").add(patt);
        document.getElementById('id_cliente').style.display = 'None';
        document.getElementById('div_proveedor').style.display = 'None';
    }else if( $t =='Patrocinio' || $t =='Otro' || $t =='Contrato Servicio Proveedor'){
        document.getElementById("idcliente").required=false;
        document.getElementById("proveedor").required=true;
        document.getElementById('id_cliente').style.display = 'None';
        document.getElementById('div_proveedor').style.display = '';
    }else{
        document.getElementById("idcliente").required=true;
        document.getElementById("proveedor").required=false;
        document.getElementById('id_cliente').style.display = '';
        document.getElementById('div_proveedor').style.display = 'None';
    }
}
function fnUpdateClienteSolicitudFactura() {
    $t = $('select[name=id_cliente]').val();
    if ($t == "") {
        document.getElementById("eje_estrategico").required = true;
        document.getElementById("cliente").required = true;
        document.getElementById("pais").required = true;
        document.getElementById("reeup").required = true;
        document.getElementById('datos_cliente').style.display = '';
        document.getElementById('div_pais').style.display = '';
        document.getElementById('div_reeup').style.display = '';
    }else{
        document.getElementById("eje_estrategico").required = false;
        document.getElementById("cliente").required = false;
        document.getElementById("pais").required = false;
        document.getElementById("reeup").required = false;
        document.getElementById('datos_cliente').style.display = 'None';
        document.getElementById('div_reeup').style.display = 'None';
        document.getElementById('div_pais').style.display = 'None';
    }
}


function fnUpdateTipoContratoFirmado(){
    $t = $('select[name=tipo]').val();
    if($t == "Proforma"){
        document.getElementById("idcliente").required=false;
        document.getElementById("centro").required=false;
        document.getElementById("proveedor").required=false;
        document.getElementById('id_cliente').style.display = 'None';
        document.getElementById('div_centro').style.display = 'None';
        document.getElementById('div_proveedor').style.display = 'None';

    }else if( $t =='Patrocinio' || $t =='Otro' || $t =='Contrato Servicio Proveedor'){
        document.getElementById("idcliente").required=false;
        document.getElementById("proveedor").required=true;
        document.getElementById("centro").required=false;
        document.getElementById('id_cliente').style.display = 'None';
        document.getElementById('div_centro').style.display = 'None';
        document.getElementById('proy_gespro').style.display = 'None';
        document.getElementById('div_proveedor').style.display = '';
    }else{
        document.getElementById("idcliente").required=true;
        document.getElementById("proveedor").required=false;
        document.getElementById("centro").required=true;
        document.getElementById('id_cliente').style.display = '';
        document.getElementById('div_centro').style.display = '';
        document.getElementById('proy_gespro').style.display = '';
        document.getElementById('div_proveedor').style.display = 'None';
        document.getElementById('no_c').pattern="[0-9]/[0-9][0-9][0-9][0-9]|[0-9]/[0-9][0-9][0-9][0-9]_[0-9]|[0-9]/[0-9][0-9][0-9][0-9]_[0-9]_[0-9]|[0-9]/[0-9][0-9][0-9][0-9]_[0-9]_[0-9][0-9]|[0-9]/[0-9][0-9][0-9][0-9]_[0-9][0-9]|[0-9]/[0-9][0-9][0-9][0-9]_[0-9][0-9]_[0-9]|[0-9]/[0-9][0-9][0-9][0-9]_[0-9][0-9]_[0-9][0-9]|[0-9][0-9]/[0-9][0-9][0-9][0-9]|[0-9][0-9]/[0-9][0-9][0-9][0-9]_[0-9]|[0-9][0-9]/[0-9][0-9][0-9][0-9]_[0-9]_[0-9]|[0-9][0-9]/[0-9][0-9][0-9][0-9]_[0-9]_[0-9][0-9]|[0-9][0-9]/[0-9][0-9][0-9][0-9]_[0-9][0-9]|[0-9][0-9]/[0-9][0-9][0-9][0-9]_[0-9][0-9]_[0-9]|[0-9][0-9]/[0-9][0-9][0-9][0-9]_[0-9][0-9]_[0-9][0-9]|[0-9][0-9][0-9]/[0-9][0-9][0-9][0-9]|[0-9][0-9][0-9]/[0-9][0-9][0-9][0-9]_[0-9]|[0-9][0-9][0-9]/[0-9][0-9][0-9][0-9]_[0-9]_[0-9]|[0-9][0-9][0-9]/[0-9][0-9][0-9][0-9]_[0-9]_[0-9][0-9]|[0-9][0-9][0-9]/[0-9][0-9][0-9][0-9]_[0-9][0-9]|[0-9][0-9][0-9]/[0-9][0-9][0-9][0-9]_[0-9][0-9]_[0-9]|[0-9][0-9][0-9]/[0-9][0-9][0-9][0-9]_[0-9][0-9]_[0-9][0-9]";
        document.getElementById('no_c').title="El formato establecido es ###/AAAA o ###/AAAA_##";


    }
}
function fnUpdateControlFactura() {
    $tipo = $('select[name=estadocf]').val();
    if ($tipo == "F.Cancelada") {
        document.getElementById("descripcion").required=true;
    } else {
        document.getElementById("descripcion").required=false;
    }
}

function FnValidarPersona(){

    var fact1=document.crear_personas.facturador_pr_1.checked;
    var directivo1=document.crear_personas.directivo_pr_1.checked;
    var contacto1=document.crear_personas.contacto_pr_1.checked;

    if(fact1==false && directivo1==false && contacto1==false && document.crear_personas.nombre_pr_1.value!=""){
        alert("La persona con nombre "+document.crear_personas.nombre_pr_1.value+" debe ser al menos Directivo, Facturador o Contacto");
        return false;
    }

    var fact2=document.crear_personas.facturador_pr_2.checked;
    var directivo2=document.crear_personas.directivo_pr_2.checked;
    var contacto2=document.crear_personas.contacto_pr_2.checked;

    if(fact2==false && directivo2==false && contacto2==false && document.crear_personas.nombre_pr_2.value!=''){
        alert("La persona con nombre "+document.crear_personas.nombre_pr_2.value+" debe ser al menos Directivo, Facturador o Contacto");
        return false;
    }

    var fact3=document.crear_personas.facturador_pr_3.checked;
    var directivo3=document.crear_personas.directivo_pr_3.checked;
    var contacto3=document.crear_personas.contacto_pr_3.checked;
    if(fact3==false && directivo3==false && contacto3==false && document.crear_personas.nombre_pr_3.value!=''){
        alert("La persona con nombre "+document.crear_personas.nombre_pr_3.value+" debe ser al menos Directivo, Facturador o Contacto");
        return false;
    }

    var fact4=document.crear_personas.facturador_pr_4.checked;
    var directivo4=document.crear_personas.directivo_pr_4.checked;
    var contacto4=document.crear_personas.contacto_pr_4.checked;
    if(fact4==false && directivo4==false && contacto4==false && document.crear_personas.nombre_pr_4.value!=''){
        alert("La persona con nombre "+document.crear_personas.nombre_pr_4.value+" debe ser al menos Directivo, Facturador o Contacto");
        return false;
    }

    var fact5=document.crear_personas.facturador_pr_5.checked;
    var directivo5=document.crear_personas.directivo_pr_5.checked;
    var contacto5=document.crear_personas.contacto_pr_5.checked;
    if(fact5==false && directivo5==false && contacto5==false && document.crear_personas.nombre_pr_5.value!=''){
        alert("La persona con nombre "+document.crear_personas.nombre_pr_5.value+" debe ser al menos Directivo, Facturador o Contacto");
        return false;
    }

    var fact6=document.crear_personas.facturador_pr_6.checked;
    var directivo6=document.crear_personas.directivo_pr_6.checked;
    var contacto6=document.crear_personas.contacto_pr_6.checked;
    if(fact6==false && directivo6==false && contacto6==false && document.crear_personas.nombre_pr_6.value!=''){
        alert("La persona con nombre "+document.crear_personas.nombre_pr_6.value+" debe ser al menos Directivo, Facturador o Contacto");
        return false;
    }

    var fact7=document.crear_personas.facturador_pr_7.checked;
    var directivo7=document.crear_personas.directivo_pr_7.checked;
    var contacto7=document.crear_personas.contacto_pr_7.checked;
    if(fact7==false && directivo7==false && contacto7==false && document.crear_personas.nombre_pr_7.value!=''){
        alert("La persona con nombre "+document.crear_personas.nombre_pr_7.value+" debe ser al menos Directivo, Facturador o Contacto");
        return false;
    }
    var fact8=document.crear_personas.facturador_pr_8.checked;
    var directivo8=document.crear_personas.directivo_pr_8.checked;
    var contacto8=document.crear_personas.contacto_pr_8.checked;
    if(fact8==false && directivo8==false && contacto8==false && document.crear_personas.nombre_pr_8.value!=''){
        alert("La persona con nombre "+document.crear_personas.nombre_pr_8.value+" debe ser al menos Directivo, Facturador o Contacto");
        return false;
    }
    var fact9=document.crear_personas.facturador_pr_9.checked;
    var directivo9=document.crear_personas.directivo_pr_9.checked;
    var contacto9=document.crear_personas.contacto_pr_9.checked;
    if(fact9==false && directivo9==false && contacto9==false && document.crear_personas.nombre_pr_9.value!=''){
        alert("La persona con nombre "+document.crear_personas.nombre_pr_9.value+" debe ser al menos Directivo, Facturador o Contacto");
        return false;
    }
    var fact10=document.crear_personas.facturador_pr_10.checked;
    var directivo10=document.crear_personas.directivo_pr_10.checked;
    var contacto10=document.crear_personas.contacto_pr_10.checked;

    if(fact10==false && directivo10==false && contacto10==false && document.crear_personas.nombre_pr_10.value!=''){
        alert("La persona con nombre "+document.crear_personas.nombre_pr_10.value+" debe ser al menos Directivo, Facturador o Contacto");
        return false;
    }
        
    return true;
}
function FnValidarContrato(){
    var acapite_h_1 = document.procesar.acapite_h_1.value;
    var clausula_h_1 = document.procesar.clausula_h_1.value;
    var entregable_h_1 = document.procesar.entregable_h_1.value;


    if (clausula_h_1=='' && entregable_h_1!='')
    {
        alert("Debe introducir datos en el campo clausula");
        return false;
    }

    if (acapite_h_1!=''){
        if ((clausula_h_1=='Primera' || clausula_h_1=='primera') &&(acapite_h_1.split('.')[0]!=1) ){
            alert("El acapite debe comenzar 1. porque hace referencia a la clausula Primera.");
            return false;
        }

        if ((clausula_h_1=='Segunda' || clausula_h_1=='segunda') &&(acapite_h_1.split('.')[0]!=2) ){
            alert("El acapite debe comenzar 2. porque hace referencia a la clausula Segunda.");
            return false;
        }

        if ((clausula_h_1=='Tercera' || clausula_h_1=='tercera') &&(acapite_h_1.split('.')[0]!=3) ){
            alert("El acapite debe comenzar 3. porque hace referencia a la clausula Tercera.");
            return false;
        }

        if ((clausula_h_1=='Cuarta' || clausula_h_1=='cuarta') &&(acapite_h_1.split('.')[0]!=4) ){
            alert("El acapite debe comenzar 4. porque hace referencia a la clausula Cuarta.");
            return false;
        }

        if ((clausula_h_1=='Quinta' || clausula_h_1=='quinta') &&(acapite_h_1.split('.')[0]!=5) ){
            alert("El acapite debe comenzar 5. porque hace referencia a la clausula Quinta.");
            return false;
        }

        if ((clausula_h_1=='Sexta' || clausula_h_1=='sexta') &&(acapite_h_1.split('.')[0]!=6) ){
            alert("El acapite debe comenzar 6. porque hace referencia a la clausula Sexta.");
            return false;
        }

        if ((clausula_h_1=='Septima' || clausula_h_1=='septima') &&(acapite_h_1.split('.')[0]!=7) ){
            alert("El acapite debe comenzar 7. porque hace referencia a la clausula Septima.");
            return false;
        }

        if ((clausula_h_1=='Octava' || clausula_h_1=='octava') &&(acapite_h_1.split('.')[0]!=8) ){
            alert("El acapite debe comenzar 8. porque hace referencia a la clausula Octava.");
            return false;
        }

        if ((clausula_h_1=='Novena' || clausula_h_1=='novena') &&(acapite_h_1.split('.')[0]!=9) ){
            alert("El acapite debe comenzar 9. porque hace referencia a la clausula Novena.");
            return false;
        }
    }
    if( clausula_h_1!='' && entregable_h_1=='' && (document.procesar.ingresocuc_h_1.value.split('.')[0]==0 && document.procesar.ingresocup_h_1.value.split('.')[0]==0 && document.procesar.ingresomlc_h_1.value.split('.')[0]==0)){
        alert("Para poder guardar el elemento clausula es necesario completar los datos correspondientes a un hito");
        return false;
    }
    /*Elementos del hito n�mero 1*/
    if (entregable_h_1!='' && (document.procesar.ingresocuc_h_1.value.split('.')[0]==0 && document.procesar.ingresocup_h_1.value.split('.')[0]==0 && document.procesar.ingresomlc_h_1.value.split('.')[0]==0))
    {
        alert("Todas las monedas del hito con inciso "+document.procesar.inciso_h_1.value+' tienen valor 0, al menos uno debe tener un monto');
        return false;
    }


    /*Elementos del hito n�mero 2*/
    if (document.procesar.entregable_h_2.value!='' && document.procesar.ingresocuc_h_2.value.split('.')[0]==0 && document.procesar.ingresocup_h_2.value.split('.')[0]==0 && document.procesar.ingresomlc_h_2.value.split('.')[0]==0)
    {
        alert("Todas las monedas del hito con inciso "+document.procesar.inciso_h_2.value+' tienen valor 0, al menos uno debe tener un monto');
        return false;
    }

    if (document.procesar.inciso_h_2.value=='' && document.procesar.entregable_h_2.value!='')
    {
        alert("EL hito que tiene como entregable "+document.procesar.entregable_h_2.value+' no tiene inciso asignado');
        return false;
    }

    if (document.procesar.inciso_h_2.value!='' && document.procesar.entregable_h_2.value=='')
    {
        alert("En el hito con inciso "+document.procesar.inciso_h_2.value+' debe introduccir un entregable');
        return false;
    }



    /*Elementos del hito n�mero 3*/
    if (document.procesar.entregable_h_3.value!='' && document.procesar.ingresocuc_h_3.value.split('.')[0]==0 && document.procesar.ingresocup_h_3.value.split('.')[0]==0 && document.procesar.ingresomlc_h_3.value.split('.')[0]==0)
    {
        alert("Todas las monedas del hito con inciso "+document.procesar.inciso_h_3.value+' tienen valor 0, al menos uno debe tener un monto');
        return false;
    }

    if (document.procesar.inciso_h_3.value=='' && document.procesar.entregable_h_3.value!='')
    {
        alert("EL hito que tiene como entregable "+document.procesar.entregable_h_3.value+' no tiene inciso asignado');
        return false;
    }

    if (document.procesar.inciso_h_3.value!='' && document.procesar.entregable_h_3.value=='')
    {
        alert("En el hito con inciso "+document.procesar.inciso_h_3.value+' debe introduccir un entregable');
        return false;
    }


    /*Elementos del hito n�mero 4*/
    if (document.procesar.entregable_h_4.value!='' && document.procesar.ingresocuc_h_4.value.split('.')[0]==0 && document.procesar.ingresocup_h_4.value.split('.')[0]==0 && document.procesar.ingresomlc_h_4.value.split('.')[0]==0)
    {
        alert("Todas las monedas del hito con inciso "+document.procesar.inciso_h_4.value+' tienen valor 0, al menos uno debe tener un monto');
        return false;
    }

    if (document.procesar.inciso_h_4.value=='' && document.procesar.entregable_h_4.value!='')
    {
        alert("EL hito que tiene como entregable "+document.procesar.entregable_h_4.value+' no tiene inciso asignado');
        return false;
    }

    if (document.procesar.inciso_h_4.value!='' && document.procesar.entregable_h_4.value=='')
    {
        alert("En el hito con inciso "+document.procesar.inciso_h_4.value+' debe introduccir un entregable');
        return false;
    }


    /*Elementos del hito n�mero 5*/
    if (document.procesar.entregable_h_5.value!='' && document.procesar.ingresocuc_h_5.value.split('.')[0]==0 && document.procesar.ingresocup_h_5.value.split('.')[0]==0 && document.procesar.ingresomlc_h_5.value.split('.')[0]==0)
    {
        alert("Todas las monedas del hito con inciso "+document.procesar.inciso_h_5.value+' tienen valor 0, al menos uno debe tener un monto');
        return false;
    }

    if (document.procesar.inciso_h_5.value=='' && document.procesar.entregable_h_5.value!='')
    {
        alert("EL hito que tiene como entregable "+document.procesar.entregable_h_5.value+' no tiene inciso asignado');
        return false;
    }

    if (document.procesar.inciso_h_5.value!='' && document.procesar.entregable_h_5.value=='')
    {
        alert("En el hito con inciso "+document.procesar.inciso_h_5.value+' debe introduccir un entregable');
        return false;
    }


    /*Elementos del hito n�mero 6*/
    if (document.procesar.entregable_h_6.value!='' && document.procesar.ingresocuc_h_6.value.split('.')[0]==0 && document.procesar.ingresocup_h_6.value.split('.')[0]==0 && document.procesar.ingresomlc_h_6.value.split('.')[0]==0)
    {
        alert("Todas las monedas del hito con inciso "+document.procesar.inciso_h_6.value+' tienen valor 0, al menos uno debe tener un monto');
        return false;
    }

    if (document.procesar.inciso_h_6.value=='' && document.procesar.entregable_h_6.value!='')
    {
        alert("EL hito que tiene como entregable "+document.procesar.entregable_h_6.value+' no tiene inciso asignado');
        return false;
    }

    if (document.procesar.inciso_h_6.value!='' && document.procesar.entregable_h_6.value=='')
    {
        alert("En el hito con inciso "+document.procesar.inciso_h_6.value+' debe introduccir un entregable');
        return false;
    }


    /*Elementos del hito n�mero 7*/
    if (document.procesar.entregable_h_7.value!='' && document.procesar.ingresocuc_h_7.value.split('.')[0]==0 && document.procesar.ingresocup_h_7.value.split('.')[0]==0 && document.procesar.ingresomlc_h_7.value.split('.')[0]==0)
    {
        alert("Todas las monedas del hito con inciso "+document.procesar.inciso_h_7.value+' tienen valor 0, al menos uno debe tener un monto');
        return false;
    }

    if (document.procesar.inciso_h_7.value=='' && document.procesar.entregable_h_7.value!='')
    {
        alert("EL hito que tiene como entregable "+document.procesar.entregable_h_7.value+' no tiene inciso asignado');
        return false;
    }

    if (document.procesar.inciso_h_7.value!='' && document.procesar.entregable_h_7.value=='')
    {
        alert("En el hito con inciso "+document.procesar.inciso_h_7.value+' debe introduccir un entregable');
        return false;
    }


    /*Elementos del hito n�mero 8*/
    if (document.procesar.entregable_h_8.value!='' && document.procesar.ingresocuc_h_8.value.split('.')[0]==0 && document.procesar.ingresocup_h_8.value.split('.')[0]==0 && document.procesar.ingresomlc_h_8.value.split('.')[0]==0)
    {
        alert("Todas las monedas del hito con inciso "+document.procesar.inciso_h_8.value+' tienen valor 0, al menos uno debe tener un monto');
        return false;
    }

    if (document.procesar.inciso_h_8.value=='' && document.procesar.entregable_h_8.value!='')
    {
        alert("EL hito que tiene como entregable "+document.procesar.entregable_h_8.value+' no tiene inciso asignado');
        return false;
    }

    if (document.procesar.inciso_h_8.value!='' && document.procesar.entregable_h_8.value=='')
    {
        alert("En el hito con inciso "+document.procesar.inciso_h_8.value+' debe introduccir un entregable');
        return false;
    }


    /*Elementos del hito n�mero 9*/
    if (document.procesar.entregable_h_9.value!='' && document.procesar.ingresocuc_h_9.value.split('.')[0]==0 && document.procesar.ingresocup_h_9.value.split('.')[0]==0 && document.procesar.ingresomlc_h_9.value.split('.')[0]==0)
    {
        alert("Todas las monedas del hito con inciso "+document.procesar.inciso_h_9.value+' tienen valor 0, al menos uno debe tener un monto');
        return false;
    }

    if (document.procesar.inciso_h_9.value=='' && document.procesar.entregable_h_9.value!='')
    {
        alert("EL hito que tiene como entregable "+document.procesar.entregable_h_9.value+' no tiene inciso asignado');
        return false;
    }

    if (document.procesar.inciso_h_9.value!='' && document.procesar.entregable_h_9.value=='')
    {
        alert("En el hito con inciso "+document.procesar.inciso_h_9.value+' debe introduccir un entregable');
        return false;
    }


    /*Elementos del hito n�mero 10*/
    if (document.procesar.entregable_h_10.value!='' && document.procesar.ingresocuc_h_10.value.split('.')[0]==0 && document.procesar.ingresocup_h_10.value.split('.')[0]==0 && document.procesar.ingresomlc_h_10.value.split('.')[0]==0)
    {
        alert("Todas las monedas del hito con inciso "+document.procesar.inciso_h_10.value+' tienen valor 0, al menos uno debe tener un monto');
        return false;
    }

    if (document.procesar.inciso_h_10.value=='' && document.procesar.entregable_h_10.value!='')
    {
        alert("EL hito que tiene como entregable "+document.procesar.entregable_h_10.value+' no tiene inciso asignado');
        return false;
    }

    if (document.procesar.inciso_h_10.value!='' && document.procesar.entregable_h_10.value=='')
    {
        alert("En el hito con inciso "+document.procesar.inciso_h_10.value+' debe introduccir un entregable');
        return false;
    }


    /*Elementos del hito n�mero 11*/
    if (document.procesar.entregable_h_11.value!='' && document.procesar.ingresocuc_h_11.value.split('.')[0]==0 && document.procesar.ingresocup_h_11.value.split('.')[0]==0 && document.procesar.ingresomlc_h_11.value.split('.')[0]==0)
    {
        alert("Todas las monedas del hito con inciso "+document.procesar.inciso_h_11.value+' tienen valor 0, al menos uno debe tener un monto');
        return false;
    }

    if (document.procesar.inciso_h_11.value=='' && document.procesar.entregable_h_11.value!='')
    {
        alert("EL hito que tiene como entregable "+document.procesar.entregable_h_11.value+' no tiene inciso asignado');
        return false;
    }

    if (document.procesar.inciso_h_11.value!='' && document.procesar.entregable_h_11.value=='')
    {
        alert("En el hito con inciso "+document.procesar.inciso_h_11.value+' debe introduccir un entregable');
        return false;
    }
    /*Elementos del hito n�mero 12*/
    if (document.procesar.entregable_h_12.value!='' && document.procesar.ingresocuc_h_12.value.split('.')[0]==0 && document.procesar.ingresocup_h_12.value.split('.')[0]==0 && document.procesar.ingresomlc_h_12.value.split('.')[0]==0)
    {
        alert("Todas las monedas del hito con inciso "+document.procesar.inciso_h_12.value+' tienen valor 0, al menos uno debe tener un monto');
        return false;
    }

    if (document.procesar.inciso_h_12.value=='' && document.procesar.entregable_h_12.value!='')
    {
        alert("EL hito que tiene como entregable "+document.procesar.entregable_h_12.value+' no tiene inciso asignado');
        return false;
    }

    if (document.procesar.inciso_h_12.value!='' && document.procesar.entregable_h_12.value=='')
    {
        alert("En el hito con inciso "+document.procesar.inciso_h_12.value+' debe introduccir un entregable');
        return false;
    }

    /*Elementos del hito n�mero 13*/
    if (document.procesar.entregable_h_13.value!='' && document.procesar.ingresocuc_h_13.value.split('.')[0]==0 && document.procesar.ingresocup_h_13.value.split('.')[0]==0 && document.procesar.ingresomlc_h_13.value.split('.')[0]==0)
    {
        alert("Todas las monedas del hito con inciso "+document.procesar.inciso_h_13.value+' tienen valor 0, al menos uno debe tener un monto');
        return false;
    }

    if (document.procesar.inciso_h_13.value=='' && document.procesar.entregable_h_13.value!='')
    {
        alert("EL hito que tiene como entregable "+document.procesar.entregable_h_13.value+' no tiene inciso asignado');
        return false;
    }

    if (document.procesar.inciso_h_13.value!='' && document.procesar.entregable_h_13.value=='')
    {
        alert("En el hito con inciso "+document.procesar.inciso_h_13.value+' debe introduccir un entregable');
        return false;
    }

    return true;

}



function FnValidarDirectorio(){

    if (document.directorio.telefono.value=='' && document.directorio.correo.value=='')
    {
        alert("Debe introducir datos en el campo Nombre o Tel\u00e9fono");
        return false;
    }


    return true;

}
function FnValidarSolicitud(){

    $tipo = $('select[name=tipo_solicitud]').val();
    var horaactual= new Date();
    var year='';
    var mes=horaactual.getMonth()+1;
    var dia=horaactual.getDate()
    if (mes<10)mes=0+''+mes;
    if (dia<10)dia=0+''+dia;

    if (horaactual.getYear() < 2000)
        year= (1900 + horaactual.getYear());
    else
        year=horaactual.getYear();
    var fecha = year+''+mes+''+dia;
    var fecha_1=document.solicitudes.fecha.value.substring(6,10)+''+document.solicitudes.fecha.value.substring(3,5)+''+document.solicitudes.fecha.value.substring(0,2);
    var fecha_2=document.solicitudes.fecha_encuentro.value.substring(6,10)+''+document.solicitudes.fecha_encuentro.value.substring(3,5)+''+document.solicitudes.fecha_encuentro.value.substring(0,2);

    if((fecha_1<=fecha && $tipo!='' && $tipo!='Encuentro') || (fecha_2<=fecha && $tipo!='' &&  $tipo=='Encuentro'))
    {
        alert("Las solicitudes como m\u00ednimo deben hacerse con 1 d\u00eda de antelaci\u00f3n. Por favor seleccione una fecha posterior a "+dia+'-'+mes+'-'+year+".");
        return false;
    }

    return true;

}
function FnValidarFicha(){


}


function FnValidarActaEntrega(){

    $estado = $('select[name=estado]').val();
    var accion=document.actaentrega.accion.value;


    if (accion=='Archivar' &&  $estado!='Recibida' ){
        alert('Pase a recibida el estado del acta');
        return false;
    }

     if ($estado=="Recibida" && (document.actaentrega.nombre_apellidos_entrega.value=='' || document.actaentrega.nombre_apellidos_recibe.value==''|| document.actaentrega.cargo_entrega.value==''|| document.actaentrega.cargo_recibe.value==''|| document.actaentrega.fecha_entrega.value==''|| document.actaentrega.fecha_recibe.value==''))
    {
        alert("Para poder pasar el Acta de Entrega al estado Recibida debe llenar los datos correspondientes a la entrega, digase nombre y apellidos, cargo y fecha correspondiente al recibo y la entrega");
        return false;
    }

    /*Acta de entrega numero 2*/
    if (document.actaentrega.nombre_doc_2.value!='' && document.actaentrega.cant_ejemplares_2.value=='')
    {
        alert("Por favor introduzca la cantidad de ejemplares del documento 2");
        return false;
    }
    if ((document.actaentrega.nombre_doc_2.value=='' && document.actaentrega.cant_ejemplares_2.value!='')|| (document.actaentrega.observaciones_2.value!='' && document.actaentrega.nombre_doc_2.value==''))
    {
        alert("Por favor introduzca la descripci\u00f3n del documento 2");
        return false;
    }

    /*Acta de entrega numero 3*/
    if (document.actaentrega.nombre_doc_3.value!='' && document.actaentrega.cant_ejemplares_3.value=='')
    {
        alert("Por favor introduzca la cantidad de ejemplares del documento 3");
        return false;
    }
    if ((document.actaentrega.nombre_doc_3.value=='' && document.actaentrega.cant_ejemplares_3.value!='')|| (document.actaentrega.observaciones_3.value!='' && document.actaentrega.nombre_doc_3.value==''))
    {
        alert("Por favor introduzca la descripci\u00f3n del documento 3");
        return false;
    }

    /*Acta de entrega numero 4*/
    if (document.actaentrega.nombre_doc_4.value!='' && document.actaentrega.cant_ejemplares_4.value=='')
    {
        alert("Por favor introduzca la cantidad de ejemplares del documento 4");
        return false;
    }
    if ((document.actaentrega.nombre_doc_4.value=='' && document.actaentrega.cant_ejemplares_4.value!='')|| (document.actaentrega.observaciones_4.value!='' && document.actaentrega.nombre_doc_4.value==''))
    {
        alert("Por favor introduzca la descripci\u00f4n del documento 4");
        return false;
    }

    /*Acta de entrega numero 5*/
    if (document.actaentrega.nombre_doc_5.value!='' && document.actaentrega.cant_ejemplares_5.value=='')
    {
        alert("Por favor introduzca la cantidad de ejemplares del documento 5");
        return false;
    }
    if ((document.actaentrega.nombre_doc_5.value=='' && document.actaentrega.cant_ejemplares_5.value!='')|| (document.actaentrega.observaciones_5.value!='' && document.actaentrega.nombre_doc_5.value==''))
    {
        alert("Por favor introduzca la descripci\u00f5n del documento 5");
        return false;
    }

    /*Acta de entrega numero 6*/
    if (document.actaentrega.nombre_doc_6.value!='' && document.actaentrega.cant_ejemplares_6.value=='')
    {
        alert("Por favor introduzca la cantidad de ejemplares del documento 6");
        return false;
    }
    if ((document.actaentrega.nombre_doc_6.value=='' && document.actaentrega.cant_ejemplares_6.value!='')|| (document.actaentrega.observaciones_6.value!='' && document.actaentrega.nombre_doc_6.value==''))
    {
        alert("Por favor introduzca la descripci\u00f6n del documento 6");
        return false;
    }

    /*Acta de entrega numero 7*/
    if (document.actaentrega.nombre_doc_7.value!='' && document.actaentrega.cant_ejemplares_7.value=='')
    {
        alert("Por favor introduzca la cantidad de ejemplares del documento 7");
        return false;
    }
    if ((document.actaentrega.nombre_doc_7.value=='' && document.actaentrega.cant_ejemplares_7.value!='')|| (document.actaentrega.observaciones_7.value!='' && document.actaentrega.nombre_doc_7.value==''))
    {
        alert("Por favor introduzca la descripci\u00f7n del documento 7");
        return false;
    }

    /*Acta de entrega numero 8*/
    if (document.actaentrega.nombre_doc_8.value!='' && document.actaentrega.cant_ejemplares_8.value=='')
    {
        alert("Por favor introduzca la cantidad de ejemplares del documento 8");
        return false;
    }
    if ((document.actaentrega.nombre_doc_8.value=='' && document.actaentrega.cant_ejemplares_8.value!='')|| (document.actaentrega.observaciones_8.value!='' && document.actaentrega.nombre_doc_8.value==''))
    {
        alert("Por favor introduzca la descripci\u00f8n del documento 8");
        return false;
    }

    /*Acta de entrega numero 9*/
    if (document.actaentrega.nombre_doc_9.value!='' && document.actaentrega.cant_ejemplares_9.value=='')
    {
        alert("Por favor introduzca la cantidad de ejemplares del documento 9");
        return false;
    }
    if ((document.actaentrega.nombre_doc_9.value=='' && document.actaentrega.cant_ejemplares_9.value!='')|| (document.actaentrega.observaciones_9.value!='' && document.actaentrega.nombre_doc_9.value==''))
    {
        alert("Por favor introduzca la descripci\u00f9n del documento 9");
        return false;
    }

    /*Acta de entrega numero 3*/
    if (document.actaentrega.nombre_doc_3.value!='' && document.actaentrega.cant_ejemplares_3.value=='')
    {
        alert("Por favor introduzca la cantidad de ejemplares del documento 3");
        return false;
    }
    if ((document.actaentrega.nombre_doc_3.value=='' && document.actaentrega.cant_ejemplares_3.value!='')|| (document.actaentrega.observaciones_3.value!='' && document.actaentrega.nombre_doc_3.value==''))
    {
        alert("Por favor introduzca la descripci\u00f3n del documento 3");
        return false;
    }

    return true;

}


