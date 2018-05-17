var banner = {w: 0, h: 0};
var imagem = {w: 0, h: 0};
var original;
var imagemTemp;

$(document).ready(function() {
    if (serie.banner_projeto!=null && serie.banner_projeto!="") {
        $("#upload-preview").show().find("img").attr("src", "servidor/projetos/banners/"+serie.banner_projeto);
    }
    
    original = $("#imagem-projeto img").attr("src");
    
    $("#paginas span[data-pagina="+ultimaPagina+"]").click();

    if (typeof usuario!="undefined" || tipo_usuario!=0) {
    	if (serie.id_usuario==usuario.id) {
    		$("#capitulos").prepend("<p class='aviso'>Capítulos não publicados não podem ser vistos pelos leitores.</p>");
    	}
    }
});

$("#icon-mudar-banner").click(function() {
    $("#upload-banner").fadeIn().css({display: "flex"});
});

$("#upload-banner .fechar").click(function() {
    $("#upload-banner").fadeOut();
});

$(".editar").click(function(e) {
    e.preventDefault();
    cap = capitulos[$(this).closest("li.capitulo").attr("data-id")];
    
   // console.log((serie.tipo==1?"comic-":"novel-")+serie.nome.toLowerCase()+"-"+cap.nome.toLowerCase()); return;
    
    location.href = (serie.tipo==1?"comic/":"novel/")+serie.id+"/"+cap.id;
});

