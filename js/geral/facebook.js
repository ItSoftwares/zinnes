$(document).ready(function() {
    if (typeof usuario=="undefined") {
        window.fbAsyncInit = function() {
            FB.init({
                appId      : '282526908951813',
                xfbml      : true,
                version    : 'v2.12'
            });
            FB.getLoginStatus(function(response) {
                callBackMudancasStatusFacebook(response);
                // console.log(response);
            });
        };

    	// Load the SDKhronously
    	(function(d, s, id){
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    }
});
 
function callBackMudancasStatusFacebook(response, botao) {
    botao = botao || 0;
    console.log(response);
    // O objeto de resposta é retornado com o campo de estados que faz com que o aplicativo saiba o status de login da pessoal atual
     
    if (response.status==='connected') {
        // Se já estiver logado
        if (botao!=0) testeAPIFacebook();
        console.log("Precisa fazer Login no site!");
    } else if (response.status==="not_authorized") {
        // Se estiver logado no facebook mais não no app
        console.log("Não Logado no site!");
        $("#login-facebook").removeClass("disabled");
    } else {
        // não está logado nem no app nem no face
        console.log("Login no facebook inexistente!");
        $("#login-facebook").removeClass("disabled");
    }
}
 
function testeAPIFacebook() {
    FB.api("/me/", {fields: 'email,id,name,gender'}, function(response){
        console.log(response.email);
        // data = response;
        data = {};
        data.facebook = response.id;
        data.funcao = "facebook";
        data.nome = response.name;
        data.email = response.email;
        data.sexo = response.gender=="male"?1:response.gender=="female"?2:null;
        console.log(data);
        // return;
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
                    // $("#container-login #cadastro, #container-login #login")[0].reset();
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
}
 
function loginFacebook() {
    FB.login(function(response) {
        callBackMudancasStatusFacebook(response, 1);
    }, {scope: 'public_profile,email'});
}
 
function logoutFacebook() {
    FB.logout(function(response) {
        callBackMudancasStatusFacebook(response);
        // chamarInformacao("Fez Logoof");
    });
}

function apagarUsuarioFacebook() {
    FB.api(
        '/me/permissions/',
        'DELETE',
        {},
        function(response) {
            console.log(response);
        }
    );
}