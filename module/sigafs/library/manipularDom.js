/*
 * AUTOR: Pablo Caraballo
 * Modificado: 30/10/2006(PC): Se usa solo el metodo setAttribute y x, donde se pueda.
 * Modificado: 30/11/2006(PC): Corrige problema de IE para agregar el atributo 'name' a un elemento.
 * Requiere la libreria cross-browser 'core.js', 'x_event.js' y x_dom.js
 * xDef: es una funcion de la libreria x_core.js.
 * xCreateElement: es una funcion de la libreria x_dom.js.
 * xAppendChild: es una funcion de la libreria x_dom.js.
 */

var mD = {

/*
 * Esta funcion solo es usada internamente y permite corregir el problema de IE
 * para agregar el atributo 'name' a un nodo.
 * ver: http://www.thunderguy.com/semicolon/2005/05/23/setting-the-name-attribute-in-internet-explorer/
 *   y: http://www.easy-reader.net/archives/2005/09/02/death-to-bad-dom-implementations/#comment-444
 */
    creaElementoConNombre: function (tipoNodo, nombreNodo)
    {
        var elemento = null;
        // Tratemos con IE; esto falla en los exploradores que mantengan el estandar
        try
        {
            elemento = xCreateElement('<' + tipoNodo + ' name="' + nombreNodo + '">');
        }
        catch (e)
        {}
        if (!elemento || elemento.nodeName != tipoNodo.toUpperCase())
        {
            // Otros exploradores diferente a IE; usa el setAttribute para nombrar el elemento.
            // Tambien, pudiera usarse el metodo canonico: elemento.setAttribute(...);
            elemento = xCreateElement(tipoNodo);
            elemento.setAttribute('name', nombreNodo);
        }
        return elemento;
    },

/*
 * Esta funcion se usa, principalmente, internamente y permite agregar atributos.
 * elemento: Nodo, al cual se le agregaran los atributos. Requerido.
 * atributos: objeto con elementos {llave:valor,...} de el(los) atributos a incluir. Opcional.
 * xDef: es una funcion de la libreria x_core.js.
 */
    agregaAtributo: function (elemento, atributos)
    {

        if (!xDef(elemento) || !xDef(atributos) || (typeof atributos != 'object')) return;

        for (at in atributos)
        {
            switch (at.toUpperCase())
            {
                case 'CLASS':
                    if (!elemento.className || elemento.className != atributos[at]) elemento.className = atributos[at];
                    break;
                case 'COLSPAN':
                    if (!elemento.colSpan || elemento.colSpan != atributos[at]) elemento.colSpan = atributos[at];
                    break;
                default:
                    elemento.setAttribute(at, atributos[at]);
            }
        }
    },

/*
 * Esta funcion se usa para anexar eventos a un elemento, cuando la funcion es creada dinamicamente.
 * elemento: Nodo, al cual se le anexara la funcion. Requerido.
 * evento: Evento a anexar ('click', etc.). Requerido.
 * xDef: es una funcion de la libreria x_core.js.
 */
    anexaEvento: function(elemento, evento, funcion, argumentos)
    {
        if (!xDef(elemento) || !xDef(evento) || !xDef(funcion) || (typeof funcion != 'function')) return;
        if (xDef(argumentos) && (typeof argumentos == 'object') && (argumentos instanceof Array))
        {
            xAddEventListener(elemento, evento, function()
                                            {
                                                funcion.apply(null, argumentos);
                                            }, false);
        }
        else
        {
            xAddEventListener(elemento, evento, funcion, false);
        }
    },

/*
 * tipo: Tipo de etiqueta general: <div>, <span>, etc. Requerido.
 * nombre: nombre del elemento. Requerido.
 * id: id del elemento. Debe colocarse null, si no se necesita.
 * atributos: objeto con elementos: llave=valor, de cualquier otro atributo a incluir. Valor, null, u omitido.
 */
    agregaNodoElemento: function (tipo, nombre, id, atributos)
    {
        if (xDef(nombre))
        {
            var el = mD.creaElementoConNombre(tipo, nombre);
        }
        else
        {
            var el = xCreateElement(tipo);
            return el;
        }

        if (xDef(id)) el.setAttribute('id', id);
        else
        {
            el.setAttribute('id', nombre);
            return el;
        }

        mD.agregaAtributo(el, atributos);

        return el;
    },

/*
 * el: nodo del elemento al cual se le quiere agregar un nodo de texto. Requerido.
 * texto: Texto a ser agregado. Requerido.
 */
    agregaNodoTexto: function (el, texto)
    {
        if (xDef(el))
        {
            var nodoTexto = document.createTextNode(texto);
            xAppendChild(el, nodoTexto);
        }
    },

/*
 * onClick: funcion o comando a ejecutar al ocurrir el evento 'onClick'. Requerido.
 * texto: Texto a ser colocado entre las etiquetas 'A'. Opcional.
 */
    agregaVinculo: function (texto, atributos)
    {
        vinculo = xCreateElement('a');

        if (xDef(texto) && (typeof texto == 'string'))
        {
            mD.agregaNodoTexto(vinculo, texto);
            mD.agregaAtributo(vinculo, atributos);
        }

        return vinculo;
    },

/*
 * tabla: Nodo del tipo 'tabla', al cual se le agregara una fila. Requerido.
 * indice: indice donde se insertara la fila. Si es igual a -1 o
 *         al numero de filas existentes en la tabla, la nueva fila se agrega.
 *         El valor no debe ser menor de -1 o mayor al numero de filas.
 *         Si no se define, se coloca en -1. Opcional.
 * atributos: objeto con elementos {llave:valor,...} de el(los) atributos a incluir. Opcional.
 * xDef: es una funcion de la libreria x_core.js.
 */
    insertarFila: function (tabla, indice, atributos)
    {
        if (!xDef(tabla)) return;

        if (!xDef(indice) || (indice < -1)) indice = -1;

        if (indice > tabla.childNodes.length) indice = tabla.childNodes.length;

        var fila = tabla.insertRow(indice);

        mD.agregaAtributo(fila, atributos);

        return fila;

    },

/*
 * fila: Nodo del tipo 'tr', al cual se le agregara una celda. Requerido.
 * indice: indice donde se insertara la celda. Si es igual a -1 o
 *         al numero de celdas existentes en la fila, la nueva celda se agrega.
 *         El valor no debe ser menor de -1 o mayor al numero de celdas.
 *         Si no se define, se coloca en -1. Opcional.
 * atributos: objeto con elementos: {llave:valor,...} de el(los) atributos a incluir. Opcional.
 * texto: texto a ser colocado en la celda. Opcional.
 */
    insertarCelda: function (fila, indice, atributos, texto)
    {
        if (!xDef(fila)) return;

        if (!xDef(indice) || (indice < -1)) indice = -1;

        if (indice > fila.childNodes.length) indice = fila.childNodes.length;

        var celda = fila.insertCell(indice);

        mD.agregaAtributo(celda, atributos);

        if (xDef(texto) && (typeof texto == 'string'))
        {
            mD.agregaNodoTexto(celda, texto);
        }

        return celda;

    },

/*
 * insertarFilaCompleta(tabla, indice, atributos[, atributos, texto][, atributos, texto]...)
 * tabla: debe ser un elemento 'table, definido antes de llamar la funcion. Requerido.
 * indice y el primer atributos son OPCIONALES y se usan para insertar la fila.
 * Los atributos y texto siguientes se aplican a cada celda a incluir.
 * Usa 'insertarFila' e 'insertarCelda'.
 */
    insertarFilaCompleta: function ()
    {
        var long = arguments.length;
        if (long < 1) return;

        var fila = mD.insertarFila(arguments[0], arguments[1], arguments[2]);
        if (!xDef(fila)) return;

        if (long < 4) return fila;

        for (var i=3; i<long; i+=2)
        {
            if (xDef(arguments[i+1]))
            {
                mD.insertarCelda(fila, -1, arguments[i], arguments[i+1]);
            }
            else
            {
                mD.insertarCelda(fila, -1, arguments[i]);
            }
        }
        return fila;
    },

/*
 * insertarVariasCeldas(fila, indice, atributos, texto[, atributos, texto][, atributos, texto]...)
 * fila: debe ser un elemento 'tr, definido antes de llamar la funcion. Requerido.
 * indice: indica el lugar de la fila donde se insertaran las celdas.
 * El primer atributos y texto son OPCIONALES y se usan para insertar la primera celda.
 * Los atributos y texto siguientes se aplican a cada otra celda a incluir.
 * Usa 'insertarCelda'.
 */
    insertarVariasCeldas: function ()
    {
        var long = arguments.length;
        if ((long < 1) || !xDef(arguments[0])) return;
// El primer argumento tiene que ser el elemento fila creado antes de llamar a esta funcion.
        var fila = arguments[0];

        if (xDef(arguments[1])) var indice = arguments[1];
        else var indice = -1;

// Las celdas podrian insertarse en el medio de dos existentes. Si no, las agregaremos al final.
// Esta fila es necesaria porque la variable indice se estara incrementandose cada vez.
        if ((indice < 0) || (indice >= fila.childNodes.length)) indice = fila.childNodes.length;

        for (var i=2; i<long; i+=2)
        {
            if (xDef(arguments[i+1]))
            {
                mD.insertarCelda(fila, indice, arguments[i], arguments[i+1]);
            }
            else
            {
                mD.insertarCelda(fila, indice, arguments[i]);
            }
// Incrementamos indice para agregar la proxima celda a la que acabamos de insertar.
            indice++;
        }
        return fila;
    },

/*
 * tipo: Tipo de entrada: text, button, submit, reset, etc. Requerido.
 * nombre: nombre del elemento a crear. Requerido.
 * id: id del elemento a crear. Valor, null, u omitido.
 * tamano: tama#o del campo a crear. Valor, null, u omitido.
 * maxTamano: tama#o maximo del campo a crear. Valor, null, u omitido.
 * atributos:  objeto con elementos {llave:valor,...} de el(los) otro(s) atributo(s) a incluir. Opcional.
 */
    agregaEntrada: function (tipo, nombre, valor, id, tamano, maxTamano, atributos)
    {
        if (xDef(nombre))
        {
            var entrada = mD.creaElementoConNombre('input', nombre);
        }
        else
        {
            var entrada = xCreateElement('input');
        }

        if (xDef(tipo)) entrada.setAttribute('type', tipo);
        else return null;

        if (xDef(valor)) entrada.setAttribute('value', valor);
        else entrada.setAttribute('value', '');

        if (xDef(id)) entrada.setAttribute('id', id);
        else entrada.setAttribute('id', nombre);

        if (xDef(tamano))
        {
            entrada.setAttribute('size', tamano);
            if (xDef(maxTamano)) entrada.setAttribute('maxlength', maxTamano);
            else entrada.setAttribute('maxlength', tamano);
        }
        mD.agregaAtributo(entrada, atributos);

        return entrada;
    },

/*
 * texto: Texto a ser colocado entre las etiquetas <label> y </label>. Requerido.
 * id: id de la nueva etiqueta. Valor, null, u omitido.
 * tecla: tecla de acceso rapido.
 * para: id del elemento asociado. Valor, null, u omitido.
 * atributos:  objeto con elementos {llave:valor,...} de el(los) otro(s) atributo(s) a incluir. Opcional.
 */
    agregaEtiqueta: function (texto, id, para, tecla, atributos)
    {
        var etiqueta = xCreateElement('label');

        if (xDef(texto)) var nodoTexto = document.createTextNode(texto);
        if (xDef(id)) etiqueta.setAttribute('id', id);
        if (xDef(para)) etiqueta.setAttribute('for', para);
        if (xDef(tecla)) etiqueta.setAttribute('accesskey', tecla);

        mD.agregaAtributo(etiqueta, atributos);

        xAppendChild(etiqueta, nodoTexto);
        return etiqueta;
    },

/*
 * texto: Texto a ser colocado sobre el boton. Requerido.
 * nombre: nombre del nuevo boton. Valor, null, u omitido.
 * id: id del nuevo boton. Valor, null, u omitido.
 * atributos:  objeto con elementos {llave:valor,...} de el(los) otro(s) atributo(s) a incluir. Opcional.
 */
    agregaBoton: function (texto, nombre, id, atributos)
    {
        if (xDef(nombre))
        {
            var boton = mD.creaElementoConNombre('button', nombre);
        }
        else
        {
            var boton = xCreateElement('button');
        }

        if (xDef(texto)) var textoE = document.createTextNode(texto);
        else return boton;

        xAppendChild(boton, textoE);

        if (xDef(id)) boton.setAttribute('id', id);
        else
            if (xDef(nombre)) boton.setAttribute('id', nombre);
            else return boton;

        mD.agregaAtributo(boton, atributos);

        return boton;
    },

/*
 * el: nodo del elemento al cual se le quiere agregar un nodo <br />. Requerido.
 * numero: numero de <br /> a insertar. Numero, null, u omitido.
 */
    agregaElementoBr: function (el, numero)
    {
        if (!xDef(el)) return;

        if (!xDef(numero)) numero = 1;

        for (var i = 0; i < numero; i++)
        {
            var elBr = xCreateElement('br');
            xAppendChild(el, elBr);
        }
    },

/*
 * nodoPadre: elemento nodo al que se le agregaran los tres (3) nuevos nodos (entrada escondida, span (despliegue) e imagen)
 * nombreEntrada: nombre e id del nuevo elemento 'input' escondido, el cual contendra la fecha en formato de la bd.
 *                El 'id' debe ser unico.
 * fechaVen: nombre e id del elemento span (despliegue de la fecha). El 'id' debe ser unico.
 * botFecha: nombre e id del elemento img que se usara como boton para buscar la fecha. El 'id' debe ser unico.
 * valFecha: Valor (objeto Date) del elemento 'input' escondido.
 */
    preparaFecha: function (nodoPadre, nombreEntrada, fechaVen, botFecha, valFecha)
    {
        if (!xDef(valFecha) || (valFecha == ''))
        {
            var valFecha = ahora;
        }

        var valorFecha = valFecha.formato('aa-mm-dd');
        var fechaMostrada = valFecha.formato('dd/mm/aa');
        var fechaBdeD = mD.agregaEntrada("hidden", nombreEntrada, valorFecha, nombreEntrada, 10, 10);
        var fechaVenezolana = mD.agregaNodoElemento("span", fechaVen);
        mD.agregaNodoTexto(fechaVenezolana, fechaMostrada);
        var botonFecha = mD.agregaNodoElemento("img", botFecha, botFecha, {"src": "imagenes/img.gif", "style": "cursor: pointer; border: 1px solid red;", "title": "Presione para seleccionar la fecha", "onmouseover": "this.style.background='red';", "onmouseout": "this.style.background=''"} );
        mD.agregaHijo(nodoPadre, fechaBdeD, fechaVenezolana, botonFecha);
        Calendar.setup(
        {
// En los sihuientes casos se paso la variable correspondiente al objeto del elemento HTML,
// porque no consigue el elemento HTML con ese id y reporta null en Calendar.setup (calendar-config.js)
                        'inputField'  : fechaBdeD         // id del campo de entrada
                        ,'displayArea': fechaVenezolana   // id del campo de salida (SPAN)
                        ,'button'     : botonFecha        // id del boton (IMG en este caso)
        }
        );
    },

/*
 * nodoPadre: elemento nodo al que se le agrega el elemento 'SELECT'.
 * nombreSelect: nombre e id del nuevo elemento 'SELECT'.
 * arregloOpciones: Arreglo que debe contener las opciones a desplegar dentro del 'SELECT'.
 * ValPredeterminado: Valor predeterminado.
 */
    preparaSelect: function (nodoPadre, nombreSelect, arregloOpciones, valPredeterminado)
    {
        var seleccion = mD.agregaNodoElemento("select", nombreSelect);
        for (var i=0, len=arregloOpciones.length; i < len; i++)
        {
            var opcion = xCreateElement("option");
            opcion.setAttribute("value", arregloOpciones[i][0]);
            if (valPredeterminado == arregloOpciones[i][0]) opcion.setAttribute('selected', true);
            mD.agregaNodoTexto(opcion, arregloOpciones[i][1]);
            mD.agregaHijo(seleccion, opcion);
        }
        mD.agregaHijo(nodoPadre, seleccion);
    },

/*
 * nodoPadre: elemento nodo al que se le agrega el elemento 'RADIO'.
 * nombreEntrada: nombre e id del nuevo elemento de entrada 'RADIO'.
 * arregloRadio: Arreglo que debe contener las diferentes opciones 'RADIO'. RADIO solo permite seleccionar una opcion.
 * ValPredeterminado: Valor predeterminado.
 */
    preparaRadio: function (nodoPadre, nombreEntrada, arregloRadio, valPredeterminado)
    {
        for (var i=0, len=arregloRadio.length; i < len; i++)
        {
            var radio = mD.agregaEntrada("radio", nombreEntrada, arregloRadio[i][0], nombreEntrada + i);
            if (valPredeterminado == arregloRadio[i][0]) radio.setAttribute('checked', true);
            mD.agregaHijo(nodoPadre, radio);
            mD.agregaNodoTexto(nodoPadre, arregloRadio[i][1]);
        }
    },

/*
 * nodoPadre: elemento nodo al que se le agrega el elemento 'textarea'.
 * nombreArea: nombre e id del nuevo elemento 'textarea'.
 * columnas: Numero de columnas del elemento 'textarea'.
 * filas: Numero de filas del elemento 'textarea'.
 * valor: Valor inicial
 */
    preparaAreaTexto: function (nodoPadre, nombreArea, columnas, filas, valor)
    {
        var areaTexto = mD.agregaNodoElemento('textarea', nombreArea, nombreArea, {  "cols":columnas, "rows":filas } );
        if (xDef(valor))
        {
            mD.agregaNodoTexto(areaTexto, valor);
        }
        mD.agregaHijo(nodoPadre, areaTexto);
    },
/*
 * El primer argumento debe ser el nodo padre, al que se le agregaran hijos.
 * El segundo hasta n argumentos son los nodos hijos.
 * Se requieren el primer y segundo argumento.
 */
    agregaHijo: function ()
    {
        if (arguments.length < 2) return;

        var nodoPadre = arguments[0];

        for (var i=1, len = arguments.length; i<len; i++)
        {
            xAppendChild(nodoPadre, arguments[i]);
        }
    },

/*
 * el: nodo de Texto al cual se le quiere remplazar el texto. Requerido.
 * texto: Texto definitivo en el nodo.
 * xDef: es una funcion de la libreria v3_xlib.js.
 */
    remplazaTexto: function (el, texto)
    {
        if (xDef(el))
        {
            mD.limpiaTexto(el);	// Limpia el texto existente.
            var nodoTexto = document.createTextNode(texto);
            xAppendChild(el, nodoTexto);
        }
    },

/*
 * el: nodo al cual se le limpiaran (removeran) todos sus nodos hijos. Requerido.
 * xDef: es una funcion de la libreria v3_xlib.js.
 */
    limpiaTexto: function (el)
    {
        if (xDef(el) && (typeof el == 'object')) {
            while (el.firstChild)
            {
                el.removeChild(el.firstChild);
//				alert ('while: ' + typeof el.firstChild + ' ' + typeof el);
            }
        }
    },

/*
 * el: nodo de donde se leera el texto. Requerido.
 * xDef: es una funcion de la libreria v3_xlib.js.
 */
    leeTexto: function (el)
    {
        var texto = "";

        if (xDef(el))
        {
            if (el.childNodes)
            {
                for (var i = 0, len = el.childNodes.length; i < len; i++)
                {
                    var nodoHijo = el.childNodes[i];
                    if (nodoHijo.nodeValue != null)
                    {
                        texto = texto + nodoHijo.nodeValue;
                    }
                }
            }
        }
        return texto;
    }

}