var secret = "2N8I1RF1_CSo4BC_PwFuDi19";
var googleUser = {};

$(document).ready(function() {
	if (logado==0) iniciarGoogle();
});

function iniciarGoogle() {
    gapi.load('auth2', function(){
		// Retrieve the singleton for the GoogleAuth library and set up the client.
		auth2 = gapi.auth2.init({
		client_id: '662723215242-6ber8do472s3g6cgpsskmvab8b23tiv3.apps.googleusercontent.com',
		cookiepolicy: 'single_host_origin',
		// Request scopes in addition to 'profile' and 'email'
		//scope: 'additional_scope'
		});
		attachSignin(document.getElementById('login-google'));
    });
}

function attachSignin(element) {
	// console.log(element.id);
	auth2.attachClickHandler(element, {},
		function(googleUser) {
	    	console.log(googleUser);
	    	loginGoogle(googleUser.getBasicProfile());
			// document.getElementById('name').innerText = "Signed in: " + googleUser.getBasicProfile().getName();
	    }, function(error) {
	      	console.log(JSON.stringify(error, undefined, 2));
    	}
    );
}

function loginGoogle(response) {
	data = {};
    data.google = response.Eea;
    data.funcao = "google";
    data.nome = response.ig;
    data.email = response.U3;
    data.sexo = 0;
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