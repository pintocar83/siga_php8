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



function Form_IMPRIMIR_REPORTE_Imprimir(){
	
	var sw_ocultar_c=0;
	if(xGetElementById("OCULTAR_FILAS_FFCO_1").checked)
		sw_ocultar_c=1;
		
	if(xGetElementById("TIPO_REPORTE_FIR_1").checked)
		window.open("../../report/contabilidad_diario.php?FI="+xGetElementById("FECHA_INICIO_FIR").value+"&FF="+xGetElementById("FECHA_FIN_FIR").value);
	else if(xGetElementById("TIPO_REPORTE_FIR_2").checked){
		if(xGetElementById("SELECT_MAYOR_FIR").value==1)
			window.open('../../report/contabilidad_mayor_a.php?id_cuenta_contable='+xGetElementById("CODIGO_CONTABLE_FIR").value+"&FI="+xGetElementById("FECHA_INICIO_FIR").value+"&FF="+xGetElementById("FECHA_FIN_FIR").value);
		else
			window.open('../../report/contabilidad_mayor_b.php?id_cuenta_contable='+xGetElementById("CODIGO_CONTABLE_FIR").value+"&FI="+xGetElementById("FECHA_INICIO_FIR").value+"&FF="+xGetElementById("FECHA_FIN_FIR").value);
		}
	else if(xGetElementById("TIPO_REPORTE_FIR_3").checked)
		window.open("../../report/contabilidad_balance_comprobacion.php?FI="+xGetElementById("FECHA_INICIO_FIR").value+"&FF="+xGetElementById("FECHA_FIN_FIR").value);
	else if(xGetElementById("TIPO_REPORTE_FIR_4").checked){
		if(xGetElementById("SELECT_BALANCEGENERAL_FIR").value==1)		
		  window.open("../../report/contabilidad_balance_general_a.php?FF="+xGetElementById("FECHA_FIN_FIR").value+"&OCULTAR="+sw_ocultar_c);
		else
		  window.open("../../report/contabilidad_balance_general_b.php?FF="+xGetElementById("FECHA_FIN_FIR").value+"&OCULTAR="+sw_ocultar_c);
	  }
	/*else if(xGetElementById("TIPO_REPORTE_FIR_5").checked)
		window.open("reportes/EstadoDeIngresosEgresos.php?FI="+xGetElementById("FECHA_INICIO_FIR").value+"&FF="+xGetElementById("FECHA_FIN_FIR").value);*/
else if(xGetElementById("TIPO_REPORTE_FIR_5").checked)
		window.open("../../report/contabilidad_estado_resultados.php?FI="+xGetElementById("FECHA_INICIO_FIR").value+"&FF="+xGetElementById("FECHA_FIN_FIR").value);
	}

