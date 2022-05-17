<?php
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once("../../library/db.controller.php");
include_once("../../library/siga.config.php");
include_once("../../library/siga.class.php");
include_once("../../library/functions/formatDate.php");
include_once("../../library/functions/str_clear.php");



$CURSO=str_clear($_REQUEST['curso']);
if(!$CURSO) exit;


$db=SIGA::DBController();

$detalle_curso=$db->Execute("
                      SELECT
                        ca.*,
                        c.denominacion as nombrecurso,
                        c.acronimo as acronimocurso,
                        tc.denominacion,
                        tc.horario,
                        tc.dias,
                        s.denominacion as sala
                      FROM
                        modulo_asl.curso_aperturado as ca,
                        modulo_asl.curso as c,
                        modulo_asl.instructor as i,
                        modulo_asl.turno_curso as tc,
                        modulo_asl.sala as s
                      WHERE
                        c.id=ca.id_curso and
                        ca.id_instructor=i.id and
                        ca.id_turno=tc.id and
                        ca.id_sala=s.id and
                        ca.codigo='$CURSO'
                      ");


$participantes=$db->Execute("
  SELECT    
    ins.nombre as institucion,
    p.identificacion_tipo as nacionalidad,
    p.identificacion_numero as cedula,
    split_part(p.denominacion,';',1) as primer_nombre,
    split_part(p.denominacion,';',2) as segundo_nombre,
    split_part(p.denominacion,';',3) as primer_apellido,
    split_part(p.denominacion,';',4) as segundo_apellido,
    p.telefono,
    p.correo
  FROM
    modulo_asl.inscrito as i,
    modulo_asl.institucion as ins,
    modulo_asl.estado as e,
    modulo_base.persona as p
  WHERE
    p.id=i.id_persona and
    i.id_curso_aperturado='".$detalle_curso[0]['id']."' and
    i.id_institucion=ins.id and
    i.id_estado=e.id and
    (e.id=2 or e.id=3 or e.id=4)
  ORDER BY
    p.identificacion_tipo, p.identificacion_numero
  ");











?>
CORREO: 
<?php
for($j=0;$j<count($participantes);$j++) if(trim($participantes[$j]['correo'])!="")echo $participantes[$j]['correo'].", ";
?>
<br /><br /><br /><br />

TITULO: CONFIRMACIÓN CURSO <?php echo strtoupper($detalle_curso[0]['nombrecurso']);?>. ASL - FUNDACITE SUCRE
<br /><br /><br /><br />

Buenos días. <br />
<br />
Estimados Compañeros, la presente tiene como finalidad confirmar el inicio del curso "<?php echo mb_convert_case($detalle_curso[0]['nombrecurso'], MB_CASE_TITLE, "UTF-8");?>", de la Academia de Software Libre, Programa Nacional del Ministerio del Poder Popular para Ciencia, Tecnología e Innovación.<br />
<br />
Información del Curso:<br />
Curso: <?php echo mb_convert_case($detalle_curso[0]['nombrecurso'], MB_CASE_TITLE, "UTF-8");?><br />
Fecha de Realización: <?php echo "Del ".formatDate($detalle_curso[0]['fecha_inicio'])." al ".formatDate($detalle_curso[0]['fecha_culminacion']);?><br />
Horario: <?php echo ucfirst(strtolower($detalle_curso[0]['denominacion']));?><br />
Lugar: <?php echo ucfirst(strtolower($detalle_curso[0]['sala']));?><br />
Duración: 40 horas<br />
<br />
Los cursos son gratuitos y son avalados a través de un Certificado de Aprobación emitido por la Academia de Software Libre, para lo cual se realizarán evaluaciones de los módulos. Es importante resaltar que con el 25% de inasistencias no podrá ser certificado, por lo que es importante gestionar los permisos en sus respectivos sitios de trabajo.<br />
<br />
<br />
Sin otro particular...<br />
<br />
Coordinación Académica ASL - Sede Sucre<br />
FUNDACITE SUCRE<br />









