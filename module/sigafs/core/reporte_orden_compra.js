/**
* Sistema para la Gestion Administrativa Fundacite Sucre (SIGAFS)
* Copyright (C) 2009 by FUNDACITE Sucre
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
* @author Carlos J. Pinto B. <pintocar83@gmail.com>
* @date 2009-08-01
* @version 20091113
*/



function Form_REPORTE_ORDEN_COMPRA__Imprimir(){
	if(xGetElementById("TIPO_REPORTE_ROCS_1").checked)
		window.open("../../report/orden_compra_listado_xls.php?tipo=OC&inicio="+xGetElementById("FECHA_INICIO_ROCS").value+"&fin="+xGetElementById("FECHA_FIN_ROCS").value);
	else if(xGetElementById("TIPO_REPORTE_ROCS_2").checked)
		window.open("../../report/orden_compra_afectacion_presupuestaria_listado_xls.php?tipo=OC&inicio="+xGetElementById("FECHA_INICIO_ROCS").value+"&fin="+xGetElementById("FECHA_FIN_ROCS").value);
	else if(xGetElementById("TIPO_REPORTE_ROCS_3").checked)
		window.open("../../report/orden_compra_listado_xls.php?tipo=OS&inicio="+xGetElementById("FECHA_INICIO_ROCS").value+"&fin="+xGetElementById("FECHA_FIN_ROCS").value);
	else if(xGetElementById("TIPO_REPORTE_ROCS_4").checked)
		window.open("../../report/orden_compra_afectacion_presupuestaria_listado_xls.php?tipo=OS&inicio="+xGetElementById("FECHA_INICIO_ROCS").value+"&fin="+xGetElementById("FECHA_FIN_ROCS").value);
	
	}

