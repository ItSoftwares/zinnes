var slideAtual=1;
var intervalo;

$(document).ready(function() {
	url = window.location.href;
	if (url.indexOf("www") == -1) location.href = url.replace('://', '://www.');
	$("#slide-control span:first-child").addClass('select');

	intervalo = setInterval(function() {
		passarSlide();
	}, 10000);
});

function passarSlide() {
	$("#slides ul li[data-slide="+slideAtual+"]").fadeOut(function() {
		$("#slide-control span").removeClass();

		slideAtual++;

		if (slideAtual>slides.length) slideAtual=1;
		// console.log(slideAtual);
		$("#slides ul li[data-slide="+slideAtual+"]").fadeIn();
		$("#slide-control span[data-slide="+slideAtual+"]").addClass('select');
	});
}

$(".seta.direita").click(function() {
	slide = Number(slideAtual)+1>slides.length?1:Number(slideAtual)+1;

	$("#slide-control span[data-slide="+slide+"]").click();
});

$(".seta.esquerda").click(function() {
	slide = Number(slideAtual)-1<1?slides.length:Number(slideAtual)-1;
	// console.log(slide);
	$("#slide-control span[data-slide="+slide+"]").click();
});

$("#slide-control span:not(.select)").click(function(e) {
	e.preventDefault();

	clearInterval(intervalo);

	numeroSlide = $(this).attr("data-slide");
	$("#slides ul li[data-slide="+slideAtual+"]").fadeOut(function() {
		$("#slide-control span").removeClass();

		slideAtual = numeroSlide;

		if (slideAtual>slides.length) slideAtual=1;
		// console.log(slideAtual);
		$("#slides ul li[data-slide="+slideAtual+"]").fadeIn();
		$("#slide-control span[data-slide="+slideAtual+"]").addClass('select');

		intervalo = setInterval(function() {
			passarSlide();
		}, 10000);
	});
});

$("#subir").click(function() {
	$("html, body").stop().animate({scrollTop:0});
});

$("#publishers button").click(function() {
	if (tipo_usuario==0) {
		$("#link-login a").click();
		$("#label-cadastro").click();
	} else {
		location.href = "/dashboard"
	}
});