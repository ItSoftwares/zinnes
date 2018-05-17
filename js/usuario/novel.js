var contador = 1;
var larguraPagina;
var original;
var imagem = {w:0, h:0};
var agendar;
var publicado = false;
var imagemTemp = 0;
//var diaEscolhido;

$(document).ready(function() {
    larguraPagina = $(window).width();
    $(document).scroll();
    
    if ("id" in novel) {
        novel.data = timeToTimestamp(novel.data);
        novel.data_ultima_atualizacao = timeToTimestamp(novel.data_ultima_atualizacao);
        
        $("#ultima-atualizacao").text("Ultima atualização feita em "+getDataAbreviada(novel.data_ultima_atualizacao)+", às "+getHora(novel.data_ultima_atualizacao));
        $(".publicar-agora").attr("disabled", false);
        
        if (novel.data_lancamento==null) {
            $("#agendar-box button").attr("disabled", true);
        } else {
            if (novel.rascunho==1) {
                novel.data_lancamento = timeToTimestamp(novel.data_lancamento);
                $(".dia-escolhido").text(getData(novel.data_lancamento));
                
                dataTemp = new Date(novel.data_lancamento*1000);
                diaEscolhido = dataTemp.getDate();
                mesEscolhido = dataTemp.getMonth();
                anoEscolhido = dataTemp.getFullYear();
                $(".semana span[data-dia="+diaEscolhido+"]").addClass("selecionado");
                
                atualizarCalendario(dataTemp.getMonth()+1, dataTemp.getFullYear());
            }
        }

        if (novel.rascunho==0) {
            publicado = true;
            $(".agendar").attr("disabled", true);
            $(".salvar").attr("disabled", true);
            $(".publicar-agora").html("Atualizar <i class='fa fa-check'></i>");
        } else {
            $(".salvar, .agendar").attr("disabled", false);
        }
    } 
    else {
        $("#ultima-atualizacao").text("Nada foi salvo ainda!");
        
        $(".publicar-agora").attr("disabled", true);
        novel = {};
    }
    $("#texto p").each(function(i, elem) {
        value = $(elem).text();
        if (value=="" || value.length==0 || value=="\n" || value=="\r") {
            $(elem).remove();
        }
    });
    $("#texto").keyup();
    original = $(".upload-thumb-label img").attr("src");
});

$(document).scroll(function() {
    scroll = $(document).scrollTop();
    alturaTexto = $("#texto").offset().top;
    textoAltura = $("#ui-novel").height();
    
    if ($(window).width()>999) {
        $("#ui-novel").css("left", "");
        $("#ui-novel").css("bottom", "");
        if (scroll+10>alturaTexto) {
            if (scroll+textoAltura-10>alturaTexto+$("#texto").height()) {
                $("#ui-novel").css({position: "absolute", top: $("article").height()-textoAltura, right: "calc(100% + 5px)"});
            } else {
                right = larguraPagina-(larguraPagina-1000)/2+5;
                $("#ui-novel").css({position: "fixed", top: 10, right: right});
            }
        } else {
            $("#ui-novel").css({position: "absolute", top: 90, right: "calc(100% + 5px)"});
        }
    } else {
        $("#ui-novel").css("right", "");
        $("#ui-novel").css("top", "");
        if (scroll+25>alturaTexto) {
            if (scroll+textoAltura-10>alturaTexto+$("#texto").height()) {
                // console.log(1)
            } else {
                $("#ui-novel").css({position: "absolute", left: "3px"});
                if ($("#ui-novel").hasClass('aberto')) $("#ui-novel").removeClass()
            }
        } else {
            $("#ui-novel").css({position: "fixed", left: "10px"});
        }
    }
});

$("#ui-novel i").click(function() {
    $("#ui-novel").toggleClass('aberto');
});

$(window).resize(function() {
    larguraPagina = $(this).width();
});

$("article label").hover(function() {
    $(this).find("span").fadeIn();
}, function() {
    $(this).find("span").fadeOut();
});

