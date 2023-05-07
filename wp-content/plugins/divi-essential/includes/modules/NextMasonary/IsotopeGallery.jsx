import React, { useState, useEffect, useRef } from "react";
import Isotope from 'isotope-layout'

const IsotopeGallery = ({ filter, filterLayout, filterAllText, columns, gutter, children }) => {
  const [ mount, setMount ] = useState( false );
  const isotope = useRef();
  const isotopeSelector = useRef();
  useEffect(() => {
      setMount( true );
    if(isotopeSelector.current) {
      isotope.current = new Isotope(isotopeSelector.current, {
        layoutMode: 'masonry',
        itemSelector: ".dnxte-msnary-item",
        percentPosition: true,
        stagger: 0,
        // transitionDuration: 90,
        // percentPosition: true,
        horizontalOrder: true,
        masonry: {
          cols: columns,
          // columnWidth: '.grid-sizer',
          gutter: parseInt(gutter, 10),
        },
      });
      if(isotope.current) isotope.current.layout();
    }
     if(isotopeSelector.current) {
        window.addEventListener('resize', function() {
          if(isotope.current) isotope.current.layout();
        });
      }
    // cleanup
    if(isotope.current) return () => isotope.current.destroy();
  }, [columns, mount]);

  // useEffect(() => {
  //  if(isotopeSelector.current) {
  //   window.addEventListener('resize', function() {
  //     if(isotope.current) isotope.current.layout();
  //   });
  //  }
  //   if(isotope.current) return () => {
  //     isotope.current.destroy();
  //   }
  // },[]);

  if( !mount ) return '';
  return (
    <div className="dnxte-msnary-wrapper">
        {Array.isArray(filter) && (
        <div className="dnxte-msnary-item-wrapper">
            <ul
              className={`dnxte-msnary-filter-items dnxte-msnary-layout-${filterLayout}`}
            >
            <li className="dnxte-msnary-filter-item active">
              {filterAllText}
            </li>
            {filter.length > 0 && filter.map(item => (
              <li key={item.term_id} className="dnxte-msnary-filter-item">{item.name}</li>
            ))}
          </ul>
        </div>)
        }
        <div className="dnxte-grid">
          <div className="grid-sizer" />
          <div ref={isotopeSelector} className="grid dnxte-msnary-grid">
            {children}
          </div>
        </div>
    </div>
  );
};

export default IsotopeGallery;
