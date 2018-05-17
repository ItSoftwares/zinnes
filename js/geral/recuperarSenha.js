var intervalo;

$(document).ready(function() {
	intervalo = setInterval(contar, 986);
});

$("#recuperar form").submit(function(e) {
	e.preventDefault();

	if ($("#recuperar [name=senha]").val()!=$("#repetir-senha-trocar").val()) {
		chamarPopupInfo("Repita a senha corretamente!");
		$("#repetir-senha").focus().select();
		return;
	}

	data = {};
	data.funcao = "atualizar";
	data.id = usuario.id;
	data.senha = $("#recuperar [name=senha]").val();

	chamarPopupLoading("Aguarde enquanto atualizamos sua senha!");
	$("#recuperar button, #recuperar form input").attr("disabled", true);
    $.ajax({
        type: "post",
        url: "php/handler/usuarioHandler.php",
        data: data,
        success: function(result) {
           // console.log(result);
            result = JSON.parse(result);
            console.log(result);
            
            if (result.estado==1) {
                chamarPopupConf("Senha atualizada. Iremos lhe redirecionar para a página inicial, faça login com sua nova senha para acessar sua conta");

                setTimeout(function() {
                	location.href = "/";
                }, 4000);
            } else {
                chamarPopupInfo(result.mensagem);
                $("#recuperar button, #recuperar form input").attr("disabled", false);
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

function contar() {
	minutos = parseInt(time/60);
	segundos = time-minutos*60;

	$("#minutos").text(colocarZero(minutos)+":"+colocarZero(segundos));

	time--;

	if (time==0) {
		clearInterval(intervalo);
		$("#recuperar form button").attr("disabled", true)
	}
}
