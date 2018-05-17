var original;
var imagem = {w: 0, h: 0};
var img;

$(document).ready(function() {
    $("input[data-api]").attr("disabled", true);
    original = $("#foto-perfil img").attr("src");
});

$(document).scroll(function() {
   // scroll = $(document).scrollTop();   
   // if (scroll+$(window).height()>$(document).height()-$("body > footer").height()) {
   //     console.log("Teste");
   //     $("#salvar").addClass("estatico");
   // } else {
   //     $("#salvar").removeClass("estatico");
   // }
});

$("#editar input[type=file]").change(function() {
    input = this;
    
    if (input.files && input.files[0]) {
        if (input.files[0].size>2*1024*1024) {
            chamarPopupInfo("A imagem deve ter até 2Mb");
            limparImagemPerfil();
            return;
        }
        
        var reader = new FileReader();
        img = new Image();
        
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
            
            $("#foto-perfil img").attr("src", img.src);
            imagem.w = img.width;
            imagem.h = img.height;
        }

        reader.onload = function (e) {
            img.src = e.target.result;
        }

        reader.readAsDataURL(input.files[0]);
    }
}); 

$("#editar form").submit(function(e) {
    e.preventDefault();
    
    senhaEditar = $("#editar [name=senha]").val();
    repetirSenhaEditar = $("#editar #repetir").val();
    
    if (senhaEditar.length>0 && senhaEditar!=repetirSenhaEditar) {
        chamarPopupInfo("Repita a senha corretamente!");
        $("#editar #repetir").focus();
        return;
    }
    
    data = new FormData(this);
    data.append("funcao", "atualizar");
    if (imagem.w>0) {
        data.append("larguraImagem", imagem.w);
        data.append("alturaImagem", imagem.h);
    }
    
    $(this).find("button, input:not([data-api]), textarea, select").attr("disabled", true);
    chamarPopupLoading("Atualizando dados!");
    
    $.ajax({
        type: "post",
        url: "php/handler/usuarioHandler.php",
        data: data,
        success: function(result) {
            console.log(result);
            result = JSON.parse(result);
            
            if (result.estado==1) {
                chamarPopupConf(result.mensagem);
                
                $.each(result.atualizado, function(i, value) {
                    perfil[i] = value;
                });
                
                if (perfil.id==usuario.id && typeof img != "undefined") {
                    $("#header-foto-perfil img").attr("src", img.src);
                }
                
                $("#editar").find("button, input:not([data-api]), textarea, select").attr("disabled", false);
                original = $("#imagem-projeto img").attr("src");
            } else {
                chamarPopupInfo(result.mensagem);
                $("#editar").find("button, input:not([data-api]), textarea, select").attr("disabled", false);
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

$("#bloquear").click(function() {
    if (perfil.estado_conta<2) {
        confirmacao("Bloquear usuário", "Você deseja bloquear este usuário temporariamente ou permanentemente?", function() {
            // TEMPORARIAMENTE
            escolherBloqueio(3);
        }, function() {
            // PERMANENTEMENTE
            escolherBloqueio(2);
        }, "Temporariamente", "Permanentemente");
    } else {
        // DESBLOQUEAR
        bloqueioUsuario({id: perfil.id, funcao: 'atualizar', estado_conta: perfil.confirmado});
    }
});

function limparImagemPerfil() {
    $("#foto-perfil img").attr("src", original);
    $("#editar input[type=file]").val("");
    
    imagem.w = 0;
    imagem.h = 0;
}

function escolherBloqueio(tipo) {
    data = {id: perfil.id, funcao: "atualizar", estado_conta: tipo};
    
    // TEMPORARIAMENTE
    if (tipo==2) {
        $("#promp-input").attr("type", "number");
        
        promp("Dias até desbloqueio", "Dias inteiros", function() {
            data.dias_bloqueio = $("#promp-input").val();
            bloqueioUsuario(data);
        }, function() {
            console.log("Cancelar");
        });
    } 
    // PERMANENTEMENTE
    else if (tipo==3) {
        bloqueioUsuario(data);
    }
}

function bloqueioUsuario(data) {
    chamarPopupLoading("Aguarde...")
   // console.log(data);
   // return;
    $("#editar").find("button, input, textarea").attr("disabled", true);
    $.ajax({
        type: "post",
        url: "php/handler/usuarioHandler.php",
        data: data,
        success: function(result) {
            console.log(result);
            result = JSON.parse(result);
            
            if (result.estado==1) {
                chamarPopupConf(result.mensagem);
                
                $.each(result.atualizado, function(i, value) {
                    perfil[i] = value;
                });
                
                if (perfil.estado_conta>1) {
                    $("#bloquear").addClass("desbloquear").html("Desbloquear <i class='fa fa-unlock'>");
                    mensagem="";
                    if (perfil.estado_conta==2) {
                        mensagem = "<p>Conta bloqueada até: "+getData(perfil.dias_bloqueio);
                    } else {
                        mensagem = "<p>Bloqueado permanemente!";
                    }
                    console.log(mensagem)
                    $("#foto-perfil").after(mensagem);
                } else {
                    $("#bloquear").removeClass("desbloquear").html("Bloquear <i class='fa fa-lock'>");
                    $("#editar aside p").remove();
                }
                
                $("#editar").find("button, input, textarea").attr("disabled", false);
            } else {
                chamarPopupInfo(result.mensagem);
                $("#editar").find("button, input, textarea").attr("disabled", false);
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