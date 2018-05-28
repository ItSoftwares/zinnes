var tags = [];
var funcaoSerie = "";
var id_serie;

$(document).ready(function() {
    if (series==null) series = [];
    else {
        temp = {};
        $.each(series, function(i, value) {
            temp[value.id] = value;
        });
        series = temp;
    }
    
    atualizarSeries();
});

$("#enviar-confirmacao").click(function() {
    data = {funcao: "confirmar-conta"};
    
    $(this).attr("disabled", true);
    $.ajax({
        type: "post",
        url: "php/handler/usuarioHandler.php",
        data: data,
        success: function(result) {
            console.log(result);
            result = JSON.parse(result);
            
            if (result.estado==1) {
                chamarPopupInfo(result.mensagem);
                
                $("#enviar-confirmacao").attr("disabled", false);
            } else {
                chamarPopupInfo(result.mensagem);
                $("#enviar-confirmacao").attr("disabled", false);
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

$("#ativar").click(function() {
    data = {funcao: "confirmar-conta-moderador", id: usuario.id, confirmado: 1};
    
    $(this).attr("disabled", true);
    $.ajax({
        type: "post",
        url: "php/handler/usuarioHandler.php",
        data: data,
        success: function(result) {
            console.log(result);
            result = JSON.parse(result);
            
            if (result.estado==1) {
                chamarPopupInfo(result.mensagem);
                usuario.confirmado = 1;
                $("#enviar-confirmacao").attr("disabled", false);
            } else {
                chamarPopupInfo(result.mensagem);
                $("#enviar-confirmacao").attr("disabled", false);
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

$('#form-serie').on('keypress', function (e) {
    var keyCode = e.keyCode || e.which;
    if (keyCode === 13) {
        e.preventDefault();
        
        temp = $("#tags").val(); 
        
        if (temp.length>0) {
            if (tags.indexOf(temp)!=-1) {
                chamarPopupInfo("Tag já adicionada!");
                return;
            } else if (temp.indexOf("_")!=-1) {
                chamarPopupInfo("Tag Inválida!");
                return;
            }
            
            tags.push(temp);
            temp = "<li>"+temp+" <i class='fa fa-times remover-tag'></i></li>";

            $("#lista-tags").append(temp);
            $("#tags").val("");
            console.log(tags);
            
            if (tags.length==5) {
                $("#tags").attr("disabled", true);
            }
        }
    }
});

$(document).on("click", ".remover-tag", function() {
    temp = $(this).parent().text();
    
    i = tags.indexOf(temp);
    tags.splice(i, 1);
    
    $(this).parent().remove();
    $("#tags").attr("disabled", false);
    console.log(tags);
})

$("#form-serie .fechar").click(function() {
    $("#form-serie").fadeOut();
});

$("#nova-novel a, #nova-comic a").click(function(e) {
    e.preventDefault();
    if (usuario.confirmado==0) {
        chamarPopupInfo("Você precisa verificar sua conta para poder criar series. Clique no botão <B>ENVIAR CONFIRMAÇÃO</B> e lhe enviaremos um email com link para confirmação!");
        return;
    }

    tags = [];
    $("#lista-tags li").remove();
    
    tipo = $(this).parent().attr("data-tipo");
    console.log(tipo)
    if (tipo=="comic") {
        $("#form-serie [name=tipo]").val(1);
    } else if (tipo=="novel") {
        $("#form-serie [name=tipo]").val(2);
    }
    
    $("#form-serie").fadeIn().css({display: "flex"});
    $("#form-serie h3").text("Nova Serie");
    $("#form-serie [name=tipo]").attr("disabled", false);
    $("#form-serie [name=nome]").focus();
    funcaoSerie = "nova";
});

$(document).on("click", ".editar:not(.disabled)", function(e) {
    e.preventDefault();
    tags = [];
    $("#lista-tags li").remove();
    id_serie = $(this).closest("li").attr("data-id");
    
    $.each(series[id_serie], function(i, value) {
        $("#form-serie [name="+i+"]").val(value);
        
        if (i=="tags") {
            tags = value.split("__")
            $.each(tags, function(j, tag) {
                temp = "<li>"+tag+" <i class='fa fa-times remover-tag'></i></li>";

                $("#lista-tags").append(temp);
            });
            
            if (tags.length==5) {
                $("#tags").attr("disabled", true);
            }
        }
    });
    
    $("#form-serie").fadeIn().css({display: "flex"});
    $("#form-serie h3").text("Editar Serie");
    $("#form-serie [name=tipo]").attr("disabled", true);
    $("#form-serie [name=nome]").focus();
    funcaoSerie = "atualizar";
});

$(document).on("click", ".excluir:not(.disabled)", function(e) {
    e.preventDefault();
    id_serie = $(this).closest("li").attr("data-id");
    data = {id: id_serie, funcao: "excluir", id_usuario: usuario.id};
    
    confirmacao("Remover serie", "Deseja realmente remover a serie? Todos os capítulos serão permanentemente perdidos!", function() {
        // console.log("apagar");
        chamarPopupLoading("Aguarde enquanto apagamos os capítulos e a serie!");
        $(".projeto-acoes i").addClass('disabled');
        $.ajax({
            type: "post",
            url: "php/handler/serieHandler.php",
            data: data,
            success: function(result) {
               // console.log(result);
                result = JSON.parse(result);
                console.log(result);
                
                if (result.estado==1) {
                    chamarPopupConf(result.mensagem);
                    
                    delete series[id_serie];

                    atualizarSeries();
                    
                    $(".projeto-acoes i").removeClass('disabled');
                } else {
                    chamarPopupInfo(result.mensagem);
                    $(".projeto-acoes i").removeClass('disabled');
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
    }, function() {});
});

$("#form-serie form").submit(function(e) {
    e.preventDefault();
    
    data = formToArray($(this).serializeArray());

    if (data.id_genero==0) {
    	chamarPopupInfo('Escolha um gênero válido!');
    	$("[name=id_genero]").focus();
    	return;
    }
    
    //gerar tags
    data.tags = tags.join("__");
    data.funcao = funcaoSerie;
    
    if (funcaoSerie=="atualizar") {
        data.id = id_serie;
        data.id_usuario = series[id_serie].id_usuario;
    }
    
    $("#form-serie button").attr("disabled", true);
    $.ajax({
        type: "post",
        url: "php/handler/serieHandler.php",
        data: data,
        success: function(result) {
           // console.log(result);
            result = JSON.parse(result);
            console.log(result);
            
            if (result.estado==1) {
                chamarPopupConf(result.mensagem);
                if (data.funcao=="nova") series[result.serie.id] = result.serie;
                atualizarSeries();
                
                $("#form-serie form")[0].reset();
                $("#form-serie i.fechar").click();
                $("#form-serie button").attr("disabled", false);
            } else {
                chamarPopupInfo(result.mensagem);
                $("#form-serie button").attr("disabled", false);
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

function atualizarSeries() {
    if (usuario.confirmado==0) return;
    
    $("article ul").children("li, button").remove();

    if (Object.keys(series).length>0) {
        $("article .aviso").hide();
    } else {
        $("article .aviso").show().css({display: "table"})
    }
    
    $.each(series, function(i, value) {
        value.time = timeToTimestamp(value.data_criacao);
        
        value.capitulos = value.capitulos==null?0:value.capitulos;
        value.visualizacoes = value.visualizacoes==null?0:value.visualizacoes;
        value.gosteis = value.gosteis==null?0:value.gosteis;
        value.seguidores = value.seguidores==null?0:value.seguidores;
        temp = "";

        temp += "<li class='projeto' data-id='"+i+"' data-tipo='"+value.tipo+"'>";
        temp += "<div class='projeto-fundo'></div>";
        temp += "<header class='projeto-topo'> ";  

        temp += value.thumb_projeto!=null?"<img src='servidor/projetos/thumbs/"+value.thumb_projeto+"'>":"<i class='fa fa-eye-slash'></i>";
        temp += "<div>";
        temp += "<h3><a href='serie/"+value.id+"'>"+value.nome+"</a></h3>";
        temp += "<div class='lado-a-lado'>";
        temp += "<p><b>"+value.seguidores+" Seguidores</b></p>";
        temp += "<span class='dot'></span>";
        temp += "<p><b>"+value.capitulos+" Capítulos</b></p>";
        temp += "<span class='dot'></span>";
        temp += "<p><i class='fa fa-clock'></i> "+getDataAbreviada(value.time)+"</p>";
        temp += "</div>";
        temp += "</div>";
        temp += "</header>";

        temp += "<div class='projeto-info'>";
        temp += "<ul class='lado-a-lado'>";
        temp += "<li class='projeto-visualizacoes'>";
        temp += "<i class='fa fa-eye'></i>";
        temp += "<h4>"+value.visualizacoes+"</h4>";
        temp += "<p>Visualizações</p>";
        temp += "</li>";
        temp += "<li class='dot'></li>";
        temp += "<li class='projeto-gosteis'>";
        temp += "<i class='fa fa-heart'></i>";
        temp += "<h4>"+value.gosteis+"</h4>";
        temp += "<p>Gosteis</p>";
        temp += "</li>";
        temp += "</ul>";
        temp += "</div>";

        temp += "<nav class='projeto-acoes'>";
        temp += "<a href='"+(value.tipo==1?"comic/"+value.id:"novel/"+value.id)+"' class='botao2 novo-capitulo' title='Novo Capítulo'><i class='fa fa-plus novo'></i></a>";
        temp += "<a href='' class='botao2 editar-serie' title='Editar Serie'><i class='fa fa-edit editar'></i></a>";
        temp += "<a href='' class='botao2 excluir-serie' title='Excluir Serie'><i class='fa fa-trash excluir'></i></a>";
        temp += "</nav>";
        temp += "</li>";
        
        projeto = $(temp).appendTo("#series article > ul");
        projeto.css({opacity: 1});;
       // $("").append(temp);
    });
    
   // $("li.projeto").css({opacity: 1});
}