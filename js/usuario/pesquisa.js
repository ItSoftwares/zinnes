$(document).ready(function() {
    carregarPaginas();
});

$("#pesquisa input").keydown(function(e) {
    // console.log(e.keyCode);
    if (e.keyCode==13) {
        if ($(this).val().length>0) {
            location.href = "/pesquisa/"+tipo+"/1/"+$(this).val();
        }
    }

    if (pagina<Math.ceil(qtd/20)) {
    	$("#paginas #proxima").attr("disabled", false);
    }

    if (pagina>1) {
    	$("#paginas #anterior").attr("disabled", false);
    }
});

$("#abas li").click(function(e) {
	e.preventDefault();

	if ($(this).hasClass("comic")) {
		location.href = "/pesquisa/comic/1/"+pesquisa;
	} else if ($(this).hasClass("novel")) {
        location.href = "/pesquisa/novel/1/"+pesquisa;
    } 
    else if ($(this).hasClass("serie")) {
		location.href = "/pesquisa/serie/1/"+pesquisa;
    } 
    else {
		location.href = "/pesquisa/autor/1/"+pesquisa;
	}
});

$(document).on("click", "#paginas span:not(.atual)", function() {
	pagina = $(this).attr("data-pagina");
	location.href = "/pesquisa/"+tipo+"/"+pagina+"/"+pesquisa;
});

$("#proxima").click(function() {
    link = "pesquisa/" + tipo + "/" + (Number(pagina)+1) + (pesquisa!=""? "/" + pesquisa : "");
    // console.log(link); return;
    location.href = link;
});

$("#anterior").click(function() {
    link = "pesquisa/" + tipo + "/" + (Number(pagina)-1) + (pesquisa!=""? "/" + pesquisa : "");
    // console.log(link); return;
    location.href = link;
});

function carregarPaginas() {
    ultima = Math.ceil(qtd/20);

    $("#paginas span").remove();

    inicio = pagina-5<1?1:pagina-5;

    fim = ultima<inicio+10?ultima:inicio+10

    for (var i = inicio; i <= fim; i++) {
        temp = "";
        if (inicio>1) {
            temp += "<span data-pagina=1>1</span>";
            temp += "<span>...</span>";
        }

        temp += "<span data-pagina="+i+">"+i+"</span>";

        if (i==inicio+10 && i<ultima) {
            temp += "<span>...</span>";
            temp += "<span data-pagina="+ultima+">"+ultima+"</span>";
        }

        $("#paginas #proxima").before(temp);
    }

    if (pagina>1) $("#anterior").attr("disabled", false);

    if (qtd - pagina*20 > 0) $("#proxima").attr("disabled", false);

    $("#paginas span[data-pagina="+pagina+"]").addClass("atual");
}

$("#resultados .usuario a").click(function(e) {
    if (moderador==1) {
        e.preventDefault();
        usuarioId = $(this).attr("data-id");

        if (usuarioId==1) {
            chamarPopupInfo("O Administrador geral não pode deixar de ser administrador!");
            return;
        }

        $.ajax({
            type: "post",
            url: "php/handler/usuarioHandler.php",
            data: {funcao: "moderador", id: usuarioId},
            success: function(result) {
                result = JSON.parse(result);
                console.log(result);
                
                if (result.estado==1) {
                    chamarPopupConf(result.mensagem);

                    setTimeout(function() {
                        location.href = "/admPainel";    
                    }, 2000);
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
});