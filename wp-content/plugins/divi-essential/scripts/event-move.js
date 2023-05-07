/**
 * Minified by jsDelivr using UglifyJS v3.3.25.
 * Original file: /npm/jquery.event.move@1.3.6/js/jquery.event.move.js
 * 
 * Do NOT use SRI with dynamically generated files! More information: https://www.jsdelivr.com/using-sri-with-dynamic-files
 */
!function(t){"function"==typeof define&&define.amd?define(["jquery"],t):t(jQuery)}(function(a,e){var r=6,i=a.event.add,o=a.event.remove,d=function(t,e,n){a.event.trigger(e,n,t)},u=window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame||window.oRequestAnimationFrame||window.msRequestAnimationFrame||function(t,e){return window.setTimeout(function(){t()},25)},c={textarea:!0,input:!0,select:!0,button:!0},m={move:"mousemove",cancel:"mouseup dragstart",end:"mouseup"},s="touchmove",f="touchend",t="touchend";function n(t){var n=t,a=!1,i=!1;function e(t){a?(n(),u(e),a=!(i=!0)):i=!1}this.kick=function(t){a=!0,i||e()},this.end=function(t){var e=n;t&&(i?(n=a?function(){e(),t()}:t,a=!0):t())}}function v(){return!0}function p(){return!1}function g(t){t.preventDefault()}function h(t){c[t.target.tagName.toLowerCase()]||t.preventDefault()}function l(t,e){var n,a;if(t.identifiedTouch)return t.identifiedTouch(e);for(n=-1,a=t.length;++n<a;)if(t[n].identifier===e)return t[n]}function X(t,e){var n=l(t.changedTouches,e.identifier);if(n&&(n.pageX!==e.pageX||n.pageY!==e.pageY))return n}function Y(t){_(t,t.data,t,y)}function w(t){y()}function y(){o(document,m.move,Y),o(document,m.cancel,w)}function T(t){var e=t.data,n=X(t,e);n&&_(t,e,n,k)}function S(t){var e=t.data;l(t.changedTouches,e.identifier)&&k(e.identifier)}function k(t){o(document,"."+t,T),o(document,"."+t,S)}function _(t,e,n,a){var i=n.pageX-e.startX,o=n.pageY-e.startY;i*i+o*o<r*r||function(t,e,n,a,i,o){var r,u;e.target;r=t.targetTouches,u=t.timeStamp-e.timeStamp,e.type="movestart",e.distX=a,e.distY=i,e.deltaX=a,e.deltaY=i,e.pageX=n.pageX,e.pageY=n.pageY,e.velocityX=a/u,e.velocityY=i/u,e.targetTouches=r,e.finger=r?r.length:1,e._handled=q,e._preventTouchmoveDefault=function(){t.preventDefault()},d(e.target,e),o(e.identifier)}(t,e,n,i,o,a)}function q(){return this._handled=v,!1}function A(t){t._handled()}function D(t){var e=t.data.timer;(t.data.touch=t).data.timeStamp=t.timeStamp,e.kick()}function F(t){var e=t.data.event,n=t.data.timer;o(document,m.move,D),o(document,m.end,F),b(e,n,function(){setTimeout(function(){o(e.target,"click",p)},0)})}function R(t){var e=t.data.event,n=t.data.timer,a=X(t,e);a&&(t.preventDefault(),e.targetTouches=t.targetTouches,t.data.touch=a,t.data.timeStamp=t.timeStamp,n.kick())}function x(t){var e,n=t.data.event,a=t.data.timer;l(t.changedTouches,n.identifier)&&(e=n,o(document,"."+e.identifier,R),o(document,"."+e.identifier,x),b(n,a))}function b(t,e,n){e.end(function(){return t.type="moveend",d(t.target,t),n&&n()})}a.event.special.movestart={setup:function(t,e,n){return i(this,"movestart.move",A),!0},teardown:function(t){return o(this,"dragstart drag",g),o(this,"mousedown touchstart",h),o(this,"movestart",A),!0},add:function(t){"move"!==t.namespace&&"moveend"!==t.namespace&&(i(this,"dragstart."+t.guid+" drag."+t.guid,g,e,t.selector),i(this,"mousedown."+t.guid,h,e,t.selector))},remove:function(t){"move"!==t.namespace&&"moveend"!==t.namespace&&(o(this,"dragstart."+t.guid+" drag."+t.guid),o(this,"mousedown."+t.guid))},_default:function(o){var r,u;o._handled()&&(r={target:o.target,startX:o.startX,startY:o.startY,pageX:o.pageX,pageY:o.pageY,distX:o.distX,distY:o.distY,deltaX:o.deltaX,deltaY:o.deltaY,velocityX:o.velocityX,velocityY:o.velocityY,timeStamp:o.timeStamp,identifier:o.identifier,targetTouches:o.targetTouches,finger:o.finger},u={event:r,timer:new n(function(t){var e,n,a,i;e=r,n=u.touch,a=u.timeStamp,i=a-e.timeStamp,e.type="move",e.distX=n.pageX-e.startX,e.distY=n.pageY-e.startY,e.deltaX=n.pageX-e.pageX,e.deltaY=n.pageY-e.pageY,e.velocityX=.3*e.velocityX+.7*e.deltaX/i,e.velocityY=.3*e.velocityY+.7*e.deltaY/i,e.pageX=n.pageX,e.pageY=n.pageY,d(o.target,r)}),touch:e,timeStamp:e},o.identifier===e?(i(o.target,"click",p),i(document,m.move,D,u),i(document,m.end,F,u)):(o._preventTouchmoveDefault(),i(document,s+"."+o.identifier,R,u),i(document,t+"."+o.identifier,x,u)))}},a.event.special.move={setup:function(){i(this,"movestart.move",a.noop)},teardown:function(){o(this,"movestart.move",a.noop)}},a.event.special.moveend={setup:function(){i(this,"movestart.moveend",a.noop)},teardown:function(){o(this,"movestart.moveend",a.noop)}},i(document,"mousedown.move",function(t){var e,n;1!==(n=t).which||n.ctrlKey||n.altKey||(e={target:t.target,startX:t.pageX,startY:t.pageY,timeStamp:t.timeStamp},i(document,m.move,Y,e),i(document,m.cancel,w,e))}),i(document,"touchstart.move",function(t){var e,n;c[t.target.tagName.toLowerCase()]||(n={target:(e=t.changedTouches[0]).target,startX:e.pageX,startY:e.pageY,timeStamp:t.timeStamp,identifier:e.identifier},i(document,s+"."+e.identifier,T,n),i(document,f+"."+e.identifier,S,n))}),"function"==typeof Array.prototype.indexOf&&function(t,e){for(var n=["changedTouches","targetTouches"],a=n.length;a--;)-1===t.event.props.indexOf(n[a])&&t.event.props.push(n[a])}(a)});
//# sourceMappingURL=/sm/f7f1ac2dc7bb806b0a27e162f37dd0b18f107228974dd11fd328132c5c944a16.map