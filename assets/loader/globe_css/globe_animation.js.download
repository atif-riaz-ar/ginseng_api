/* jquery.js */
/* velocity.js */
/* A: looped continents animation */

$(document).ready(function(){

	/* VARIABLES SETUP */

	/*  Time to wait before intro animation after Globe is visible (ms)  */
	var globe_intro_delay = 500;
    /*  Globe intro animation duration (ms)  */
    var globe_intro_duration = 800;
    /*  Globe intro animation easing  */
    var globe_intro_easing = "swing";

	/*  Globe continents' animation loop interval (ms) */
    var continents_loop_interval = 8000;

	/* VARIABLES SETUP: END */



    var $globe_fr = $(".globe__worldmap__front"),
		$globe_bk = $(".globe__worldmap__back"),
		$globe = $(".globe");

	var globe_el_x = [0, -473];
    var globe_played = false;


	/*  Set the Globe  */
	function set_globe() {
        $.Velocity.hook($globe, "visibility", "hidden");
	    $.Velocity.hook($globe_fr, "translateX", globe_el_x[1] + "px");
	    $.Velocity.hook($globe_bk, "translateX", globe_el_x[0] + "px");
	}


    /*  Run Globe intro animation  */
    function globe_intro() {
        $globe.velocity({scale: [1, 0], opacity: 1}, {visibility: "visible", duration: globe_intro_duration, easing: globe_intro_easing});
		continents_loop();
    }


    /*  Run Globe animation  */
    function continents_loop() {
		// Note: 'for' loop needed to fix some lag in iOS browsers
    	for (var i = 0; i < 99; i++) {
			$globe_fr.velocity({translateX: [ globe_el_x[0], globe_el_x[1] ] }, {duration: continents_loop_interval, easing: "linear"});
			$globe_bk.velocity({translateX: [ globe_el_x[1], globe_el_x[0] ] }, {duration: continents_loop_interval, easing: "linear"});
    	};  
		$globe_fr.velocity({translateX: [ globe_el_x[0], globe_el_x[1] ] }, {duration: continents_loop_interval, easing: "linear"});
		$globe_bk.velocity({translateX: [ globe_el_x[1], globe_el_x[0] ] }, {duration: continents_loop_interval, easing: "linear", complete: continents_loop });  
    }


	/* Toggle animation */
	$('#toggle_animation').click(function(){

		if ( $globe_fr.hasClass("velocity-animating") ) {
			$globe.velocity("stop", true);
			$globe_fr.velocity("stop", true);
			$globe_bk.velocity("stop", true);
		} else {
	        set_globe();
			globe_intro();
		}

	});


    /* Activate animation on these events: onScroll, onLoad */
    var elem = $(".globe__placeholder");

    function isScrolledIntoView(elem) {
        var docViewTop = $(window).scrollTop();
        var docViewBottom = docViewTop + $(window).height();
        var elemTop = $(elem).offset().top;
        var elemBottom = elemTop + $(elem).height();
		return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
    };

    function isScrolled() {
        if (isScrolledIntoView(elem) && globe_played == false) {
			setTimeout(globe_intro,globe_intro_delay);
            globe_played = true;
            $(window).unbind('scroll');
        }
    }

	$(window).load(set_globe);
	$(window).load(isScrolled);
	$(window).scroll(isScrolled);

});
