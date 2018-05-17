var pin;
var imagem_visualizada;
var imagem_temp = 0;
var dimensoes={};
var cache = {};

$(document).ready(function() {
	$.each(slides, function(i, value) {
		dimensoes[i] = {w:0, h:0};
	});

    carregarLinks();
});

$("#pin input").keyup(function() {
	if ($(this).parent().attr("id")=="pin_4") {
		pin = "";

		$("#pin input").each(function(i, value) {
			pin += $(this).val();
		});

		if (codigoEntrada!="aaaa") {
			if (pin==codigoEntrada) {
				$("#pin").fadeOut();
				$('#div_session_write').load('adm/escreverSessao.php?pin=valido');
			} else {
				resetarFormPin("PIN incorreto!");
			}
		}
	} else {
		// console.log($(this).next())
		if ($(this).val().length==1) {
			$(this).parent().next().find("input").focus();
		} else if ($(this).val().length>1) {
			resetarFormPin("Digite um PIN válido!");
		}
	}
});

$("#email input[name=usuario]").bind("keyup", function(e) {
    valor = $(this).val();

    clearTimeout($.data(this, 'timer'));

    if (e.keyCode == 13) {
        pesquisar(valor, true);
        e.preventDefault(); 
    }
    else
        $(this).data('timer', setTimeout(function(){
            pesquisar(valor);
        }, 500));
});

$(document).on("click", "#lista-usuarios li", function() {
    nickname = $(this).find("a").text();

    $("#email input[name=usuario]").val(nickname).focus().select();
    $("#lista-usuarios").children().remove();
});