$(".excluir").click(function(e) {
    e.preventDefault();

    index = $(this).closest("li").attr("data-id");
    id_capitulo = index; id_capitulo = capitulos[id_capitulo].id;
    nome = $(this).closest("li").find("h3").text();
    data = {
    	id: id_capitulo, 
    	funcao: "excluir", 
    	nome: nome, 
    	serie: serie.id, 
    	id_usuario: serie.id_usuario,
    	tipo: serie.tipo
    };
    // console.log(data); return;
    
    confirmacao("Remover Capítulo", "Deseja realmente excluir este capítulo? Todas as informações serão permanentemente apagadas!", function() {
        // console.log("apagar");
        chamarPopupLoading("Aguarde enquanto apagamos os capítulos e a serie!");
        $(".projeto-acoes i").addClass('disabled');
        $.ajax({
            type: "post",
            url: "php/handler/tituloHandler.php",
            data: data,
            success: function(result) {
                console.log(result);
                result = JSON.parse(result);
                console.log(result);
                
                if (result.estado==1) {
                    chamarPopupConf(result.mensagem);

                    $("#capitulos li[data-id="+index+"]").remove();
                    
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

$("#upload-banner input[type=file]").change(function() {
    input = this;
    
    if (input.files && input.files[0]) {
        if (input.files[0].size>2*1024*1024) {
            chamarPopupInfo("A imagem deve ter até 2Mb");
            limparImagemBanner();
            return;
        }
        
        var reader = new FileReader();
        var img = new Image();
        
        img.onload = function() {
            if (img.width<1350) {
                chamarPopupInfo("A imagem deve ter pelo menos 1350 Pixels de largura");
                limparImagemBanner();
                return;
            }
            
            proporcaoHeight = 325*img.width/1350;
            
            if (img.height<proporcaoHeight) {
                chamarPopupInfo("Proporções inválidas. Uma imagem de largura "+img.width+"px deve ter pelo menos "+proporcaoHeight+"px de altura.");
                limparImagemBanner();
                return;
            }
            
            $("#upload-preview img").attr("src", img.src);
            banner.w = img.width;
            banner.h = img.height;
            $("#upload-preview").show();
            $("#upload-banner form button").attr("disabled", false);
        }

        reader.onload = function (e) {
            img.src = e.target.result;
        }

        reader.readAsDataURL(input.files[0]);
    }
});

$("#imagem-projeto input[type=file]").change(function() {
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
                $("#imagem-projeto > i").hide();
                imagemTemp = $("<img>").attr("src", img.src).appendTo("#imagem-projeto")
            } else {
                $("#imagem-projeto img").attr("src", img.src);
            }
            
            imagem.w = img.width;
            imagem.h = img.height;
            $("#salvar").show().css({display: "block"});
        }

        reader.onload = function (e) {
            img.src = e.target.result;
        }

        reader.readAsDataURL(input.files[0]);
    }
}); 

$("#imagem-projeto form").submit(function(e) {
    e.preventDefault();
    
    data = new FormData(this);
    
    data.append("funcao", "atualizar");
    
    data.append("larguraImagem", imagem.w);
    data.append("alturaImagem", imagem.h);
    data.append("id", serie.id);
    data.append("id_usuario", serie.id_usuario);
    
    $("#salvar").attr("disabled", true);
    chamarPopupLoading("Atualizando Imagem!");
    
    $.ajax({
        type: "post",
        url: "php/handler/serieHandler.php",
        data: data,
        success: function(result) {
            console.log(result);
            result = JSON.parse(result);
            
            if (result.estado==1) {
                chamarPopupConf(result.mensagem);
                
                $("#salvar").attr("disabled", false).hide();
                original = $("#imagem-projeto img").attr("src");
            } else {
                chamarPopupInfo(result.mensagem);
                
                $("#salvar").attr("disabled", false);
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

$("#copiar").click(function(e) {
    e.preventDefault();
    // console.log(typeof window.copy);
    url = $(this).attr("href");

    var input = $('<input>').val(url).appendTo('body').select()
    document.execCommand('copy');
    input.remove();

    chamarPopupInfo("Link copiado para o clipboard!");
});

$("#upload-banner form").submit(function(e) {
    e.preventDefault();
    
    data = new FormData(this);
    
    data.append("funcao", "atualizar");
    
    data.append("larguraImagem", banner.w);
    data.append("alturaImagem", banner.h);
    data.append("id", serie.id);
    data.append("id_usuario", serie.id_usuario);
    
    $("#upload-banner button").attr("disabled", true);
    chamarPopupLoading("Atualizando Imagem!");
    
    $.ajax({
        type: "post",
        url: "php/handler/serieHandler.php",
        data: data,
        success: function(result) {
            console.log(result);
            result = JSON.parse(result);
            
            if (result.estado==1) {
                chamarPopupConf(result.mensagem);
                
                $("#upload-banner button").attr("disabled", false);
                originalBanner = result.atualizado.banner_projeto;
                
                $("#upload-banner").fadeOut().find("input").val("");
                
                $("#upload-preview img").attr("src", "servidor/projetos/banners/"+originalBanner);
                
                $("#serie-topo").css({backgroundImage: "url('servidor/projetos/banners/"+originalBanner+"')", backgroundSize: "cover"}).removeClass();
            } else {
                chamarPopupInfo(result.mensagem);
                
                $("#upload-banner button").attr("disabled", false);
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

$("#salvar").click(function() {
    $("#imagem-projeto form").submit();
});

$("#paginas span").click(function() {
    paginaAtual = $(this).text();
    
    $(".capitulo:not([data-pagina="+paginaAtual+"])").fadeOut();
    $(".capitulo[data-pagina="+paginaAtual+"]").fadeIn();
});

$("#seguir").click(function() {
    if (typeof usuario=="undefined" || tipo_usuario==0) {
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
        url: "php/handler/serieHandler.php",
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

function limparImagemBanner() {
    $("#input-imagem-banner").val("");
    $("#upload-preview").hide();
    $("#upload-preview img").attr("src", "");
    $("#upload-banner form button").attr("disabled", true);
    banner = {w: 0, h: 0};
}

function limparImagemPerfil() {
    if (typeof original=="undefined") {
        imagemTemp.remove();
        $("#imagem-projeto > i").show();
    }
    else {
        $("#foto-perfil img").attr("src", original);
        $("#editar input[type=file]").val("");
    }
    
    imagem.w = 0;
    imagem.h = 0;
    
    $("#salvar").hide();
}

function atualizarImagem(form, dimensoes) {
    
}