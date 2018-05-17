var tela = 1;

$(document).ready(function() {
	// $("#link-login").addClass("selecionado");
});

$("#label-login").click(function() {
	if (tela==1) return;
	
	$("#atalhos label").removeClass();
	$(this).addClass("marcado");
	
	// $("form#cadastro").fadeOut(function() {
	// 	$("form#login").fadeIn();
	// });
	
	$("form#cadastro").css({left: "150%", opacity: 0});
	$("form#login").css({left: "0", opacity: 1});
	$("#formularios").css({height: $("form#login").height()+"px"});
	
	tela=1;
});

$("#label-cadastro").click(function() {
	if (tela==2) return;
	
	$("#atalhos label").removeClass();
	$(this).addClass("marcado");

	// $("form#login").fadeOut(function() {
	// 	$("form#cadastro").fadeIn();
	// });
	
	$("form#login").css({left: "-150%", opacity: 0});
	$("form#cadastro").css({left: "0", opacity: 1});
	$("#formularios").css({height: $("form#cadastro").height()+"px"});
	
	$("#formularios").css({height: +"px"})
	
	tela=2;
});

$("#container-login .fechar").click(function() {
    $("#container-login").fadeOut();
});

$("#cadastro").submit(function(e) {
    e.preventDefault();
    
    senha = $("#cadastro [name=senha]").val();
    repetirSenha = $("#cadastro #repetir-senha").val();
    
    if (senha!=repetirSenha) {
        chamarPopupInfo("Repita a senha corretamente!");
        $("#cadastro #repetir-senha").focus();
        return;
    }
    
    data = formToArray($(this).serializeArray());
    data.funcao = "cadastro";
    
    $(this).find("button").attr("disabled", true);
    chamarPopupLoading("Aguarde enquanto criamos sua conta!");
    $.ajax({
        type: "post",
        url: "php/handler/usuarioHandler.php",
        data: data,
        success: function(result) {
            console.log(result);
            result = JSON.parse(result);
            
            if (result.estado==1) {
                chamarPopupConf(result.mensagem+"<br>Faça Login para continuar!");
                
                $("#label-login").click();
                $("#login [name=email]").focus();
                $("#container-login #cadastro, #container-login #login")[0].reset();
            } else {
                chamarPopupInfo(result.mensagem);
                $("#cadastro").find("button").attr("disabled", false);
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

$("#login").submit(function(e) {
    e.preventDefault();
    
    data = formToArray($(this).serializeArray());
    data.funcao = "login";
    
    $(this).find("button").attr("disabled", true);
    chamarPopupLoading("Verificando credenciais!");
    $.ajax({
        type: "post",
        url: "php/handler/usuarioHandler.php",
        data: data,
        success: function(result) {
            console.log(result);
            result = JSON.parse(result);
            
            if (result.estado==1) {
                chamarPopupConf(result.mensagem);
                
                setTimeout(function() {
                    location.reload();
                }, 3000);
            } else {
                chamarPopupInfo(result.mensagem);
                $("#login").find("button").attr("disabled", false);
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

$("#esqueci").click(function() {
    promp("Recuperar Conta", "Email", function() {
        recuperarSenha($("#promp-input").val());
    }, function() {
        console.log("Cancelar");
    });
});

$("#login-facebook").click(function() {
    if ($(this).hasClass("disabled")) return;
    $(this).addClass("disabled");
    loginFacebook();
});

function recuperarSenha(email) {
    data = {email: email, funcao: "recuperarSenha"};

    chamarPopupLoading("Aguarde enquanto enviamos um email para recuperação da senha!");
    $.ajax({
        type: "post",
        url: "php/handler/usuarioHandler.php",
        data: data,
        success: function(result) {
            console.log(result);
            result = JSON.parse(result);
            
            if (result.estado==1) {
                chamarPopupConf(result.mensagem);
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