import React, { Component } from "react";

class FacebookProvider extends Component {
  componentDidMount() {
    window.fbAsyncInit = function() {
        window.FB &&
        window.FB.init({
            cookie: true, // enable cookies to allow the server to access the session
            xfbml: true, // parse social plugins on this page
            version: "v8.0", // use version 8.0
        });
    };

    (function(d, s, id) {
        var js,
        fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    })(document, "script", "fb-jssdk");

    window.fbAsyncInit();
    if (window.FB) {
        window.FB.XFBML.parse();
    }
}

  render() {
    return (
      <>
        {this.props.children}
        <div id="fb-root" />
        <script
          async
          defer
          crossOrigin="anonymous"
          src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v8.0"
        ></script>
      </>
    );
  }
}

const parseSdk = () => {
  if (window.FB) {
    window.FB.XFBML.parse();
  }
};

export { FacebookProvider, parseSdk };
