//onsubmit="return false;", para evitar reenvio del formulario al presionar enter
var _formulario_sigafs=document.getElementsByTagName("form");
for(var i=0;i<_formulario_sigafs.length;i++)
  _formulario_sigafs[i].setAttribute("onsubmit","return false;");
