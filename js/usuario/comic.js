var numeroImagem = 1;
var uploads = {};
var original;
var dimensoes = {w:0, h:0};
var ordemOriginal = {};
var ordemAtual = {};

$(document).ready(function() {
    atualizarArquivos();

    if (typeof comic.id != "undefined") {
        comic.data = timeToTimestamp(comic.data);

        $(".publicar-agora").attr("disabled", false);

        if (comic.data_lancamento==null) {
            $("#agendar-box button").attr("disabled", true);
        } else {
            if (comic.rascunho==1) {
                comic.data_lancamento = timeToTimestamp(comic.data_lancamento);
                $(".dia-escolhido").text(getData(comic.data_lancamento));

                dataTemp = new Date(comic.data_lancamento*1000);
                diaEscolhido = dataTemp.getDate();
                mesEscolhido = dataTemp.getMonth();
                anoEscolhido = dataTemp.getFullYear();
                $(".semana span[data-dia="+diaEscolhido+"]").addClass("selecionado");

                atualizarCalendario(dataTemp.getMonth()+1, dataTemp.getFullYear());
            }
        }

        if (comic.rascunho==0) {
            publicado = true;
            $(".agendar").attr("disabled", true);
            $(".salvar").attr("disabled", true);
            $(".publicar-agora").html("Atualizar <i class='fa fa-check'></i>");
        } else {
            $(".salvar, .agendar").attr("disabled", false);
        }
    }
    else {
        $(".salvar, .agendar").attr("disabled", false);

        $(".publicar-agora").attr("disabled", true);
    }

    original = $(".upload-thumb-label img").attr("src");
});

