var comentarios = {};
var comentariosNivel1 = [];
var comentariosNivel2 = {};
var acabouComentarios = false;
var ultimoIdComentarios = null;
var carregandoComentarios = false;
var paginaAtual = 1;
var paginas = 0;
var criadas;

$(document).ready(function() {
    // if (localStorage.lateral==0) { 
    //     $("#tudo").removeClass("aberto");
    //     if ($(window).width()<1000) {
    //         $("body").removeClass('hidden');
    //     }
    // } else {
    //     if ($(window).width()<1000) {
    //         $("body").addClass('hidden');
    //     }
    // }
    
    // if (localStorage.escuro==1) {
    //     $("#tudo").addClass("escuro");
    // }
    
    if (typeof comic!="undefined") {
        temp = {};
        $.each(arquivos, function(i, value) {
            temp[value.split("-")[0]] = value;
        });
        arquivos = temp;
        carregarControlesComic();
    }
    
    ajustarLateral();

    // pegarComentarios(); 
});

$(document).on("scroll", function() {
    if ($("#comentarios .img-loading").isInViewport() && !acabouComentarios && !carregandoComentarios) {
        console.log(0);
        carregandoComentarios = true;
        pegarComentarios(ultimoIdComentarios);
    }
    
    ajustarLateral();
});

$(window).resize(function() {
    ajustarLateral();
});

$("#main").resize(function() {
    ajustarLateral();
});

$(".aside-menu").click(function() {
    $("#tudo").toggleClass("aberto");
    
    data = new Date().getTime()+3600*24*365*1000;
    if ($("#tudo").hasClass("aberto")) {
        // localStorage.lateral = 1;
        cookie = "lateral=1; expires="+(new Date(data).toUTCString())+"; path=/;";
        document.cookie = cookie;
        if ($(window).width()<1000) {
            $("body").addClass('hidden');
        }
    } else {
        // localStorage.lateral = 0;
        cookie = "lateral=0; expires="+(new Date(data).toUTCString())+"; path=/;";
        document.cookie = cookie;
        if ($(window).width()<1000) {
            $("body").removeClass('hidden');
        }
    }
    ajustarLateral();
});

$("#menu-atalhos a").hover(function() {
    $(this).find("p").fadeIn().css({display: "flex"});
}, function() {
    $(this).find("p").fadeOut();
});

$("#copiar").click(function(e) {
    e.preventDefault();
    // console.log(typeof window.copy);
    url = $(this).attr("href");

    var input = $('<input>').val(url).appendTo('body').select()
    document.execCommand('copy');
    input.remove();

    chamarPopupInfo("Link copiado para o clipboard!");
});

$("#luz").click(function() {
    $("#tudo").toggleClass("escuro");
    data = new Date().getTime()+3600*24*365*1000;
    if ($("#tudo").hasClass("escuro")) {
        // localStorage.escuro = 1;
        cookie = "escuro=1; expires="+(new Date(data).toUTCString())+"; path=/;";
        document.cookie = cookie
    } else {
        // localStorage.escuro = 0;
        cookie = "escuro=0; expires="+(new Date(data).toUTCString())+"; path=/;";
        document.cookie = cookie;
    }
});

