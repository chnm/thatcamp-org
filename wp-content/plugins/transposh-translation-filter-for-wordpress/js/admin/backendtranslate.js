/*
 * Transposh v0.9.3
 * http://transposh.org/
 *
 * Copyright 2013, Team Transposh
 * Licensed under the GPL Version 2 or higher.
 * http://transposh.org/license
 *
 * Date: Mon, 06 May 2013 02:15:55 +0300
 */
(function(e){function t(b,a){n+=1;e("#progress_bar").progressbar("value",n/o*100);e("#p").text("("+a+") "+b);n===o&&e("#tr_loading").data("done",true)}function j(b,a,c,f){a=e("<div>"+e.trim(a)+"</div>").text();t(a,c);clearTimeout(p);l+=1;k.push(b);g.push(a);h.push(c);i.push(f);p=setTimeout(function(){var a={action:"tp_translation",items:l},d;for(d=0;d<l;d+=1)k[d]!==k[d-1]&&(a["tk"+d]=k[d]),h[d]!==h[d-1]&&(a["ln"+d]=h[d]),g[d]!==g[d-1]&&(a["tr"+d]=g[d]),i[d]!==i[d-1]&&(a["sr"+d]=i[d]);e.ajax({type:"POST",
url:t_jp.ajaxurl,data:a,success:function(){},error:function(){}});l=0;g=[];k=[];h=[];i=[]},200)}function u(b,a,c){var f=c;f==="zh"?f="zh-chs":f==="zh-tw"&&(f="zh-cht");t_jp.dmt(a,function(a){e(a).each(function(a){j(b[a],this.TranslatedText,c,2)})},f)}function v(b,a,c){t_jp.dat(a,function(a){a.responseStatus>=200&&a.responseStatus<300&&(a.responseData.translatedText!==void 0?j(b[0],a.responseData.translatedText):e(a.responseData).each(function(a){this.responseStatus===200&&j(b[a],this.responseData.translatedText,
c,3)}))},c)}function q(b,a,c){t_jp.dgpt(a,function(a){e(a.results).each(function(a){j(b[a],this,c,1)})},c)}function w(b,a,c){t_jp.dgt(a,function(f){typeof f.error!=="undefined"?q(b,a,c):e(f.data.translations).each(function(a){j(b[a],this.translatedText,c,1)})},c)}function r(b,a,c){t_be.m_langs.indexOf(c)!==-1&&t_jp.preferred==="2"?u(b,a,c):t_be.a_langs.indexOf(c)!==-1&&(t_jp.olang==="en"||t_jp.olang==="es")?v(b,a,c):t_jp.google_key?w(b,a,c):q(b,a,c)}function s(b){var a="",c=[],f=[],j,d,m,k=0,g=[],
h=[],i;e("#tr_loading").data("done",false);e.ajax({url:ajaxurl,dataType:"json",data:{action:"tp_post_phrases",post:b},cache:false,success:function(b){e("#tr_translate_title").html("Translating post: "+b.posttitle);if(b.length===void 0)e("#tr_loading").html("Nothing left to translate"),e("#tr_loading").data("done",true);else{n=o=0;for(d in b.p)o+=b.p[d].l.length;e("#tr_loading").html('<br/>Translation: <span id="p"></span><div id="progress_bar"/>');e("#progress_bar").progressbar({value:0});for(var l in b.langs){a=
b.langs[l];f=[];c=[];for(d in b.p)m=b.p[d],m.l.indexOf(a)!==-1&&(f.push(unescape(d)),c.push(m.t),m.l.splice(m.l.indexOf(a),1),m.l.length===0&&(b.length-=1,delete b.p[d]));if(f.length){for(j in f)i=f[j],k+i.length>x&&(r(h,g,a),k=0,g=[],h=[]),k+=i.length,h.push(c[j]),g.push(i);r(h,g,a)}}}}})}var p,l=0,g=[],k=[],h=[],i=[],x=512,o=0,n=0;window.translate_post=s;e(function(){t_be.post&&s(t_be.post)})})(jQuery);