$(".upload-thumb-label input[type=file]").change(function() {
    input = this;
    
    if (input.files && input.files[0]) {
        if (input.files[0].size>2*1024*1024) {
            chamarPopupInfo("A imagem deve ter até 2Mb");
            limparImagemPerfil();
            return;
        }
        
        var reader = new FileReader();
        var img = new Image();
        
        img.onload = function() {
            if (img.width<200 || img.height<200) {
                chamarPopupInfo("A imagem deve ter pelo menos 200 Pixels");
                limparImagemPerfil();
                return;
            }
            
            proporcaoHeight = 200*img.height/img.width;
            
            if (proporcaoHeight<200) {
                chamarPopupInfo("Proporções inválidas. A imagem deve ser quadrada");
                limparImagemPerfil();
                return;
            }
            
            if (typeof original=="undefined") {
                $(".upload-thumb-label > i").hide();
                if (imagemTemp==0) imagemTemp = $("<img>").attr("src", img.src).appendTo(".upload-thumb-label");
                else imagemTemp.attr("src", img.src);
            } else {
                $(".upload-thumb-label img").attr("src", img.src);
            }
            
            imagem.w = img.width;
            imagem.h = img.height;
        }

        reader.onload = function (e) {
            img.src = e.target.result;
        }

        reader.readAsDataURL(input.files[0]);
    }
}); 

$("#titulo").keyup(function() {
    titulo = $(this).val();
    $("[name=nome]").val(titulo);
   // console.log(titulo);
});

$("#texto").keydown(function(e) {
    if ($(this).html()=="<br>") $(this).text("");

    tamanhoTexto = $(this).text().length
    if (e.which != 8 && tamanhoTexto>35000-1) e.preventDefault();
    
    if(e.keyCode == '13') document.execCommand('formatBlock', false, 'p');
});

$("#texto").keyup(function(e) {
    tamanhoTexto = $(this).text().length;
    
    $("[name=texto]").val($("#texto").html());
    
    // console.log(e.keyCode)
    $("#caracteres b").text(tamanhoTexto);

    if (tamanhoTexto>0) {
        $("#ui-novel button").attr("disabled", false);
        if (publicado) return;
        $("#cabecalho .salvar, #info-botoes .salvar").attr("disabled", false);
        if (typeof novel.id != "undefined" && novel.rascunho==1) $(".agendar").attr("disabled", false);
    } else {
        $("#ui-novel button").attr("disabled", true);
        if (publicado) return;
        $("#cabecalho button:not(.publicar-agora), #info-botoes button:not(.publicar-agora)").attr("disabled", true);
    }
})

$("#agendar-box .fechar").click(function() {
    $("#agendar-box").fadeOut();
});

$(".agendar").click(function() {
    $("#agendar-box").fadeIn().css({display: "flex"});
});

$("#negrito").click(function() {
    document.execCommand("bold")
});

$("#sublinhado").click(function() {
    document.execCommand("underline")
});

$("#italico").click(function() {
    document.execCommand("italic")
});

$("#texto")[0].addEventListener("paste", function(e) {
    // cancel paste
    e.preventDefault();

    // get text representation of clipboard
    // console.log(e.clipboardData.getData("text"));
    var text = e.clipboardData.getData("text").split("\n");

    temp = "";
    $.each(text, function(i, value) {
        if (value!="" && value.length>0 && value!="\n" && value!="\r") {
            temp += "<p>"+value+"</p>";
        }
        // if (value.length==1) console.log(value=="\r");
    });
    text = temp;
    // insert text manually
    document.execCommand("insertHTML", false, text);
});

$(".semana span").click(function() {
    $(".semana span").removeClass("selecionado");
    $(this).addClass("selecionado");
    
    diaEscolhido = Number($(this).text());
    mesEscolhido = mesCalendario;
    anoEscolhido = anoCalendario;
    
    hoje = new Date();
    dataTemp = new Date(anoCalendario, mesCalendario, diaEscolhido);
    $(".dia-escolhido").text(getData(dataTemp.getTime()/1000));
    
    if (dataTemp.getTime()>hoje.getTime()) $("#agendar-box button").attr("disabled", false);
    else $("#agendar-box button").attr("disabled", true);
});

