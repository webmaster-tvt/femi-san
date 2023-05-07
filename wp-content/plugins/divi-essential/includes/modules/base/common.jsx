import React from "react";
import shapes from "./shape";

/**
 * Title Component
 * @param {{
    title: string
    dynamicTitle: object
    tag: String
    classes: string
  }},,,
 * @param {string} String- As title props, you have to serve title text slug(not dynamic slug)
 * @param {object} Object- As dynamicTitle props, serve dynamic title text slug(ex: this.props.dynamic.your_title_slug)
 * @param {String} String- As tag props , serve your title's html tag (ex: h1,h2,h3 etc).By default it's 'h2'.
 * @param {string} String- As classes props, you have to serve Title css className, By default there will be no className
 */
const Title = ({
  title,
  dynamicTitle,
  tag: HeadingTag = "h2",
  classes = "",
}) => {
  if ("" === title) return "";
  let titleComponent = dynamicTitle.render();
  if (dynamicTitle.loading) return titleComponent;
  return <HeadingTag className={classes}>{titleComponent}</HeadingTag>;
};

/**
 * Description Component
 * @param {{
 *  desc: String
 *  dynamicDesc: Object
 *  classes: String
 * }}
 * @param {string} String- As desc props, you have to serve description text slug(not dynamic slug)
 * @param {object} Object- As dynamicDesc props, serve dynamic description text slug(ex: this.props.dynamic.your_desc_slug)
 * @param {string} String- As classes props, you have to serve Description css className, By default there will be no className
 */
const Description = ({ desc, dynamicDesc, classes = "" }) => {
  if ("" === desc) return "";
  let descComponent = dynamicDesc.render();
  if (dynamicDesc.loading) return descComponent;
  return (
    <p
      className={classes}
      dangerouslySetInnerHTML={{ __html: dynamicDesc.value }}
    />
  );
};

/**
 * Button Component
 * @param {{
 *  text: String
 *  dynamicText: String
 *  link: String,
 *  linkTarget: String,
 *  classes: String
 * }}
 * @param {string} String- As text props, you have to serve button text slug(not dynamic slug)
 * @param {object} Object- As dynamicText props, serve dynamic button text slug(ex: this.props.dynamic.your_button_slug)
 * @param {String} String- As link props, serve valid url (ex: https://divinext.com)
 * @param {String} String- As linkTarget props, serve link target(ex: _blank, _self etc.)
 * @param {string} String- As classes props, you have to serve Button css className, By default there will be no className
 */
const Button = ({ text, dynamicText, link, linkTarget, classes = "" }) => {
  const validURL = (str) => {
    const pattern = /^((http|https|ftp):\/\/)/;
    if (!pattern.test(str)) return `http://${str}`;
    return str;
  };
  if ("" === text) return "";
  const validLink = validURL(link);
  let buttonComponent = dynamicText.render();
  if (dynamicText.loading) return buttonComponent;
  return (
    <a className={classes} href={validLink} target={linkTarget}>
      {buttonComponent}
    </a>
  );
};

/**
 * Image Component
 * @param {{
 *  dynamicImg: Object
 *  lastEdited: String
 *  hoverEnabled: String
 *  imageTablet: String
 *  imagePhone: String
 *  imageHover: String
 *  imgAlt: Object
 *  classes: String
 * }}
 * @param {object} Object -As dynamicImg props, serve dynamic image slug(ex: this.props.dynamic.your_image_slug)
 * @param {string} String -As lastEdited props, serve image slug last edited(ex: this.props.your_image_slug_last_edited).note that lastEdited props should not be dynamic
 * @param {string} String -As hoverEnabled props, serve image slug hover enabled(ex: this.props.your_image_slug__hover_enabled). note that hoverEnabled props should not be dynamic
 * @param {string} String -As imageTablet props, serve dynamic image slug tablet(ex: this.props.dynamic.your_image_slug_tablet)
 * @param {string} String -As imagePhone props, serve dynamic image slug phone(ex: this.props.dynamic.your_image_slug_phone)
 * @param {string} String -As imageHover props, serve dynamic image slug hover(ex: this.props.dynamic.your_image_slug__hover)
 * @param {string} String -As imgAlt props, serve dynamic image alt text(ex: this.props.dynamic.your_image_slug_alt_text)
 * @param {string} String -As classes props, you have to serve image css className, By default there will be no className
 */

