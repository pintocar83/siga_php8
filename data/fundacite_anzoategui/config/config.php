<?php
$siga_title="Sistema Integrado para la Gestión Administrativa - Sector Público";
$siga_title_login="Bienvenido al Sistema Integrado para la<br>Gestión Administrativa - Sector Público";
$siga_title_logout="Sistema Integrado para la<br>Gestión Administrativa - Sector Público";

$siga_apps_common=array(
  array("id"=>"asistencia_registro","icon"=>"image/menu/icon-asistencia_registro.png","name"=>"Registro de Asistencia","public"=>true),
  array("id"=>"directorio","icon"=>"image/menu/icon-directorio.png","name"=>"Directorio<br>Telefónico","public"=>true),
  array("id"=>"galeria","icon"=>"image/menu/icon-multimedia.png","name"=>"Galeria<br>Multimedia","public"=>true),  
  array("id"=>"http://www.fundacite-sucre.gob.ve/#","icon"=>"image/menu/icon-www.png","name"=>"Página web Institucional","public"=>true),
  array("id"=>"https://www.zoho.com/mail/login.html#","icon"=>"image/menu/icon-mail.png","name"=>"Correo<br>Institucional","public"=>true),
  array("id"=>"http://copernico.fundacite-sucre.gob.ve:631/printers/#","icon"=>"image/menu/icon-impresoras.png","name"=>"Servidor de Impresión","public"=>true),
  array("id"=>"http://sigafs.fundacite-sucre.gob.ve/#","icon"=>"image/menu/icon-sigafs.png","name"=>"SIGAFS<br>2009-2014","public"=>true),
  array("id"=>"module/documentador/#","icon"=>"image/menu/icon-documentador.png","name"=>"Documentador de Sistemas","public"=>true),
  array("id"=>"lector_qr","icon"=>"image/menu/icon-lector_qr.png","name"=>"Lector QR","public"=>true),
  );

