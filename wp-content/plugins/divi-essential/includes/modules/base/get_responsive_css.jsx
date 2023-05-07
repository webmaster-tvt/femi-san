window.DndhCommon = {};

window.DndhCommon.get_responsive_css = function(
  props,
  property,
  css_selector,
  css_property,
  important = true
) {
  var css = [];

  const responsive_active =
    props[property + "_last_edited"] &&
    props[property + "_last_edited"].startsWith("on");

  var declaration_desktop = "";
  var declaration_tablet = "";
  var declaration_phone = "";
  const is_important = important ? "!important" : "";

  switch (css_property) {
    case "margin":
    case "padding":
      if (props[property]) {
        var values = props[property].split("|");
        declaration_desktop = `${css_property}-top: ${values[0]}${is_important};
        ${css_property}-right: ${
          values[1]
        }${is_important};
        ${css_property}-bottom: ${
          values[2]
        }${is_important};
        ${css_property}-left: ${
          values[3]
        }${is_important};`;
      }

      if (responsive_active && props[property + "_tablet"]) {
        var tablet_values = props[property + "_tablet"].split("|");
        declaration_tablet = `${css_property}-top: ${
          tablet_values[0]
        }${is_important};
        ${css_property}-right: ${
          tablet_values[1]
        }${is_important};
        ${css_property}-bottom: ${
          tablet_values[2]
        }${is_important};
        ${css_property}-left: ${
          tablet_values[3]
        }${is_important};`;
      }

      if (responsive_active && props[property + "_phone"]) {
        var phone_values = props[property + "_phone"].split("|");
        declaration_phone = `${css_property}-top: ${
          phone_values[0]
        }${is_important};
        ${css_property}-right: ${
          phone_values[1]
        }${is_important};
        ${css_property}-bottom: ${
          phone_values[2]
        }${is_important};
        ${css_property}-left: ${
          phone_values[3]
        }${is_important};`;
      }
      break;
    default:
      //Default is applied for values like height, color etc.
      declaration_desktop = `${css_property}: ${
        props[property]
      }${is_important};`;
      declaration_tablet = `${css_property}: ${
        props[property + "_tablet"]
      }${is_important};`;
      declaration_phone = `${css_property}: ${
        props[property + "_phone"]
      }${is_important};`;
  }

  css.push({
    selector: css_selector,
    declaration: declaration_desktop
  });

  if (props[property + "_tablet"] && responsive_active) {
    css.push({
      selector: css_selector,
      declaration: declaration_tablet,
      device: "tablet"
    });
  }

  if (props[property + "_phone"] && responsive_active) {
    css.push({
      selector: css_selector,
      declaration: declaration_phone,
      device: "phone"
    });
  }

  return css;
};