const Image = ({
  dynamicImg,
  lastEdited,
  hoverEnabled,
  imageTablet,
  imagePhone,
  imageHover,
  imgAlt,
  classes = "",
}) => {
  let img = dynamicImg;
  if ("" === img.value) return "";

  if (img.loading) return img.render();

  if ("on|tablet" === lastEdited) {
    return (
      <img
        src={imageTablet.value ? imageTablet.value : img.value}
        alt={imgAlt.value}
        className={classes}
      />
    );
  } else if ("on|phone" === lastEdited) {
    return (
      <img
        src={
          imagePhone.value
            ? imagePhone.value
            : imageTablet.value
            ? imageTablet.value
            : img.value
        }
        alt={imgAlt.value}
        className={classes}
      />
    );
  } else if ("on|hover" === hoverEnabled) {
    return (
      <img
        src={imageHover.value ? imageHover.value : img.value}
        alt={imgAlt.value}
        className={classes}
      />
    );
  } else {
    return <img src={img.value} alt={imgAlt.value} className={classes} />;
  }
};

const imageFunc = (props, slug, alt_slug, classes = "", imageRef = "") => {
  // let img = dynamicImg;
  const img = props["dynamic"][slug];
  const imageTablet = props["dynamic"][`${slug}_tablet`];
  const imagePhone = props["dynamic"][`${slug}_phone`];
  const imageHover = props["dynamic"][`${slug}__hover`];
  const lastEdited = props[`${slug}_last_edited`];
  const hoverEnabled = props[`${slug}__hover_enabled`];
  const imgAlt = props["dynamic"][alt_slug];

  if ("" === img.value) return "";

  if (img.loading) return img.render();

  if ("on|tablet" === lastEdited) {
    return (
      <img
        src={imageTablet.value ? imageTablet.value : img.value}
        alt={imgAlt.value}
        className={classes}
        ref={imageRef}
      />
    );
  } else if ("on|phone" === lastEdited) {
    return (
      <img
        src={
          imagePhone.value
            ? imagePhone.value
            : imageTablet.value
            ? imageTablet.value
            : img.value
        }
        alt={imgAlt.value}
        className={classes}
        ref={imageRef}
      />
    );
  } else if ("on|hover" === hoverEnabled) {
    return (
      <img
        src={imageHover.value ? imageHover.value : img.value}
        alt={imgAlt.value}
        className={classes}
        ref={imageRef}
      />
    );
  } else {
    return (
      <img
        src={img.value}
        alt={imgAlt.value}
        className={classes}
        ref={imageRef}
      />
    );
  }
};

/**
 * Icon Component
 * @param {{
 *  utils: Object
 *  tag: String
 *  icon: String
 *  classes: String
 * }}
 * @param {Object} Object -As utils props, serve DIVI's utility object
 * @param {String} String -As tag props, serve Icon's html tag, By default it's' span tag.
 * @param {String} String -As icon props, serve Icon slug, note that icon slug should not be dynamic.
 * @param {string} String -As classes props, you have to serve icon css className, By default there will be no className
 */

const Icon = ({ utils, tag: Tag = "span", icon, classes = "" }) => {
  if (icon === "") return "";
  const oldIcon = icon && icon.split("||");
  if (!oldIcon[0]) return "";
  const processedIcon = utils.processIconFontData
    ? utils.processFontIcon(icon)
    : utils.processFontIcon(oldIcon[0]);

  if (oldIcon.length > 1 && utils.processIconFontData) {
    let fontIconProperties = utils.processIconFontData(icon);
    return (
      <Tag
        className={classes}
        style={{
          fontFamily:
            fontIconProperties && fontIconProperties["iconFontFamily"],
          fontWeight:
            fontIconProperties && fontIconProperties["iconFontWeight"],
        }}
      >
        {processedIcon}
      </Tag>
    );
  } else {
    return <Tag className={classes}>{processedIcon}</Tag>;
  }
};

const applyCss = (cssObject, css, classes, lastEdited, hoverEnabled = "") => {
  css.push([
    {
      selector: classes.desktop,
      declaration: cssObject.desktop,
    },
  ]);

  css.push([
    {
      selector: classes.desktop,
      declaration: cssObject.tablet,
      device: "tablet",
    },
  ]);
  css.push([
    {
      selector: classes.desktop,
      declaration: cssObject.phone,
      device: "phone",
    },
  ]);

  if (classes.hover && cssObject.hover && "on|hover" === hoverEnabled) {
    css.push([
      {
        selector: classes.hover,
        declaration: cssObject.hover,
      },
    ]);
  }
  return;
};

