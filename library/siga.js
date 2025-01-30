
siga.task={};
siga.window={};
siga.timer={};
siga.calendar={};
siga.menuMain={};
siga.menuContext={};
siga.desktopIcon={};

siga.menu={};

siga.window.list=new Array();
siga.window.visible=new Array();

siga.setHtml=function(o){
  document.getElementById(o.id).innerHTML=o.html;
};

siga.isDefine=function(module){
  if(!siga._value[module])
    return false;
  if(!siga._value[module]["define"])
    return false;
  return true;
};

siga.define=function(module,config){
  siga._value[module]=new Array();
  siga._value[module]["define"]=true;
  siga.setAccess(module);
  Ext.define(module,config);
};


siga.require=function(module){
  if(siga.isDefine(module))
    return;
  Ext.util.CSS.swapStyleSheet(module+"-css","module/"+module+"/?action=css");
  var response = Ext.Ajax.request({async: false,url: "module/"+module+"/?action=javascript"});
  eval(response.responseText);
  siga._value[module]=new Array();
  siga._value[module]["define"]=true;
};

siga.require.js=function(file){
  if(siga.isDefine(file))
    return;
  var response = Ext.Ajax.request({async: false,url: file});
  window.eval(response.responseText);
  siga._value[file]=new Array();
  siga._value[file]["define"]=true;
};

siga.require.css=function(file){
  if(siga.isDefine(file))
    return;
  Ext.util.CSS.swapStyleSheet(file+"-css",file);
  siga._value[file]=new Array();
  siga._value[file]["define"]=true;
};


siga.loader=function(module,onLoad){
  if(siga.isDefine(module))
    return;
  siga.css({
    url: "module/"+module+"/?action=css",
    onLoad: function(){
      siga.js({
        url: "module/"+module+"/?action=javascript",
        onLoad: function(){if(onLoad)onLoad();}
      })
    }
  });
};

siga.open=function(module,parameter){
  //si module culmina con # significa que es una url, entonces abrirla en una nueva ventana
  if(module.charAt(module.length-1)=="#"){window.open(module); return;}
  //si el menu esta abierto ocultarlo
  if(siga.menu.isVisible())
    siga.menu.hide();

  if(!siga.isDefine(module)){
    Ext.get("siga-loading-module").set({style:{display: 'block'}, title:'Cargando módulo: '+module+', por favor espere...'});
    siga.loader(module,function(){
      Ext.get("siga-loading-module").set({style:{display: 'none'}, title:''});
      siga.window.create(module,parameter);
      });
  }
  else{
    if(siga.window.isOpen(module))
      siga.window.focus(module);
    else
      siga.window.create(module,parameter);
  }
};

siga.close=function(module){
  if(!siga.isDefine(module))
    return;
  Ext.getCmp(siga.window.getId(module)).close();
};

siga.setAccess=function(module){
  if(!siga._value[module])
    siga._value[module]=new Array();
  siga._value[module]["access"]=(siga.get({action:'access',module:module})).access;
},

siga.getAccess=function(module){
  if(!siga.isDefine(module))
    siga.setAccess(module);
  return siga._value[module]["access"];
},

siga.access=function(module){
  if(!siga.isDefine(module)){
    if(!siga._value[module])
      siga._value[module]=new Array();
    siga._value[module]["access"]=(siga.get({action:'access',module:module})).access;
  }
  return siga._value[module]["access"];
},

siga.window.create=function(module,parameter){
  if(!siga.isDefine(module)){
    Ext.MessageBox.show({
      title: "Error",
      msg: "<b>El usuario actual no tiene acceso al módulo.<br/><div style='margin-left:20px;color:#777777;'>"+module+"</div></b>",
      buttons: Ext.Msg.OK,
      icon: Ext.MessageBox.ERROR
    });
    return;
  }

  var win=Ext.create(module,{parameter: parameter});
  siga.window.setId(module,win.getId());

  win.on('minimize',function(w){siga.window.hide(module);siga.menu.show(true);});

  win.on('close',function(w){
    for(var i=0;i<siga.window.list.length;i++)//buscar y quitar elemento del arreglo list, que contiene la lista de ventanas abiertas
      if(module==siga.window.list[i]){
        siga.window.list.splice(i,1);
        break;
      }
    if(!siga.window.getCmp(module).modal)//mostrar el menu cuando no sea una ventana modal
      siga.menu.show(true);
  });

  siga.window.list.push(module);

  if(win.modal)
    win.show();
  else
    siga.window.focus(module);

  if(win.init)
    win.init();
};