function limparImagemPerfil() {
    if (typeof original=="undefined") {
        imagemTemp.remove();
        $(".upload-thumb-label > i").show();
    }
    else {
        $(".upload-thumb-label img").attr("src", original);
        $(".upload-thumb-label input[type=file]").val("");
    }
    
    imagem.w = 0;
    imagem.h = 0;
}

// SUBMITS
$(".salvar").click(function() {
    $("#enviar-novel").submit();
});

$("#enviar-novel").submit(function(e) {
    e.preventDefault();
    
    $("#texto p:empty").remove();
    $("[name=texto]").val($("#texto").html());
    
    var data = new FormData(this);
    
    temp = formToArray($(this).serializeArray());
    
    temp.id_projeto = serie.id;
    
    if (!('id' in novel)) temp.funcao = "novo";
    else {
        temp.funcao = "atualizar";
        temp.id = novel.id;
    }
    
    $.each(temp, function(i, value) {
        data.append(i, value);
    });
    
    if (imagem.w>0) {
        data.append("larguraImagem", imagem.w);
        data.append("alturaImagem", imagem.h);
    }
    
    atualizarNovel(data, $("#cabecalho button, #info-botoes button"));
    
    $(".publicar-agora").attr("disabled", "false");
});

$("#agendar-box button").click(function() {
    var data = new FormData();
    data.append("data_lancamento", getTimeEscolhido(diaEscolhido, mesCalendario+1, anoCalendario)/1000);
   // console.log(data.get('data_lancamento')); return;
    data.append("funcao", "atualizar");
    data.append("id", novel.id);
    
    atualizarNovel(data, $(this), "Data de lançamento do título foi atualizado!");
});

$(".publicar-agora").click(function() {
    if (novel.rascunho==1) {
        $("#texto p:empty").remove();
        $("[name=texto]").val($("#texto").html());
        
        var data = new FormData($("#enviar-novel")[0]);

        temp = formToArray($("#enviar-novel").serializeArray());

        temp.id_projeto = serie.id;
        temp.funcao = "publicar";
        temp.id = novel.id;

        $.each(temp, function(i, value) {
            data.append(i, value);
        });

        if (imagem.w>0) {
            data.append("larguraImagem", imagem.w);
            data.append("alturaImagem", imagem.h);
        }

        atualizarNovel(data, $("#cabecalho button, #info-botoes button"));
    } else {
        $("#enviar-novel").submit();
    }
});

function atualizarNovel(data, botoes, mensagemExito) {
    botoes.attr("disabled", true);
    mensagemExito = mensagemExito || 0;

    data.append("id_usuario", serie.id_usuario);
    data.append("serie", serie.nome);
    if (data.get("funcao")=="novo") data.append("serie", serie.nome);

    // console.log(data); return;

    $.ajax({
        type: "post",
        url: "php/handler/tituloHandler.php",
        data: data,
        success: function(result) {
            console.log(result);
            result = JSON.parse(result);
            
            if (result.estado==1) {
                chamarPopupConf(mensagemExito!=0?mensagemExito:result.mensagem);

                $.each(result.titulo, function(i, value) {
                    novel[i] = value;
                });
                
                novel.data = timeToTimestamp(novel.data);
                novel.data_ultima_atualizacao = timeToTimestamp(novel.data_ultima_atualizacao);
                
                $("#ultima-atualizacao").text("Ultima atualização feita em "+getDataAbreviada(novel.data_ultima_atualizacao)+", às "+getHora(novel.data_ultima_atualizacao));
                
                window.history.pushState("Atualização url", "Titulo", "novel/"+serie.id+"/"+novel.id);
                
                document.title = novel.nome+" | ZINNES";
                
                if (data.get("funcao")=="publicar") {
                    setTimeout(function() {
                        location.href = "lerNovel/"+novel.id;
                    }, 2000);
                }
                
                botoes.attr("disabled", false);
            } else {
                chamarPopupInfo(result.mensagem);
                botoes.attr("disabled", false);
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
        },
        cache: false,
        contentType: false,
        processData: false
    });
}