var aberto = "";
var idPopup = 1;
var popups = {};
var notificacoes = [];
var notificacoesAcabou = false;
var ultimaData = 0;
var meses = ['Janeiro','Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro','Novembro', 'Dezembro'];
var acabouNotificacoes = false;
var ultimaData = 99999999999;
var carregandoNotificacoes = false;

$(document).ready(function() {
    $(document).scroll();
});

$(document).scroll(function() {
    if ($(window).width()>=1000) return;
    scroll = $(document).scrollTop();

    if (scroll>0) {
        $("#topo").addClass("escuro");
    } else {
        $("#topo").removeClass("escuro");
    }
});

$("#menu-toogle").click(function() {
    $("#topo").toggleClass('aberto');
});

$("#pesquisar i").click(function() {
	if ($(this).parent().hasClass('aberto')) {
		p = $("#pesquisar input").val();
		console.log(p);
		if (p.length>0) {
	        link = "/pesquisa/serie/1/"+$(this).val(); 
	        // console.log(link); return;
	        location.href = link;
	    } else {
	    	$(this).parent().removeClass();
	    }
	} else
		$("#pesquisar").addClass("aberto").find("input").focus();
});

// $("#pesquisar.aberto i").click(function() {
	
// });

$("#pesquisar input").focusout(function() {
	// $(this).parent().removeClass();
});

$("#pesquisar input").keydown(function(e) {
    // console.log(e.keyCode);
    if (e.keyCode==13) {
        if ($(this).val().length>0) {
            link = "/pesquisa/serie/1/"+$(this).val(); 
            // console.log(link); return;
            location.href = link;
        }
    }
});

$("#header-foto-perfil > img, #header-foto-perfil > i").click(function() {
	if (aberto=="notificacao") $("#icon-notificacao > i").click();
	
	if ($("#perfil-painel").hasClass("aberto")) {
		$("#perfil-painel").hide().removeClass("aberto");
		aberto = "";
	} else {
		$("#perfil-painel").show().addClass("aberto");
		aberto = "perfil";
	}
});

$("#icon-notificacao > i").click(function() {
	if (aberto=="perfil") $("#header-foto-perfil > img, #header-foto-perfil > i").click();

    if ($("#topo").hasClass('aberto')) $("#menu-toogle").click();
	
	if ($("#icon-notificacao").hasClass("aberto")) {
		$("#notificacoes").hide();
        $("#icon-notificacao").removeClass("aberto");
		aberto = "";
        
        lerNotificacoes();
	} else {
		$("#notificacoes").show();
        $("#icon-notificacao").addClass("aberto");
		aberto = "notificacao";
        
        if (!notificacoesAcabou) {
            pegarNotificacoes(ultimaData);
        }
	}
});

$("#notificacoes ul").scroll(function() {
    if ($("#notificacoes .img-loading").isInViewport() && !acabouNotificacoes && !carregandoNotificacoes) {
        // console.log(0);
        carregandoNotificacoes = true;
        pegarNotificacoes(ultimaData);
    }
});

$("#link-login a").click(function(e) {
    e.preventDefault();
    
    $("#container-login").fadeIn().css({display: "flex"});
    if ($("#topo").hasClass('aberto')) $("#menu-toogle").click();
});

$(document).on("click", ".popup", function() {
   // console.log("a")
    $(this).clearQueue().fadeOut(function() {
        $(this).remove();
        organizarPopups();
    });
});

$.fn.isInViewport = function() {
    var elementTop = $(this).offset().top;
    var elementBottom = elementTop + $(this).outerHeight();

    var viewportTop = $(window).scrollTop();
    var viewportBottom = viewportTop + $(window).height();

    return elementBottom > viewportTop && elementTop < viewportBottom;
};

function chamarPopupInfo(mensagem, tempo) {
    tempo = tempo || 10000;
    popupInfo = $("<div class='popup popup-info'>").appendTo("body");
    popupInfo.html(mensagem).attr("data-id", idPopup);
    organizarPopups();
    
    popupInfo.delay(tempo).fadeOut(function() {
        $(this).remove();
        // console.log($('.popup-info').length);
        organizarPopups();
    });
    
    idPopup++;
}

function chamarPopupErro(mensagem, tempo) {
    tempo = tempo || 10000;
    popupInfo = $("<div class='popup popup-erro'>").appendTo("body");
    popupInfo.html(mensagem).attr("data-id", idPopup);
    organizarPopups();
    
    popupInfo.delay(tempo).fadeOut(function() {
        $(this).remove();
        // console.log($('.popup-erro').length);
        organizarPopups();
    });
    
    idPopup++;
}

function chamarPopupConf(mensagem, tempo) {
    tempo = tempo || 10000;
    popupInfo = $("<div class='popup popup-conf'>").appendTo("body");
    popupInfo.html(mensagem).attr("data-id", idPopup);
    organizarPopups();
    
    popupInfo.delay(tempo).fadeOut(function() {
        $(this).remove();
        // console.log($('.popup-conf').length);
        organizarPopups();
    });
    
    idPopup++;
}

function chamarPopupLoading(mensagem) {
    popupInfo = $("<div class='popup popup-loading'>").appendTo("body");
    popupInfo.html("<div class='img-loading'></div>"+mensagem).attr("data-id", idPopup);
    organizarPopups();
    idPopup++;
}

function removerLoading() {
    $(".popup-loading").fadeOut(function() {
        $(this).remove();
        // console.log($('.popup-loading').length);
        organizarPopups();
    });
}

function organizarPopups() {
    qtdPopups = $(".popup").length;
   // console.log(qtdPopups);
    altura = 10;
    
    $(".popup").each(function(i, elem) {
        $(elem).css({bottom: altura});
        altura+= $(elem).outerHeight()+10;
    });
}

