/**
 * Minified by jsDelivr using UglifyJS v3.4.3.
 * Original file: /npm/promise-polyfill@8.0.0/lib/index.js
 * 
 * Do NOT use SRI with dynamically generated files! More information: https://www.jsdelivr.com/using-sri-with-dynamic-files
 */
"use strict";var promiseFinally=function(n){var t=this.constructor;return this.then(function(e){return t.resolve(n()).then(function(){return e})},function(e){return t.resolve(n()).then(function(){return t.reject(e)})})},setTimeoutFunc=setTimeout;function noop(){}function bind(e,n){return function(){e.apply(n,arguments)}}function Promise(e){if(!(this instanceof Promise))throw new TypeError("Promises must be constructed via new");if("function"!=typeof e)throw new TypeError("not a function");this._state=0,this._handled=!1,this._value=void 0,this._deferreds=[],doResolve(e,this)}function handle(t,o){for(;3===t._state;)t=t._value;0!==t._state?(t._handled=!0,Promise._immediateFn(function(){var e=1===t._state?o.onFulfilled:o.onRejected;if(null!==e){var n;try{n=e(t._value)}catch(e){return void reject(o.promise,e)}resolve(o.promise,n)}else(1===t._state?resolve:reject)(o.promise,t._value)})):t._deferreds.push(o)}function resolve(n,e){try{if(e===n)throw new TypeError("A promise cannot be resolved with itself.");if(e&&("object"==typeof e||"function"==typeof e)){var t=e.then;if(e instanceof Promise)return n._state=3,n._value=e,void finale(n);if("function"==typeof t)return void doResolve(bind(t,e),n)}n._state=1,n._value=e,finale(n)}catch(e){reject(n,e)}}function reject(e,n){e._state=2,e._value=n,finale(e)}function finale(e){2===e._state&&0===e._deferreds.length&&Promise._immediateFn(function(){e._handled||Promise._unhandledRejectionFn(e._value)});for(var n=0,t=e._deferreds.length;n<t;n++)handle(e,e._deferreds[n]);e._deferreds=null}function Handler(e,n,t){this.onFulfilled="function"==typeof e?e:null,this.onRejected="function"==typeof n?n:null,this.promise=t}function doResolve(e,n){var t=!1;try{e(function(e){t||(t=!0,resolve(n,e))},function(e){t||(t=!0,reject(n,e))})}catch(e){if(t)return;t=!0,reject(n,e)}}Promise.prototype.catch=function(e){return this.then(null,e)},Promise.prototype.then=function(e,n){var t=new this.constructor(noop);return handle(this,new Handler(e,n,t)),t},Promise.prototype.finally=promiseFinally,Promise.all=function(n){return new Promise(function(o,r){if(!n||void 0===n.length)throw new TypeError("Promise.all accepts an array");var i=Array.prototype.slice.call(n);if(0===i.length)return o([]);var s=i.length;function c(n,e){try{if(e&&("object"==typeof e||"function"==typeof e)){var t=e.then;if("function"==typeof t)return void t.call(e,function(e){c(n,e)},r)}i[n]=e,0==--s&&o(i)}catch(e){r(e)}}for(var e=0;e<i.length;e++)c(e,i[e])})},Promise.resolve=function(n){return n&&"object"==typeof n&&n.constructor===Promise?n:new Promise(function(e){e(n)})},Promise.reject=function(t){return new Promise(function(e,n){n(t)})},Promise.race=function(r){return new Promise(function(e,n){for(var t=0,o=r.length;t<o;t++)r[t].then(e,n)})},Promise._immediateFn="function"==typeof setImmediate&&function(e){setImmediate(e)}||function(e){setTimeoutFunc(e,0)},Promise._unhandledRejectionFn=function(e){"undefined"!=typeof console&&console&&console.warn("Possible Unhandled Promise Rejection:",e)},module.exports=Promise;
//# sourceMappingURL=/sm/9a99d5f427f63762a45de7d80aa787d2f58f62a3810c4df3d9d199aac9b19a54.map