const applyCustomSpacing = (
  props,
  css,
  slug,
  cssProperty,
  marginPadding,
  important = true
) => {
  if ("" !== props[slug]) {
    css.push(
      window.DndhCommon.get_responsive_css(
        props,
        slug,
        cssProperty,
        marginPadding,
        important
      )
    );
  }
  return;
};

const applyBgCss = (css, props, backgroundObject) => {
  const {
    slug,
    use_color_gradient_slug,
    gradient,
    css_property,
  } = backgroundObject;
  let bg_style = "",
    bg_style_tablet = "",
    bg_style_phone = "",
    bg_style_hover = "",
    has_bg_gradient = null,
    gradient_bg = "",
    gradient_bg_tablet = "",
    gradient_bg_phone = "",
    gradient_bg_hover = "";

  const use_color_gradient = use_color_gradient_slug || "off";
  const gradient_type = props[gradient.type] || "linear";
  const gradient_direction = props[gradient.direction] || "180deg";
  const gradient_radial = props[gradient.radial] || "center";

  const gradient_stops = props[`${slug}_gradient_stops`]
    ? props[`${slug}_gradient_stops`].split("|").join(",")
    : "";
  const gradient_stops_tablet = props[`${slug}_gradient_stops_tablet`]
    ? props[`${slug}_gradient_stops_tablet`].split("|").join(",")
    : "";
  const gradient_stops_phone = props[`${slug}_gradient_stops_phone`]
    ? props[`${slug}_gradient_stops_phone`].split("|").join(",")
    : "";
  const gradient_stops_hover = props[`${slug}_gradient_stops__hover`]
    ? props[`${slug}_gradient_stops__hover`].split("|").join(",")
    : "";
  const is_hover_enabled = props[`${slug}__hover_enabled`]
    ? props[`${slug}__hover_enabled`].startsWith("on")
    : false;


  if (use_color_gradient === "on") {
    const direction =
      gradient_type === "linear"
        ? gradient_direction
        : `circle at ${gradient_radial}`;

    gradient_bg = `${gradient_type}-gradient(${direction}, ${gradient_stops})`;
    gradient_bg_tablet = `${gradient_type}-gradient(${direction}, ${gradient_stops_tablet})`;
    gradient_bg_phone = `${gradient_type}-gradient(${direction}, ${gradient_stops_phone})`;
    gradient_bg_hover = is_hover_enabled
      ? `${gradient_type}-gradient(${direction}, ${gradient_stops_hover})`
      : "";

    has_bg_gradient = true;
  } else {
    has_bg_gradient = false;
  }

  if (gradient_bg !== "") {
    bg_style = `background-image: ${gradient_bg} !important;`;
    bg_style_tablet = `background-image: ${gradient_bg_tablet} !important;`;
    bg_style_phone = `background-image: ${gradient_bg_phone} !important;`;
    bg_style_hover = `background-image: ${gradient_bg_hover} !important;`;
  }

  if (!has_bg_gradient) {
    const bg_color = props[slug];
    const bg_color_tablet = props[`${slug}_tablet`];
    const bg_color_phone = props[`${slug}_phone`];
    const bg_color_hover = props[`${slug}__hover`];

    if (bg_color !== "") {
      bg_style = `background-color: ${bg_color} !important;`;
      bg_style_tablet = `background-color: ${bg_color_tablet} !important;`;
      bg_style_phone = `background-color: ${bg_color_phone} !important;`;
      bg_style_hover = `background-color: ${bg_color_hover} !important;`;
    }
  }
  
  css.push([
    {
      selector: css_property.desktop,
      declaration: bg_style,
    },
  ]);

  css.push([
    {
      selector: css_property.desktop,
      declaration: bg_style_tablet,
      device: "tablet",
    },
  ]);

  css.push([
    {
      selector: css_property.desktop,
      declaration: bg_style_phone,
      device: "phone",
    },
  ]);

  css.push([
    {
      selector: css_property.hover,
      declaration: bg_style_hover,
    },
  ]);

  return;
};

