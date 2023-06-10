$(function(){

    $("ul.menus li").hover(function(){

        $(this).addClass("hover");
        $('ul:first',this).css('visibility', 'visible');

    }, function(){

        $(this).removeClass("hover");
        $('ul:first',this).css('visibility', 'hidden');

    })

});

/**
 * Created by Jose Carlos on 26/8/2018.
 */
function mueveReloj(){
    momentoActual = new Date();
    hora = momentoActual.getHours();
    minuto = momentoActual.getMinutes();
    segundo = momentoActual.getSeconds();
    dayValue = momentoActual.getDay()
    monthValue = momentoActual.getMonth()
    dateText = "";

    if (segundo < 10)
        segundo = "0" + segundo;

    if (minuto < 10)
        minuto = "0" + minuto;

    if(hora>=12 && hora<=23)
        m="PM";
    else
        m="AM";

    if(hora>=12)
        hora = hora-12;


    if (hora < 10)
        hora = "0" + hora;



    /*Poner el dia en letra*/
    if (dayValue == 0)
        dateText += "Domingo";
    else if (dayValue == 1)
        dateText += "Lunes";
    else if (dayValue == 2)
        dateText += "Martes";
    else if (dayValue == 3)
        dateText += "Miercoles";
    else if (dayValue == 4)
        dateText += "Jueves";
    else if (dayValue == 5)
        dateText += "Viernes";
    else if (dayValue == 6)
        dateText += "Sabado";

    /*Poner el mes en letra*/
    dateText +=", "+momentoActual.getDate()+ " de ";
    if (monthValue == 0)
        dateText += "Enero";
    if (monthValue == 1)
        dateText += "Febrero";
    if (monthValue == 2)
        dateText += "Marzo";
    if (monthValue == 3)
        dateText += "Abril";
    if (monthValue == 4)
        dateText += "Mayo";
    if (monthValue == 5)
        dateText += "Junio";
    if (monthValue == 6)
        dateText += "Julio";
    if (monthValue == 7)
        dateText += "Agosto";
    if (monthValue == 8)
        dateText += "Septiembre";
    if (monthValue == 9)
        dateText += "Octubre";
    if (monthValue == 10)
        dateText += "Noviembre";
    if (monthValue == 11)
        dateText += "Diciembre";

    var anno='';
    if (momentoActual.getYear() < 2000){
        dateText += " de "  + (1900 + momentoActual.getYear())
        anno=1900 + momentoActual.getYear();
    }
    else{
        dateText += " de " + (momentoActual.getYear())
        anno=momentoActual.getYear();
    }
    horaImprimible = dateText + " " + hora + ":" + minuto + ":" + segundo+" "+m;

    reloj.innerHTML = horaImprimible;//cl=clock=reloj
    year.innerHTML = anno;//cl=clock=reloj
    yearacta.innerHTML = anno;//cl=clock=reloj

    setTimeout("mueveReloj()",1000);
}
