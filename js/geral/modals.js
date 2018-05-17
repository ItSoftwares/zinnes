var modal_promp = false;
var modal_confirm = false;

$("#modals i.fechar").click(function() {
    $(this).parent().fadeOut();
});

function promp(titulo, label, sim, nao) {
    $("#modal-promp h3").text(titulo);
    $("#modal-promp label").text(label);
    
    $("#modal-promp").fadeIn().css("display","flex");
    
   // if (!modal_promp) {
        $("#modal-promp .modal-cancelar").click(function() {
            $(this).unbind("click");
            nao();
            $("#modal-promp").fadeOut();
        });
        $("#modal-promp .modal-ok").click(function() {
            if ($("#promp-input").val().length==0) {
                $("#promp-input").focus();
                return;
            }
            $(this).unbind("click");
            sim();
            $("#modal-promp").fadeOut();
        });
   // }
    
    modal_promp = true;
} 

function confirmacao(titulo, texto, sim, nao, botao1, botao2) {
    
    botao1 = botao1 || "CANCELAR";
    botao2 = botao2 || "OK";
    $("#modal-confirm h3").text(titulo);
    $("#modal-confirm p").text(texto);
    $("#modal-confirm .modal-cancelar").text(botao1);
    $("#modal-confirm .modal-ok").text(botao2);
    
    $("#modal-confirm").fadeIn().css("display","flex");
   // if (!modal_confirm) {
        $("#modal-confirm .modal-cancelar").click(function() {
            $(this).unbind("click");
            nao();
            $("#modal-confirm").fadeOut();
        });
        $("#modal-confirm .modal-ok").click(function() {
            $(this).unbind("click");
            sim();
            $("#modal-confirm").fadeOut();
        });
   // }
    
    modal_confirm = true;
} 