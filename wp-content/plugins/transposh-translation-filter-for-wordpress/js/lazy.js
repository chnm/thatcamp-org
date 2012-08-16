/*
 * xLazyLoader 1.5 - Plugin for jQuery
 * 
 * Load js, css and images asynchron and get different callbacks
 *
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 *
 * Depends:
 *   jquery.js
 *
 *  Copyright (c) 2010 Oleg Slobodskoi (jsui.de)
 */
(function(a){function m(){function g(a,e){m[a](e,function(a){a=="error"?h.push(e):j.push(e)&&d.each(e);p()},"lazy-loaded-"+(d.name?d.name:(new Date).getTime()),d[a+"Key"]?"?key="+d[a+"Key"]:"")}function i(a){d.complete(a,j,h);d[a](a=="error"?h:j);clearTimeout(q);clearTimeout(s)}function p(){j.length==k.length?i("success"):j.length+h.length==k.length&&i("error")}function r(){h.push(this.src);p()}var m=this,d,j=[],h=[],q,s,n,k=[];this.init=function(b){b&&(d=a.extend({},a.xLazyLoader.defaults,b),n={js:d.js,
css:d.css,img:d.img},a.each(n,function(a,c){typeof c=="string"&&(c=c.split(","));k=k.concat(c)}),k.length?(d.timeout&&(q=setTimeout(function(){var e=j.concat(h);a.each(k,function(c,b){a.inArray(b,e)==-1&&h.push(b)});i("error")},d.timeout)),a.each(n,function(b,c){a.isArray(c)?a.each(c,function(a,c){g(b,c)}):typeof c=="string"&&g(b,c)})):i("error"))};this.js=function(b,e,c,d){var o=a('script[src*="'+b+'"]');if(o.length)o.attr("pending")?o.bind("scriptload",e):e();else{var f=document.createElement("script");
f.setAttribute("type","text/javascript");f.setAttribute("charset","UTF-8");f.setAttribute("src",b+d);f.setAttribute("id",c);f.setAttribute("pending",1);f.onerror=r;a(f).bind("scriptload",function(){a(this).removeAttr("pending");e();setTimeout(function(){a(f).unbind("scriptload")},10)});var g=false;f.onload=f.onreadystatechange=function(){if(!g&&(!this.readyState||/loaded|complete/.test(this.readyState)))g=true,f.onload=f.onreadystatechange=null,a(f).trigger("scriptload")};l.appendChild(f)}};this.css=
function(b,e,c,d){if(a('link[href*="'+b+'"]').length)e();else{var g=a('<link rel="stylesheet" type="text/css" media="all" href="'+b+d+'" id="'+c+'"></link>')[0];a.browser.msie?g.onreadystatechange=function(){/loaded|complete/.test(g.readyState)&&e()}:a.browser.opera?g.onload=e:(location.hostname.replace("www.",""),/http:/.test(b)&&/^(\w+:)?\/\/([^\/?#]+)/.exec(b),e());l.appendChild(g)}};this.img=function(a,d,c,g){c=new Image;c.onload=d;c.onerror=r;c.src=a+g};this.disable=function(b){a("#lazy-loaded-"+
b,l).attr("disabled","disabled")};this.enable=function(b){a("#lazy-loaded-"+b,l).removeAttr("disabled")};this.destroy=function(b){a("#lazy-loaded-"+b,l).remove()}}a.xLazyLoader=function(a,i){typeof a=="object"&&(i=a,a="init");(new m)[a](i)};a.xLazyLoader.defaults={js:[],css:[],img:[],jsKey:null,cssKey:null,imgKey:null,name:null,timeout:2E4,success:a.noop,error:a.noop,complete:a.noop,each:a.noop};var l=document.getElementsByTagName("head")[0]})(t_jp.$);