siga.window.getId=function(module){
  return siga._value[module]["id"];
};

siga.window.setId=function(module,id){
  siga._value[module]["id"]=id;
};

siga.window.isOpen=function(module){
  if(Ext.getCmp(siga.window.getId(module)))
    return true;
  return false;
};

siga.window.isVisible=function(module){
  return Ext.getCmp(siga.window.getId(module)).isVisible();
};

siga.window.show=function(module){
  Ext.getCmp(siga.window.getId(module)).show();
};

siga.window.hide=function(module){
  Ext.getCmp(siga.window.getId(module)).hide();
};

siga.window.focus=function(module){
  for(var i=0;i<siga.window.list.length;i++)
    if(module!=siga.window.list[i])
      siga.window.hide(siga.window.list[i]);
  siga.window.show(module);
};

siga.window.getCmp=function(module){
  if(!siga.isDefine(module))
    return null;
  return Ext.getCmp(siga.window.getId(module));
};

siga.getCmp=function(module){
  if(!siga.isDefine(module))
    return null;
  return Ext.getCmp(siga.window.getId(module));
};

siga.menu.isVisible=function(){
  return Ext.get("siga-apps").getStyle("display")=='block'?true:false;
};

siga.menu.show=function(_generate_visible){
  if(Ext.WindowManager.mask)
  if(Ext.WindowManager.mask.isVisible())
    return;

  siga.setHtml({id:"siga-apps-title",html:""});

  Ext.get("siga-apps").set({style:{display: 'block'}});
  Ext.get("siga-apps-button").addCls("open");
  Ext.get("siga-apps-button").removeCls("close");
  Ext.get("siga-apps-button").set({onclick: "siga.menu.hide(true)"});

  if(siga.window.list.length==0)
    return;

  var wbase=280;
  var factor=0;
  var style={};

  var w,h,div_main,div_canvas,div_cubierta,div_titulo;
  //var wtotal=window.innerWidth;
  var wtotal=300;
  var htotal=window.innerHeight;
  var wspace=0;
  var hspace=10;
  var wwindow, hwindow;
  var module="";
  var module_id="";

  var posx=window.innerWidth-wtotal;
  var posy=10;
  for(var i=0;i<siga.window.list.length;i++){
    module=siga.window.list[i];
    module_id=siga.window.getId(module);

    if(_generate_visible)
      siga.window.visible[module]=siga.window.isVisible(module);

    wwindow=Ext.getCmp(module_id).width;
    hwindow=Ext.getCmp(module_id).height;
    factor=wbase/wwindow;

    Ext.getCmp(module_id).show();
    Ext.getCmp(module_id).setDisabled(true);

    _style={
      'top': posy + 'px',
      'left': posx + 'px',
      '-webkit-transform':          'scale('+factor+')',
      '-webkit-transform-origin':   '0 0',
      '-moz-transform':             'scale('+factor+')',
      '-moz-transform-origin':      '0 0',
      '-ms-transform':              'scale('+factor+')',
      '-ms-transform-origin':       '0 0',
      '-o-transform':               'scale('+factor+')',
      '-otransform-origin':         '0 0',
      'transform':                  'scale('+factor+')',
      'transform-origin':           '0 0',
    };

    var _opt={style: _style, onclick:"siga.menu.hide(); siga.window.show('"+module+"')"};

    if(Ext.getCmp(module_id).getEl().shadow)
      if(Ext.getCmp(module_id).getEl().shadow.el)
        Ext.get(Ext.getCmp(module_id).getEl().shadow.el.id).set(_opt);

    Ext.getCmp(module_id).getEl().set(_opt);

    //crear el div del titulo
    var div_titulo_base=document.createElement("div");
    Ext.get(div_titulo_base).set({
      id: 'siga-apps-title-'+module_id,
      cls: 'siga-apps-title'+(siga.window.visible[siga.window.list[i]]?":hover":""),
      style:{
        'position': 'absolute',
        'top': (posy-5) + 'px',
        'left': posx + 'px',
        'width': (wbase)+'px',
        'text-align': 'center',
        'z-index': '50000'
      }
    });

    //crear el div del titulo
    var div_titulo=document.createElement("div");
    div_titulo.innerHTML=Ext.getCmp(module_id).title;//"Titulo de la Ventana";
    Ext.get(div_titulo).set({
      cls: 'siga-apps-title-text',
      onclick: _opt.onclick
    });

    div_titulo_base.appendChild(div_titulo);

    //crear el div de cerrar
    div_close=document.createElement("div");
    Ext.get(div_close).set({cls: 'siga-apps-title-close icon-close-white',onclick:"siga.close('"+module+"'); siga.menu.show(false);"});

    Ext.get(div_titulo_base).appendChild(div_close);

    Ext.get("siga-apps-title").appendChild(div_titulo_base);

    //ocultar titulo y botones del header de la ventana
    if(Ext.get(module_id+"_header"))
      Ext.get(module_id+"_header").set({style:{'display':'none'}});

    posy+=hwindow*factor+hspace;
  }
};

