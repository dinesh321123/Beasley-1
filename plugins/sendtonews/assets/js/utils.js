
class AjaxRequestError extends Error{constructor(message,status,response){super(message);this.status=status;this.response=response;}}
class AjaxRequest{constructor(url,method){this.url=url;this.method=method;this.data={};}
send(cancelToken){if(!this.promise){this.promise=new Promise((resolve,reject)=>{const ajaxSettings={url:this.url,type:this.method,success:(response)=>{resolve(response);},error:(xhr,status,error)=>{if(status==="abort"){reject(new CancellationError());}
else{reject(new AjaxRequestError(error,xhr.status,xhr.responseJSON));}},data:this.data};if(!ajaxSettings.data.cid){ajaxSettings.data.cid=sendtonews_utils_i18n.cid;}
if(!ajaxSettings.data.authcode){ajaxSettings.data.authcode=sendtonews_utils_i18n.authcode;}
const request=jQuery.ajax(ajaxSettings);if(cancelToken){cancelToken.onCancelled=cancelToken.onCancelled.then(()=>request.abort().catch(err=>0));}});}
return this.promise;}}
class CancellationError extends Error{}
class CancelToken{constructor(){let resolve=null;this.isCancelled=false;this.onCancelled=new Promise((promiseResolve,reject)=>{resolve=promiseResolve;});this.onCancelled.then(()=>this.isCancelled=true);this.cancel=resolve;}}
function escapeHtml(unsafe){if(!unsafe){return"";}
return unsafe.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;").replace(/'/g,"&#039;");}
function sesssionStorageEnabled(){const name="s2n-sessionStorage";try{sessionStorage.setItem(name,name);sessionStorage.removeItem(name);return true;}catch(e){return false;}}
function cookiesEnabled(){return navigator&&navigator.cookieEnabled;}
function throwIfCookiesNotEnabled(before){if(!sesssionStorageEnabled()||!cookiesEnabled()){if(before){before.call();}
throw new Error();}}