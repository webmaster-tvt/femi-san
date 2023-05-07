jQuery(document).ready(function($) {
  if ($(".dnxte_logo_carousel_child").length) {
    $(".dnxte_logo_carousel_child").addClass("swiper-slide");
    $(".dnxte_logo_carousel_child .et_pb_module_inner").addClass(
      "dnext-logo-carosuel-item"
    );
  }

  if ($(".dnxte_coverflowslider_child").length) {
    $(".dnxte_coverflowslider_child ").each(function() {
      const imagePosition = $(this)
        .find(".dnxte-coverflow-image-container")
        .data("imagePosition");

      $(this).addClass("swiper-slide");
      $(this)
        .children(".et_pb_module_inner")
        .addClass(
          `dnxte-coverflowslider-item ${
            imagePosition ? `dnxte-coverflow-carousel-${imagePosition}` : ""
          }`
        );
    });
  }

  if ($(".dnxte_3dcubeslider_child").length) {
    $(".dnxte_3dcubeslider_child").addClass("swiper-slide");
    $(".dnxte_3dcubeslider_child .et_pb_module_inner").addClass(
      "dnxte-3dcubeslider-item"
    );
  }
  if ($(".dnxte_thumbs_gallery_child").length) {
    $(".dnxte_thumbs_gallery_child").addClass("swiper-slide");
    $(".dnxte_thumbs_gallery_child .et_pb_module_inner").addClass(
      "dnext-thumbs-gallery-item"
    );
  }

  if ($(".dnxte_testimonial_child").length) {
    $(".dnxte_testimonial_child").addClass("swiper-slide");
    $(".dnxte_testimonial_child .et_pb_module_inner").addClass(
      "dnxte-tstimonial-item"
    );
  }

  // adjust br tag in ultimate image hover effect module....
  if ($(".dnext-neip-uih-descwrap").length) {
    $(".dnext-neip-uih-descwrap").each(function() {
      $("p:empty").remove();
      $(".dnext-neip-uih-des-pra").after("<br>");
    });
  }

  (function($) {
    "use strict";
    /*============================= Swiper Slider =================================*/
    $(".dnext-logo-carosuel-active").each(function(e) {
      const logoData = $(this).data();
      const carouselBreakpoint =
        logoData.breakpoints && logoData.breakpoints.split("|");

      const logoSwiper = new Swiper(this, {
        direction: logoData.direction,
        speed: logoData.speed ? parseInt(logoData.speed) : 400,
        spaceBetween: parseInt(logoData.spacing),
        grabCursor: "on" === logoData.grab,
        preventClicksPropagation: true,
        autoplay: {
          enabled: 1 === logoData.autoplay,
          delay: logoData.delay ? parseInt(logoData.delay) : 0,
          disableOnInteraction: !1,
        },
        slidesPerView: carouselBreakpoint && parseInt(carouselBreakpoint[0]),
        centeredSlides: "on" === logoData.center,
        loop: "on" === logoData.loop,
        breakpoints: {
          1024: {
            slidesPerView:
              carouselBreakpoint && parseInt(carouselBreakpoint[0]),
          },
          768: {
            slidesPerView:
              carouselBreakpoint && parseInt(carouselBreakpoint[1]),
          },
          479: {
            slidesPerView:
              carouselBreakpoint && parseInt(carouselBreakpoint[2]),
          },
          200: {
            slidesPerView:
              carouselBreakpoint && parseInt(carouselBreakpoint[2]),
          },
        },
        pagination: {
          el: ".swiper-pagination",
          dynamicBullets: "on" === logoData.paginationBullets,
          clickable: true,
          type: logoData.paginationType,
        },
        keyboard: {
          enabled: "on" === logoData.keyboard,
          onlyInViewport: false,
        },
        mousewheel: {
          enabled: "on" === logoData.mouse,
          invert: true,
        },
        navigation: {
          nextEl: $(this).siblings(".swiper-button-next")[0],
          prevEl: $(this).siblings(".swiper-button-prev")[0],
        },
        observer: !0,
        observeParents: !0,
        observeSlideChildren: !0,
      });

      if (1 === logoData.autoplay && logoData.pauseonhover === 1) {
        $(this).on("mouseenter", function() {
          logoSwiper.autoplay.stop();
        });
        $(this).on("mouseleave", function() {
          logoSwiper.autoplay.start();
        });
      }
    });

    $(".dnxte-coverflowslider-active").length &&
      $(".dnxte-coverflowslider-active").each(function(e) {
        const coverflow = $(this).data();
        const coverflowBreakpoint =
          coverflow.breakpoints && coverflow.breakpoints.split("|");

        const coverflowSwiper = new Swiper(this, {
          direction: coverflow.direction,
          autoHeight: "on" === coverflow.autoheight,
          speed: coverflow.speed ? parseInt(coverflow.speed) : 400,
          spaceBetween: parseInt(coverflow.spacing),
          grabCursor: "on" === coverflow.grab,
          preventClicksPropagation: true,
          centeredSlides: 1 === coverflow.center,
          autoplay: {
            enabled: 1 === coverflow.autoplay,
            delay: coverflow.delay ? parseInt(coverflow.delay) : 0,
            disableOnInteraction: !1,
          },
          effect: "coverflow",
          coverflowEffect: {
            slideShadows: "on" === coverflow.covershadow,
            rotate: +coverflow.coverrotate,
            stretch: +coverflow.coverstretch,
            depth: +coverflow.coverdepth,
          },
          slidesPerView:
            coverflowBreakpoint && parseInt(coverflowBreakpoint[0]),
          centeredSlides: 1 === coverflow.center,
          loop: "on" === coverflow.loop,
          breakpoints: {
            1024: {
              slidesPerView:
                coverflowBreakpoint && parseInt(coverflowBreakpoint[0]),
            },
            768: {
              slidesPerView:
                coverflowBreakpoint && parseInt(coverflowBreakpoint[1]),
            },
            479: {
              slidesPerView:
                coverflowBreakpoint && parseInt(coverflowBreakpoint[2]),
            },
            200: {
              slidesPerView:
                coverflowBreakpoint && parseInt(coverflowBreakpoint[2]),
            },
          },
          pagination: {
            el: ".swiper-pagination",
            dynamicBullets: "on" === coverflow.paginationBullets,
            clickable: true,
            type: coverflow.paginationType,
          },
          keyboard: {
            enabled: "on" === coverflow.keyboardenable,
            onlyInViewport: false,
          },
          mousewheel: {
            enabled: "on" === coverflow.mouse,
            invert: true,
          },
          navigation: {
            nextEl:
              $(this).siblings(".swiper-button-next")[0] ||
              $(this)
                .siblings(".swiper-button-container")
                .children(".swiper-button-next")[0],
            prevEl:
              $(this).siblings(".swiper-button-prev")[0] ||
              $(this)
                .siblings(".swiper-button-container")
                .children(".swiper-button-prev")[0],
          },
          observer: !0,
          observeParents: !0,
          observeSlideChildren: !0,
        });

        // console.log($(this).siblings(".swiper-button-container").children(".swiper-button-prev")[0])
        if (1 === coverflow.autoplay && coverflow.pauseonhover === 1) {
          $(this).on("mouseenter", function() {
            coverflowSwiper.autoplay.stop();
          });
          $(this).on("mouseleave", function() {
            coverflowSwiper.autoplay.start();
          });
        }
      });

    $(".dnxte-3dcubeslider-active").length &&
      $(".dnxte-3dcubeslider-active").each(function() {
        const cubeslider3d = $(this).data();
        var cubeslider = new Swiper(this, {
          autoHeight: false,
          speed: cubeslider3d.speed ? parseInt(cubeslider3d.speed) : 400,
          direction: cubeslider3d.direction,
          grabCursor: "on" === cubeslider3d.grab,
          loop: "on" === cubeslider3d.loop,
          effect: "cube",
          cubeEffect: {
            slideShadows: 1 === cubeslider3d.slideshadows,
            shadow: 1 === cubeslider3d.shadow,
            shadowOffset:
              1 === cubeslider3d.shadow
                ? parseInt(cubeslider3d.shadowoffset)
                : 0,
            shadowScale:
              1 === cubeslider3d.shadow
                ? parseFloat(cubeslider3d.shadowscale)
                : 0,
          },
          autoplay: {
            enabled: 1 === cubeslider3d.autoplay,
            delay: cubeslider3d.delay ? parseInt(cubeslider3d.delay) : 0,
            disableOnInteraction: !1,
          },
          pagination: {
            el: ".swiper-pagination",
            dynamicBullets: "on" === cubeslider3d.paginationBullets,
            clickable: true,
            type: cubeslider3d.paginationType,
          },
          keyboard: {
            enabled: "on" === cubeslider3d.keyboardenable,
            onlyInViewport: false,
          },
          mousewheel: {
            enabled: "on" === cubeslider3d.mouse,
            invert: true,
          },
          navigation: {
            nextEl: $(this).siblings(".swiper-button-next")[0],
            prevEl: $(this).siblings(".swiper-button-prev")[0],
          },
          observer: !0,
          observeParents: !0,
          observeSlideChildren: !0,
        });

        if (1 === cubeslider3d.autoplay && cubeslider3d.pauseonhover === 1) {
          $(this).on("mouseenter", function() {
            cubeslider.autoplay.stop();
          });
          $(this).on("mouseleave", function() {
            cubeslider.autoplay.start();
          });
        }
      });

    $(".dnext-thumbs-gallery-top").length &&
      $(".dnext-thumbs-gallery-top").each(function(e) {
        // thumbs bottom slider gallery
        const that =
          "." +
          $(this)
            .parent()
            .parent()
            .parent()
            .attr("class")
            .split(" ")
            .join(".");

        const thumbsBottomData = $(
          that + " .dnext-thumbs-gallery-bottom"
        ).data();
        const thumbsBottomBreakpoint =
          thumbsBottomData.breakpoints &&
          thumbsBottomData.breakpoints.split("|");

        const thumbsBottomSwiper = new Swiper(
          that + " .dnext-thumbs-gallery-bottom",
          {
            direction: thumbsBottomData.direction,
            speed: thumbsBottomData.speed
              ? parseInt(thumbsBottomData.speed)
              : 400,
            spaceBetween: parseInt(thumbsBottomData.spacing),
            grabCursor: "on" === thumbsBottomData.grab,
            preventClicksPropagation: true,
            autoplay: {
              enabled: 1 === thumbsBottomData.autoplay,
              delay: thumbsBottomData.delay
                ? parseInt(thumbsBottomData.delay)
                : 0,
              disableOnInteraction: !1,
            },
            slidesPerView:
              thumbsBottomBreakpoint && parseInt(thumbsBottomBreakpoint[0]),
            centeredSlides: "on" === thumbsBottomData.center,
            loop: "on" === thumbsBottomData.loop,
            breakpoints: {
              1024: {
                slidesPerView:
                  thumbsBottomBreakpoint && parseInt(thumbsBottomBreakpoint[0]),
              },
              768: {
                slidesPerView:
                  thumbsBottomBreakpoint && parseInt(thumbsBottomBreakpoint[1]),
              },
              479: {
                slidesPerView:
                  thumbsBottomBreakpoint && parseInt(thumbsBottomBreakpoint[2]),
              },
              200: {
                slidesPerView:
                  thumbsBottomBreakpoint && parseInt(thumbsBottomBreakpoint[2]),
              },
            },
            keyboard: {
              enabled: "on" === thumbsBottomData.keyboard,
              onlyInViewport: false,
            },
            mousewheel: {
              enabled: "on" === thumbsBottomData.mouse,
              invert: true,
            },
            observer: !0,
            observeParents: !0,
            observeSlideChildren: !0,
            watchSlidesVisibility: true,
            watchSlidesProgress: true,
          }
        );

        if (
          1 === thumbsBottomData.autoplay &&
          thumbsBottomData.pauseonhover === 1
        ) {
          $(this).on("mouseenter", function() {
            thumbsBottomSwiper.autoplay.stop();
          });
          $(this).on("mouseleave", function() {
            thumbsBottomSwiper.autoplay.start();
          });
        }
        // thumbs bottom slider gallery

        const thumbsData = $(this).data();
        const thumbsBreakpoint =
          thumbsData.breakpoints && thumbsData.breakpoints.split("|");
        const thumbsSwiper = new Swiper(this, {
          direction: thumbsData.direction,
          speed: thumbsData.speed ? parseInt(thumbsData.speed) : 400,
          spaceBetween: parseInt(thumbsData.spacing),
          grabCursor: "on" === thumbsData.grab,
          preventClicksPropagation: true,
          autoplay: {
            enabled: 1 === thumbsData.autoplay,
            delay: thumbsData.delay ? parseInt(thumbsData.delay) : 0,
            disableOnInteraction: !1,
          },
          slidesPerView: thumbsBreakpoint && parseInt(thumbsBreakpoint[0]),
          centeredSlides: "on" === thumbsData.center,
          loop: "on" === thumbsData.loop,
          breakpoints: {
            1024: {
              slidesPerView: thumbsBreakpoint && parseInt(thumbsBreakpoint[0]),
            },
            768: {
              slidesPerView: thumbsBreakpoint && parseInt(thumbsBreakpoint[1]),
            },
            479: {
              slidesPerView: thumbsBreakpoint && parseInt(thumbsBreakpoint[2]),
            },
            200: {
              slidesPerView: thumbsBreakpoint && parseInt(thumbsBreakpoint[2]),
            },
          },
          pagination: {
            el: ".swiper-pagination",
            dynamicBullets: "on" === thumbsData.paginationBullets,
            clickable: true,
            type: thumbsData.paginationType,
          },
          keyboard: {
            enabled: "on" === thumbsData.keyboard,
            onlyInViewport: false,
          },
          mousewheel: {
            enabled: "on" === thumbsData.mouse,
            invert: true,
          },
          navigation: {
            nextEl: $(this).siblings(".swiper-button-next")[0],
            prevEl: $(this).siblings(".swiper-button-prev")[0],
          },
          thumbs: {
            swiper: thumbsBottomSwiper,
          },
          observer: !0,
          observeParents: !0,
          observeSlideChildren: !0,
        });

        if (1 === thumbsData.autoplay && thumbsData.pauseonhover === 1) {
          $(this).on("mouseenter", function() {
            thumbsSwiper.autoplay.stop();
          });
          $(this).on("mouseleave", function() {
            thumbsSwiper.autoplay.start();
          });
        }
      });

    $(".dnxte-tstimonial-wrap").length &&
      $(".dnxte-tstimonial-wrap").each(function(e) {
        const testimonialData = $(this).data();
        const testimonialBreakpoints =
          testimonialData.breakpoints && testimonialData.breakpoints.split("|");
        const testimonialSwiper = new Swiper(this, {
          autoplay: {
            enabled: 1 === testimonialData.autoplay,
            delay: testimonialData.delay ? parseInt(testimonialData.delay) : 0,
            disableOnInteraction: !1,
          },
          slidesPerView:
            testimonialBreakpoints && parseInt(testimonialBreakpoints[0]),
          centeredSlides: "on" === testimonialData.center,
          spaceBetween: parseInt(testimonialData.spacing),
          direction: testimonialData.direction,
          speed: testimonialData.speed ? parseInt(testimonialData.speed) : 400,
          grabCursor: "on" === testimonialData.grab,
          loop: "on" === testimonialData.loop,
          breakpoints: {
            1024: {
              slidesPerView:
                testimonialBreakpoints && parseInt(testimonialBreakpoints[0]),
            },
            768: {
              slidesPerView:
                testimonialBreakpoints && parseInt(testimonialBreakpoints[1]),
            },
            479: {
              slidesPerView:
                testimonialBreakpoints && parseInt(testimonialBreakpoints[2]),
            },
            200: {
              slidesPerView:
                testimonialBreakpoints && parseInt(testimonialBreakpoints[2]),
            },
          },
          pagination: {
            el: ".swiper-pagination",
            dynamicBullets: "on" === testimonialData.paginationBullets,
            clickable: true,
            type: testimonialData.paginationType,
          },
          navigation: {
            nextEl: $(this).siblings(".swiper-button-next")[0],
            prevEl: $(this).siblings(".swiper-button-prev")[0],
          },
          keyboard: {
            enabled: "on" === testimonialData.keyboard,
            onlyInViewport: false,
          },
          mousewheel: {
            enabled: "on" === testimonialData.mousewheel,
            invert: true,
          },
          observer: !0,
          observeParents: !0,
          observeSlideChildren: !0,
        });

        if (
          1 === testimonialData.autoplay &&
          testimonialData.pauseonhover === 1
        ) {
          $(this).on("mouseenter", function() {
            testimonialSwiper.autoplay.stop();
          });
          $(this).on("mouseleave", function() {
            testimonialSwiper.autoplay.start();
          });
        }
      });

    // Blog Slider.
    $(".dnxte-blog-carousel-slide-active").each(function(e) {
      const blogslide = $(this).data();

      const blogslideBreakpoint =
        blogslide.breakpoints && blogslide.breakpoints.split("|");

      const blogslidespaceBetween =
        blogslide.spaceBetween && blogslide.spaceBetween.split("|");

      var dnxteBlogslider = new Swiper(this, {
        autoHeight: "on" === blogslide.autoheight,
        speed: blogslide.speed ? parseInt(blogslide.speed) : 400,
        centeredSlides: 1 === blogslide.center,
        direction: "horizontal",
        slidesPerView: blogslideBreakpoint && parseInt(blogslideBreakpoint[0]),
        spaceBetween:
          blogslidespaceBetween && parseInt(blogslidespaceBetween[0]),
        effect: "coverflow",
        coverflowEffect: {
          slideShadows: "on" === blogslide.covershadow,
          rotate: +blogslide.coverrotate,
          stretch: +blogslide.coverstretch,
          depth: +blogslide.coverdepth,
        },
        breakpoints: {
          1024: {
            slidesPerView:
              blogslideBreakpoint && parseInt(blogslideBreakpoint[0]),
            spaceBetween:
              blogslidespaceBetween && parseInt(blogslidespaceBetween[0]),
          },
          768: {
            slidesPerView:
              blogslideBreakpoint && parseInt(blogslideBreakpoint[1]),
            spaceBetween:
              blogslidespaceBetween && parseInt(blogslidespaceBetween[1]),
          },
          479: {
            slidesPerView:
              blogslideBreakpoint && parseInt(blogslideBreakpoint[2]),
            spaceBetween:
              blogslidespaceBetween && parseInt(blogslidespaceBetween[2]),
          },
          200: {
            slidesPerView:
              blogslideBreakpoint && parseInt(blogslideBreakpoint[2]),
            spaceBetween:
              blogslidespaceBetween && parseInt(blogslidespaceBetween[2]),
          },
        },
        grabCursor: "on" === blogslide.grabCursor,
        loop: "on" === blogslide.loop,
        autoplay: {
          enabled: 1 === blogslide.autoplay,
          delay: blogslide.delay ? parseInt(blogslide.delay) : 1000,
          disableOnInteraction: !1,
        },
        pagination: {
          el: ".swiper-pagination",
          dynamicBullets: "on" === blogslide.paginationBullets,
          clickable: "on" === blogslide.clickable,
          type: blogslide.paginationType,
        },
        keyboard: {
          enabled: "on" === blogslide.keyboardenable,
          onlyInViewport: false,
        },
        mousewheel: {
          enabled: "on" === blogslide.mouse,
          invert: true,
        },
        navigation: {
          nextEl:
            $(this).siblings(".swiper-button-next")[0] ||
            $(this)
              .siblings(".swiper-button-container")
              .children(".swiper-button-next")[0],
          prevEl:
            $(this).siblings(".swiper-button-prev")[0] ||
            $(this)
              .siblings(".swiper-button-container")
              .children(".swiper-button-prev")[0],
        },
      });
      if (1 === blogslide.autoplay && blogslide.pauseonhover === 1) {
        $(this).mouseenter(function() {
          dnxteBlogslider.autoplay.stop();
        });
        $(this).mouseleave(function() {
          dnxteBlogslider.autoplay.start();
        });
      }
    });

    $(".dnxte-lottie").length &&
      $(".dnxte-lottie").each(function() {
        const lottieData = $(this).data();

        const lottie = bodymovin.loadAnimation({
          wrapper: this,
          animType: "svg",
          loop: lottieData.loop === "on",
          path: lottieData.path,
          autoplay: lottieData.autoplay === "on",
        });

        lottie.setSpeed(parseFloat(lottieData.speed));
        lottie.setDirection(parseInt(lottieData.direction));

        if (lottieData.autoplay === "off" && lottieData.playHover === "on") {
          lottie.goToAndStop(parseInt(lottieData.startFrame), true);
        }

        $(this).on("mouseenter", function() {
          if (lottieData.autoplay === "off" && lottieData.playHover === "on") {
            lottie.play();
          } else if (
            lottieData.autoplay === "on" &&
            lottieData.stopHover === "on"
          ) {
            lottie.pause();
          }
        });

        $(this).on("mouseleave", function() {
          if (lottieData.autoplay === "off" && lottieData.playHover === "on") {
            lottie.pause();
          } else if (
            lottieData.autoplay === "on" &&
            lottieData.stopHover === "on"
          ) {
            lottie.play();
          }
        });
      });

    // Before After Slider
    $(".dnxtecomparisonslide").length &&
      $(".dnxtecomparisonslide").each(function(e) {
        const beforeAfterData = $(this).data();
        $(this).twentytwenty({
          default_offset_pct: beforeAfterData.offset, // How much of the before image is visible when the page loads
          orientation: beforeAfterData.orientation, // Orientation of the before and after images ('horizontal' or 'vertical')
          before_label: beforeAfterData.beforeLabel, // Set a custom before label
          after_label: beforeAfterData.afterLabel, // Set a custom after label
          no_overlay: "off" === beforeAfterData.overlay, //Do not show the overlay with before and after
          move_slider_on_hover: "on" === beforeAfterData.moveslideronhover, // Move slider on mouse hover?
          move_with_handle_only: "on" === beforeAfterData.movewithhandleonly, // Allow a user to swipe anywhere on the image to control slider movement.
          click_to_move: "on" === beforeAfterData.clicktomove, // Allow a user to click (or tap) anywhere on the image to move the slider to that location.
        });
      });

    $(".dnxte-twitter-tweet").length &&
      $(".dnxte-twitter-tweet").each(function(e) {
        const tweetData = $(this).data();

        window.twttr &&
          window.twttr.widgets.createTweet(tweetData.id, this, {
            theme: tweetData.themeName,
            cards: tweetData.showCards === "on" ? "" : "hidden",
            conversation: tweetData.showConversation === "on" ? "" : "none",
            width: tweetData.width || 325,
            align: tweetData.align || "center",
            lang: tweetData.lang || "en",
          });
      });

    $(".dnxte-twitter-follow").length &&
      $(".dnxte-twitter-follow").each(function(e) {
        const followData = $(this).data();

        if (window.twttr && followData.username !== "") {
          window.twttr.widgets.createFollowButton(followData.username, this, {
            showScreenName: "on" === followData.showScreenName,
            showCount: "on" === followData.showCount,
            size: followData.size || "large",
            lang: followData.lang || "en",
          });
        }
      });

    // hotspot bottom position settings
    $(".dnxte-hostpot-hotspots__wrapper").each(function() {
      const isBottom = $(this)
        .parent()
        .parent()
        .hasClass("tooltip-bottom");

      const that =
        "." +
        $(this)
          .parent()
          .parent()
          .parent()
          .parent()
          .parent()
          .parent()
          .attr("class")
          .split(" ")
          .join(".");

      isBottom &&
        $(
          `${that} .dnxte-hostpot-hotspots__wrapper, ${that} .dnxte-hostpot-tooltip-text`
        ).hover(
          () => {
            //hover
            $(`${that} .dnxte-hostpot-tooltip-text`).addClass(
              "tooltip-hover-bottom"
            );
          },
          () => {
            //out
            $(`${that} .dnxte-hostpot-tooltip-text`).removeClass(
              "tooltip-hover-bottom"
            );
          }
        );
    });
  })(jQuery);
});