const getAlignment = (slug, props, prefix = "") => {
  let alignment, alignment_tablet, alignment_phone;
  if (prefix === "") {
    alignment = props[slug] ? `${slug}_` + props[slug] : "";
    alignment_tablet = props[`${slug}_tablet`]
      ? `${slug}_tablet_` + props[`${slug}_tablet`]
      : "";
    alignment_phone = props[`${slug}_phone`]
      ? `${slug}_phone_` + props[`${slug}_phone`]
      : "";
  } else {
    alignment = props[slug] ? `${prefix}_${slug}_` + props[slug] : "";
    alignment_tablet = props[`${slug}_tablet`]
      ? `${prefix}_${slug}_tablet_` + props[`${slug}_tablet`]
      : "";
    alignment_phone = props[`${slug}_phone`]
      ? `${prefix}_${slug}_phone_` + props[`${slug}_phone`]
      : "";
  }

  return `${alignment} ${alignment_tablet} ${alignment_phone}`;
};

const renderStars = (star_rating = 5, star_scale) => {
  let ratingScale = parseFloat(star_scale),
    rating = star_rating > ratingScale ? ratingScale : star_rating,
    starsHtml = "",
    flooredRating = Math.floor(rating);

  for (let stars = 1; stars <= ratingScale; stars++) {
    if (stars <= flooredRating) {
      starsHtml += '<i class="divinext-star-full et-pb-icon"></i>';
    } else if (flooredRating + 1 === stars && rating !== flooredRating) {
      starsHtml +=
        '<i class="divinext-star-' +
        (rating - flooredRating).toFixed(1) * 10 +
        ' et-pb-icon"></i>';
    } else {
      starsHtml += '<i class="divinext-star-empty et-pb-icon"></i>';
    }
  }

  return { __html: starsHtml };
};

const getShapes = (props, css, slugsAndSelector) => {
  const shapeSize = props[slugsAndSelector.maskSize] || "contain";
  const imageHorizontal = props[slugsAndSelector.imageHorizontal] || 0;
  const imageVertical = props[slugsAndSelector.imageVertical] || 0;

  const maskPositionCss = () => {
    // position start
    css.push([
      {
        selector: `${slugsAndSelector.selector} img`,
        declaration: `transform: matrix(1, 0, 0, 1, ${imageHorizontal}, ${imageVertical});`,
      },
    ]);

    if (
      "on|tablet" ===
        props[`${slugsAndSelector.imageHorizontal}_last_edited`] ||
      "on|tablet" === props[`${slugsAndSelector.imageVertical}_last_edited`]
    ) {
      css.push([
        {
          selector: `${slugsAndSelector.selector} img`,
          declaration: `transform: matrix(1, 0, 0, 1, ${props[
            `${slugsAndSelector.imageHorizontal}_tablet`
          ] || imageHorizontal}, ${props[
            `${slugsAndSelector.imageVertical}_tablet`
          ] || imageVertical});`,
          device: "tablet",
        },
      ]);
    } else if (
      "on|phone" === props[`${slugsAndSelector.imageHorizontal}_last_edited`] ||
      "on|phone" === props[`${slugsAndSelector.imageVertical}_last_edited`]
    ) {
      css.push([
        {
          selector: `${slugsAndSelector.selector} img`,
          declaration: `transform: matrix(1, 0, 0, 1, ${props[
            `${slugsAndSelector.imageHorizontal}_phone`
          ] ||
            props[`${slugsAndSelector.imageHorizontal}_phone`] ||
            imageHorizontal}, ${props[
            `${slugsAndSelector.imageVertical}_phone`
          ] ||
            props[`${slugsAndSelector.imageVertical}_phone`] ||
            imageVertical});`,
          device: "phone",
        },
      ]);
    } else if (
      "on|phone" ===
        props[`${slugsAndSelector.imageHorizontal}__hover_enabled`] ||
      "on|phone" === props[`${slugsAndSelector.imageVertical}__hover_enabled`]
    ) {
      css.push([
        {
          selector: `${slugsAndSelector.selector} img:hover`,
          declaration: `transform: matrix(1, 0, 0, 1, ${
            props[`${slugsAndSelector.imageHorizontal}__hover`]
          }, ${props[`${slugsAndSelector.imageVertical}__hover`]});`,
        },
      ]);
    }
    // position end
    return;
  };

  if (
    "off" !== props[slugsAndSelector.useMask] &&
    "none" !== props[slugsAndSelector.maskShape]
  ) {
    const shape = shapes(props[slugsAndSelector.maskShape]);
    const shapeString = `data:image/svg+xml;base64,${window.btoa(shape)}`;

    css.push([
      {
        selector: slugsAndSelector.selector,
        declaration: `mask-image: url(${shapeString});
        -webkit-mask-image: url(${shapeString});mask-size: ${shapeSize};-webkit-mask-size: ${shapeSize};`,
      },
    ]);

    maskPositionCss();
  }
  if (
    "on" !== props[slugsAndSelector.useMask] &&
    "off" !== props[slugsAndSelector.useMaskUpload] &&
    props[slugsAndSelector.maskUpload]
  ) {
    css.push([
      {
        selector: slugsAndSelector.selector,
        declaration: `mask-image: url(${props[slugsAndSelector.maskUpload]});
        -webkit-mask-image: url(${
          props[slugsAndSelector.maskUpload]
        });mask-size: ${shapeSize};-webkit-mask-size: ${shapeSize};`,
      },
    ]);

    css.push([
      {
        selector: `${slugsAndSelector.selector} img`,
        declaration: `transform: matrix(1, 0, 0, 1, ${imageHorizontal}, ${imageVertical});`,
      },
    ]);

    maskPositionCss();
  }
};