siga.menu.hide=function(_cancel){
  var me=this;
  var module="";
  var module_id="";

  siga.setHtml({id:"siga-apps-title",html:""});
  Ext.get("siga-apps").set({style:{display: 'none'}});
  if(Ext.get("siga-apps-button")){
    Ext.get("siga-apps-button").addCls("normal");
    Ext.get("siga-apps-button").removeCls("open");
    Ext.get("siga-apps-button").set({onclick: "siga.menu.show(true)"});
  }

  for(var i=0;i<siga.window.list.length;i++){
    module=siga.window.list[i];
    module_id=siga.window.getId(module);

    Ext.getCmp(module_id).setDisabled(false);

    _style={
      '-webkit-transform':          'scale(1)',
      '-webkit-transform-origin':   '0 0',
      '-moz-transform':             'scale(1)',
      '-moz-transform-origin':      '0 0',
      '-ms-transform':              'scale(1)',
      '-ms-transform-origin':       '0 0',
      '-o-transform':               'scale(1)',
      '-otransform-origin':         '0 0',
      'transform':                  'scale(1)',
      'transform-origin':           '0 0',
    };

    var _opt={style: _style, onclick:""};

    if(Ext.getCmp(module_id).getEl().shadow)
      if(Ext.getCmp(module_id).getEl().shadow.el)
        Ext.get(Ext.getCmp(module_id).getEl().shadow.el.id).set(_opt);
    Ext.getCmp(module_id).getEl().set(_opt);

    Ext.getCmp(module_id).center();
    if(Ext.get(module_id+"_header"))
      Ext.get(module_id+"_header").set({style:{'display':''}});

    if(_cancel && siga.window.visible[module])
      siga.window.show(module);
    else
      siga.window.hide(module);
  }
};

siga.menu.option=function(id){
  //ocultar todos las opciones del menu del centro
  var node=document.getElementById("siga-apps-center").childNodes;
  for(var i=0;i<node.length;i++){
    if(node[i].nodeValue != null) continue;
    Ext.get(node[i].id).set({style:{'display': 'none'}});
  }
  //mostrar la opcion seleccionado
  Ext.get("siga-apps-center-"+id).set({style:{'display': 'block'}});

  //limpiar el selected de la opcion principal del menu
  var node=document.getElementById("siga-apps-left").childNodes;
  for(var i=0;i<node.length;i++){
    if(node[i].nodeValue != null) continue;
    Ext.get(node[i].id).removeCls("selected");
  }
  //activa el elemento seleccionado
  Ext.get("siga-apps-left-"+id).addCls("selected");
};

siga.help=function(link){
  window.open(link,'siga_help','width=700,height=850');
  //window.open(link);
};

//Para controlar las verificaciones periodicar
//1)Comprobar estado de la session
//2)Actualizar Fecha y Hora en la barra principal
siga.timer={
  //comprobar cada 1 minutos
  intervalTimeOut: 1000*60,
  //manejador del interval
  intervalHandler: null,
  //indica si la session esta iniciada al cargar la pagina por primera vez
  sessionStatusOnload: siga.value("sesion_abierta"),
  //Almacena el resultado de la consulta al servidor
  result: null,
};

