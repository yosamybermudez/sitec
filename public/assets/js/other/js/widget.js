$(function(){
			
	$("#list-entrega-solicitado").hide();
	$("#list-entrega-x-cumplir").hide();
	$("#list-entrega-cumplido").hide();
	$("#list-solicitado").hide();
	$("#list-x-cumplir").hide();
	$("#list-cumplido").hide();
	$("#list-encuentro-solicitado").hide();
	$("#list-encuentro-x-cumplir").hide();
	$("#list-encuentro-cumplido").hide();
	$("#list-devolucion-solicitado").hide();
	$("#list-devolucion-x-cumplir").hide();
	$("#list-devolucion-cumplido").hide();
	$("#list-recogida-solicitado").hide();
	$("#list-recogida-x-cumplir").hide();
	$("#list-recogida-cumplido").hide();

	$("#list-encuentro-solicitado-button").click(function(){
		$(".cat-list").slideUp(600);
		$("#list-encuentro-solicitado").slideDown(600);
	});
	
	$("#list-encuentro-x-cumplir-button").click(function(){
		$(".cat-list").slideUp(600);
		$("#list-encuentro-x-cumplir").slideDown(600);
	});	
	
	$("#list-encuentro-cumplido-button").click(function(){
		$(".cat-list").slideUp(600);
		$("#list-encuentro-cumplido").slideDown(600);
	});
	
	$("#list-entrega-solicitado-button").click(function(){
		$(".cat-list").slideUp(600);
		$("#list-entrega-solicitado").slideDown(600);
	});
	
	$("#list-entrega-x-cumplir-button").click(function(){
		$(".cat-list").slideUp(600);
		$("#list-entrega-x-cumplir").slideDown(600);
	});	
	
	$("#list-entrega-cumplido-button").click(function(){
		$(".cat-list").slideUp(600);
		$("#list-entrega-cumplido").slideDown(600);
	});

	$("#list-solicitado-button").click(function(){
		$(".cat-list").slideUp(600);
		$("#list-solicitado").slideDown(600);
	});

	$("#list-x-cumplir-button").click(function(){
		$(".cat-list").slideUp(600);
		$("#list-x-cumplir").slideDown(600);
	});

	$("#list-cumplido-button").click(function(){
		$(".cat-list").slideUp(600);
		$("#list-cumplido").slideDown(600);
	});

	$("#list-devolucion-solicitado-button").click(function(){
		$(".cat-list").slideUp(600);
		$("#list-devolucion-solicitado").slideDown(600);
	});
	
	$("#list-devolucion-x-cumplir-button").click(function(){
		$(".cat-list").slideUp(600);
		$("#list-devolucion-x-cumplir").slideDown(600);
	});	
	
	$("#list-devolucion-cumplido-button").click(function(){
		$(".cat-list").slideUp(600);
		$("#list-devolucion-cumplido").slideDown(600);
	});
	
	$("#list-recogida-solicitado-button").click(function(){
		$(".cat-list").slideUp(600);
		$("#list-recogida-solicitado").slideDown(600);
	});
	
	$("#list-recogida-x-cumplir-button").click(function(){
		$(".cat-list").slideUp(600);
		$("#list-recogida-x-cumplir").slideDown(600);
	});	
	
	$("#list-recogida-cumplido-button").click(function(){
		$(".cat-list").slideUp(600);
		$("#list-recogida-cumplido").slideDown(600);
	});	
	
	$("#list-four-button").click(function(){
		$(".cat-list").slideUp(600);
		$("#list-four").slideDown(600);
	});
	
	$("#catNav li a").click(function() {
		$("#catNav li").removeClass("activeCatButton");
		$(this).parent().addClass("activeCatButton");
	});
});