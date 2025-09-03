(function($) {
    "use strict";
    $('.counter').counterUp({
        delay: 10,
        time: 1000,
        formatter: function (n) {
          console.log(n);
          //return n.replace(/,/g, '.');
          //return '$' + n.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
          var formattedNumber = n.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1.');
          console.log(formattedNumber);
          console.log(formattedNumber.replace('.', ','));
          return formattedNumber.replace('.', ',');
        }
    });
})(jQuery);