//Iniciar el timer, se usa en el Onload de la pagina actual
siga.timer.init=function(){
  siga.timer.updateAll();
  siga.timer.intervalHandler=setInterval(
    function(){
      siga.timer.updateAll();
    },
    siga.timer.intervalTimeOut
  );
}

//Actualizar todo, llama a todas las funciones a actualizar periodicamente
siga.timer.updateAll=function(){
  //buscar información de la session,
  //retorna el estatus de la session: abierta o cerrada, fecha: fecha actual en letras, hora: hora actual
  siga.timer.result=siga.get({action:"session"});
  //verfica el estatus de la session, si esta cerrada recargar la pagina
  siga.timer.updateSessionStatus();
  //actualizar la hora en la barra
  siga.timer.updateTime();
};

//Verifica el estatus de la session, si la session es cerrada o abierta en otra ventana del mismo navegador -> recargar la pagina
siga.timer.updateSessionStatus=function(){
  var _session=siga.timer.result.session;
  if(_session){
    if(siga.timer.sessionStatusOnload==true)
      return;
  }
  else{
    if(siga.timer.sessionStatusOnload==false)
      return;
  }
  window.location.reload();
};

//Actualizar hora en la barra
siga.timer.updateTime=function(){
  document.getElementById("siga-infotime").innerHTML=siga.timer.result.hora;
  document.getElementById("siga-infotime").title=siga.timer.result.fecha_letra;
};

siga.calendar={
  el: null
};

siga.calendar.open=function(){
  if(siga.calendar.el==null)
    siga.calendar.el=Ext.create("Ext.picker.Date",{renderTo:'siga-window-container',style:'top: 0px; right:0px; position: absolute; z-index:19999;', constrain: true, constrainTo: 'siga-window-container',  hidden: true, showToday: false, draggable: true});

  if(siga.calendar.el.isVisible())
    siga.calendar.el.hide();
  else{
    siga.calendar.el.show();
    var d=String(siga.timer.result.fecha).split("-");
    siga.calendar.el.setValue(new Date(d[0],d[1]-1,d[2]));
  }
};

siga.database={
  el: null
};

siga.database.open=function(ev){
  if(siga.database.el==null){
    const data_disponible=siga.value("data_disponible")||[];
    var items=data_disponible.map((row)=>{
      const check = +row["id"] === +siga.value('anio');
      return {
        xtype: check?'menucheckitem':'menuitem',
        checked: check,
        text: row['nombre'],
        disabled: check,
        handler: function(){
          Ext.Msg.show({
            title:'Año de Trabajo',
            message: '¿Desea cambiar al año de trabajo '+row["id"]+'?',
            buttons: Ext.Msg.YESNO,
            icon: Ext.Msg.QUESTION,
            fn: function(btn) {
              if (btn === 'yes') {
                var msgWait=Ext.Msg.wait('Seleccionando año de trabajo '+row["id"]+'. Por favor espere...', "Año de Trabajo",{text:''});
                msgWait.setAlwaysOnTop(true);
                Ext.Ajax.request({
                  method: 'POST',
                  url:'module/anio_trabajo/',
                  params:{
                    action: 'onChange',
                    data: row["id"],
                  },
                  success:function(request){
                    var result=Ext.JSON.decode(request.responseText);
                    if(result.success){
                      window.location.reload();
                    }
                    else{
                      msgWait.close();
                      alert(result.message);
                    }
                  },
                  failure:function(request){
                    msgWait.close();
                    var result=Ext.JSON.decode(request.responseText);
                  }
                });
              }
            }
          });
        }
      }
    });

    if(data_disponible.length>0 && siga.value("user")==='admin'){
      var ultima_data_disponible=data_disponible[data_disponible.length-1];
      var anio_agregar = Number(ultima_data_disponible.id)+1;
      items.push({
        text: 'Crear '+anio_agregar,
        handler: function(){
          Ext.Msg.show({
            title:'Año de Trabajo',
            message: 'El año de '+anio_agregar+' no existe.<br>¿Desea crearlo?',
            buttons: Ext.Msg.YESNO,
            icon: Ext.Msg.QUESTION,
            fn: function(btn) {
              if (btn === 'yes') {
                var msgWait=Ext.Msg.wait('Seleccionando año de trabajo '+anio_agregar+'. Por favor espere...', "Año de Trabajo",{text:''});
                msgWait.setAlwaysOnTop(true);
                Ext.Ajax.request({
                  method: 'POST',
                  url:'module/anio_trabajo/',
                  params:{
                    action: 'onChange',
                    data: anio_agregar,
                    crear: true
                  },
                  success:function(request){
                    var result=Ext.JSON.decode(request.responseText);
                    if(result.success){
                      window.location.reload();
                    }
                    else{
                      msgWait.close();
                      alert(result.message);
                    }
                  },
                  failure:function(request){
                    msgWait.close();
                    var result=Ext.JSON.decode(request.responseText);
                  }
                });
              }
            }
          });
        }
      });
    }

    items.push({
      xtype: 'menuseparator'
    });

    items.push({
      text: 'Cerrar Sesión',
      handler: function(){
        siga.open("logout");
      }
    });

    siga.database.el=Ext.create("Ext.menu.Menu",{
      renderTo: Ext.getBody(),
      floating: true,
      ignoreParentClicks: true,
      items: items,
      listeners: {
        show: () => {
          siga.database.el.alignTo("siga-infodatabase",'tr-br?',[0,5]);
        }
      }
    });
    siga.database.el.alignTo("siga-infodatabase",'tr-br?',[0,5]);
  }


  if(siga.calendar.el && siga.calendar.el.isVisible())
    siga.calendar.el.hide();

  if(siga.database.el.isVisible())
    siga.database.el.hide();
  else{
    siga.database.el.show();
  }
};

