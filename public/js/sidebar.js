/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
/*!*********************************!*\
  !*** ./resources/js/sidebar.js ***!
  \*********************************/
__webpack_require__.r(__webpack_exports__);
Object(function webpackMissingModule() { var e = new Error("Cannot find module 'simplebar'"); e.code = 'MODULE_NOT_FOUND'; throw e; }());
// Usage: https://github.com/Grsmto/simplebar


var initialize = function initialize() {
  initializeSimplebar();
  initializeSidebarCollapse();
};

var initializeSimplebar = function initializeSimplebar() {
  var simplebarElement = document.getElementsByClassName("js-simplebar")[0];

  if (simplebarElement) {
    var simplebarInstance = new Object(function webpackMissingModule() { var e = new Error("Cannot find module 'simplebar'"); e.code = 'MODULE_NOT_FOUND'; throw e; }())(document.getElementsByClassName("js-simplebar")[0]);
    /* Recalculate simplebar on sidebar dropdown toggle */

    var sidebarDropdowns = document.querySelectorAll(".js-sidebar [data-bs-parent]");
    sidebarDropdowns.forEach(function (link) {
      link.addEventListener("shown.bs.collapse", function () {
        simplebarInstance.recalculate();
      });
      link.addEventListener("hidden.bs.collapse", function () {
        simplebarInstance.recalculate();
      });
    });
  }
};

var initializeSidebarCollapse = function initializeSidebarCollapse() {
  var sidebarElement = document.getElementsByClassName("js-sidebar")[0];
  var sidebarToggleElement = document.getElementsByClassName("js-sidebar-toggle")[0];

  if (sidebarElement && sidebarToggleElement) {
    sidebarToggleElement.addEventListener("click", function () {
      sidebarElement.classList.toggle("collapsed");
      sidebarElement.addEventListener("transitionend", function () {
        window.dispatchEvent(new Event("resize"));
      });
    });
  }
}; // Wait until page is loaded


document.addEventListener("DOMContentLoaded", function () {
  return initialize();
});
/******/ })()
;