const getBlurbAlignment = (slug, props, classes, prefix = "") => {
  const arr = {
    "dnxt-blurb-wrapper-one-top-left": "top-left",
    "dnxt-blurb-wrapper-one-top-center": "top-center",
    "dnxt-blurb-wrapper-one-top-right": "top-right",
    "dnxt-blurb-wrapper-one-left-top": "left-top",
    "dnxt-blurb-wrapper-one-left-center": "left-center",
    "dnxt-blurb-wrapper-one-left-bottom": "left-bottom",
    "dnxt-blurb-wrapper-one-bottom-left": "bottom-left",
    "dnxt-blurb-wrapper-one-bottom-center": "bottom-center",
    "dnxt-blurb-wrapper-one-bottom-right": "bottom-right",
    "dnxt-blurb-wrapper-one-right-top": "right-top",
    "dnxt-blurb-wrapper-one-right-center": "right-center",
    "dnxt-blurb-wrapper-one-right-bottom": "right-bottom",
  };
  let alignment, alignment_tablet, alignment_phone;
  if (prefix === "") {
    alignment = props[slug] ? classes + arr[props[slug]] : "";
    alignment_tablet = props[`${slug}_tablet`]
      ? classes + arr[props[`${slug}_tablet`]] + "_tablet"
      : "";
    alignment_phone = props[`${slug}_phone`]
      ? `${classes}` + arr[props[`${slug}_phone`]] + "_phone"
      : "";
  } else {
    alignment = props[slug] ? `${prefix}_${classes}` + arr[props[slug]] : "";
    alignment_tablet = props[`${slug}_tablet`]
      ? `${prefix}_${classes}` + props[`${slug}_tablet`] + "_tablet"
      : "";
    alignment_phone = props[`${slug}_phone`]
      ? `${prefix}_${classes}` + props[`${slug}_phone`] + "_phone"
      : "";
  }

  return `${alignment} ${alignment_tablet} ${alignment_phone}`;
};

const SliderArrow = ({ arrowClass, slidePrev, slideNext }) => (
  <div className={`swiper-button-container ${arrowClass()}`}>
    <div className="swiper-button-prev" data-icon="4" onClick={slidePrev} />
    <div className="swiper-button-next" data-icon="5" onClick={slideNext} />
  </div>
);

const isBulletsClickable = (clickable) =>
  "on" === clickable ? "swiper-pagination-clickable" : "";