siga.desktopIcon.add=function(name,icon,onclick){
  var app=document.createElement("div");
  Ext.get(app).set({cls: 'icon-desktop'});
  app.innerHTML='<img src="'+icon+'" width="48" height="48" onclick="'+onclick+'" /><br /><span onclick="'+onclick+'">'+name+'</span>';
  Ext.get("siga-window-container").appendChild(app);
};

siga.get=function(params){
  var response = Ext.Ajax.request({async: false, url: "library/siga.get.php", params: params, timeout: 30000});
  return Ext.decode(response.responseText);
};

siga.base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=siga.base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=siga.base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/\r\n/g,"\n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}};

siga.jappix=function(){
  siga.js({
    url: "module/jappix/server/get.php?l=es&t=js&g=mini.xml&ver=1.0.2",
    onLoad: function(){
      var result=siga.get({action:"session_hash"});
      result=siga.base64.decode(siga.base64.decode(result.session)).split("/");
      result={user:result[0],password:result[1]};

      JappixMini.launch({
         connection: {
            domain: "fundacite-sucre.gob.ve",
            user: result.user,
            password: result.password,
         },

         application: {
            network: {
              autoconnect: true,
            },

            interface: {
              showpane: true,
              animate: true,
            },

            user: {
              random_nickname: false,
            },
        },
     });
    }
  });
}


siga.onPersona=function(o){
  var titulo="";
  var url="module/persona/";
  var onList="";
  var onGet="";

  if(o.tipo=="N"){//Natural
    titulo="Beneficirios / Personas Naturales";
    columnas={field: ["identificacion","denominacion"], title: ["Cédula","Denominación"], width: ['20%','80'], sort: ["ASC",'NULL']};
  }
  else if (o.tipo=="J"){//Juridico
    titulo="Proveedores / Personas Jurídicas";
    columnas={field: ["identificacion","denominacion"], title: ["RIF","Denominación"], width: ['20%','80'], sort: ["ASC",'NULL']};
  }
  else{
    alert("Tipo de persona inválido");
    return;
  }
  onList="onList_Select";
  onGet="onGet_Select";
  if(o.onList) onList=o.onList;
  if(o.onGet) onList=o.onGet;
  if(o.url) url=o.url;

  var campo={
    fieldLabel: titulo,
    setValue: function(v){
      var resp=Ext.Ajax.request({
        async: false,
        url:campo.internal.url,
        params: Ext.JSON.decode('{"action": "'+campo.internal.actionOnGet+'", "'+campo.internal.valueField+'": "'+v+'"}')
      });
      if(resp.statusText=="OK"){
        var result=Ext.JSON.decode(resp.responseText);
        if(o.onAccept)
          return o.onAccept(result);
        return true;
      }
      return false;
    },
    internal:{
      page:1,
      limit: 100,
      valueField: 'id',
      columns: columnas,
      url: url,
      actionOnList: onList,
      actionOnGet: onGet,
      params: {tipo: o.tipo}
    }
  };
  //var selector=new Ext.form.eWindowSelect({internal:{parent: campo}});
  var _opt={};
  _opt.internal={};
  _opt.internal.parent=campo;
  _opt.internal.params={tipo: o.tipo};
  var selector=Ext.create("siga.windowSelect",_opt);
  selector.show();
  selector.search();
};