$("#upload-paginas-comic").change(function(e) {
    files = e.target.files;

    [].slice.call(files).sort(function(a, b) {
        nomeA = a.name.replace(/\.[^/.]+$/, "");
        nomeB = b.name.replace(/\.[^/.]+$/, "");

        return nomeB - nomeA;
    });

    var processedFile = {}, eFile = {};
    var indices = {};
    // var numero = 0;
    $.each(files, function(indice, file) {
        if (file.size>3*1024*1024) {
            chamarPopupInfo("A imagem <b>"+file.name+"</b> tem mais de 2Mb!");
            return true;
        }

        numero = adicionarArquivo(file.name);
        indices[indice] = numero;

        var imagem = {w: 0, h: 0};
        var reader = new FileReader();
        var img = new Image();
        var fileData = {};

        reader.onload = (function (p) {
            return function (e) {
                img.src = e.target.result;
                processedFile[indices[indice]] = p;
                eFile[indices[indice]] = e;
            };
        })(file);

        reader.readAsDataURL(file);

        img.onload = function() {
            callback(indices[indice]);

            imagem.w = img.width;
            imagem.h = img.height;
            fileData.larguraImagem = imagem.w;
            fileData.alturaImagem = imagem.h;
        }
    });

    callback = function(index) {
        // console.log(index);
        fileData = {name: processedFile[index].name, fileData: eFile[index].target.result};
       // fileData = {name: processedFile.name, fileData: "temp"};

        // or add to list to submit as group later
        uploads[index] = fileData;
        $(".arquivo[data-numero="+index+"] .estado").text("Pendente");
    };
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
                imagemTemp = $("<img>").attr("src", img.src).appendTo(".upload-thumb-label")
            } else {
                $(".upload-thumb-label img").attr("src", img.src);
            }
            
            dimensoes.w = img.width;
            dimensoes.h = img.height;
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

$(document).on("click", ".excluir", function() {
    num = $(this).closest(".arquivo").attr("data-numero");
    nome = $(this).closest(".arquivo").attr("data-nome");
    pendente = $(this).closest(".arquivo").hasClass('pendente');
    $(this).closest(".arquivo").remove();
    delete uploads[num];
    
    reorganizarArquivos();
    
    console.log(ordemAtual);
    
    qtd--;
    $("#paginas b").text(qtd);
    
    if (pendente) return;

    data = new FormData();
    temp = {};
    temp.funcao = "apagarImagem";
    temp.id = comic.id;
    temp.id_projeto = serie.id;
    temp.apagar = nome;
    temp.ordem = ordemAtual;
    temp.numero = num;
    
    
    $.each(temp, function(i, value) {
        if (i=="ordem") data.append(i, JSON.stringify(value));
        else data.append(i, value);
    });
    
    atualizarComic(data, $(this), 1);
});

$(document).on("click", ".subir", function() {
    este = $(this).closest(".arquivo");
    anterior = este.prev();
    
    if (este.attr("data-numero")==1) return;
    
    este.insertBefore(anterior);
    reorganizarArquivos();
});

$(document).on("click", ".descer", function() {
    este = $(this).closest(".arquivo");
    proximo = este.next();
    
    if (este.attr("data-numero")==$(".arquivo").length) return;
    
    este.insertAfter(proximo);
    reorganizarArquivos();
});

$(document).on("click", ".ver", function() {
    este = $(this).closest(".arquivo");
    
    if (este.hasClass('pendente')) {
        $("#visualizar-imagem img").attr("src", uploads[este.attr("data-numero")].fileData);
    } else {
        $("#visualizar-imagem img").attr("src", "/servidor/titulos/comics/"+comic.id+"/"+este.find("span.nome").text());
    }

    $("#visualizar-imagem").show().css({display: "flex"});
});

$("#agendar-box .fechar").click(function() {
    $("#agendar-box").fadeOut();
});

$(".agendar").click(function() {
    $("#agendar-box").fadeIn().css({display: "flex"});
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

$("#agendar-box button").click(function() {
    var data = new FormData();
    data.append("data_lancamento", getTimeEscolhido(diaEscolhido, mesCalendario+1, anoCalendario)/1000);
   // console.log(data.get('data_lancamento')); return;
    data.append("funcao", "atualizar");
    data.append("id", comic.id);
    
    atualizarComic(data, $(this), "Data de lançamento do título foi atualizado!");
});

function atualizarArquivos() {
    qtd = 0;
    // if (ar)
    arquivos.informacoes.sort(function(a, b) {
        nomeA = a.filename.split("-")[0];
        nomeB = b.filename.split("-")[0];

        return nomeA - nomeB;
    });

    $.each(arquivos.informacoes, function(indice, value) {
        temp = "";
        
        temp += "<li class='arquivo postada' data-nome="+value.basename+" data-numero="+numeroImagem+">";
        temp += "<div class='barra-progresso'></div>";
        temp += "<span class='numero'>"+numeroImagem+"</span>";
        temp += "<span class='nome'>"+value.basename+"</span>";
       // temp += "<span class='tamanho'>256kb</span>";
        temp += "<span class='estado'>Postada</span>";
        temp += "<span class='acao'><i class='fa fa-angle-up subir'></i><i class='fa fa-angle-down descer'></i><i class='fa fa-eye ver'></i><i class='fa fa-trash excluir'></i></span>";
        temp += "</li>";
        
        ordemOriginal[numeroImagem] = value.basename;
        ordemAtual[numeroImagem] = value.basename;
        
        numeroImagem++;
        qtd++;
        
        $("#arquivos-conteudo .sem-arquivos").before(temp);
    });
    
    if (qtd>0) {
        $("li.sem-arquivos").hide();
    } else {
        $("li.sem-arquivos").show();
    }
    
    $("#paginas b").text(qtd);
}

function adicionarArquivo(nome) {
    temp = "";

    temp += "<li class='arquivo pendente' data-numero="+numeroImagem+">";
    temp += "<div class='barra-progresso'></div>";
    temp += "<span class='numero'>"+numeroImagem+"</span>";
    temp += "<span class='nome'>"+nome+"</span>";
       // temp += "<span class='tamanho'>256kb</span>";
    temp += "<span class='estado'>Carregando</span>";
    temp += "<span class='acao'><i class='fa fa-angle-up subir'></i><i class='fa fa-angle-down descer'></i><i class='fa fa-eye ver'></i><i class='fa fa-trash excluir'></i></span>";
    temp += "</li>";
    
    numeroImagem++;
    qtd++;

    $("#arquivos-conteudo .sem-arquivos").before(temp);
    $("#paginas b").text(qtd);
    
    if (qtd>0) {
        $("li.sem-arquivos").hide();
    } else {
        $("li.sem-arquivos").show();
    }
    
    return numeroImagem-1;
}

function removerArquivo() {
    
}

function reorganizarArquivos() {
   // uploadsTemp = Object.assign({}, uploads);
    uploadsTemp = {};
    ordemTemp = {};
    numeroImagem = 1;
    $(".arquivo").each(function() {
        if ($(this).hasClass("pendente")) {
            numeroAntigo = $(this).attr("data-numero");
            uploadsTemp[numeroImagem] = uploads[numeroAntigo];
        }
        $(this).attr("data-numero", numeroImagem).find(".numero").text(numeroImagem);
        
        if ($(this).hasClass("postada")) {
            ordemTemp[numeroImagem] = $(this).attr("data-nome");
        }
        numeroImagem++;
    });
    
    uploads = Object.assign({}, uploadsTemp);
    ordemAtual = Object.assign({}, ordemTemp);
}

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

$(".publicar-agora").click(function() {
    form = $("#enviar-comic");
    var data = new FormData(form[0]);
    
    temp = formToArray(form.serializeArray());
    
    tempOrdem = {};
    $.each(ordemAtual, function(i, value) {
        if (value!=ordemOriginal[i]) tempOrdem[i] = value;
    });
    
    temp.funcao = comic.rascunho==1?"publicar":"atualizar";
    temp.imagens = uploads;
    temp.ordem = tempOrdem;
    temp.id_projeto = serie.id;
    temp.id = comic.id;
    
    if (dimensoes.w>0) {
        temp.larguraImagem = dimensoes.w;
        temp.alturaImagem = dimensoes.h;
    }
    
    $.each(temp, function(i, value) {
        if (i=="imagens" || i=="ordem") data.append(i, JSON.stringify(value));
        else data.append(i, value);
    });
    
    atualizarComic(data, $(this), comic.rascunho==0?0:"O capitulo foi publicado, os seus seguidores serão notificados!");
    
    $(".publicar-agora").attr("disabled", true);
});

$(".salvar").click(function() {
    $("#enviar-comic").submit();
});

$("#enviar-comic").submit(function(e) {
    e.preventDefault();
    
    var data = new FormData(this);
    
    temp = formToArray($(this).serializeArray());
    
    if (comic.id!=null) funcao = "atualizar";
    else funcao="novo";
    
    tempOrdem = {};
    $.each(ordemAtual, function(i, value) {
        if (value!=ordemOriginal[i]) tempOrdem[i] = value;
    });
    
    temp.imagens = uploads;
    temp.ordem = tempOrdem;
    temp.funcao = funcao;
    temp.id_projeto = serie.id;
    temp.id = comic.id;
    
    if (dimensoes.w>0) {
        temp.larguraImagem = dimensoes.w;
        temp.alturaImagem = dimensoes.h;
    }
    
    $.each(temp, function(i, value) {
        if (i=="imagens" || i=="ordem") data.append(i, JSON.stringify(value));
        else 
        	data.append(i, value);
    });
    
    result = atualizarComic(data, $("#cabecalho button"));
    
    if (result==1)$(".publicar-agora").attr("disabled", false);
});

function atualizarComic(data, botoes, mensagemExito) {
    if (qtd==0) {
        chamarPopupInfo("Envie pelo menos 3 páginas!");
        return 0;
    }
    
    if ($("#titulo").val().length==0) {
        chamarPopupInfo("Informe um título!");
        $("#titulo").focus();
        return 0;
    }

    data.append("id_usuario", serie.id_usuario);
    data.append("serie", serie.nome);
    if (data.get("funcao")=="novo") data.append("serie", serie.nome);

    mensagemExito = mensagemExito || 0;
    botoes.attr("disabled", true);
    
    chamarPopupLoading("Aguarde enquanto atualizamos o capítulo!");

    $.ajax({
        type: "post",
        url: "php/handler/tituloHandler.php",
        data: data,
        success: function(result) {
            console.log(result);
            result = JSON.parse(result);
            
            if (result.estado==1) {
                uploads = {};
                if (mensagemExito!=1) chamarPopupConf(mensagemExito!=0?mensagemExito:result.mensagem);
                
                $.each(result.titulo, function(i, value) {
                    comic[i] = value;
                });
                
                $.each(result.nomes, function(i, value) {
                    $("li.arquivo[data-numero="+i+"]").attr("data-nome", value);
                    $("li.arquivo[data-numero="+i+"] .nome").text(value);
                });
                $("li.arquivo .estado").text("Postada");
                $("li.arquivo").removeClass("pendente").addClass('postada');

                comic.data = timeToTimestamp(comic.data);

                window.history.pushState("Atualização url", "Titulo", "comic/"+serie.id+"/"+comic.id);

                document.title = comic.nome+" | ZINNES";
                
                botoes.attr("disabled", false);
                
                if (data.get("funcao")=="publicar") {
                    $(".publicar-agora").html("Atualizar <i class='fa fa-check'></i>");
                    $(".salvar, .agendar").attr("disabled", true);
                    comic.rascunho = 0;
                } else if (data.get("funcao")=="novo") {
                	comic.rascunho = 1;
                }
                
                reorganizarArquivos();
                return 1;
            } else {
                chamarPopupInfo(result.mensagem);
                botoes.attr("disabled", false);
                return 0;
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
            setTimeout(function() {
                $(".barra-progresso").width("0%");
            }, 2000);
        },
        beforeSend: function() {
            var percentVal = 0;
            $(".barra-progresso").width(percentVal+"%");
            console.log(data);
        },
        xhr: function() {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function(evt) {
                // console.log(evt);
                if (evt.lengthComputable) {
                    var percentComplete = (evt.loaded / evt.total) * 100;
                    var percentVal = percentComplete + '%';
                    // console.log(percentVal);
                    $(".barra-progresso").width(percentVal);
                    //Do something with upload progress here
                }
           }, false);
           return xhr;
        },
        cache: false,
        contentType: false,
        processData: false
    });
}

$("#visualizar-imagem .fechar").click(function() {
    $("#visualizar-imagem").fadeOut();
});