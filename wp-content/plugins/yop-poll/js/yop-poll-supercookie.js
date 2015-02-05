(function() {

})();
function setsuperCookie(){
    var superCookieSetup = {extended:true, swfURL:"/super-cookie/swf/supercookie.swf", expressInstaller:"/super-cookie/swfobject/expressInstall.swf"};
    src="/super-cookie/superCookie-min.js";
    src="/js/swfobject/swfobject.js";
    var obj = {};
   // for(var i=0;i<elements.length;i++) {
     //   obj[elements[i]] = document.getElementById(elements[i]).value;
    //}
    superCookie.setItem("test", "val=valoare_cookie;expiry=2222");
    superCookie.setItem("test", "val=valoardhdjhjdkhe_cookie;expiry=2222");
    console.log(1);
    console.log(superCookie.getItem("test"));
   // superCookie.setItem("multipleItems", obj);
}