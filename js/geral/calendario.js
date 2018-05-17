var dataGeral;
var mesCalendario;
var anoCalendario;
var diaEscolhido;
var mesEscolhido;
var anoEscolhido;

$(document).ready(function() {
    dataGeral = new Date();
    mesCalendario = dataGeral.getMonth();
    anoCalendario = dataGeral.getFullYear();

    $("#calendario").css({opacity: 1}); 

    atualizarCalendario(mesCalendario, anoCalendario);
})

$("#voltar-mes").click(function() {
    $(".semana span").removeClass("selecionado");
    anoCalendario = mesCalendario>0?anoCalendario:anoCalendario-1;
    mesCalendario = mesCalendario>0?mesCalendario-1:11;
    
    console.log(mesCalendario+" - "+anoCalendario);
    
    atualizarCalendario(mesCalendario, anoCalendario);
});

$("#avancar-mes").click(function() {
    $(".semana span").removeClass("selecionado");
    anoCalendario = mesCalendario<11?anoCalendario:anoCalendario+1;
    mesCalendario = mesCalendario<11?mesCalendario+1:0;
    
    console.log(mesCalendario+" - "+anoCalendario);
    
    atualizarCalendario(mesCalendario, anoCalendario);
});

//$(".semana span").click(function() {
//    diaEscolhido = Number($(this).text());
    
//    console.log(getTimeEscolhido(disEscolhido, mesCalendario, anoCalendario));
//});

function getDiaMes(mes, ano) {
//        mes--;
    var dias = [];
    var data = new Date(ano, mes, 1);

    while (data.getMonth() === mes) {
        var dia = data.getDate();
        dias.push(dia<10?"0"+dia:dia);
        data.setDate(data.getDate()+1);
    }

    return dias;
}

function atualizarCalendario(mes, ano) {
    var semana = 0;
    var dias = getDiaMes(mes, ano);
    dataGeral = new Date();

    var diaSemana = new Date(ano, mes, 1).getDay();
    
    $("#calendario .hoje").removeClass();

    if ($(".semana").length<6) {
        while ($(".semana").length<6) {
            $(".semana:last-child").clone().appendTo("#calendario article");
        }
    }

    while (dias.length>0) {
        var temp = 1;
        semana++;
        while (temp<8) {
            if (temp<=diaSemana) {
                $(".semana:nth-child("+semana+") span:nth-child("+temp+")").text("").attr("data-nada", 1).attr("data-dia",0);
            } else if (dias.length>0) {
                if (dataGeral.getDate()==dias[0] && mes===dataGeral.getMonth() && ano==dataGeral.getFullYear()) {
                    $(".semana:nth-child("+semana+") span:nth-child("+temp+")").addClass("hoje");
                    $("#dia").text(dias[0]);
                }
                $(".semana:nth-child("+semana+") span:nth-child("+temp+")").text(dias[0]).attr("data-nada",0).attr("data-dia",dias[0]);
                if (anoEscolhido==ano && mesEscolhido==mes && diaEscolhido==dias[0]) $(".semana:nth-child("+semana+") span:nth-child("+temp+")").addClass("selecionado");
                dias.splice(0,1);
            } else {
                $(".semana:nth-child("+semana+") span:nth-child("+temp+")").text("").attr("data-nada",1).attr("data-dia",0);
            }

            temp++;
        }
        diaSemana=0;
    }

    while (semana<7) {
        semana++;
        $(".semana:nth-child("+semana+")").remove();
    }
    
    $("#mes").text(meses[mesCalendario]+" -");
    $("#ano").text(anoCalendario);
}

function getTimeEscolhido(dia, mes, ano) {
    // Mes dia e ano
    data = new Date(mes+","+dia+","+ano);
    
    return data.getTime();
}