const paginationClass = (props, type, bullets, clickable = true) => {
  const clickableClass = isBulletsClickable(props[clickable]);
  if (props[type] === "none") return "swiper-pagination swiper-pagination-none";
  else if (props[type] === "bullets" && props[bullets] === "on")
    return `swiper-pagination ${clickableClass} swiper-pagination-bullets swiper-pagination-bullets-dynamic mt-10`;
  else if (props[type] === "bullets")
    return `swiper-pagination ${clickableClass} swiper-pagination-bullets mt-10`;
  else if (props[type] === "fraction")
    return "swiper-pagination swiper-pagination-fraction";
  else if (props[type] === "progressbar")
    return "swiper-pagination swiper-pagination-progressbar";
};
const applyAllBgCss = (css, props, elements) => {
  for (let key in elements) {
    applyBgCss(css, props, {
      slug: `${key}bg_color`,
      use_color_gradient_slug: props[`${key}bg_use_color_gradient`],
      gradient: {
        type: `${key}bg_color_gradient_type`,
        direction: `${key}bg_color_gradient_direction`,
        radial: `${key}bg_color_gradient_direction_radial`,
        start: `${key}bg_color_gradient_start`,
        end: `${key}bg_color_gradient_end`,
        start_position: `${key}bg_color_gradient_start_position`,
        end_position: `${key}bg_color_gradient_end_position`,
        overlays_image: `${key}bg_color_gradient_overlays_image`,
      },
      css_property: elements[key],
    });
  }
};

const applyPsuedoIconCss = (iconSlug, selector, props, css) => {
  // icon settings
  const fontName = { fa: "FontAwesome", divi: "ETmodules" };
  let oldIcon = props[iconSlug] ? props[iconSlug].split("||") : "";
  let oldIconTablet = props[`${iconSlug}_tablet`]
    ? props[`${iconSlug}_tablet`].split("||")
    : "";
  let oldIconPhone = props[`${iconSlug}_phone`]
    ? props[`${iconSlug}_phone`].split("||")
    : "";

  let fontStyle =
    oldIcon[1] &&
    fontName.hasOwnProperty(oldIcon[1]) &&
    `font-family: ${
      fontName[oldIcon[1] || "ETmodules"]
    } !important;font-weight: ${
      oldIcon[2]
    } !important;content: attr(data-icon);`;
  let fontStyleTablet =
    oldIconTablet[1] &&
    fontName.hasOwnProperty(oldIconTablet[1]) &&
    `font-family: ${
      fontName[oldIconTablet[1] || "ETmodules"]
    } !important;font-weight: ${
      oldIconTablet[2]
    } !important;content: attr(data-icon-tablet) !important;`;
  let fontStylePhone =
    oldIconPhone[1] &&
    fontName.hasOwnProperty(oldIconPhone[1]) &&
    `font-family: ${
      fontName[oldIconPhone[1] || "ETmodules"]
    } !important;font-weight: ${
      oldIconPhone[2]
    } !important;content: attr(data-icon-phone) !important;`;

  css.push([
    {
      selector,
      declaration: fontStyle,
    },
  ]);
  css.push([
    {
      selector,
      declaration: fontStyleTablet,
      device: "tablet",
    },
  ]);
  css.push([
    {
      selector,
      declaration: fontStylePhone,
      device: "phone",
    },
  ]);
};

const IconUsingPsuedo = ({ utils, iconSlug, props, classes = "" }) => {
  let oldFrontIcon = props[iconSlug] ? props[iconSlug].split("||") : "";
  let oldFrontIconTablet = props[`${iconSlug}_tablet`]
    ? props[`${iconSlug}_tablet`].split("||")
    : "";
  let oldFrontIconPhone = props[`${iconSlug}_phone`]
    ? props[`${iconSlug}_phone`].split("||")
    : "";

  let frontIcon = oldFrontIcon[0] ? utils.processFontIcon(oldFrontIcon[0]) : "";
  let frontIconTablet = oldFrontIconTablet[0]
    ? utils.processFontIcon(oldFrontIconTablet[0])
    : frontIcon;
  let frontIconPhone = oldFrontIconPhone[0]
    ? utils.processFontIcon(oldFrontIconPhone[0])
    : frontIconTablet;

  return (
    <span
      className={classes}
      data-icon={frontIcon}
      data-icon-tablet={frontIconTablet}
      data-icon-phone={frontIconPhone}
    />
  );
};

export {
  Title,
  Description,
  Button,
  Image,
  imageFunc,
  Icon,
  applyCss,
  applyCustomSpacing,
  applyBgCss,
  applyAllBgCss,
  getAlignment,
  getBlurbAlignment,
  renderStars,
  getShapes,
  SliderArrow,
  paginationClass,
  applyPsuedoIconCss,
  IconUsingPsuedo,
};
