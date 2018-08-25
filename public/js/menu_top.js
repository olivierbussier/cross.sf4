// Fonctions pour le menu latéral
// -------------------------------
(function($){
	$('#menu-icon').click(function(e){
		e.preventDefault();
		$('body').toggleClass('with--sidebar');
	})

	$('#site-cache').click(function(e){
		$('body').removeClass('with--sidebar');
	})

	$('#menu').click(function(e){
		$('body').removeClass('with--sidebar');
	})
})(jQuery);

// Fonctions pour le menu top
// -------------------------------
window.onload=function(){
	var header  = document.querySelector("#menu");
	var bandeau = document.querySelector("#bandeau");
	//var textaff = document.getElementById("demo");

	function scrolled(){
		var windowHeight = document.body.clientHeight,
			currentScroll = document.body.scrollTop || document.documentElement.scrollTop;
	    
		  //textaff.innerHTML = 'doc.body.st: ' + document.body.scrollTop+', '+document.documentElement.scrollTop+', ' + currentScroll + '<br>';
		header.className = (currentScroll >= bandeau.offsetHeight) ? "menu fixed" : "menu";
	}
	addEventListener("scroll", scrolled, false);
};