$("#definir-pin").click(function() {
	if (pin.length!=4 || typeof Number(pin) != "number") {
		resetarFormPin("PIN inválido!");
		return;
	}

	$("#pin button, #pin input").attr("disabled", true);
    $.ajax({
        type: "post",
        url: "php/handler/usuarioHandler.php",
        data: {pin: pin, funcao: "atualizarPin", id_usuario: usuario.id},
        success: function(result) {
            result = JSON.parse(result);
            console.log(result);
            
            if (result.estado==1) {
                chamarPopupConf(result.mensagem);

                $("#pin").fadeOut();

                $("#pin button, #pin input").attr("disabled", false);
            } else {
                chamarPopupInfo(result.mensagem);
                $("#pin button, #pin input").attr("disabled", false);
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

$("#redefinir").click(function() {
    $("#definir-pin").show()
    $("#pin").fadeIn().find("h1").html("Defina um PIN de acesso <br>Somente números");
    codigoEntrada = "aaaa";
});

$(document).keyup(function(e) {
    if ($("#visualizar-imagem").css("display")!="flex") return;

    if (e.keyCode==37) {
        $(".setas#anterior").click();
    } else if (e.keyCode==39) {
        $(".setas#proximo").click();
    }
});

$("#moderadores > button").click(function(e) {
	e.preventDefault();
	msg = "Escolha o usuário que vc quer tornar Moderador da página!";
	msg = msg.replace(/ /g, "%20");
	$('#div_session_write').load('adm/escreverSessao.php?info_msg='+msg+"&moderador=1");
	// return;
	location.href = "/pesquisa/autor/1";
});

$("#moderadores .remover").click(function(e) {
	e.preventDefault();

	li = $(this).closest(".moderador");
	usuarioId = li.attr("data-id");

	$.ajax({
        type: "post",
        url: "php/handler/usuarioHandler.php",
        data: {funcao: "moderador", id: usuarioId},
        success: function(result) {
            result = JSON.parse(result);
            console.log(result);
            
            if (result.estado==1) {
                chamarPopupConf(result.mensagem);

                li.remove();

                if ($(".moderador").length==0) $("#moderadores ul").append("<p class='aviso'>Nenhum moderador</p>");
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
});

$("#slides div img").click(function() {
	src = $(this).attr("src");

	imagem_visualizada = $(this).closest("div").attr("data-index");

	$("#visualizar-imagem").fadeIn().css({display: "flex"}).find('img').attr("src", src);
});

$("#visualizar-imagem .fechar").click(function() {
	$("#visualizar-imagem").fadeOut();
});

$(".setas").click(function() {
	if ($(this).attr("id")=='anterior' && imagem_visualizada!=0) {
		imagem_visualizada--;
		$("#visualizar-imagem img").attr("src", "/servidor/slides/"+slides[imagem_visualizada]);
	} else if ($(this).attr("id")=='proximo' && imagem_visualizada!=slides.length-1) {
		imagem_visualizada++;
		$("#visualizar-imagem img").attr("src", "/servidor/slides/"+slides[imagem_visualizada]);
	}
});

$(document).on("change", "#slides input[type=file]", function() {
    input = this;
    pai = $(this).closest("div");

    if (input.files && input.files[0]) {
        if (input.files[0].size>3*1024*1024) {
            chamarPopupInfo("A imagem deve ter até 3Mb");
            restaurarImagem(pai);
            return;
        }
        
        var reader = new FileReader();
        var img = new Image();
        
        img.onload = function() {
            if (img.width>1400 || img.height>840) {
                chamarPopupInfo("A imagem deve ter menos de 1400 Pixels de largura e 840 pixels de altura!");
                restaurarImagem(pai);
                return;
            }
            
            if (pai.attr("id")=="novo") {
            	console.log("a");
                $("#novo i.icon").hide();
                if (imagem_temp==0) imagem_temp = $("<img>").attr("src", img.src).appendTo("#novo");
                else imagem_temp.attr("src", img.src);

                dimensoes[slides.length] = {w:0 , h:0};
                pai.attr("data-index", slides.length);
            } else {
                $(pai).find("img").attr("src", img.src);
            }
            
            pai.find("i.salvar").show().css({display: "flex"});
            pai.find("i.remover").hide();

            dimensoes[pai.attr("data-index")].w = img.width;
        	dimensoes[pai.attr("data-index")].h = img.height;
        }

        reader.onload = function (e) {
            img.src = e.target.result;
        }

        reader.readAsDataURL(input.files[0]);
    }
}); 

$(document).on("click", "#slides i.remover", function() {
    imagemSrc = $(this).closest("div").attr("data-index");

    imagemSrc = slides[imagemSrc];

    $("#slides .remover, #slides .salvar").addClass("disabled");
    $("#slides input").attr("disabled", true);
    $.ajax({
        type: "post",
        url: "php/handler/usuarioHandler.php",
        data: {funcao: "removerSlide", id_usuario: usuario.id, imagem: imagemSrc, slides: JSON.stringify(slides)},
        success: function(result) {
            console.log(result);
            result = JSON.parse(result);
            
            if (result.estado==1) {
                chamarPopupConf(result.mensagem);

                slides = result.slides;
                links = result.links;
                carregarLinks();
                atualizarSlides();

                $("#slides .remover, #slides .salvar").removeClass("disabled");
                $("#slides input").attr("disabled", false);
            } else {
                chamarPopupInfo(result.mensagem);
                $("#slides .remover, #slides .salvar").removeClass("disabled");
                $("#slides input").attr("disabled", false);
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

$(document).on("click", "#slides i.salvar", function() {
    data = new FormData();
    pai = $(this).closest("div");
    index = pai.attr("data-index");

    if (pai.attr("id")!="novo") {
        data.append("atualizar", true);
        data.append("imagemAntiga", slides[index]);
    } else data.append("numero", slides.length+1);

    data.append("funcao", "adicionarSlide");
    data.append("slide", pai.find("input")[0].files[0]);
    data.append("w", dimensoes[index].w);
    data.append("h", dimensoes[index].h);
    // console.log(data); return;
    chamarPopupLoading("Aguarde enquanto atualizamos o capítulo!");
    
    $("#slides .remover, #slides .salvar").addClass("disabled");
    $("#slides input").attr("disabled", true);
    $.ajax({
        type: "post",
        url: "php/handler/usuarioHandler.php",
        data: data,
        success: function(result) {
            console.log(result);
            result = JSON.parse(result);
            
            if (result.estado==1) {
                chamarPopupConf(result.mensagem);

                if (data.get("atualizar")) {
                    slides[index] = result.slide;
                } else {
                    slides.push(result.slide);
                }

                links = result.links;
                carregarLinks();

                restaurarImagem(pai);
                atualizarSlides();
                
                $("#slides .remover, #slides .salvar").removeClass("disabled");
                $("#slides input").attr("disabled", false);

            } else {
                chamarPopupInfo(result.mensagem);

                $("#slides .remover, #slides .salvar").removeClass("disabled");
                $("#slides input").attr("disabled", false);

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
});

$("#slides button").click(function() {
    $("#links").fadeIn().css({display: "flex"});
});

$("#links .fechar").click(function() {
    $("#links").fadeOut();
});

$("#comentarios input").keyup(function(e) {
    if (e.keyCode==13) {
        busca = $(this).val().replace(/ /g, "%20");
        $('#comentarios ul').load('adm/comentarios.php?busca='+busca);
    }
});

$("#links form").submit(function(e) {
    e.preventDefault();
    e.preventDefault();

    data = formToArray($(this).serializeArray());
    data.funcao = "atualizarLinkSlide";

    chamarPopupLoading("Aguarde, estamos atualizando os links!");
    $("#links button, #links input").attr("disabled", true);
    $.ajax({
        type: "post",
        url: "php/handler/usuarioHandler.php",
        data: data,
        success: function(result) {
            console.log(result);
            result = JSON.parse(result);
            
            if (result.estado==1) {
                chamarPopupConf(result.mensagem);

                links = result.links;
                carregarLinks();

                $("#links button, #links input").attr("disabled", false);
            } else {
                chamarPopupInfo(result.mensagem);
                $("#links button, #links input").attr("disabled", false);
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

$("#email form").submit(function(e) {
    e.preventDefault();

    data = formToArray($(this).serializeArray());
    data.funcao = "emailModerador";

    if (data.mensagem.length==0 || data.mensagem=="") {
        chamarPopupInfo("Escreva algo!");
        $("#email textarea").focus();
        return;
    }

    chamarPopupLoading("Aguarde, estamos enviando o Email!");
    $("#email button, #email input, #email textarea").attr("disabled", true);
    $.ajax({
        type: "post",
        url: "php/handler/usuarioHandler.php",
        data: data,
        success: function(result) {
            console.log(result);
            result = JSON.parse(result);
            
            if (result.estado==1) {
                chamarPopupConf(result.mensagem);

                $("#email form")[0].reset();
                $("#email button, #email input, #email textarea").attr("disabled", false);
            } else {
                chamarPopupInfo(result.mensagem);
                $("#email button, #email input, #email textarea").attr("disabled", false);
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

function restaurarImagem(pai) {
	if (pai.attr("id")=="novo") {
		imagem_temp.remove();
        imagem_temp = 0;
		pai.find("i.icon").show();
		pai.find("i.salvar").hide();
	} else {
		index = pai.attr("data-index");

		pai.find("img").attr("src", "/servidor/slides/"+slides[index]);
	}

	pai.find("input[type=file]").val("");
}

function atualizarSlides() {
    $("#slides section div:not(#novo)").remove();
    contador = 0;
    $.each(slides, function(i, value) {
        contador++;
        temp = "";

        temp += "<div data-index="+i+">";
        temp += "<i class='fa fa-save salvar' title='Salvar imagem para slide'></i>";
        temp += "<i class='fa fa-trash remover' title='Remover slide'></i>";
        temp += "<img src='/servidor/slides/"+value+"'>";

        temp += "<label for='slide_"+contador+"'>Trocar Imagem</label>";
        temp += "<input type='file' name='slide_"+contador+"' id='slide_"+contador+"'>";
        temp += "</div>";

        $("#slides #novo").before(temp);
    });

    if (contador==6) $("#novo").hide();
    else $("#novo").show();
}

function carregarLinks() {
    $("#links form .campos").children().remove();

    $.each(links, function(i, value) {
        temp = "";

        temp += "<div class='input linha'>";
        temp += "<input type='text' name='link_"+value.numero+"' value='"+value.link+"' placeholder='Link do slide "+value.numero+"'>";
        temp += "<i class='fa fa-link'></i>";
        temp += "</div>";

        $("#links form .campos").append(temp);
    });
}

function atualizarLista(data) {
    // console.log(data);
    count = 0;
    $.each(data, function(i, value) {
        if (count==10) return true;
        elem = $('<li><a></a></li>');

        elem.find("a").text(value.nickname);

        elem.appendTo($("#lista-usuarios"));
        count++;
    });

    console.log(cache);
}

function pesquisar(palavra, force) {
    $("#lista-usuarios").children().remove();

    if (!force && palavra.length < 4) 
        return '';

    if(cache.hasOwnProperty(palavra)) {
        atualizarLista(cache[palavra]);
        return;
    }

    // console.log(palavra);
    $.ajax({
        type: "post",
        url: "php/handler/usuarioHandler.php",
        //: false,
        data: {nickname: palavra, funcao: "pesquisar"},
        success: function(result) {
            result = JSON.parse(result);
            // console.log(result);
            
            cache[palavra] = result.resultados;

            atualizarLista(result.resultados);
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

function resetarFormPin(mensagem) {
    $("#pin input").val("");
    chamarPopupInfo(mensagem);
    $("#pin_1 input").focus();
}