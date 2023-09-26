/*** Stick Menu ***/
jQuery(function($){
  $(window).scroll(function() {
    var winTop = $(window).scrollTop();
    if (winTop >= 100) {
      $(".header-area").addClass("is-sticky");
    } else {
	  $(".header-area").removeClass("is-sticky");
    }
  })
})

/*** Change Color Preset ***/
jQuery(function ($) {  
        a = $(".sp-megamenu-parent > li.active > a").css("color");
        document.documentElement.style.setProperty('--background-color',a);
        document.documentElement.style.setProperty('--text-color',a);
        document.documentElement.style.setProperty('--border-color',a);
});

/**** counter *****/
jQuery(function ($) {  
$('.count').each(function () {
    $(this).prop('Counter',0).animate({
        Counter: $(this).text()
    }, {
        duration: 4000,
        easing: 'swing',
        step: function (now) {
            $(this).text(Math.ceil(now));
        }
    });
});
});
