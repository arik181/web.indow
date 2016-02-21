/*
** AnythingSlider v1.0.1 | (c) 2013 three17design | three17desig.org
*/
(function() {
  $.fn.carousel = function(options) {

    var array, bringFront, carousel, carouselCycle, indexReference, itemList, maxHeight, maxWidth, minHeight, minWidth, navWidth, newLeft, newLeftWidth, setParentSize, settings, startCarousel, startingHeight, startingWidth, stopCarousel, takeAway, transition;
    itemList = $(this).children();
    carousel = $(this);
    carouselCycle = "";
    maxHeight = 0;
    maxWidth = 0;
    minHeight = $(itemList[0]).height();
    minWidth = $(itemList[0]).width();
    array = ['scale-down', 'shuffle', 'slide-diagonal', 'slide-up', 'block', 'slide-left', 'fade'];
    /*DEFAULT SETTINGS*/

    settings = $.extend({
      style: 'fade',
      transitionSpeed: 'normal',
      carouselSpeed: 5000,
      arrows: true,
      buttons: true,
      buttonsTheme: 'dots',
      stopOnHover: false,
      carouselHeight: 'crop',
      carouselWidth: 'crop'
    }, options);
    settings.transitionSpeed = (function() {
      switch (settings.transitionSpeed) {
        case 'slow':
          return 600;
        case 'normal':
          return 400;
        case 'fast':
          return 200;
        default:
          return settings.transitionSpeed;
      }
    })();
    /*HIDE ALL ITEMS AND SET THEIR CSS*/

    itemList.css({
      'position': 'absolute',
      'z-index': '1'
    });
    itemList.hide();
    /*SET THE PARENT DIV SIZE*/

    $(window).resize(function() {
      var newLeft;
      setParentSize();
      carousel.css('height', $(itemList[indexReference]).css('height'));
      if (settings.buttons === true && settings.buttonsTheme === "dots") {
        newLeft = (maxWidth - carousel.find('.carouselNav').width()) / 2;
        return carousel.find('.carouselNav').css('left', "" + newLeft + "px");
      }
    });
    setParentSize = function() {
      var count, maxCount;
      maxHeight = 0;
      maxWidth = 0;
      count = -1;
      maxCount = 0;
      return itemList.each(function(index, element) {
        if ($(element).height() > maxHeight) {
          maxHeight = $(element).height();
        }
        if ($(element).width() > maxWidth) {
          maxWidth = $(element).width();
        }
        if ($(element).height() < minHeight) {
          minHeight = $(element).height();
        }
        if ($(element).width() < minWidth) {
          minWidth = $(element).width();
        }
        if ($(element).width() > $(element).css('max-width')) {
          $(element).css('max-width', 'none');
        } else {
          $(element).css('max-width', '100%');
        }
        if ($(element).data('link')) {
          return $(element).click(function() {
            return window.open($(element).data('link'));
          }).css('cursor', 'pointer');
        }
      });
    };
    setParentSize();
    /*SET THE PARENT DIV CSS*/

    startingHeight = (function() {
      switch (settings.carouselHeight) {
        case 'dynamic':
          return $(itemList[0]).height();
        case 'crop':
          return minHeight;
        default:
          return maxHeight;
      }
    })();
    startingWidth = (function() {
      switch (settings.carouselWidth) {
        case 'dynamic':
          return $(itemList[0]).width();
        case 'crop':
          return minWidth;
        default:
          return maxWidth;
      }
    })();
    carousel.css({
      'position': 'relative',
      'height': startingHeight + 'px',
      'width': startingWidth + 'px',
      'overflow': 'hidden',
      'max-width': '100%'
    });
    /*SET THE HEIGHT AGAIN IN CASE IT'S DIFFERENT*/

    if (settings.carouselHeight === 'crop' || settings.carouselHeight === 'dynamic') {
      carousel.css('height', $(itemList[0]).height());
    }
    /*SET THE INDEX REFERENCE*/

    indexReference = 0;
    /*SET ANIMATION SWITCHES*/

    bringFront = function(item) {
      var newLeft;
      switch (settings.style) {
        case 'fade':
          $(item).fadeIn(settings.transitionSpeed);
          break;
        case 'slide-left':
          $(item).css({
            'left': maxWidth,
            'z-index': '2'
          }).show().animate({
            left: 0
          }, settings.transitionSpeed, function() {
            return $(item).css('left', 'auto');
          });
          break;
        case 'block':
          $(item).show();
          break;
        case 'slide-up':
          $(item).css({
            'top': maxHeight,
            'z-index': '2'
          }).show().animate({
            top: 0
          }, settings.transitionSpeed);
          break;
        case 'slide-diagonal':
          $(item).css({
            'left': maxWidth,
            'top': maxHeight,
            'z-index': '2'
          }).show().animate({
            left: 0,
            top: 0
          }, settings.transitionSpeed);
          break;
        case 'shuffle':
          $(item).show().animate({
            top: -maxHeight / 1.25
          }, settings.transitionSpeed / 2, function() {
            return $(item).animate({
              top: 0
            }, settings.transitionSpeed / 2);
          });
          setTimeout(function() {
            return $(item).css('z-index', '2');
          }, settings.transitionSpeed / 2);
          break;
        case 'scale-down':
          $(item).fadeIn(settings.transitionSpeed);
      }
      if (settings.carouselHeight === 'dynamic') {
        carousel.animate({
          height: $(item).height()
        }, settings.transitionSpeed);
      }
      if (settings.carouselWidth === 'dynamic') {
        if ($(window).width() > carousel.width()) {
          $(item).css('max-width', 'none');
        }
        carousel.animate({
          width: $(item).css('width')
        }, settings.trainsitionSpeed);
        if (settings.buttons === true && settings.buttonsTheme === "dots") {
          newLeft = ($(item).width() - carousel.find('.carouselNav').width()) / 2;
          return carousel.find('.carouselNav').css('left', "" + newLeft + "px");
        }
      }
    };
    takeAway = function(item) {
      switch (settings.style) {
        case 'fade':
          return $(item).fadeOut(settings.transitionSpeed);
        case 'slide-left':
          $(item).css('z-index', '1');
          $(item).animate({
            left: -100
          }, settings.transitionSpeed);
          return setTimeout(function() {
            return $(item).fadeOut();
          }, settings.transitionSpeed / 1.4);
        case 'slide-up':
          $(item).css('z-index', '1');
          $(item).animate({
            top: -100
          }, settings.transitionSpeed);
          return setTimeout(function() {
            return $(item).fadeOut();
          }, settings.transitionSpeed / 1.4);
        case 'slide-diagonal':
          $(item).css('z-index', '1');
          return setTimeout(function() {
            return $(item).fadeOut();
          }, settings.transitionSpeed / 1.4);
        case 'block':
          return $(item).hide();
        case 'shuffle':
          $(item).animate({
            top: maxHeight / 1.25
          }, settings.transitionSpeed / 2, function() {
            return $(item).animate({
              top: 0
            }, settings.transitionSpeed / 2, function() {
              return $(item).hide();
            });
          });
          return setTimeout(function() {
            return $(item).css('z-index', '1');
          }, settings.transitionSpeed / 2);
        case 'scale-down':
          return $(item).slideUp(settings.transitionSpeed);
      }
    };
    /*SET THE TRANSITION FUNCTION*/

    transition = function(newIndex) {
      takeAway(itemList[indexReference]);
      carousel.find('.activeCarouselNav').removeClass('activeCarouselNav');
      if (newIndex || newIndex === 0) {
        indexReference = newIndex;
        carousel.find('.carouselNav li:eq(' + newIndex + ')').addClass('activeCarouselNav');
      } else {
        if (indexReference + 1 >= $(itemList).length) {
          indexReference = 0;
        } else {
          indexReference++;
        }
        carousel.find(".carouselNav li:eq(" + indexReference + ")").addClass('activeCarouselNav');
      }
      return bringFront(itemList[indexReference]);
    };
    /*SET THE CAROUSEL BUTTONS/NAV*/

    if (settings.buttons === true) {
      switch (settings.buttonsTheme) {
        case "lines":
          navWidth = 100 / itemList.length + "%";
          break;
        case "dots":
          navWidth = '15px';
          break;
        case "numbers":
          navWidth = "30px";
      }
      $('<ul></ul>').prependTo(carousel).addClass('carouselNav ' + settings.buttonsTheme);
      itemList.each(function(index, slide) {
        return $('<li></li>').css('width', navWidth).attr('title', $(slide).attr('data-index', index).data('shortDescription')).appendTo($(slide).parent().find('ul')).on('mouseover', function() {
          stopCarousel();
          if ($(this).index() !== indexReference) {
            return transition(index);
          }
        });
      });
      $('.carouselNav li:eq(0)').addClass('activeCarouselNav');
      if (settings.carouselWidth === 'crop') {
        newLeftWidth = minWidth;
      } else if (settings.carouselWidth === 'dynamic') {
        newLeftWidth = $(itemList[0]).width();
      } else {
        newLeftWidth = maxWidth;
      }
      newLeft = (newLeftWidth - carousel.find('.carouselNav').width()) / 2;
      if (settings.buttonsTheme === "dots") {
        carousel.find('.carouselNav').css('left', "" + newLeft + "px");
      } else if (settings.buttonsTheme === "numbers") {
        carousel.find('.carouselNav li').each(function(index, element) {
          return $(element).text(index + 1);
        });
      }
      carousel.find('.carouselNav').on('mouseover', function() {
        console.log('mouseover');
        return stopCarousel();
      });
      carousel.find('.carouselNav').on('mouseout', function() {
        console.log('mouseout');
        return startCarousel();
      });
    }
    /*SET THE NEXT AND LAST LINKS*/

    if (settings.arrows === true) {
      $('<a/>').prependTo(carousel).addClass('carousel-next').on('click', function() {
        return transition();
      }).on('mouseover', function() {
        return stopCarousel();
      }).on('mouseout', function() {
        return startCarousel();
      });
      $('<a/>').prependTo(carousel).addClass('carousel-last').on('click', function() {
        var newIndex;
        if (indexReference === 0) {
          newIndex = itemList.length - 1;
        } else {
          newIndex = indexReference - 1;
        }
        return transition(newIndex);
      }).on('mouseover', function() {
        return stopCarousel();
      }).on('mouseout', function() {
        return startCarousel();
      });
    }
    /*SHOW THE FIRST SLIDE*/

    $(itemList[0]).show();
    startCarousel = function() {
      stopCarousel();
      carouselCycle = window.setInterval(transition, settings.carouselSpeed);
      return true;
    };
    stopCarousel = function() {
      window.clearInterval(carouselCycle);
      return true;
    };
    /*INITIATE THE CAROUSEL*/

    stopCarousel();
    startCarousel();
    /*SET CAROUSEL TO STOP ON HOVER*/

    if (settings.stopOnHover === true) {
      carousel.on('mouseover', function() {
        return stopCarousel();
      });
      return carousel.on('mouseout', function() {
        return startCarousel();
      });
    }
  };
}).call(this);