if(SIGA::user()):
  $siga_apps_admin_all=array(
                        array("id"=>"module/administrador/phppgadmin/#","icon"=>"image/menu/icon-phppgadmin.png","name"=>"phpPgAdmin"),
                        array("id"=>"module/administrador/phpmyadmin/#","icon"=>"image/menu/icon-phpmyadmin.png","name"=>"phpMyAdmin"),
                        array("id"=>"modulo/administrador/codiad/#","icon"=>"image/menu/icon-codiad.png","name"=>"Codiad"),
                        //array("id"=>"servicio_tecnico","icon"=>"image/menu/icon-serviciotecnico.png","name"=>"Servicio Técnico"),
                        array("id"=>"usuario","icon"=>"image/menu/icon-usuarios.png","name"=>"Usuarios y Permisología"),
                        );
  $siga_apps_admin=array();
  
  for($i=0;$i<count($siga_apps_admin_all);$i++):
    if(SIGA::access($siga_apps_admin_all[$i]["id"]))
      $siga_apps_admin[]=$siga_apps_admin_all[$i];
  endfor;

  $siga_apps=array(
    array("id"=>"favoritos","icon"=>"image/menu/icon-favoritos.png","name"=>"Favoritos",
          "option"=>array(
                          "Área Personal"=>array(
                                            array("id"=>"ficha_consulta","icon"=>"image/menu/icon-ficha.png","name"=>"Mi Expediente"),
                                            //array("id"=>"usuario_clave","icon"=>"image/menu/icon-password.png","name"=>"Cambiar<br>Contraseña"),
                                            array("id"=>"usuario_preferencias","icon"=>"image/menu/icon-user_background.png","name"=>"Fondo de<br>Pantalla"),
                                            array("id"=>"logout","icon"=>"image/menu/icon-logout.png","name"=>"Cerrar<br>Sesión","public"=>true),  
                                          ),
                          "Acesso Rápido"=>$siga_apps_common,
                          //HERRAMIENTAS DEL ADMINISTRADOR, MOSTRAR UNICAMENTE CUANDO EXISTAN ELEMENTOS ACCESIBLES
                          $siga_apps_admin?"Herramientas de la Unidad de Informática":""=>$siga_apps_admin
                        )),
    
    array("id"=>"administrativa","icon"=>"image/menu/icon-administrativa.png","name"=>"Sistema<br>Administrativo",
          "option"=>array(
                          "Procesos"=>array(
                                          array("id"=>"requisicion_externa","icon"=>"image/menu/icon-requisicion.png","name"=>"Requisición<br>Externa"),
                                          array("id"=>"orden_compra","icon"=>"image/menu/icon-orden_compra.png","name"=>"Orden de Compra"),
                                          array("id"=>"orden_servicio","icon"=>"image/menu/icon-orden_servicio.png","name"=>"Orden de Servicio"),
                                          array("id"=>"orden_pago","icon"=>"image/menu/icon-orden_pago.png","name"=>"Orden de Pago"),
                                          //array("id"=>"cheque_orden_pago","icon"=>"image/menu/icon-cheque.png","name"=>"Cheque<br>Orden de Pago"),
                                          array("id"=>"pago","icon"=>"image/menu/icon-cheque.png","name"=>"Pago por Cheque o Transferencia"),
                                          array("id"=>"cheque_directo","icon"=>"image/menu/icon-cheque_directo.png","name"=>"Cheque<br>Directo"),
                                          array("id"=>"banco_movimiento","icon"=>"image/menu/icon-banco_movimiento.png","name"=>"Movimientos<br>Bancarios"),
                                          array("id"=>"comprobante","icon"=>"image/menu/icon-comprobante.png","name"=>"Comprobante"),
                                          array("id"=>"comprobante_retencion","icon"=>"image/menu/icon-retencion_comprobante.png","name"=>"Comprobante de<br>Retención"),
                                          array("id"=>"nomina","icon"=>"image/menu/icon-nomina.png","name"=>"Nómina"),
                                          array("id"=>"traspaso","icon"=>"image/menu/icon-traspaso.png","name"=>"Traspaso"),
                                          array("id"=>"credito_adicional","icon"=>"image/menu/icon-credito_adicional.png","name"=>"Crédito Adicional"),
                                          array("id"=>"reduccion","icon"=>"image/menu/icon-reduccion.png","name"=>"Reducción"),
                                          array("id"=>"disponibilidad","icon"=>"image/menu/icon-disponibilidad.png","name"=>"Disponibilidad<br>Presupuestaria"),
                                          array("id"=>"formulacion","icon"=>"image/menu/icon-formulacion.png","name"=>"Formulación"),                                        
                                          array("id"=>"cerrar_mes","icon"=>"image/menu/icon-cerrar_mes.png","name"=>"Cierre de Meses"),
                                          
                                          ),
                          "Reportes"=>array(
                                          array("id"=>"reporte_contabilidad","icon"=>"image/menu/icon-reportes.png","name"=>"Contabilidad"),
                                          array("id"=>"reporte_ejecucion","icon"=>"image/menu/icon-reportes.png","name"=>"Ejecución (Presupuesto)"),
                                          array("id"=>"reporte_mayor","icon"=>"image/menu/icon-reportes.png","name"=>"Mayor Analítico (Presupuesto)"),
                                          array("id"=>"reporte_banco","icon"=>"image/menu/icon-reportes.png","name"=>"Estado de Cuenta (Banco)"),
                                          array("id"=>"reporte_retencion","icon"=>"image/menu/icon-reportes.png","name"=>"Retenciones<br>IVA / ISLR"),
                                          //array("id"=>"modulo_nomina/constancia_trabajo","icon"=>"image/menu/icon-reportes.png","name"=>"Constancias<br>de Trabajo")
                                          ),
                          
                          "Definiciones"=>array(
                                          array("id"=>"proveedor","icon"=>"image/menu/icon-proveedor.png","name"=>"Proveedores"),
                                          array("id"=>"beneficiario","icon"=>"image/menu/icon-beneficiario.png","name"=>"Beneficiarios"),
                                          array("id"=>"ficha","icon"=>"image/menu/icon-ficha.png","name"=>"Ficha del<br>Personal"),
                                          array("id"=>"materiales","icon"=>"image/menu/icon-materiales.png","name"=>"Materiales y/o<br>Suministros"),
                                          array("id"=>"bienes","icon"=>"image/menu/icon-bienes.png","name"=>"Bienes"),
                                          array("id"=>"servicios","icon"=>"image/menu/icon-servicios.png","name"=>"Servicios"),
                                          array("id"=>"factura","icon"=>"image/menu/icon-factura.png","name"=>"Facturas"),
                                          array("id"=>"banco_cuenta","icon"=>"image/menu/icon-banco_cuenta.png","name"=>"Cuentas Bancaria"),
                                          array("id"=>"cuenta_presupuestaria","icon"=>"image/menu/icon-cuenta_presupuestaria.png","name"=>"Plan de Cuentas<br>Presupuestarias"),
                                          array("id"=>"cuenta_contable","icon"=>"image/menu/icon-cuenta_contable.png","name"=>"Plan de Cuentas<br>Contables"),
                                          array("id"=>"convertidor","icon"=>"image/menu/icon-convertidor.png","name"=>"Convertidor"),
                                          array("id"=>"estructura_presupuestaria","icon"=>"image/menu/icon-estructura_presupuestaria.png","name"=>"Estructura<br>Presupuestaria"),
                                          array("id"=>"cargo","icon"=>"image/menu/icon-cargo.png","name"=>"Cargos / Impuestos"),
                                          array("id"=>"retencion","icon"=>"image/menu/icon-retencion.png","name"=>"Retenciones"),
                                          ),
                        )),
    
      /*array("id"=>"academica","icon"=>"image/menu/icon-academica.png","name"=>"Academia de Software Libre",
          "option"=>array(
                          "Academia de Software Libre"=>array(
                                          array("id"=>"academia_dependencia","icon"=>"image/menu/icon-dependencia.png","name"=>"Dependencias e Instituciones"),
                                          array("id"=>"academia_curso_aperturado","icon"=>"image/menu/icon-curso_abrir.png","name"=>"Abrir o Cerrar<br>Cursos"),
                                          array("id"=>"academia_inscribir","icon"=>"image/menu/icon-curso_inscribir.png","name"=>"Inscribir"),
                                          array("id"=>"academia_preinscribir","icon"=>"image/menu/icon-curso_preinscribir.png","name"=>"Pre-Incribir"),
                                          array("id"=>"academia_facilitador","icon"=>"image/menu/icon-facilitador.png","name"=>"Facilitadores"),
                                          array("id"=>"academia_reportes","icon"=>"image/menu/icon-reportes.png","name"=>"Reportes"),
                                          )
                        )),*/
      
      array("id"=>"asistencia","icon"=>"image/menu/icon-asistencia.png","name"=>"Sistema de<br>Asistencia",
          "option"=>array(
                          "Sistema de Asistencia"=>array(
                                          array("id"=>"asistencia_registro","icon"=>"image/menu/icon-asistencia_registro.png","name"=>"Registro de<br>Asistencia","public"=>true),
                                          array("id"=>"asistencia_validar","icon"=>"image/menu/icon-asistencia_validar.png","name"=>"Validar"),
                                          array("id"=>"asistencia_consulta","icon"=>"image/menu/icon-asistencia_consultar.png","name"=>"Consulta","public"=>true),
                                          array("id"=>"ficha","icon"=>"image/menu/icon-ficha.png","name"=>"Ficha del<br>Personal"),
                                         ),
                           /*"Visitantes"=>array(
                                          array("id"=>"asistencia_visitante","icon"=>"image/menu/icon-asistencia_registro.png","name"=>"Registro de<br>Visitantes","public"=>true),
                                        )*/
                        )),
      /*array("id"=>"planificacion","icon"=>"image/menu/icon-planificacion.png","name"=>"Planificación","users"=>array("admin"),
          "option"=>array(
                          "Definiciones"=>array(
                                            array("id"=>"ficha","icon"=>"image/menu/icon-ficha.png","name"=>"Recurso<br>Humano"),
                                            array("id"=>"estructura_presupuestaria","icon"=>"image/menu/icon-estructura_presupuestaria.png","name"=>"Acciones /<br> Proyectos"),
                                            array("id"=>"unidad_coordinacion","icon"=>"image/menu/icon-unidad_coordinacion.png","name"=>"Unidad /<br>Coordinación"),
                                          ),
                          "Planificación"=>array(
                                          array("id"=>"meta_fisica","icon"=>"image/menu/icon-meta_fisica.png","name"=>"Metas Físicas<br>y Actividades"),
                                          array("id"=>"meta_fisica/informar","icon"=>"image/menu/icon-meta_fisica_informe.png","name"=>"Informar<br>Actividad"),
                                        )
                        )),*/
    );
else:
  $siga_apps=array(
    array("id"=>"favoritos","icon"=>"image/menu/icon-favoritos.png","name"=>"Favoritos",
          "option"=>array(
                          "Área Personal"=>array(
                                          array("id"=>"login","icon"=>"image/menu/icon-login.png","name"=>"Iniciar<br>Sesión","public"=>true),
                                          ),
                         // "Acesso Rápido"=>$siga_apps_common,
                        )),
    );
endif;
?>