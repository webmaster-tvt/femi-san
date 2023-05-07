jQuery(document).ready(function ($) {
    "use strict";

    var _doc = document;

    $('body').on('click','.toggle-icon-wrapp,.close-icon', function(){
        $('.cww-menu-outer-wrapp').toggleClass('visible');
        $('body').toggleClass('menu-opened modal-window-active');
    });

    $('body').on('click','.toggle-icon-wrapp', function(){
        $('header.site-header .close-icon').focus();
    });

    $('body').on('click','.close-icon', function(){   
        $('button.button.is-text.toggle-icon-wrapp').focus();
    });


    _doc.addEventListener( 'keydown', function( event ) {
        var toggleTarget, modal, selectors, elements, menuType, bottomMenu, activeEl, lastEl, firstEl, tabKey, shiftKey;
            
        if ( _doc.body.classList.contains( 'modal-window-active' ) ) {
            toggleTarget = '.site-header .menu-wrapp .cww-menu-outer-wrapp';
            selectors = 'input, a, button';
            modal = _doc.querySelector( toggleTarget );
            elements = modal.querySelectorAll( selectors );
            elements = Array.prototype.slice.call( elements );
            if ( '.menu-modal' === toggleTarget ) {
                menuType = window.matchMedia( '(min-width: 1000px)' ).matches;
                menuType = menuType ? '.expanded-menu' : '.mobile-menu';
                elements = elements.filter( function( element ) {
                    return null !== element.closest( menuType ) && null !== element.offsetParent;
                } );
                elements.unshift( _doc.querySelector( '.mob-toggle-menu-button' ) ); 
                bottomMenu = _doc.querySelector( '.menu-last-focus-item' );
                if ( bottomMenu ) {
                    bottomMenu.querySelectorAll( selectors ).forEach( function( element ) {
                        elements.push( element );
                    } );
                }
            }

            lastEl      = elements[ elements.length - 1 ];
            firstEl     = elements[0];
            activeEl    = _doc.activeElement;
            tabKey      = event.keyCode === 9;
            shiftKey    = event.shiftKey;

            if ( ! shiftKey && tabKey && lastEl === activeEl ) {
                event.preventDefault();
                firstEl.focus();
            }

            if ( shiftKey && tabKey && firstEl === activeEl ) {
                event.preventDefault();
                lastEl.focus();
            }
        }
    } );

});