siga.onCuentaBancaria=function(o){
  var campo={
    fieldLabel: 'Banco - Listado de Cuentas',
    setValue: function(v){},
    internal:{
      page:1,
      limit: 100,
      valueField: 'id',
      columns: {
        field: ["numero_cuenta","denominacion","cuenta_contable","denominacion_contable","banco","tipo","id_cuenta_contable"],
        title: ["Número","Descripción","Cuenta Contable","Denominación"],
        width: ['15%','36%','13%','36%'],
        sort: ["","ASC","",""]
      },
      gridList:{
        features:[{
          ftype: 'grouping',
          groupHeaderTpl: '{name}',
          collapsible : false,
        }],
        groupField: 'banco'
      },
      url: 'module/banco_cuenta/',
      actionOnList: 'onList',
      actionOnGet: 'onGet',
      onBeforeAccept: function(dataview, record, item, index, e){
        var result=[];
        result[0]=[];
        result[0]["id"]=record.get("id");
        result[0]["numero_cuenta"]=record.get("numero_cuenta");
        result[0]["denominacion"]=record.get("denominacion");
        result[0]["tipo"]=record.get("tipo");
        result[0]["banco"]=record.get("banco");
        result[0]["id_cuenta_contable"]=record.get("id_cuenta_contable");
        result[0]["cuenta_contable"]=record.get("cuenta_contable");
        result[0]["denominacion_contable"]=record.get("denominacion_contable");
        if(o.onAccept)
          return o.onAccept(result);
        return true;
      },
      onAccept: function(){}
    }
  };
  var _opt={};
  _opt.width=920;
  _opt.height=400;
  _opt.internal={};
  _opt.internal.parent=campo;
  var selector=Ext.create("siga.windowSelect",_opt);
  selector.show();
  selector.search();
}

siga.onCalendar=function(elDom){
  if (!siga.onCalendar.el)
    siga.onCalendar.el=Ext.create("Ext.picker.Date",{
      floating: true,
      hidden: true,
      showToday: true,
      draggable: true,
      handler: function(picker, date){
        elDom.value=Ext.Date.format(date,'d/m/Y');
        siga.onCalendar.el.hide();
      }
    });
  if(!siga.onCalendar.el.isVisible()){
    siga.onCalendar.el.show();
    siga.onCalendar.el.alignTo(elDom,"bl");
  }
  else
    siga.onCalendar.el.hide();
};

siga.onGetComprobante=function(o){
  var _detalle=((o.detalle===undefined)?true:o.detalle);
  var _tmp=null;
  var _comprobante=[];
  for(var i=0;i<o.id.length;i++){
    _tmp=Ext.Ajax.request({
      async: false,
      url:"module/comprobante/",
      params: {
        action: 'onGet',
        id: o.id[i],
        detalle: _detalle
      }
    });
    if(_tmp.statusText=="OK"){
      var _retorno=Ext.JSON.decode(_tmp.responseText);
      _comprobante[i]=_retorno[0];
    }
  }
  return _comprobante;
};

siga.onListCuentaPresupuestaria=function(){
  if(siga.value('cuenta_presupuestaria'))
    return siga.value('cuenta_presupuestaria');
  _tmp=Ext.Ajax.request({
      async: false,
      url:"module/cuenta_presupuestaria/",
      params: {
        action: 'onList',
        start: 0,
        limit: 'ALL',
        filtro: '4%',
        mode_assoc: true
      }
    });
    if(_tmp.statusText=="OK"){
      var _retorno=Ext.JSON.decode(_tmp.responseText);
      siga.value('cuenta_presupuestaria',_retorno['result']);
      return _retorno['result'];
    }
  return [];
}

siga.dsp_site=function(){
  window.open("http://dsp.com.ve/")
}

siga.dsp_author=function(){
  window.open("http://dsp.com.ve/profile.php")
}