function getData(time) {
    time = new Date(time*1000);
    return colocarZero(time.getDate())+"/"+colocarZero(time.getMonth()+1)+"/"+time.getFullYear();
}

function getDataAbreviada(time) {
    time = new Date(time*1000);
    
    return colocarZero(time.getDate())+", "+meses[time.getMonth()].substr(0, 3)+". "+time.getFullYear();
}

function getHora(time) {
    time = new Date(time*1000);
    return colocarZero(time.getHours())+":"+colocarZero(time.getMinutes());
}

function colocarZero(n) {
    if (n<10) return "0"+n;
    
    return n;
}

function pegarNotificacoes(ultimaDataTemp) {
    ultimaDataTemp = ultimaDataTemp || 0;
    ultimaDataTemp = ultimaDataTemp==99999999999?0:ultimaDataTemp;
   // $("#notificacoes ul").removeClass();
    data = {funcao: "pegarNotificacoes", ultimaData: ultimaDataTemp, id_usuario: usuario.id};
    // console.log(data);
    $.ajax({
        type: "post",
        url: "php/handler/usuarioHandler.php",
        data: data,
        success: function(result) {
           // console.log(result);
            result = JSON.parse(result);
            console.log(result);
            
            if (result.estado==1) {
                result.notificacoes = result.notificacoes==null?[]:result.notificacoes;
                result.logs = result.logs==null?[]:result.logs;
                
                resultado = result.notificacoes.concat(result.logs);
                resultado = resultado.concat(result.comentarios);
                
                $.each(resultado, function(i, value) {
                    value.time = timeToTimestamp(value.data);

                    if (ultimaData>value.time) {
                        ultimaData = value.time;
                    }
                });
                
                resultado.sort(function(a, b) {
                    return b.time - a.time;
                    
                    if (ultimaData>value.time) {
                        ultimaData = value.time;
                    }
                });
                
                $.each(resultado, function(i, value) {
                    adicionarNotificacao(value);
                });

                console.log(resultado);

                if ("acabou" in result) {
                    acabouNotificacoes = true;
                    $("#notificacoes .img-loading").hide();
                    // if ($("#nao-tem").length==0) $("#notificacoes").append("<p id='nao-tem'>Sem mais notificações!");
                }
                carregandoNotificacoes = false;
            } else {
                chamarPopupInfo(result.mensagem);
            }
            
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            chamarPopupErro("Desculpe, houve um erro, por favor atualize a página ou nos contate.");
            console.log(XMLHttpRequest);
            console.log(textStatus);
            console.log(errorThrown);
        },
        complete: function() {
            removerLoading();
        }
    });
}

function adicionarNotificacao(notificacao) {
    lido = notificacao.lido==0?"novo":"lido";
    
    if ('thumb_projeto' in notificacao) {
        url = (notificacao.tipo==1?"lerComic":"lerNovel")+"/"+notificacao.id_titulo;
        
        temp = "<li class='notificacao "+lido+"'>";
        temp += "<a href='"+url+"'>";
        if (notificacao.thumb_titulo!=null) temp += "<img src='servidor/titulos/thumbs/"+notificacao.thumb_titulo+"'>";
        else temp += "<img src='servidor/projetos/thumbs/"+notificacao.thumb_projeto+"'>";
        temp += "<div>";
        temp += "<h4>Novo capítulo de: "+notificacao.nome+"</h4>";
        temp += "<p>"+notificacao.descricao+"</p>";
        temp += "<span class='data'>"+getData(notificacao.time)+"</span>";
        
        $("#notificacoes ul .img-loading").before(temp);
    } 
    else if ('id_comentario' in notificacao && !('id_moderador' in notificacao)) {
    	url = (notificacao.id_usuario!=null?"/perfil":((notificacao.tipo==1?"lerComic":"lerNovel")+"/"+notificacao.id_titulo));
        
        temp = "<li class='notificacao "+lido+"'>";
        temp += "<a href='"+url+"'>";
        if (notificacao.foto_perfil!=null) temp += "<img src='servidor/thumbs-usuarios/"+notificacao.foto_perfil+"'>";
        else temp += "<img src='img/profile-default.png'>";
        temp += "<div>";
        temp += "<h4>Comentário de <b>"+notificacao.nickname+"</b></h4>";
        if (notificacao.id_usuario==null) temp += "<p>Comentario no capítulo "+notificacao.titulo+"</p>";
        else temp += "<p>Comentário feito em seu mural.</p>";
        temp += "<span class='data'>"+getData(notificacao.time)+"</span>";
        
        $("#notificacoes ul .img-loading").before(temp);
    }
    else {
        temp = "<li class='notificacao log "+lido+"'>";
        temp += "<a href=''>";
        temp += "<h4>Log do Sistema</h4>";
        temp += "<p>"+notificacao.descricao+"</p>";
        temp += "<span class='data'>"+getData(notificacao.time)+"</span>";
        
        $("#notificacoes ul .img-loading").before(temp);
    }
}

function lerNotificacoes() {
    $("#icon-notificacao .notificacao-qtd").fadeOut();
    
    $("#notificacoes li").removeClass("novo").addClass("lido");
}

function formToArray(serialized) {
    temp = {};
    
    $.each(serialized, function(i, value) {
        temp[value.name] = value.value;
    });
    
    return temp;
}

function timeToTimestamp(data) {
    if (typeof data=="number") return data;
    var t = data.split(/[- :]/);

    return new Date(Date.UTC(t[0], t[1]-1, t[2], t[3], t[4], t[5])).getTime()/1000;
}