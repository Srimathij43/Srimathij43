(()=>{"use strict";var t={857:t=>{var e=function(t){var e;return!!t&&"object"==typeof t&&"[object RegExp]"!==(e=Object.prototype.toString.call(t))&&"[object Date]"!==e&&t.$$typeof!==r},r="function"==typeof Symbol&&Symbol.for?Symbol.for("react.element"):60103;function i(t,e){return!1!==e.clone&&e.isMergeableObject(t)?a(Array.isArray(t)?[]:{},t,e):t}function s(t,e,r){return t.concat(e).map(function(t){return i(t,r)})}function n(t){return Object.keys(t).concat(Object.getOwnPropertySymbols?Object.getOwnPropertySymbols(t).filter(function(e){return Object.propertyIsEnumerable.call(t,e)}):[])}function o(t,e){try{return e in t}catch(t){return!1}}function a(t,r,l){(l=l||{}).arrayMerge=l.arrayMerge||s,l.isMergeableObject=l.isMergeableObject||e,l.cloneUnlessOtherwiseSpecified=i;var c,u,d=Array.isArray(r);return d!==Array.isArray(t)?i(r,l):d?l.arrayMerge(t,r,l):(u={},(c=l).isMergeableObject(t)&&n(t).forEach(function(e){u[e]=i(t[e],c)}),n(r).forEach(function(e){(!o(t,e)||Object.hasOwnProperty.call(t,e)&&Object.propertyIsEnumerable.call(t,e))&&(o(t,e)&&c.isMergeableObject(r[e])?u[e]=(function(t,e){if(!e.customMerge)return a;var r=e.customMerge(t);return"function"==typeof r?r:a})(e,c)(t[e],r[e],c):u[e]=i(r[e],c))}),u)}a.all=function(t,e){if(!Array.isArray(t))throw Error("first argument should be an array");return t.reduce(function(t,r){return a(t,r,e)},{})},t.exports=a}},e={};function r(i){var s=e[i];if(void 0!==s)return s.exports;var n=e[i]={exports:{}};return t[i](n,n.exports,r),n.exports}(()=>{r.n=t=>{var e=t&&t.__esModule?()=>t.default:()=>t;return r.d(e,{a:e}),e}})(),(()=>{r.d=(t,e)=>{for(var i in e)r.o(e,i)&&!r.o(t,i)&&Object.defineProperty(t,i,{enumerable:!0,get:e[i]})}})(),(()=>{r.o=(t,e)=>Object.prototype.hasOwnProperty.call(t,e)})(),(()=>{var t=r(857),e=r.n(t);class i{static ucFirst(t){return t.charAt(0).toUpperCase()+t.slice(1)}static lcFirst(t){return t.charAt(0).toLowerCase()+t.slice(1)}static toDashCase(t){return t.replace(/([A-Z])/g,"-$1").replace(/^-/,"").toLowerCase()}static toLowerCamelCase(t,e){let r=i.toUpperCamelCase(t,e);return i.lcFirst(r)}static toUpperCamelCase(t,e){return e?t.split(e).map(t=>i.ucFirst(t.toLowerCase())).join(""):i.ucFirst(t.toLowerCase())}static parsePrimitive(t){try{return/^\d+(.|,)\d+$/.test(t)&&(t=t.replace(",",".")),JSON.parse(t)}catch(e){return t.toString()}}}class s{static isNode(t){return"object"==typeof t&&null!==t&&(t===document||t===window||t instanceof Node)}static hasAttribute(t,e){if(!s.isNode(t))throw Error("The element must be a valid HTML Node!");return"function"==typeof t.hasAttribute&&t.hasAttribute(e)}static getAttribute(t,e){let r=!(arguments.length>2)||void 0===arguments[2]||arguments[2];if(r&&!1===s.hasAttribute(t,e))throw Error('The required property "'.concat(e,'" does not exist!'));if("function"!=typeof t.getAttribute){if(r)throw Error("This node doesn't support the getAttribute function!");return}return t.getAttribute(e)}static getDataAttribute(t,e){let r=!(arguments.length>2)||void 0===arguments[2]||arguments[2],n=e.replace(/^data(|-)/,""),o=i.toLowerCamelCase(n,"-");if(!s.isNode(t)){if(r)throw Error("The passed node is not a valid HTML Node!");return}if(void 0===t.dataset){if(r)throw Error("This node doesn't support the dataset attribute!");return}let a=t.dataset[o];if(void 0===a){if(r)throw Error('The required data attribute "'.concat(e,'" does not exist on ').concat(t,"!"));return a}return i.parsePrimitive(a)}static querySelector(t,e){let r=!(arguments.length>2)||void 0===arguments[2]||arguments[2];if(r&&!s.isNode(t))throw Error("The parent node is not a valid HTML Node!");let i=t.querySelector(e)||!1;if(r&&!1===i)throw Error('The required element "'.concat(e,'" does not exist in parent node!'));return i}static querySelectorAll(t,e){let r=!(arguments.length>2)||void 0===arguments[2]||arguments[2];if(r&&!s.isNode(t))throw Error("The parent node is not a valid HTML Node!");let i=t.querySelectorAll(e);if(0===i.length&&(i=!1),r&&!1===i)throw Error('At least one item of "'.concat(e,'" must exist in parent node!'));return i}}class n{publish(t){let e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{},r=arguments.length>2&&void 0!==arguments[2]&&arguments[2],i=new CustomEvent(t,{detail:e,cancelable:r});return this.el.dispatchEvent(i),i}subscribe(t,e){let r=arguments.length>2&&void 0!==arguments[2]?arguments[2]:{},i=this,s=t.split("."),n=r.scope?e.bind(r.scope):e;if(r.once&&!0===r.once){let e=n;n=function(r){i.unsubscribe(t),e(r)}}return this.el.addEventListener(s[0],n),this.listeners.push({splitEventName:s,opts:r,cb:n}),!0}unsubscribe(t){let e=t.split(".");return this.listeners=this.listeners.reduce((t,r)=>([...r.splitEventName].sort().toString()===e.sort().toString()?this.el.removeEventListener(r.splitEventName[0],r.cb):t.push(r),t),[]),!0}reset(){return this.listeners.forEach(t=>{this.el.removeEventListener(t.splitEventName[0],t.cb)}),this.listeners=[],!0}get el(){return this._el}set el(t){this._el=t}get listeners(){return this._listeners}set listeners(t){this._listeners=t}constructor(t=document){this._el=t,t.$emitter=this,this._listeners=[]}}class o{init(){throw Error('The "init" method for the plugin "'.concat(this._pluginName,'" is not defined.'))}update(){}_init(){this._initialized||(this.init(),this._initialized=!0)}_update(){this._initialized&&this.update()}_mergeOptions(t){let r=i.toDashCase(this._pluginName),n=s.getDataAttribute(this.el,"data-".concat(r,"-config"),!1),o=s.getAttribute(this.el,"data-".concat(r,"-options"),!1),a=[this.constructor.options,this.options,t];n&&a.push(window.PluginConfigManager.get(this._pluginName,n));try{o&&a.push(JSON.parse(o))}catch(t){throw console.error(this.el),Error('The data attribute "data-'.concat(r,'-options" could not be parsed to json: ').concat(t.message))}return e().all(a.filter(t=>t instanceof Object&&!(t instanceof Array)).map(t=>t||{}))}_registerInstance(){window.PluginManager.getPluginInstancesFromElement(this.el).set(this._pluginName,this),window.PluginManager.getPlugin(this._pluginName,!1).get("instances").push(this)}_getPluginName(t){return t||(t=this.constructor.name),t}constructor(t,e={},r=!1){if(!s.isNode(t))throw Error("There is no valid element given.");this.el=t,this.$emitter=new n(this.el),this._pluginName=this._getPluginName(r),this.options=this._mergeOptions(e),this._initialized=!1,this._registerInstance(),this._init()}}class a{get(t,e){let r=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"application/json",i=this._createPreparedRequest("GET",t,r);return this._sendRequest(i,null,e)}post(t,e,r){let i=arguments.length>3&&void 0!==arguments[3]?arguments[3]:"application/json";i=this._getContentType(e,i);let s=this._createPreparedRequest("POST",t,i);return this._sendRequest(s,e,r)}delete(t,e,r){let i=arguments.length>3&&void 0!==arguments[3]?arguments[3]:"application/json";i=this._getContentType(e,i);let s=this._createPreparedRequest("DELETE",t,i);return this._sendRequest(s,e,r)}patch(t,e,r){let i=arguments.length>3&&void 0!==arguments[3]?arguments[3]:"application/json";i=this._getContentType(e,i);let s=this._createPreparedRequest("PATCH",t,i);return this._sendRequest(s,e,r)}abort(){if(this._request)return this._request.abort()}setErrorHandlingInternal(t){this._errorHandlingInternal=t}_registerOnLoaded(t,e){e&&(!0===this._errorHandlingInternal?(t.addEventListener("load",()=>{e(t.responseText,t)}),t.addEventListener("abort",()=>{console.warn("the request to ".concat(t.responseURL," was aborted"))}),t.addEventListener("error",()=>{console.warn("the request to ".concat(t.responseURL," failed with status ").concat(t.status))}),t.addEventListener("timeout",()=>{console.warn("the request to ".concat(t.responseURL," timed out"))})):t.addEventListener("loadend",()=>{e(t.responseText,t)}))}_sendRequest(t,e,r){return this._registerOnLoaded(t,r),t.send(e),t}_getContentType(t,e){return t instanceof FormData&&(e=!1),e}_createPreparedRequest(t,e,r){return this._request=new XMLHttpRequest,this._request.open(t,e),this._request.setRequestHeader("X-Requested-With","XMLHttpRequest"),r&&this._request.setRequestHeader("Content-type",r),this._request}constructor(){this._request=null,this._errorHandlingInternal=!1}}class l extends o{init(){this._client=new a,this._registerEvents()}_registerEvents(){document.getElementById("wishlistButton").addEventListener("click",this._onWishlistButtonClick.bind(this)),document.getElementById("createWishlistButton").addEventListener("click",this._onCreateWishlistClick.bind(this)),document.getElementById("addToWishlistButton").addEventListener("click",this._onAddToWishlistClick.bind(this)),document.getElementById("sortType").addEventListener("change",function(){let t=document.getElementById("searchQuery");"manual"===this.value?t.style.display="block":t.style.display="none"})}_onWishlistButtonClick(){document.getElementById("wishlistModal").classList.add("show"),document.getElementById("wishlistModal").style.display="block"}_onCreateWishlistClick(){let t=document.getElementById("wishlistName").value,e=this._getProductId();if(!t){alert("Please enter a wishlist name");return}this._client.post(this.options.createURL,JSON.stringify({wishlistName:t,productId:e}),t=>{"success"===JSON.parse(t).status?(alert("Wishlist created and product added!"),location.reload()):alert("Failed to create wishlist")})}_onAddToWishlistClick(){let t=document.getElementById("existingWishlists").value,e=this._getExistingProductId();if(!t){alert("Please select a wishlist");return}this._client.post(this.options.addURL,JSON.stringify({wishlistId:t,productId:e}),t=>{"success"===JSON.parse(t).status?(alert("Product added to wishlist!"),$("#wishlistModal").modal("hide")):alert("Failed to add product to wishlist")})}_getProductId(){return document.querySelector('input[name="wishlistProductId"]').value}_getExistingProductId(){return document.querySelector('input[name="radiologiesWishlistProductId"]').value}}l.options={createURL:"/radiologies/wishlist/create",addURL:"/radiologies/wishlist/add"},window.PluginManager.register("RadiologiesWishlistPlugin",l,"[data-add-to-radiologies-wishlist]")})()})();