$("#seguir:not(.autor)").click(function() {
    if (typeof usuario=="undefined" || usuario.length==0) {
        $("#link-login a").click();
        chamarPopupInfo("Você precisa estar logado para realizar essa ação. Crie uma conta ou faça Login!");
        return;
    }
   // if ($(this).hasClass("autor")) return;
    data = {funcao: "seguir", id_usuario: usuario.id, id_projeto: serie.id};
    
    data.seguir = $(this).hasClass("seguindo") ? 1: 0;
    
    console.log(data.seguir);
    
    $(this).attr("disabled", true);
    $.ajax({
        type: "post",
        url: "../php/handler/serieHandler.php",
        data: data,
        success: function(result) {
           // console.log(result);
            result = JSON.parse(result);
            console.log(result);
            
            if (result.estado==1) {
                chamarPopupConf(result.mensagem);
                
                if (data.seguir==1) {
                    $("#seguir").removeClass("seguindo").html("Seguir <i class='fa fa-plus'>");
                } else {
                    $("#seguir").addClass("seguindo").html("Seguindo <i class='fa fa-check'>");
                }
                
                $("#seguir").attr("disabled", false);
            } else {
                chamarPopupInfo(result.mensagem);
                $("#seguir").attr("disabled", false);
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
});

$("#gostei").click(function() {
    if (typeof usuario=="undefined" || logado==0) {
        $("#link-login a").click();
        chamarPopupInfo("Você precisa estar logado para realizar essa ação. Crie uma conta ou faça Login!");
        return;
    }
    
    data = {funcao: "gostei", id_usuario: usuario.id, id_titulo: titulo.id};
    
    data.gostei = $(this).hasClass("marcado") ? 1: 0;
    
    console.log(data.gostei);
    
    $(this).attr("disabled", true);
    $.ajax({
        type: "post",
        url: "php/handler/tituloHandler.php",
        data: data,
        success: function(result) {
           // console.log(result);
            result = JSON.parse(result);
            console.log(result);
            
            if (result.estado==1) {
                chamarPopupInfo(result.mensagem);
                qtd = Number($("#gostei span").text());
                qtd2 = Number($("#likes p:first-child span").text());
                if (data.gostei==1) {
                    $("#gostei").removeClass("marcado").find("span").text(qtd-1);
                    $("#likes > p:first-child span").text(qtd2-1);
                } else {
                    $("#gostei").addClass("marcado").find("span").text(qtd+1);
                    $("#likes > p:first-child span").text(qtd2+1);
                }
                
                $("#gostei").attr("disabled", false);
            } else {
                chamarPopupInfo(result.mensagem);
                $("#gostei").attr("disabled", false);
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
});

$("#comentar textarea").focusin(function() {
    ajustarLateral();
    setTimeout(function() {
        ajustarLateral();
    },310);
});

$("#comentar textarea").focusout(function() {
    ajustarLateral();
    setTimeout(function() {
        ajustarLateral();
    },310);
});

$("#comentar textarea").keyup(function() {
    texto = $(this).val();
    
    if (texto.length==0) $("#comentar button").attr("disabled", true);
    else $("#comentar button").attr("disabled", false);
});

$("#comentar form").submit(function(e) {
    e.preventDefault();
    
    data = formToArray($(this).serializeArray());
    
    data.id = titulo.id;
    data.id_usuario = usuario.id;
    if ($("#comentar [name=id_referencia]").val()=="") delete data.id_referencia;
    if ($("#comentar [name=id]").val()=="") delete data.id;
    
    data.funcao = ("id" in data)?"atualizar-comentario":"comentar";
    if (data.funcao=="atualizar-comentario") data.nome = titulo.nome;

   // console.log(data); return;
    $("#comentar button").attr("disabled", true);
    $.ajax({
        type: "post",
        url: "php/handler/tituloHandler.php",
        data: data,
        success: function(result) {
           // console.log(result);
            result = JSON.parse(result);
            console.log(result);
            
            if (result.estado==1) {
                chamarPopupConf(result.mensagem);
    
                if (data.funcao=="comentar") {
                    result.comentario.nickname = usuario.nickname;
                    result.comentario.foto_perfil = usuario.foto_perfil;
                    comentarios[result.comentario.id] = result.comentario;
                    
                    if ('id_referencia' in data) {
                        // comentarios nivel 2
                        if (!(data.id_referencia in comentariosNivel2)) comentariosNivel2[data.id_referencia] = [];
                        novoIndice = comentariosNivel2[data.id_referencia].push(result.comentario) - 1;
                        carregarComentariosNivel2([result.comentario], 1);
                        $("html, body").animate({ scrollTop: $('.comentario[data-id='+result.comentario.id+']').offset().top }, 500);
                    } else {
                        // comentarios nivel 1
                        novoIndice = comentariosNivel1.push(result.comentario) - 1;
                        carregarComentariosNivel1([result.comentario], 1);
                    }
                    
                }
                else {
                    comentarios[result.comentario.id].texto = result.comentario.texto;
                    $('.comentario[data-id='+result.comentario.id+'] > div > p').text(result.comentario.texto);
                    $("html, body").animate({ scrollTop: $('.comentario[data-id='+result.comentario.id+']').offset().top }, 500);
                }
                
                $("#referencia i, #editar i").click();
                $("#comentar form")[0].reset();
                $("#comentar button").attr("disabled", false);
            } else {
                chamarPopupInfo(result.mensagem);
                $("#comentar button").attr("disabled", false);
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
});

$("#comentar #referencia i").click(function() {
    $("#comentar [name=id_referencia]").val("");
    $("#comentar #referencia").hide();
});

$(document).on("click", ".responder", function() {
    id_referencia = $(this).closest(".comentario").attr("data-id");
    
    $("#editar i").click();
    
    $("#comentar #referencia").show().find("span").html("Responder a <b>"+comentarios[id_referencia].nickname+"</b>");
    $("#comentar [name=id_referencia]").val(id_referencia);
    $("html, body").animate({ scrollTop: $('#comentar textarea').offset().top }, 500);
    $("#comentar textarea").focus();
});

$(document).on("click", ".comentario .excluir", function() {
    coment_id = $(this).closest(".comentario").attr("data-id");
    sub = $(this).closest(".comentario").hasClass("sub");
    pai = sub?$(this).closest(".comentario").attr("data-pai"):0;
    
    $("#referencia i, #editar i").click();
    
    confirmacao("Apagar comentário", "Deseja excluir esse comentário?", function() {
       // console.log("OK");
        data = {};
        data.funcao = "apagar-comentario";
        data.id = coment_id;
        data.id_titulo = titulo.id;
        data.id_usuario = comentarios[coment_id].id_usuario;
        data.nome = titulo.nome;
        data.texto = comentarios[coment_id].texto;

        $(".excluir, .editar").addClass("disabled");
        $.ajax({
            type: "post",
            url: "php/handler/tituloHandler.php",
            data: data,
            success: function(result) {
                console.log(result);
                result = JSON.parse(result);
               // console.log(result);

                if (result.estado==1) {
                    chamarPopupConf(result.mensagem);
                    
                    delete comentarios[coment_id];
                    $(".comentario[data-id="+coment_id+"]").remove();
                    
                    $(".excluir, .editar").removeClass("disabled");
                } else {
                    chamarPopupInfo(result.mensagem);
                    $(".excluir, .editar").removeClass("disabled");
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
        })
    }, function() {});
});

$("#comentar #editar i").click(function() {
    $("#comentar [name=id]").val("");
    $("#comentar #editar").hide();
    $("#comentar textarea").val("").keyup();
});

$(document).on("click", ".editar", function() {
    coment_id = $(this).closest(".comentario").attr("data-id");
    
    $("#referencia i").click();
    
    $("#comentar #editar").show().find("span").html("Editar coment. de <b>"+comentarios[coment_id].nickname+"</b>");
    $("#comentar [name=id]").val(coment_id);
    $("html, body").animate({ scrollTop: $('#comentar textarea').offset().top }, 500);
    $("#comentar textarea").focus().val(comentarios[coment_id].texto).keyup();
});

$(".navegacao .proximo").click(function() {
    if ($(this).attr("disabled")=="disabled") return;
    paginaEscolhida = paginaAtual+1;
    
    carregarImagens(paginaEscolhida);

    if (!$(this).parent().is(":first-child")) {
        $("html, body").stop().animate({scrollTop: $(".navegacao:first-child").offset().top});
    }
});

$(".navegacao .anterior").click(function() {
    if ($(this).attr("disabled")=="disabled") return;
    paginaEscolhida = paginaAtual-1;
    
    carregarImagens(paginaEscolhida);

    if (!$(this).parent().is(":first-child")) {
        $("html, body").stop().animate({scrollTop: $(".navegacao:first-child").offset().top});
    }
});

$(".navegacao select").change(function() {
    paginaEscolhida = $(this).val();
    
    carregarImagens(paginaEscolhida);

    $(this).blur();
});

$(document).on("click", "#imagens img", function() {
    $("#paginas .navegacao:last-child .proximo").click();
});

$(document).keyup(function(e) {
    if (serie.tipo==2) return;

    if (e.keyCode==37) {
        $("#paginas .navegacao:first-child .anterior").click();
    } else if (e.keyCode==39) {
        $("#paginas .navegacao:first-child .proximo").click();
    }
});

function ajustarLateral() {
    scroll = $(document).scrollTop();
    
    if ($(window).width()>999) {
        if (scroll>=69) {
            if (scroll+$(window).height()-69>$("#tudo").height()) {
                $("#tudo > aside").css({position: "absolute", top: "unset", bottom: 0});
                $("#menu-atalhos").css({position: "absolute"});
            } else {
                $("#tudo > aside").css({position: "fixed", top: 0, bottom: "unset"}).addClass("maximo");
                $("#menu-atalhos").css({position: "fixed"});
            }
            
            $(".aside-menu, #luz").css({top: 10});
        } else {
            $("#tudo > aside").css({position: "absolute", top: 0, bottom: "unset"}).removeClass("maximo");
            $(".aside-menu, #luz").css({top: 79});
            $("#menu-atalhos").css({position: "fixed"});
        }
    } else {
        if (scroll>=69) {
            $(".aside-menu, #luz").css({top: 10});
        } else {
            // $("#tudo > aside").css({position: "absolute", top: 0, bottom: "unset"}).removeClass("maximo");
            $(".aside-menu, #luz").css({top: 79});
            // $("#menu-atalhos").css({position: "fixed"});
        }
    }
}

function difData(d1, d2) {
    d2 = d2 || new Date().getTime()/1000;
    date1 = new Date(d1*1000);
    date2 = new Date(d2*1000);
    
    timeDiff = Math.abs(date2.getTime() - date1.getTime());
    difDias = timeDiff / (1000 * 3600 * 24);
    
    if (difDias>365) return Math.floor(difDias/365)+" anos atrás";
    else if (difDias>30) return Math.floor(difDias/30)+" meses atrás";
    else if (difDias>1) return Math.floor(difDias)+" dias atrás";
    else if (difDias*24>1) return Math.floor(difDias*24)+" horas atrás";
    else if (difDias*24*60>1) return Math.floor(difDias*24*60)+" minutos atrás";
    else return "Agora Mesmo";
}

function carregarComentariosNivel1(coment1, novo) {
    novo = novo || 0;
   // console.log(coment1.length);
    $.each(coment1, function(indice, value) {
        if (ultimoIdComentarios==null) ultimoIdComentarios = value.id;
        if (Number(ultimoIdComentarios) > Number(value.id)) ultimoIdComentarios = value.id;
        
        tempo = difData(value.time);
       // console.log(tempo);
        
        temp = "";

        temp += "<li class='comentario' data-id="+value.id+">";
        temp += "<a href='/perfil/"+value.nickname+"'>";
        if (value.foto_perfil==null) temp += "<img src='img/profile-default.png' class='comentario-foto'>";
        else temp += "<img src='servidor/thumbs-usuarios/"+value.foto_perfil+"' class='comentario-foto'>";
        temp += "</a>"
        temp += "<div>";
        temp += "<h4>"+value.nickname+" <span>"+tempo+"</span></h4>";

        temp += "<p>"+value.texto+"</p>";

        temp += "<ul class='resposta'>";
        temp += "</ul>";
       if (tipoUsuario>0) temp += "<button class='botao linha responder'>Responder</button>";
        
        if (tipoUsuario>1 || (typeof usuario!="undefined" && value.id_usuario==usuario.id)) {
            temp += "<div class='comentario-acoes'>";
            temp += "<i class='fas fa-trash excluir'></i>";
            temp += "<i class='fas fa-edit editar'></i>";
            temp += "</div>";
        }
        temp += "</div>";
        temp += "</li>";
        
        if (novo==0) $("#comentarios > ul").append(temp);
        else $("#comentarios > ul").prepend(temp);
        
        if (value.id in comentariosNivel2) {
            carregarComentariosNivel2(comentariosNivel2[value.id]);
        }
    });
    
    if (coment1==null || coment1.length<10) {
        if ($("#comentarios .aviso").length==0) $("#comentarios .img-loading").before("<p class='aviso'>Sem mais comentários!</p>");
        $("#comentarios .img-loading").hide();
        acabouComentarios = true;
    }
}

function carregarComentariosNivel2(coment2, novo, novoIndice) {
    novo = novo || 0;
    $.each(coment2, function(indice, value) {
       // value.data = timeToTimestamp(value.data);
        
        tempo = difData(value.time);
       // console.log(tempo);
        
        temp = "";

        temp += "<li class='comentario sub' data-id="+value.id+" data-pai="+value.id_referencia+">";
        temp += "<a href='/perfil/"+value.nickname+"'>";
        if (value.foto_perfil==null) temp += "<img src='img/profile-default.png' class='comentario-foto'>";
        else temp += "<img src='servidor/thumbs-usuarios/"+value.foto_perfil+"' class='comentario-foto'>";
        temp += "</a>"
        temp += "<div>";
        temp += "<h4>"+value.nickname+" <span>"+tempo+"</span></h4>";

        temp += "<p>"+value.texto+"</p>";
       // console.log(value.id_usuario);
        if (tipoUsuario>1 || (typeof usuario!="undefined" && value.id_usuario==usuario.id)) {
            temp += "<div class='comentario-acoes'>";
            temp += "<i class='fas fa-trash excluir'></i>";
            temp += "<i class='fas fa-edit editar'></i>";
            temp += "</div>";
        }
        temp += "</div>";
        temp += "</li>";
        
        if (novo==0) $("#comentarios .comentario[data-id="+value.id_referencia+"] .resposta").append(temp);
        else $("#comentarios .comentario[data-id="+value.id_referencia+"] .resposta").prepend(temp);
    });
} 

function pegarComentarios(ultimoIdComentarios) {
    ultimoIdComentarios = ultimoIdComentarios || 0;
   // $("#notificacoes ul").removeClass();
    data = {funcao: "pegar-comentarios", ultimoId: ultimoIdComentarios, id: titulo.id};
    console.log(data.ultimoIdComentarios);
    $.ajax({
        type: "post",
        url: "php/handler/tituloHandler.php",
        data: data,
        success: function(result) {
           // console.log(result);
            result = JSON.parse(result);
            console.log(result);
            
            if (result.estado==1) {
                $.each(result.comentariosNivel1, function(i, value) {
                    value.time = timeToTimestamp(value.data);
                    
                    comentariosNivel1.push(value);
                    comentarios[value.id] = value;
                });
                
                $.each(result.comentariosNivel2, function(i, value) {
                    value.time = timeToTimestamp(value.data);
                    
                    if (!(value.id_referencia in comentariosNivel2)) comentariosNivel2[value.id_referencia] = [];
                    comentariosNivel2[value.id_referencia].push(value);
                    comentarios[value.id] = value;
                });
                
                carregarComentariosNivel1(result.comentariosNivel1);
                carregandoComentarios = false;
               // if (result.comentariosNivel1==null) acabou = true;
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

function carregarControlesComic() {
    for(i=1;i<=Object.keys(arquivos).length;i++) {
        temp = "<option value="+i+">"+i+"</option>";
        
        $('.navegacao select').append($('<option>', {
            value: i,
            text: i
        }));
        
        paginas++;
    }
    criadas = paginas<3?paginas:3;
    $(".navegacao button.anterior").attr("disabled", true);
}

function carregarImagens(paginaEscolhida) {
    if ($("#imagens img").length<paginas) {
        for(i=paginaEscolhida-2;i<=paginaEscolhida+4;i++) {
            if ($("#imagens img[data-id='"+(i)+"']").length==0 && (i) in arquivos) {
                $("#imagens").append("<img src='servidor/titulos/comics/"+titulo.id+"/"+arquivos[i]+"' style='display: none' data-id="+(i)+">");
                criadas++;
            }
        }
    }
    
    
    $("#imagens img").hide();
    paginaAtual = paginaEscolhida;
    $("#imagens img[data-id="+paginaAtual+"]").show();
    $(".navegacao select").val(paginaAtual);
    
    if (paginaAtual==1) {
        $(".navegacao .anterior").attr("disabled", true);
        $(".navegacao .proximo").attr("disabled", false);
    }
    else if (paginaAtual==paginas) {
        $(".navegacao .anterior").attr("disabled", false);
        $(".navegacao .proximo").attr("disabled", true);
    } else {
        $(".navegacao .anterior").attr("disabled", false);
        $(".navegacao .proximo").attr("disabled", false);
    }
}

function copiarLink(link) {
    var temp = link;
    temp.execCommand("Copy");
}