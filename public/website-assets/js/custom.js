document.querySelectorAll('.dropdown').forEach(dropdown => {
    dropdown.addEventListener('shown.bs.dropdown', function () {
      this.querySelector('input').focus();
    });
  });


$(document).ready(function () {
  $("#myCarousel").owlCarousel({
    items: 4, // default
    margin: 10,
    nav: true,
    dots: false,
    autoplay: true,
    autoplayTimeout: 3000,
    autoplayHoverPause: true,

    // Responsive breakpoints
    responsive: {
      0: {
        items: 1 // 1 item on mobile
      },
      576: {
        items: 2 // 2 items on small screens
      },
      768: {
        items: 2 // 3 items on tablets
      },
      992: {
        items: 4 // 4 items on desktops
      }
    }
  });
});

$(document).ready(function () {
  $("#myCarousel1").owlCarousel({
    items: 4, // default
    margin: 10,
    nav: true,
    dots: false,
    autoplay: true,
    autoplayTimeout: 3500,
    autoplayHoverPause: true,

    // Responsive breakpoints
    responsive: {
      0: {
        items: 1 // 1 item on mobile
      },
      576: {
        items: 2 // 2 items on small screens
      },
      768: {
        items: 2 // 3 items on tablets
      },
      992: {
        items: 4 // 4 items on desktops
      }
    }
  });
});

$(document).ready(function () {
  $("#myCarousel2").owlCarousel({
    items: 4, // default
    margin: 10,
    nav: true,
    dots: false,
    autoplay: true,
    autoplayTimeout: 4000,
    autoplayHoverPause: true,

    // Responsive breakpoints
    responsive: {
      0: {
        items: 1 // 1 item on mobile
      },
      576: {
        items: 2 // 2 items on small screens
      },
      768: {
        items: 2 // 3 items on tablets
      },
      992: {
        items: 4 // 4 items on desktops
      }
    }
  });
});

$(document).ready(function () {
  $("#myCarousel3").owlCarousel({
    items: 4, // default
    margin: 10,
    nav: true,
    dots: false,
    autoplay: true,
    autoplayTimeout: 4500,
    autoplayHoverPause: true,

    // Responsive breakpoints
    responsive: {
      0: {
        items: 1 // 1 item on mobile
      },
      576: {
        items: 2 // 2 items on small screens
      },
      768: {
        items: 2 // 3 items on tablets
      },
      992: {
        items: 4 // 4 items on desktops
      }
    }
  });
});

$(document).ready(function () {
  $("#client-review").owlCarousel({
    items: 3, // default
    margin: 10,
    nav: false,
    dots: true,
    autoplay: true,
    autoplayTimeout: 3000,
    autoplayHoverPause: true,

    // Responsive breakpoints
    responsive: {
      0: {
        items: 1 // 1 item on mobile
      },
      576: {
        items: 2 // 2 items on small screens
      },
      768: {
        items: 2 // 3 items on tablets
      },
      992: {
        items: 3 // 4 items on desktops
      }
    }
  });
});

$(document).ready(function () {
  $("#sell-products").owlCarousel({
    items: 4, // default
    margin: 10,
    nav: true,
    dots: false,
    autoplay: true,
    autoplayTimeout: 3500,
    autoplayHoverPause: true,

    // Responsive breakpoints
    responsive: {
      0: {
        items: 1 // 1 item on mobile
      },
      576: {
        items: 2 // 2 items on small screens
      },
      768: {
        items: 2 // 3 items on tablets
      },
      992: {
        items: 4 // 4 items on desktops
      }
    }
  });
});






/* #Back To Top
================================================== */

$(document).ready(function(){

$(function(){
 
    $(document).on( 'scroll', function(){
 
      if ($(window).scrollTop() > 100) {
      $('.scroll-top-wrapper').addClass('show');
    } else {
      $('.scroll-top-wrapper').removeClass('show');
    }
  });
 
  $('.scroll-top-wrapper').on('click', scrollToTop);
});
 
function scrollToTop() {
  verticalOffset = typeof(verticalOffset) != 'undefined' ? verticalOffset : 0;
  element = $('body');
  offset = element.offset();
  offsetTop = offset.top;
  $('html, body').animate({scrollTop: offsetTop}, 500, 'linear');
}

});


new WOW().init();




// $(document).ready(function() {
//   const minus = $('.quantity__minus');
//   const plus = $('.quantity__plus');
//   const input = $('.quantity__input');
//   minus.click(function(e) {
//     e.preventDefault();
//     var value = input.val();
//     if (value > 1) {
//       value--;
//     }
//     input.val(value);
//   });
  
//   plus.click(function(e) {
//     e.preventDefault();
//     var value = input.val();
//     value++;
//     input.val(value);
//   })
// });


