import React from "react";
import { Icon } from "../../base/common";

const LayoutOne = (props) => {
  const { user, item, date, alignClass, rest } = props;
  const utils = window.ET_Builder.API.Utils;
  const button_icon = utils.processFontIcon(rest.button_icon || "");
  const customBtnCss = (isBtnOn) =>
    "on" === isBtnOn ? `et_pb_button et_pb_custom_button_icon` : "";
  return (
    <>
      <div className="dnxte-authovcard dnxte-author-avatar">
        <img src={user.avatar_url} alt={user.display_name} />
      </div>
      <div className="dnxte-content-wrapper">
        <h2 className="dnxte-entry-title">
          <a href={item.guid || "/"}>{item.post_title}</a>
        </h2>
        {"on" === rest.show_excerpt && "" !== item.post_excerpt && (
          <div
            className="dnxte-blog-post-content"
            dangerouslySetInnerHTML={{
              __html: item.post_excerpt,
            }}
          />
        )}
        <div className={`dnxte-post-meta ${alignClass}`}>
          {"on" === rest.show_author && (
            <div className="dnxte-authovcard">
              <Icon
                utils={utils}
                icon=""
                classes="dnxte-blogslider-content-icon"
              />{" "}
              <a href={user.author_url}>{user.display_name}</a>
            </div>
          )}

          {"off" !== rest.show_date && (
            <span className="dnxte-blog-published">
              <Icon
                utils={utils}
                icon="}"
                classes="dnxte-blogslider-content-icon"
              />{" "}
              {date.day} {date.month} {date.year}
            </span>
          )}
        </div>
      </div>
      {"on" === rest.show_more && (
        <div className="dnxte-readmorewrapper">
          <a
            className={`${customBtnCss(
              rest.custom_button
            )} dnxte-readmore-link`}
            href={item.guid || "/"}
            data-icon={button_icon}
          >
            {rest.show_more_text}
          </a>
        </div>
      )}
    </>
  );
};

export default LayoutOne;
