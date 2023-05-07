(function($) {
  // Image Accordion
  $.fn.dnxte_image_accordion = function() {
    let $this = $(this);
    let accordion_type = $this
      .find(".dnxte_image_accordion_wrapper")
      .data("accordion-type");

    let expandLastItem = $this
      .find(".dnxte_image_accordion_wrapper")
      .data("expand-last-item");

    let $accordion_children = $this.find(".dnxte_image_accordion_item");

    let onloadOpen = $this.find("[data-active-on-load=1]");
    let accordionExpand = $this.find("[data-accordion-expand=default]");

    //Setup active on load
    onloadOpen.parents(".dnxte_image_accordion_item").addClass("dnxte-active");
    if (accordion_type === "on_hover") {
      $accordion_children.mouseenter(function() {
        $accordion_children.removeClass("dnxte-active");
        $(this).addClass("dnxte-active");
      });

      "off" === expandLastItem &&
        $accordion_children.mouseleave(function() {
          $accordion_children.removeClass("dnxte-active");
          accordionExpand
            .parents(".dnxte_image_accordion_item")
            .addClass("dnxte-active");
        });
    }

    // Setup active on click
    if (accordion_type === "on_click") {
      $this.addClass("dnxte_clickable");
      $accordion_children.click(function(e) {
        if ($(this).hasClass("dnxte-active")) {
          return;
        }
        $accordion_children.removeClass("dnxte-active");
        $(this).addClass("dnxte-active");
      });
    }
  };

  $(document).ready(function($) {
    const $grid = $(".dnxte-msnary-grid");

    // if ($grid.length) {
    //   $grid
    //     .masonry({
    //       isFitWidth: true,
    //       itemSelector: ".dnxte-msnary-item",
    //       percentPosition: true,
    //       columnWidth: ".grid-sizer",
    //       gutter: ".gutter-sizer",
    //       stagger: 0,
    //       transitionDuration: 90,
    //       percentPosition: true,
    //       horizontalOrder: true,
    //     })
    //     .imagesLoaded(function() {
    //       $grid.masonry("reload");
    //     });
    // }

    // if ($grid.length) {
    //   $(".dnxte-grid").imagesLoaded(() => {
    //     $grid.masonry({
    //       itemSelector: ".dnxte-msnary-item",
    //       percentPosition: true,
    //       columnWidth: ".grid-sizer",
    //       gutter: ".gutter-sizer",
    //       stagger: 0,
    //       transitionDuration: 90,
    //       percentPosition: true,
    //       horizontalOrder: true,
    //     });
    //   });
    // }

    if ($grid.length) {
      $grid.each(function() {
        const { gutter } = $(this).data();
        $(this).isotope({
          // layoutMode: "masonry",
          itemSelector: ".dnxte-msnary-item",
          percentPosition: true,
          stagger: 0,
          // transitionDuration: 90,
          // percentPosition: true,
          horizontalOrder: true,
          masonry: {
            columnWidth: ".grid-sizer",
            gutter: parseInt(gutter, 10),
          },
        });
      });
    }

    $(".dnxte-msnary-filter-items li").on("click", function(item) {
      const that =
        "." +
        $(this)
          .parent()
          .parent()
          .parent()
          .parent()
          .parent()
          .attr("class")
          .split(" ")
          .join(".");

      $(`${that} .dnxte-msnary-filter-items li`).removeClass("active");
      $(this).addClass("active");
      var selector = $(this).attr("data-filter");

      $(`${that} .dnxte-msnary-grid`).isotope({
        filter: selector,
      });
    });

    const img = $(".image-link");
    const lighboxData = $grid.length && $grid.data();

    if (img.length && "none" !== lighboxData.lightbox) {
      img.each(function() {
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

        // if("none" !== lighboxData.lightbox) {

        // }
        $(`${that} .image-link`).magnificPopup({
          removalDelay: 500,
          type: "image",
          gallery: {
            enabled: true,
            navigateByImgClick: true,
            tCounter: "%curr% of %total%", // Markup for "1 of 7" counter
          },
          disableOn: function() {
            return lighboxData.lightbox;
          },
          image: {
            markup:
              '<div class="mfp-figure dnxte-msnary-mfp-config">' +
              '<div class="mfp-close"></div>' +
              '<div class="mfp-img"></div>' +
              '<div class="mfp-bottom-bar">' +
              '<div class="mfp-title"></div>' +
              '<div class="mfp-counter"></div>' +
              "</div>" +
              "</div>",
            titleSrc: function(item) {
              return item.el.attr("data-title") + item.el.attr("data-caption");
            },
          },
          zoom: {
            enabled: lighboxData.lightbox,
            duration: 500,
            opener: function(element) {
              return element.find("img");
            },
          },
        });
      });
    }

    $(".dnxte-magnifier img").each(function() {
      const magnifyData = $(this).data();
      $(this).magnify({
        speed: +magnifyData.speed,
        limitBounds: magnifyData.boundary === "on",
      });
    });

    $(document).ready(function() {
      $(".dnxte_image_accordion").each(function() {
        $(this).dnxte_image_accordion();
      });
    });
  });

  jQuery(function(dnxte_contenttoggle) {
    dnxte_contenttoggle(".dnxte-toggle-btn .dnxte-input").each(function() {
      var n = dnxte_contenttoggle(this)
          .parents(".dnxte-content-toggle")
          .find(".dnxte-content-toggle-back"),
        e = dnxte_contenttoggle(this)
          .parents(".dnxte-content-toggle")
          .find(".dnxte-content-toggle-front");
      this.checked ? (e.hide(), n.show()) : (n.hide(), e.show()),
        dnxte_contenttoggle(this).on("change", function() {
          this.checked ? (e.hide(), n.show()) : (n.hide(), e.show());
        });
    });
  });
})(jQuery);
