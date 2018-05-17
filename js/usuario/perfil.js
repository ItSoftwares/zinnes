var comentarios = {};
var comentariosNivel1 = [];
var comentariosNivel2 = {};
var acabouComentarios = false;
var ultimoIdComentarios = null;
var carregandoComentarios = false;
var coment_id;

$(document).ready(function() {
	pegarComentarios(ultimoIdComentarios);

    if ($("#icon-favoritos").hasClass('selecionado')) $("#icon-favoritos").click();
});

$("#navegacao li").click(function() {
	este = "#"+$(this).attr("id").replace("icon-", "");

	$("#main article").children().hide();
	$(este).show();

	if (este=="#projetos") {
		if ($("#projetos ul > div").length!=0) $(este).find("ul").load("usuario/partes/seriesPerfil.php?id="+usuario_perfil.id);
	} else if (este=="#favoritos") {
		if ($("#favoritos ul > div").length!=0) $(este).find("ul").load("usuario/partes/seriesPerfil.php?id="+usuario_perfil.id+"&sigo=1");
	}
	
	$("#navegacao li").removeClass("selecionado");
	$(this).addClass("selecionado")
});

$("#ultimos-titulos h3 a").click(function(e) {
	e.preventDefault();

	$("#icon-titulos").click();
});

$("#meus-likes h3 a").click(function(e) {
	e.preventDefault();

	$("#icon-gostei").click();
});

$("#comentar textarea").keyup(function() {
    texto = $(this).val();
    
    if (texto.length==0) $("#comentar button").attr("disabled", true);
    else $("#comentar button").attr("disabled", false);
});

$("#comentar form").submit(function(e) {
    e.preventDefault();
    
    data = formToArray($(this).serializeArray());
    
    data.de = typeof comentarios[coment_id]!="undefined"?comentarios[coment_id].id:usuario.id;
    if ($("#comentar [name=id_referencia]").val()=="") delete data.id_referencia;
    if ($("#comentar [name=id]").val()=="") delete data.id;
    
    data.funcao = ("id" in data)?"atualizar-comentario":"comentar";
    if (data.funcao=="atualizar-comentario") data.nickname = comentarios[coment_id].nickname;
    else {
        data.para = usuario_perfil.id;
    }

    $("#comentar button").attr("disabled", true);
    $.ajax({
        type: "post",
        url: "php/handler/usuarioHandler.php",
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
        data.id_usuario = comentarios[coment_id].id;
        data.de = comentarios[coment_id].de;
        data.para = comentarios[coment_id].para;
        data.comentario = comentarios[coment_id].texto;

        $(".excluir, .editar").addClass("disabled");
        $.ajax({
            type: "post",
            url: "php/handler/usuarioHandler.php",
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
    
    $("#comentar #editar").show().find("span").html("Editar comentário #"+comentarios[coment_id].id+" de <b>"+comentarios[coment_id].nickname+"</b>");
    $("#comentar [name=id]").val(coment_id);
    $("html, body").animate({ scrollTop: $('#comentar textarea').offset().top }, 500);
    $("#comentar textarea").focus().val(comentarios[coment_id].texto).keyup();
});

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
        
        tempo = difData(value.data);
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
        
        // console.log(value.de==usuario.id);
        if (tipoUsuario>1 || (typeof usuario != "undefined" && value.de==usuario.id)) {
            
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
        
        tempo = difData(value.data);
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
        if (tipoUsuario>1 || (typeof usuario != "undefined" && value.de==usuario.id)) {
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
    data = {funcao: "pegar-comentarios", ultimoId: ultimoIdComentarios, id: usuario_perfil.id};
    console.log(data.ultimoIdComentarios);
    $.ajax({
        type: "post",
        url: "php/handler/usuarioHandler.php",
        data: data,
        success: function(result) {
           // console.log(result);
            result = JSON.parse(result);
            console.log(result);
            
            if (result.estado==1) {
                $.each(result.comentariosNivel1, function(i, value) {
                    // var t = value.data.split(/[- :]/);

                    // value.data = new Date(Date.UTC(t[0], t[1]-1, t[2], t[3], t[4], t[5])).getTime()/1000;
                    
                    comentariosNivel1.push(value);
                    comentarios[value.id] = value;
                });
                
                $.each(result.comentariosNivel2, function(i, value) {
                    // var t = value.data.split(/[- :]/);

                    // value.data = new Date(Date.UTC(t[0], t[1]-1, t[2], t[3], t[4], t[5])).getTime()/1000;
                    
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