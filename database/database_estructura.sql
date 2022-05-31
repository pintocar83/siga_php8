/*
Reemplazar
ALTER [a-zA-Z0-9 _(),.:;-]*OWNER TO (siga|dspcom_siga);
--[a-zA-Z0-9 _(),.:;-]*[\n]
*/


SET statement_timeout = 0;
-- SET lock_timeout = 0;
-- SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;
-- SET row_security = off;


CREATE SCHEMA modulo_asistencia;





CREATE SCHEMA modulo_asl;





CREATE SCHEMA modulo_base;





CREATE SCHEMA modulo_inventario;





CREATE SCHEMA modulo_nomina;





CREATE SCHEMA modulo_planificacion;





CREATE SCHEMA modulo_planificador;





CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;



-- COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = modulo_base, pg_catalog;


CREATE FUNCTION comprobante_correlativo(integer, character varying) RETURNS character varying
    LANGUAGE plpgsql
    AS $_$


DECLARE


	_anio ALIAS FOR $1;


	_tipo ALIAS FOR $2;


	_correlativo integer;


BEGIN


	SELECT INTO _correlativo max(correlativo) FROM modulo_base.comprobante WHERE tipo=_tipo AND EXTRACT(YEAR FROM fecha)=_anio;


	IF NOT FOUND OR _correlativo IS NULL THEN


		_correlativo=0;


	END IF;


	RETURN _correlativo+1;


END


$_$;





CREATE FUNCTION item_codigo(integer) RETURNS character varying
    LANGUAGE plpgsql
    AS $_$


DECLARE


	_id_item ALIAS FOR $1;


	_codigo varchar;


BEGIN


  SELECT INTO _codigo codigo FROM modulo_base.item WHERE id=_id_item;


	IF NOT FOUND OR _codigo IS NULL THEN


		_codigo='';


	END IF;


	RETURN _codigo;


END


$_$;





CREATE FUNCTION mes_cerrado(date) RETURNS boolean
    LANGUAGE plpgsql
    AS $_$


DECLARE


	_fecha ALIAS FOR $1;


	_mes int4;


	_anio int4;


	_estado boolean;


BEGIN


	_mes=int4(to_char(_fecha,'MM'));


	_anio=int4(to_char(_fecha,'YYYY'));


	SELECT INTO _estado meses_cerrados[_mes] FROM modulo_base.anio_detalle WHERE anio=_anio  LIMIT 1;	


	IF NOT FOUND THEN


		RETURN NULL;


	END IF;


	RETURN _estado;


END


$_$;





CREATE FUNCTION unidad_medida_cantidad(integer) RETURNS numeric
    LANGUAGE plpgsql
    AS $_$


DECLARE


	_id_unidad_medida ALIAS FOR $1;


	_cantidad NUMERIC(10,2) :=0;


BEGIN


	SELECT INTO _cantidad cantidad_unidades FROM modulo_base.unidad_medida WHERE id=_id_unidad_medida;


	IF NOT FOUND OR _cantidad IS NULL THEN


		_cantidad=0;


	END IF;


	RETURN _cantidad;


END


$_$;




SET search_path = modulo_inventario, pg_catalog;


CREATE FUNCTION disponibilidad_material(integer) RETURNS numeric
    LANGUAGE plpgsql
    AS $_$


DECLARE


	_id_item ALIAS FOR $1;


	_total NUMERIC(10,2) :=0;


BEGIN


	SELECT INTO _total SUM(CASE WHEN MM.tipo ILIKE 'E' THEN MMTI.cantidad*UM.cantidad_unidades ELSE -MMTI.cantidad*UM.cantidad_unidades END)


	FROM 


		modulo_inventario.movimiento_material AS MM,


		modulo_inventario.movimiento_material_tiene_item AS MMTI,


		modulo_base.unidad_medida AS UM


	WHERE 


		MM.id=MMTI.id_movimiento_material AND


		MMTI.id_item=_id_item AND


		MMTI.id_unidad_medida=UM.id;


	IF NOT FOUND THEN


		_total=0;


	END IF;


	IF _total IS NULL THEN


		_total=0;


	END IF;


	RETURN _total;


END


$_$;





CREATE FUNCTION movimiento_material_delete() RETURNS trigger
    LANGUAGE plpgsql
    AS $$


DECLARE


	_unidad_medida_cantidad NUMERIC(10,2):=0;


	_item_codigo VARCHAR;


	_movimiento_material_tipo CHAR(1);


	_v NUMERIC(10,2):=0;


	_disponible NUMERIC(10,2):=0;


	_resultado NUMERIC(10,2):=0;


BEGIN


	_unidad_medida_cantidad:=modulo_base.unidad_medida_cantidad(OLD.id_unidad_medida);


	_item_codigo:=modulo_base.item_codigo(OLD.id_item);


	_movimiento_material_tipo:=modulo_inventario.movimiento_material_tipo(OLD.id_movimiento_material);


	IF _movimiento_material_tipo='E' THEN


		_v=1;


	ELSIF _movimiento_material_tipo='S' THEN


		_v=-1;


	ELSE


		RAISE EXCEPTION 'No pudo obtener el tipo de movimiento.';


	END IF;


	_disponible:=modulo_inventario.disponibilidad_material(OLD.id_item);


	_resultado:=_disponible - _v*_unidad_medida_cantidad*OLD.cantidad;


	IF _resultado<0 THEN


		RAISE EXCEPTION 'El ítem % solo tiene % unidades disponibles en el inventario.',_item_codigo,_disponible;	


	END IF;


  	RETURN OLD;


END


$$;





CREATE FUNCTION movimiento_material_insert() RETURNS trigger
    LANGUAGE plpgsql
    AS $$


DECLARE


	_unidad_medida_cantidad NUMERIC(10,2):=0;


 	_item_codigo VARCHAR;


	_movimiento_material_tipo CHAR(1);


	_v NUMERIC(10,2):=0;


	_disponible NUMERIC(10,2):=0;


	_resultado NUMERIC(10,2):=0;


BEGIN


	_unidad_medida_cantidad:=modulo_base.unidad_medida_cantidad(NEW.id_unidad_medida);


	_item_codigo:=modulo_base.item_codigo(NEW.id_item);


	_movimiento_material_tipo:=modulo_inventario.movimiento_material_tipo(NEW.id_movimiento_material);


	IF _movimiento_material_tipo ILIKE 'E' THEN


		_v=1;


	ELSIF _movimiento_material_tipo ILIKE 'S' THEN


		_v=-1;


	ELSE


		RAISE EXCEPTION 'No pudo obtener el tipo de movimiento.';


	END IF;


	_disponible:=modulo_inventario.disponibilidad_material(NEW.id_item);


	_resultado:=_disponible + _v*_unidad_medida_cantidad*NEW.cantidad;


	IF _resultado<0 THEN


		RAISE EXCEPTION 'El ítem % solo tiene % unidades disponibles en el inventario.',_item_codigo,_disponible;	


	END IF;


  	RETURN NEW;


END


$$;





CREATE FUNCTION movimiento_material_tipo(integer) RETURNS character varying
    LANGUAGE plpgsql
    AS $_$


DECLARE


	_tipo CHAR(1);


BEGIN


	SELECT INTO _tipo tipo FROM modulo_inventario.movimiento_material WHERE id=$1;


	IF NOT FOUND OR _tipo IS NULL THEN


		_tipo='';


	END IF;


	RETURN _tipo;


END


$_$;





CREATE FUNCTION movimiento_material_update() RETURNS trigger
    LANGUAGE plpgsql
    AS $$


DECLARE


	_unidad_medida_cantidad_new NUMERIC(10,2):=0;


	_unidad_medida_cantidad_old NUMERIC(10,2):=0;


 	_item_codigo VARCHAR;


	_movimiento_material_tipo_new CHAR(1);


	_movimiento_material_tipo_old CHAR(1);


	_v_new NUMERIC(10,2):=0;


	_v_old NUMERIC(10,2):=0;


	_disponible NUMERIC(10,2):=0;


	_resultado NUMERIC(10,2):=0;


BEGIN


	_unidad_medida_cantidad_new:=modulo_base.unidad_medida_cantidad(NEW.id_unidad_medida);


	_unidad_medida_cantidad_old:=modulo_base.unidad_medida_cantidad(OLD.id_unidad_medida);


	_item_codigo:=modulo_base.item_codigo(NEW.id_item);


	_movimiento_material_tipo_new:=modulo_inventario.movimiento_material_tipo(NEW.id_movimiento_material);


	IF _movimiento_material_tipo_new ILIKE 'E' THEN


		_v_new=1;


	ELSIF _movimiento_material_tipo_new ILIKE 'S' THEN


		_v_new=-1;


	ELSE


		RAISE EXCEPTION 'No pudo obtener el tipo de movimiento.';


	END IF;


	_movimiento_material_tipo_old:=modulo_inventario.movimiento_material_tipo(OLD.id_movimiento_material);


	IF _movimiento_material_tipo_old ILIKE 'E' THEN


		_v_old=-1;


	ELSIF _movimiento_material_tipo_old ILIKE 'S' THEN


		_v_old=1;


	ELSE


		RAISE EXCEPTION 'No pudo obtener el tipo de movimiento.';


	END IF;


	_disponible:=modulo_inventario.disponibilidad_material(NEW.id_item);


	_resultado:=_disponible + _v_new*_unidad_medida_cantidad_new*NEW.cantidad + _v_old*_unidad_medida_cantidad_old*OLD.cantidad;


	IF _resultado<0 THEN


		RAISE EXCEPTION 'El ítem % actualmente tiene % unidades disponibles. Está intentando modificarlo de % a % unidades.',_item_codigo,_disponible,OLD.cantidad,NEW.cantidad;	


	END IF;


  	RETURN NEW;


END


$$;




SET search_path = public, pg_catalog;


CREATE FUNCTION _comprobante_bancario_delete() RETURNS trigger
    LANGUAGE plpgsql
    AS $$


DECLARE


  _fecha date:=NULL;


  _operacion VARCHAR(1):=NULL;


	_disponible NUMERIC(20,2):=0;


	_resultado NUMERIC(20,2):=0;


BEGIN 


  SELECT fecha INTO _fecha FROM modulo_base.comprobante WHERE id=OLD.id_comprobante;


  IF NOT FOUND THEN


    RAISE EXCEPTION 'El comprobante no fue encontrado.';


  END IF;




  IF _mes_cerrado(_fecha) THEN


    RAISE EXCEPTION 'El mes de encuentra cerrado.';


  END IF;  




  SELECT operacion INTO _operacion FROM modulo_base.banco_movimiento_tipo WHERE id=OLD.id_banco_movimiento_tipo;


  IF NOT FOUND THEN


    RAISE EXCEPTION 'El tipo de operacion no fue encontrado.';


  END IF;


  CASE _operacion


    WHEN 'H' THEN

      RETURN OLD;


    ELSE

      _disponible:=_disponibilidad_bancaria(EXTRACT(YEAR FROM _fecha)::integer, OLD.id_banco_cuenta);


      _resultado:=_disponible - OLD.monto;


      IF _resultado>=0 THEN


        RETURN OLD;


      END IF;


      RAISE EXCEPTION 'La cuenta solo tiene disponible Bs %. [Monto: %. Diferencia: %.]',


        _formatear_numero(_disponible),


        _formatear_numero(OLD.monto),


        _formatear_numero(_resultado);


  END CASE;


  RAISE EXCEPTION 'Acción invalida para este trigger.';


END


$$;





CREATE FUNCTION _comprobante_bancario_insert() RETURNS trigger
    LANGUAGE plpgsql
    AS $$


DECLARE


  _fecha date:=NULL;


  _operacion VARCHAR(1):=NULL;


	_disponible NUMERIC(20,2):=0;


	_resultado NUMERIC(20,2):=0;


BEGIN 


  SELECT fecha INTO _fecha FROM modulo_base.comprobante WHERE id=NEW.id_comprobante;


  IF NOT FOUND THEN


    RAISE EXCEPTION 'El comprobante no fue encontrado.';


  END IF;




  IF _mes_cerrado(_fecha) THEN


    RAISE EXCEPTION 'El mes de encuentra cerrado.';


  END IF;  




  SELECT operacion INTO _operacion FROM modulo_base.banco_movimiento_tipo WHERE id=NEW.id_banco_movimiento_tipo;


  IF NOT FOUND THEN


    RAISE EXCEPTION 'El tipo de operacion no fue encontrado.';


  END IF;


  CASE _operacion


    WHEN 'H' THEN

      _disponible:=_disponibilidad_bancaria(EXTRACT(YEAR FROM _fecha)::integer, NEW.id_banco_cuenta);


      _resultado:=_disponible - NEW.monto;


      IF _resultado>=0 THEN


        RETURN NEW;


      END IF;


      RAISE EXCEPTION 'La cuenta solo tiene disponible Bs %. [Monto: %. Diferencia: %.]',


        _formatear_numero(_disponible),


        _formatear_numero(NEW.monto),


        _formatear_numero(_resultado);


    ELSE


      RETURN NEW;


  END CASE;


  RAISE EXCEPTION 'Acción invalida para este trigger.';


END


$$;





CREATE FUNCTION _comprobante_bancario_update() RETURNS trigger
    LANGUAGE plpgsql
    AS $$


DECLARE


  _fecha date:=NULL;


  _disponible_new NUMERIC(20,2):=0;


  _disponible_old NUMERIC(20,2):=0;


BEGIN 


  SELECT fecha INTO _fecha FROM modulo_base.comprobante WHERE id=OLD.id_comprobante;


  IF NOT FOUND THEN


    RAISE EXCEPTION 'El comprobante no fue encontrado.';


  END IF;




  IF _mes_cerrado(_fecha) THEN


    RAISE EXCEPTION 'El mes de encuentra cerrado.';


  END IF;    


  _disponible_new:=_disponibilidad_bancaria(EXTRACT(YEAR FROM _fecha)::integer, NEW.id_banco_cuenta);


  IF _disponible_new < 0 THEN


    RAISE EXCEPTION 'La cuenta tendrá disponible Bs % luego de la modificación. [Monto: %]',


      _formatear_numero(_disponible_new),


      _formatear_numero(NEW.monto);


  END IF;


  _disponible_old:=_disponibilidad_bancaria(EXTRACT(YEAR FROM _fecha)::integer, OLD.id_banco_cuenta);


  IF _disponible_old < 0 THEN


    RAISE EXCEPTION 'La cuenta tendrá disponible Bs % luego de la modificación. [Monto: %]',


      _formatear_numero(_disponible_old),


      _formatear_numero(OLD.monto);


  END IF;


  RETURN NEW;


END


$$;





CREATE FUNCTION _comprobante_insert_update_delete() RETURNS trigger
    LANGUAGE plpgsql
    AS $$


DECLARE


  _mensaje varchar:='El mes de encuentra cerrado.';


  _registro RECORD;


  _resultado NUMERIC(20,2):=0;


BEGIN


  IF TG_OP='INSERT' THEN


    IF _mes_cerrado(NEW.fecha) THEN


      RAISE EXCEPTION '%',_mensaje;


    END IF;  


    RETURN NEW;


  ELSIF TG_OP='UPDATE' THEN


    IF _mes_cerrado(OLD.fecha) THEN


      RAISE EXCEPTION '%',_mensaje;


    END IF;


    IF _mes_cerrado(NEW.fecha) THEN


      RAISE EXCEPTION '%',_mensaje;


    END IF;


    IF NEW.contabilizado AND NEW.contabilizado <> OLD.contabilizado THEN


      FOR _registro IN select id_accion_subespecifica, id_cuenta_presupuestaria, monto, _disponibilidad_presupuestaria(EXTRACT(YEAR FROM NEW.fecha)::integer,id_accion_subespecifica,id_cuenta_presupuestaria) as disponible from modulo_base.detalle_presupuestario where id_comprobante=NEW.id and operacion IN ('C','CC','CCP','DI') LOOP


        _resultado:=_registro.disponible - _registro.monto;


        IF _resultado<0 THEN


          RAISE EXCEPTION 'La cuenta % de % solo tiene disponible Bs %. [Monto: %. Diferencia: %.]',


            _formatear_cuenta_presupuestaria(_registro.id_cuenta_presupuestaria),


            _formatear_estructura_presupuestaria(_registro.id_accion_subespecifica),


            _formatear_numero(_registro.disponible),


            _formatear_numero(_registro.monto),


            _formatear_numero(_resultado);


        END IF;


      END LOOP;


    END IF;


    RETURN NEW;


  ELSE

    IF _mes_cerrado(OLD.fecha) THEN


      RAISE EXCEPTION '%',_mensaje;


    END IF;    


    RETURN OLD;


  END IF;  


  RAISE EXCEPTION 'Opci?n Inv?lida en TG_OP.';


END


$$;





CREATE FUNCTION _cuenta_contable_aumenta_debe(character varying) RETURNS boolean
    LANGUAGE plpgsql
    AS $_$


BEGIN	




	IF SUBSTR($1,1,1) = '1'	THEN 


		RETURN TRUE;


	END IF;


	IF SUBSTR($1,1,1) = '6'	THEN 


		RETURN TRUE;


	END IF;


	IF SUBSTR($1,1,2) = '41' THEN 


		RETURN TRUE;


	END IF;	


	RETURN FALSE;


END


$_$;





CREATE FUNCTION _detalle_contable_insert_update_delete() RETURNS trigger
    LANGUAGE plpgsql
    AS $$DECLARE


  _fecha date:=NULL;


  _id bigint:=NULL;


BEGIN


  IF TG_OP='DELETE' THEN


    _id=OLD.id_comprobante;


  ELSE


    _id=NEW.id_comprobante;


  END IF;


  SELECT fecha INTO _fecha FROM modulo_base.comprobante WHERE id=_id;


  IF NOT FOUND THEN


    RAISE EXCEPTION 'El comprobante no fue encontrado.';


  END IF;




  IF _mes_cerrado(_fecha) THEN


    RAISE EXCEPTION 'El mes de encuentra cerrado.';


  END IF;  


  IF TG_OP='DELETE' THEN


    RETURN OLD;


  ELSE


    RETURN NEW;


  END IF;


END


$$;





CREATE FUNCTION _detalle_presupuestario_delete() RETURNS trigger
    LANGUAGE plpgsql
    AS $$


DECLARE


  _fecha date:=NULL;


  _contabilizado BOOLEAN:=FALSE;


  _disponible NUMERIC(20,2):=0;


  _resultado NUMERIC(20,2):=0;


BEGIN   


  SELECT fecha, contabilizado INTO _fecha, _contabilizado FROM modulo_base.comprobante WHERE id=OLD.id_comprobante;


  IF NOT FOUND THEN


    RAISE EXCEPTION 'El comprobante no fue encontrado.';


  END IF; 




  IF _mes_cerrado(_fecha) THEN


    RAISE EXCEPTION 'El mes de encuentra cerrado.';


  END IF;




  IF NOT _contabilizado THEN


    RETURN OLD;


  END IF;




  CASE OLD.operacion    


    WHEN 'AU', 'AP' THEN

      _disponible:=_disponibilidad_presupuestaria(EXTRACT(YEAR FROM _fecha)::integer, OLD.id_accion_subespecifica, OLD.id_cuenta_presupuestaria);


      _resultado:=_disponible - OLD.monto;


      IF _resultado>=0 THEN


        RETURN OLD;


      END IF;


      RAISE EXCEPTION 'La cuenta % de % solo tiene disponible Bs %. [Monto: %. Diferencia: %.]',


        _formatear_cuenta_presupuestaria (OLD.id_cuenta_presupuestaria),


        _formatear_estructura_presupuestaria(OLD.id_accion_subespecifica),


        _formatear_numero(_disponible),


        _formatear_numero(OLD.monto),


        _formatear_numero(_resultado);


    ELSE

      RETURN OLD;


  END CASE;


  RAISE EXCEPTION 'Acción invalida para este trigger.';


END


$$;





CREATE FUNCTION _detalle_presupuestario_insert() RETURNS trigger
    LANGUAGE plpgsql
    AS $$


DECLARE


  _fecha date:=NULL;


  _contabilizado BOOLEAN:=FALSE;


  _disponible NUMERIC(20,2):=0;


  _resultado NUMERIC(20,2):=0;


BEGIN 


  SELECT fecha, contabilizado INTO _fecha, _contabilizado FROM modulo_base.comprobante WHERE id=NEW.id_comprobante;


  IF NOT FOUND THEN


    RAISE EXCEPTION 'El comprobante no fue encontrado.';


  END IF;




  IF _mes_cerrado(_fecha) THEN


    RAISE EXCEPTION 'El mes de encuentra cerrado.';


  END IF;




  IF NOT _contabilizado THEN


    RETURN NEW;


  END IF;




  CASE NEW.operacion    


    WHEN 'C', 'CC', 'CCP', 'DI' THEN

      _disponible:=_disponibilidad_presupuestaria(EXTRACT(YEAR FROM _fecha)::integer, NEW.id_accion_subespecifica, NEW.id_cuenta_presupuestaria);


      _resultado:=_disponible - NEW.monto;


      IF _resultado>=0 THEN


        RETURN NEW;


      END IF;


      RAISE EXCEPTION 'La cuenta % de % solo tiene disponible Bs %. [Monto: %. Diferencia: %.]',


        _formatear_cuenta_presupuestaria (NEW.id_cuenta_presupuestaria),


        _formatear_estructura_presupuestaria(NEW.id_accion_subespecifica),


        _formatear_numero(_disponible),


        _formatear_numero(NEW.monto),


        _formatear_numero(_resultado);


    WHEN 'AU', 'AP' THEN

      RETURN NEW;


    ELSE

      RETURN NEW;


  END CASE;


  RAISE EXCEPTION 'Acción invalida para este trigger.';


END


$$;





CREATE FUNCTION _detalle_presupuestario_update() RETURNS trigger
    LANGUAGE plpgsql
    AS $$


DECLARE


  _fecha date:=NULL;


  _contabilizado BOOLEAN:=FALSE;


  _disponible_new NUMERIC(20,2):=0;


  _disponible_old NUMERIC(20,2):=0;


BEGIN 


  SELECT fecha, contabilizado INTO _fecha, _contabilizado FROM modulo_base.comprobante WHERE id=NEW.id_comprobante;


  IF NOT FOUND THEN


    RAISE EXCEPTION 'El comprobante no fue encontrado.';


  END IF;




  IF _mes_cerrado(_fecha) THEN


    RAISE EXCEPTION 'El mes de encuentra cerrado.';


  END IF;




  IF NOT _contabilizado THEN


    RETURN NEW;


  END IF;


  _disponible_new:=_disponibilidad_presupuestaria(EXTRACT(YEAR FROM _fecha)::integer, NEW.id_accion_subespecifica, NEW.id_cuenta_presupuestaria);


  IF _disponible_new < 0 THEN


    RAISE EXCEPTION 'La cuenta % de % tendrá disponible Bs % luego de la modificación. [Monto: %]',


      _formatear_cuenta_presupuestaria (NEW.id_cuenta_presupuestaria),


      _formatear_estructura_presupuestaria(NEW.id_accion_subespecifica),


      _formatear_numero(_disponible_new),


      _formatear_numero(NEW.monto);


  END IF;


  _disponible_old:=_disponibilidad_presupuestaria(EXTRACT(YEAR FROM _fecha)::integer, OLD.id_accion_subespecifica, OLD.id_cuenta_presupuestaria);


  IF _disponible_old < 0 THEN


    RAISE EXCEPTION 'La cuenta % de % tendrá disponible Bs % luego de la modificación. [Monto: %]',


      _formatear_cuenta_presupuestaria (OLD.id_cuenta_presupuestaria),


      _formatear_estructura_presupuestaria(OLD.id_accion_subespecifica),


      _formatear_numero(_disponible_old),


      _formatear_numero(OLD.monto);


  END IF;


  RETURN NEW;


END


$$;





CREATE FUNCTION _disponibilidad_bancaria(integer, integer) RETURNS numeric
    LANGUAGE plpgsql
    AS $_$DECLARE


  _anio ALIAS FOR $1;


  _id_banco_cuenta ALIAS FOR $2;


  _total NUMERIC(20,2) :=0;


BEGIN


  SELECT INTO _total sum(case when BMT.operacion='D' then CB.monto else -CB.monto end)


  FROM modulo_base.comprobante as C, modulo_base.comprobante_bancario as CB, modulo_base.banco_movimiento_tipo as BMT


  WHERE


    EXTRACT(YEAR FROM C.fecha)=_anio AND    


    C.id=CB.id_comprobante AND


    CB.id_banco_cuenta=_id_banco_cuenta AND


    CB.id_banco_movimiento_tipo=BMT.id;


  IF NOT FOUND OR _total IS NULL THEN


    _total=0;


  END IF;


  RETURN _total;


END$_$;





CREATE FUNCTION _disponibilidad_presupuestaria(integer, integer, character varying) RETURNS numeric
    LANGUAGE plpgsql
    AS $_$


DECLARE


  _anio ALIAS FOR $1;


  _id_accion_subespecifica ALIAS FOR $2;


  _id_cuenta_presupuestaria ALIAS FOR $3;


  _total NUMERIC(20,2) :=0;


BEGIN


  SELECT INTO _total sum(case when DP.operacion='AP' or DP.operacion='AU' then DP.monto else -DP.monto end)


  FROM modulo_base.comprobante as C, modulo_base.detalle_presupuestario as DP


  WHERE


    EXTRACT(YEAR FROM C.fecha)=_anio AND


    C.contabilizado AND


    C.id=DP.id_comprobante AND


    DP.id_cuenta_presupuestaria=_id_cuenta_presupuestaria AND


    DP.id_accion_subespecifica=_id_accion_subespecifica AND


    (DP.operacion='AP' OR DP.operacion='AU' OR DP.operacion='DI' OR DP.operacion='C' OR DP.operacion='CC' OR DP.operacion='CCP');


  IF NOT FOUND OR _total IS NULL THEN


    _total=0;


  END IF;


  RETURN _total;


END


$_$;





CREATE FUNCTION _disponibilidad_presupuestaria(integer, integer, integer, character varying) RETURNS numeric
    LANGUAGE plpgsql
    AS $_$


DECLARE


  _anio ALIAS FOR $1;


  _id_fuente_recursos ALIAS FOR $2;


  _id_accion_subespecifica ALIAS FOR $3;


  _id_cuenta_presupuestaria ALIAS FOR $4;


  _total NUMERIC(20,2) :=0;


BEGIN


  SELECT INTO _total sum(case when DP.operacion='AP' or DP.operacion='AU' then DP.monto else -DP.monto end)


  FROM modulo_base.comprobante as C, modulo_base.detalle_presupuestario as DP


  WHERE


    EXTRACT(YEAR FROM C.fecha)=_anio AND


    C.contabilizado AND


    C.id=DP.id_comprobante AND


    DP.id_cuenta_presupuestaria=_id_cuenta_presupuestaria AND


    DP.id_accion_subespecifica=_id_accion_subespecifica AND


    DP.id_fuente_recursos=_id_fuente_recursos AND


    (DP.operacion='AP' OR DP.operacion='AU' OR DP.operacion='DI' OR DP.operacion='C' OR DP.operacion='CC' OR DP.operacion='CCP');


  IF NOT FOUND OR _total IS NULL THEN


    _total=0;


  END IF;


  RETURN _total;


END


$_$;





CREATE FUNCTION _disponibilidad_presupuestaria_v2(integer, integer, integer, character varying) RETURNS numeric
    LANGUAGE plpgsql
    AS $_$


DECLARE


  _anio ALIAS FOR $1;


  _id_fuente_recursos ALIAS FOR $2;


  _id_accion_subespecifica ALIAS FOR $3;


  _id_cuenta_presupuestaria ALIAS FOR $4;


  _au NUMERIC(20,2) :=0;


  _di NUMERIC(20,2) :=0;


BEGIN


  SELECT INTO _au sum(DP.monto)


  FROM modulo_base.comprobante as C, modulo_base.detalle_presupuestario as DP


  WHERE


    EXTRACT(YEAR FROM C.fecha)=_anio AND


    C.contabilizado AND


    C.id=DP.id_comprobante AND


    DP.id_cuenta_presupuestaria=_id_cuenta_presupuestaria AND


    DP.id_accion_subespecifica=_id_accion_subespecifica AND


    DP.id_fuente_recursos=_id_fuente_recursos AND


    (DP.operacion='AP' OR DP.operacion='AU');


  IF NOT FOUND OR _au IS NULL THEN


    _au=0;


  END IF;


  SELECT INTO _di sum(DP.monto)


  FROM modulo_base.comprobante as C, modulo_base.detalle_presupuestario as DP


  WHERE


    EXTRACT(YEAR FROM C.fecha)=_anio AND


    C.contabilizado AND


    C.id=DP.id_comprobante AND


    DP.id_cuenta_presupuestaria=_id_cuenta_presupuestaria AND


    DP.id_accion_subespecifica=_id_accion_subespecifica AND


    DP.id_fuente_recursos=_id_fuente_recursos AND


    (DP.operacion='DI' OR DP.operacion='C' OR DP.operacion='CC' OR DP.operacion='CCP');


  IF NOT FOUND OR _di IS NULL THEN


    _di=0;


  END IF;


  RETURN _au-_di;


END


$_$;





CREATE FUNCTION _formatear_cuenta_contable(character varying) RETURNS character varying
    LANGUAGE plpgsql
    AS $_$BEGIN


	RETURN SUBSTR($1,1,1)||'.'||


		SUBSTR($1,2,1)||'.'||


		SUBSTR($1,3,1)||'.'||


		SUBSTR($1,4,2)||'.'||


		SUBSTR($1,6,2)||'.'||


		SUBSTR($1,8,2)||'.'||


		SUBSTR($1,10,3);


--	RETURN $1;


END


$_$;





CREATE FUNCTION _formatear_cuenta_presupuestaria(character varying) RETURNS character varying
    LANGUAGE plpgsql
    AS $_$


BEGIN


	RETURN SUBSTR($1,1,3)||'.'||SUBSTR($1,4,2)||'.'||SUBSTR($1,6,2)||'.'||SUBSTR($1,8,2);


END


$_$;





CREATE FUNCTION _formatear_estructura_presupuestaria(integer) RETURNS character varying
    LANGUAGE plpgsql
    AS $_$


DECLARE


	_codigo varchar;


BEGIN


  SELECT INTO _codigo AC.tipo||AC.codigo_centralizada||'-'||AE.codigo_especifica||'-'||ASE.codigo_subespecifica FROM modulo_base.accion_subespecifica as ASE, modulo_base.accion_especifica as AE, modulo_base.accion_centralizada as AC WHERE ASE.id=$1 AND ASE.id_accion_especifica=AE.id AND AE.id_accion_centralizada=AC.id;


  IF NOT FOUND OR _codigo IS NULL THEN


		_codigo='';


	END IF;


	RETURN _codigo;


END


$_$;





CREATE FUNCTION _formatear_estructura_presupuestaria_fuente(integer, integer) RETURNS character varying
    LANGUAGE plpgsql
    AS $_$


DECLARE


	_codigo_ep varchar;


  _codigo_f varchar;


BEGIN


  SELECT INTO _codigo_ep AC.tipo||AC.codigo_centralizada||'-'||AE.codigo_especifica||'-'||ASE.codigo_subespecifica FROM modulo_base.accion_subespecifica as ASE, modulo_base.accion_especifica as AE, modulo_base.accion_centralizada as AC WHERE ASE.id=$1 AND ASE.id_accion_especifica=AE.id AND AE.id_accion_centralizada=AC.id;


  IF NOT FOUND OR _codigo_ep IS NULL THEN


		_codigo_ep='';


	END IF;


  SELECT INTO _codigo_f FR.codigo_fuente FROM modulo_base.fuente_recursos AS FR WHERE FR.id=$2;


  IF NOT FOUND OR _codigo_f IS NULL THEN


		_codigo_f='';


	END IF;


	RETURN _codigo_ep||'-'||_codigo_f;


END


$_$;





CREATE FUNCTION _formatear_estructura_presupuestaria_nivel_especifica(integer) RETURNS character varying
    LANGUAGE plpgsql
    AS $_$


DECLARE


	_codigo varchar;


BEGIN


  	SELECT INTO _codigo AC.tipo||AC.codigo_centralizada||'-'||AE.codigo_especifica FROM modulo_base.accion_especifica as AE, modulo_base.accion_centralizada as AC WHERE AE.id=$1 AND AE.id_accion_centralizada=AC.id;


  	IF NOT FOUND OR _codigo IS NULL THEN


		_codigo='';


	END IF;


	RETURN _codigo;


END


$_$;





CREATE FUNCTION _formatear_numero(numeric) RETURNS character varying
    LANGUAGE plpgsql
    AS $_$


BEGIN


  RETURN ltrim(to_char($1,'99G999G999G999G999G999G990D00'),' ');


END


$_$;





CREATE FUNCTION _if(boolean, anyelement, anyelement) RETURNS anyelement
    LANGUAGE sql
    AS $_$SELECT CASE WHEN $1 THEN $2 ELSE $3 END$_$;





CREATE FUNCTION _mes_abierto(date) RETURNS boolean
    LANGUAGE plpgsql
    AS $_$BEGIN


  RETURN NOT _mes_cerrado($1);


END$_$;





CREATE FUNCTION _mes_cerrado(date) RETURNS boolean
    LANGUAGE plpgsql
    AS $_$DECLARE


  estado boolean;


BEGIN


  SELECT INTO estado mes_cerrado[EXTRACT(MONTH FROM $1)] FROM modulo_base.anio_detalle WHERE anio=EXTRACT(YEAR FROM $1);	


  IF NOT FOUND THEN


    RETURN TRUE;


  END IF;


  RETURN estado;


END$_$;




SET search_path = modulo_asistencia, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;


CREATE TABLE asistencia (
    id bigint NOT NULL,
    id_persona bigint,
    fecha date,
    hora time without time zone,
    manual boolean DEFAULT false,
    usuario_validador character varying(15)
);





CREATE SEQUENCE asistencia_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE asistencia_id_seq OWNED BY asistencia.id;



CREATE TABLE asistencia_nota (
    id_persona bigint,
    fecha date NOT NULL,
    tipo character varying(20) DEFAULT NULL::character varying,
    descripcion text,
    id integer NOT NULL
);





CREATE SEQUENCE asistencia_nota_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE asistencia_nota_id_seq OWNED BY asistencia_nota.id;



CREATE TABLE asistencia_visitante (
    id integer NOT NULL,
    id_persona bigint,
    fecha timestamp without time zone DEFAULT now(),
    id_unidad_coordinacion integer,
    motivo character varying,
    atendido character varying(15),
    observacion character varying,
    hora_salida time without time zone
);





CREATE SEQUENCE asistencia_visitante_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE asistencia_visitante_id_seq OWNED BY asistencia_visitante.id;


SET search_path = modulo_asl, pg_catalog;


CREATE TABLE contenido_curso (
    id_curso integer NOT NULL,
    orden integer NOT NULL,
    descripcion character varying(100),
    horas integer,
    id_curso_prerequisito integer
);





CREATE TABLE curso (
    id integer NOT NULL,
    denominacion character varying(100),
    acronimo character varying(10),
    duracion integer,
    mostrar_preinscripcion integer,
    prelacion character varying(20)
);





CREATE TABLE curso_aperturado (
    id integer NOT NULL,
    id_curso integer,
    codigo character varying(30),
    fecha_inicio date,
    fecha_culminacion date,
    cupos integer,
    id_turno integer,
    id_instructor integer,
    id_sala integer,
    estado integer,
    impreso integer,
    encuesta_clave character varying(4),
    id_instructor_secundario integer
);





CREATE SEQUENCE curso_aperturado_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE curso_aperturado_id_seq OWNED BY curso_aperturado.id;



CREATE TABLE curso_encuesta (
    id_curso_aperturado integer NOT NULL,
    id_persona bigint NOT NULL,
    item1 integer,
    item2 integer,
    item3 integer,
    item4 integer,
    item5 integer,
    item6 integer,
    item7 integer,
    item8 integer,
    item9 integer,
    item10 integer,
    item11 integer,
    item12 integer,
    item13 integer,
    item14 integer,
    item15 character varying(400)
);





CREATE SEQUENCE curso_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE curso_id_seq OWNED BY curso.id;



CREATE TABLE estado (
    id integer NOT NULL,
    denominacion character varying(30)
);





CREATE SEQUENCE estado_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE estado_id_seq OWNED BY estado.id;



CREATE TABLE inscrito (
    id integer NOT NULL,
    id_curso_aperturado integer,
    id_persona bigint,
    fecha_inscripcion date,
    id_institucion integer,
    id_estado integer,
    calificacion_final character varying(10)
);





CREATE SEQUENCE inscrito_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE inscrito_id_seq OWNED BY inscrito.id;



CREATE TABLE institucion (
    id integer NOT NULL,
    nombre character varying(200)
);





CREATE SEQUENCE institucion_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE institucion_id_seq OWNED BY institucion.id;



CREATE TABLE institucion_tipo (
    id integer NOT NULL,
    tipo character varying(20)
);





CREATE TABLE instructor (
    id integer NOT NULL,
    id_persona bigint,
    notas character varying(500) NOT NULL
);





CREATE SEQUENCE instructor_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE instructor_id_seq OWNED BY instructor.id;



CREATE TABLE preinscrito (
    id integer NOT NULL,
    fecha timestamp without time zone,
    nacionalidad character(1),
    cedula integer,
    nombres_apellidos character varying(50),
    telefono character varying(64),
    correo character varying(64),
    turno character varying(10),
    id_institucion integer
);





CREATE TABLE preinscrito_curso (
    id_preinscrito integer NOT NULL,
    id_curso integer NOT NULL,
    descartar integer DEFAULT 0 NOT NULL
);





CREATE SEQUENCE preinscrito_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE preinscrito_id_seq OWNED BY preinscrito.id;



CREATE TABLE sala (
    id integer NOT NULL,
    denominacion character varying(50),
    direccion character varying(300)
);





CREATE SEQUENCE sala_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE sala_id_seq OWNED BY sala.id;



CREATE TABLE turno_curso (
    id integer NOT NULL,
    denominacion character varying(150),
    horario character varying(30),
    dias character varying(7)
);





CREATE SEQUENCE turno_curso_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE turno_curso_id_seq OWNED BY turno_curso.id;


SET search_path = modulo_base, pg_catalog;


CREATE TABLE accion_centralizada (
    id integer NOT NULL,
    tipo character varying(3),
    codigo_centralizada character varying(15),
    denominacion_centralizada character varying
);





CREATE SEQUENCE accion_centratizada_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE accion_centratizada_id_seq OWNED BY accion_centralizada.id;



CREATE TABLE accion_especifica (
    id integer NOT NULL,
    id_accion_centralizada integer,
    codigo_especifica character varying(4),
    denominacion_especifica character varying
);





CREATE SEQUENCE accion_especifica_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE accion_especifica_id_seq OWNED BY accion_especifica.id;



CREATE TABLE accion_subespecifica (
    id integer NOT NULL,
    id_accion_especifica integer,
    codigo_subespecifica character varying(2),
    denominacion_subespecifica character varying
);





CREATE SEQUENCE accion_subespecifica_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE accion_subespecifica_id_seq OWNED BY accion_subespecifica.id;



CREATE TABLE anio_detalle (
    anio integer NOT NULL,
    mes_cerrado boolean[] DEFAULT '{f,f,f,f,f,f,f,f,f,f,f,f}'::boolean[]
);





CREATE TABLE banco (
    id integer NOT NULL,
    banco character varying(100),
    codigo character varying(4),
    eliminado boolean DEFAULT false
);





CREATE TABLE banco_cuenta (
    id integer NOT NULL,
    numero_cuenta character varying(20),
    denominacion character varying(150),
    fecha_apertura date,
    fecha_cierre date,
    cuenta_activa boolean DEFAULT true,
    id_banco integer,
    id_banco_cuenta_tipo integer,
    id_cuenta_contable character varying(15),
    activo boolean DEFAULT true
);





CREATE SEQUENCE banco_cuenta_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE banco_cuenta_id_seq OWNED BY banco_cuenta.id;



CREATE TABLE banco_cuenta_tipo (
    id integer NOT NULL,
    denominacion character varying(50),
    activo boolean DEFAULT true
);





CREATE SEQUENCE banco_cuenta_tipo_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE banco_cuenta_tipo_id_seq OWNED BY banco_cuenta_tipo.id;



CREATE SEQUENCE banco_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE banco_id_seq OWNED BY banco.id;



CREATE TABLE banco_movimiento_tipo (
    id integer NOT NULL,
    codigo character varying(2),
    denominacion character varying(40),
    operacion character(1),
    activo boolean DEFAULT true
);





CREATE TABLE cargo (
    id integer NOT NULL,
    denominacion character varying(50),
    formula character varying,
    iva boolean DEFAULT false,
    id_cuenta_presupuestaria character varying(9),
    activo boolean DEFAULT true
);





CREATE SEQUENCE cargo_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE cargo_id_seq OWNED BY cargo.id;



CREATE TABLE comprobante (
    id bigint NOT NULL,
    tipo character varying(2),
    correlativo integer NOT NULL,
    fecha date,
    concepto character varying,
    contabilizado boolean DEFAULT true,
    id_persona bigint,
    usuario character varying(15)
);





COMMENT ON COLUMN comprobante.id_persona IS 'ID del proveedor, beneficiario o personal nomina';



CREATE TABLE comprobante_bancario (
    id_comprobante bigint NOT NULL,
    id_banco_cuenta integer,
    id_banco_movimiento_tipo integer,
    numero character varying(15),
    monto numeric(20,2)
);





COMMENT ON COLUMN comprobante_bancario.numero IS 'Numero de Cheque, Notas de Débito o Crédito, Transferencia, Deposito, Retiro, ...';



CREATE TABLE comprobante_datos (
    id_comprobante bigint NOT NULL,
    dato character varying(20) NOT NULL,
    valor character varying
);





CREATE SEQUENCE comprobante_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE comprobante_id_seq OWNED BY comprobante.id;



CREATE TABLE comprobante_previo (
    id_comprobante bigint NOT NULL,
    id_comprobante_previo bigint NOT NULL
);





CREATE TABLE comprobante_previo_monto_pagado (
    id_comprobante bigint NOT NULL,
    id_comprobante_previo bigint NOT NULL,
    monto_pagado numeric(20,2) DEFAULT 0
);





COMMENT ON TABLE comprobante_previo_monto_pagado IS 'Monto pagado en el cheque para una orden de pago. id_comprobante=id_cheque y id_comprobante_previo=id_orden_pago';



CREATE TABLE comprobante_tiene_cargo (
    id_comprobante bigint NOT NULL,
    id_cargo integer NOT NULL,
    monto numeric(10,2)
);





COMMENT ON COLUMN comprobante_tiene_cargo.monto IS 'Para el tipo de comprobante OC y OS es el Monto de corrección en el calculo del cargo';



CREATE TABLE comprobante_tiene_item (
    id_comprobante bigint NOT NULL,
    id_item integer NOT NULL,
    cantidad numeric(20,2),
    costo numeric(20,4),
    descuento character varying,
    aplica_iva boolean DEFAULT true,
    id_unidad_medida integer
);





COMMENT ON TABLE comprobante_tiene_item IS 'Items para las ordenes de compra y de servicio. Comprobante del tipo OC y OS';



CREATE TABLE comprobante_tiene_requisicion_externa (
    id_comprobante bigint NOT NULL,
    id_requisicion_externa integer NOT NULL
);





CREATE TABLE comprobante_tiene_retencion (
    id_comprobante bigint NOT NULL,
    id_retencion integer NOT NULL,
    monto numeric(10,2)
);





CREATE TABLE comprobante_tipo (
    tipo character varying(2) NOT NULL,
    denominacion character varying(100)
);





CREATE TABLE configuracion (
    dato character varying(100) NOT NULL,
    valor text
);





CREATE TABLE convertidor (
    id_cuenta_contable character varying(15) NOT NULL,
    id_cuenta_presupuestaria character varying(9) NOT NULL
);





CREATE TABLE cuenta_contable (
    id_cuenta_contable character varying(15) NOT NULL,
    denominacion character varying
);





CREATE TABLE cuenta_presupuestaria (
    id_cuenta_presupuestaria character varying(9) NOT NULL,
    denominacion character varying,
    padre boolean
);





CREATE TABLE detalle_contable (
    id_comprobante bigint NOT NULL,
    id_cuenta_contable character varying(15) NOT NULL,
    operacion character(1) NOT NULL,
    monto numeric(20,2)
);





CREATE TABLE detalle_presupuestario (
    id_comprobante bigint NOT NULL,
    id_accion_subespecifica integer NOT NULL,
    id_cuenta_presupuestaria character varying(9) NOT NULL,
    operacion character varying(3) NOT NULL,
    monto numeric(20,2)
);





COMMENT ON COLUMN detalle_presupuestario.operacion IS 'NN=SIN AFECTACION


AP=APERTURA DE CUENTA


AU=AUMENTO


DI=DISMINUYE


RI=REINTEGRO


C=COMPROMETICO


CC=COMPROMETIDO, CAUSADO


CCP=COMPROMETIDO, CAUSADO, PAGADO


P=PAGADO


GC=CAUSADO';



CREATE TABLE detalle_presupuestario_operacion (
    operacion character varying(3) NOT NULL,
    denominacion character varying(50) NOT NULL
);





CREATE TABLE estado (
    codigo integer NOT NULL,
    denominacion_estado character varying(20) NOT NULL
);





CREATE SEQUENCE factura_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





CREATE TABLE factura (
    id integer DEFAULT nextval('factura_id_seq'::regclass) NOT NULL,
    id_persona integer,
    fecha date,
    numero_factura character varying(10),
    numero_control character varying(10),
    total numeric(15,2) DEFAULT 0,
    informacion_iva numeric(10,2)[],
    informacion_islr numeric(10,2)[]
);





CREATE TABLE factura_tiene_cheque (
    id_factura integer NOT NULL,
    id_comprobante_cheque bigint NOT NULL
);





CREATE TABLE formulacion (
    id integer NOT NULL,
    anio integer,
    tipo character varying(2),
    id_accion_subespecifica integer,
    id_comprobante_apertura bigint
);





COMMENT ON COLUMN formulacion.tipo IS 'F=FORMUALCION, R1=REFORMULACION 1, R2=REFORMULACION 2, ...';



CREATE TABLE formulacion_detalle (
    id_formulacion integer NOT NULL,
    id_cuenta_presupuestaria character varying(9) NOT NULL,
    monto_real numeric(20,2),
    monto_estimado numeric(20,2),
    monto numeric(20,2)[]
);





CREATE SEQUENCE formulacion_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE formulacion_id_seq OWNED BY formulacion.id;



CREATE TABLE fuente_recursos (
    id integer NOT NULL,
    codigo_fuente character varying(2),
    denominacion_fuente character varying,
    activo boolean DEFAULT true
);





CREATE SEQUENCE fuente_recursos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE fuente_recursos_id_seq OWNED BY fuente_recursos.id;



CREATE TABLE item (
    id integer NOT NULL,
    id_item_tipo integer,
    id_cuenta_presupuestaria character varying(9),
    codigo character varying(10),
    item character varying,
    aplica_iva boolean DEFAULT true,
    activo boolean DEFAULT true
);





CREATE SEQUENCE item_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE item_id_seq OWNED BY item.id;



CREATE TABLE item_tipo (
    id integer NOT NULL,
    tipo character varying,
    activo boolean DEFAULT true
);





CREATE SEQUENCE item_tipo_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE item_tipo_id_seq OWNED BY item_tipo.id;



CREATE TABLE marca (
    id integer NOT NULL,
    marca character varying(100),
    activo boolean DEFAULT true
);





CREATE SEQUENCE marca_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE marca_id_seq OWNED BY marca.id;



CREATE TABLE municipio (
    codigo_estado integer NOT NULL,
    codigo integer NOT NULL,
    denominacion_municipio character varying(30) NOT NULL
);





CREATE TABLE organismo (
    id_persona bigint NOT NULL,
    www character varying(50),
    mail character varying(50)
);





CREATE TABLE parroquia (
    codigo_estado integer NOT NULL,
    codigo_municipio integer NOT NULL,
    codigo integer NOT NULL,
    denominacion_parroquia character varying(30) NOT NULL
);





CREATE TABLE persona (
    id bigint NOT NULL,
    tipo character varying(1) DEFAULT 'N'::character varying,
    identificacion_tipo character varying(1),
    identificacion_numero bigint,
    denominacion character varying(100),
    telefono character varying(100),
    correo character varying(100),
    direccion character varying(250)
);





COMMENT ON COLUMN persona.tipo IS 'N=Natural, J=Juridico';



COMMENT ON COLUMN persona.identificacion_tipo IS 'V=Venezolano E=Extranjero P=Pasaporte G=Gobierno J=Juridico ''''=Sin Identificación';



CREATE SEQUENCE persona_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE persona_id_seq OWNED BY persona.id;



CREATE TABLE persona_juridica (
    id_persona bigint NOT NULL,
    contribuyente_tipo character varying(1),
    despacho_direccion character varying(100),
    despacho_id_estado integer,
    despacho_id_municipio integer,
    despacho_ciudad character varying(20),
    contacto_nombre character varying(20)[],
    contacto_cargo character varying(20)[],
    contacto_correo character varying(20)[],
    contacto_telefono character varying(20)[]
);





CREATE TABLE persona_natural (
    id_persona bigint NOT NULL,
    fecha_nacimiento date,
    genero character(1)
);





CREATE TABLE persona_tipo (
    tipo character varying(1) NOT NULL,
    id_cuenta_contable character varying(15)
);





CREATE TABLE requisicion_externa (
    id integer NOT NULL,
    tipo character varying(2),
    id_accion_subespecifica integer,
    correlativo integer,
    fecha date,
    concepto character varying,
    eliminado boolean DEFAULT false
);





COMMENT ON COLUMN requisicion_externa.tipo IS 'OC=Orden de Compra, OS=Orden de Servicio';



CREATE SEQUENCE requisicion_externa_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE requisicion_externa_id_seq OWNED BY requisicion_externa.id;



CREATE TABLE requisicion_externa_tiene_item (
    id_requisicion_externa integer NOT NULL,
    id_item integer NOT NULL,
    cantidad numeric(10,2) DEFAULT 0,
    id_unidad_medida integer
);





CREATE TABLE retencion (
    id integer NOT NULL,
    id_retencion_tipo integer,
    denominacion character varying(100),
    formula character varying,
    id_cuenta_contable character varying(15),
    activo boolean DEFAULT true
);





CREATE TABLE retencion_comprobante (
    id integer NOT NULL,
    id_retencion_tipo integer,
    id_persona integer,
    fecha date,
    numero integer
);





CREATE SEQUENCE retencion_comprobante_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE retencion_comprobante_id_seq OWNED BY retencion_comprobante.id;



CREATE TABLE retencion_comprobante_tiene_factura (
    id_retencion_comprobante integer NOT NULL,
    id_factura integer NOT NULL
);





CREATE SEQUENCE retencion_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE retencion_id_seq OWNED BY retencion.id;



CREATE TABLE retencion_tipo (
    id integer NOT NULL,
    denominacion character varying(10)
);





CREATE SEQUENCE retencion_tipo_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE retencion_tipo_id_seq OWNED BY retencion_tipo.id;



CREATE TABLE unidad_coordinacion (
    id integer NOT NULL,
    coordinacion character varying,
    activo boolean DEFAULT true
);





CREATE SEQUENCE unidad_coordinacion_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE unidad_coordinacion_id_seq OWNED BY unidad_coordinacion.id;



CREATE TABLE unidad_coordinacion_tiene_persona (
    id_unidad_coordinacion integer NOT NULL,
    id_persona bigint NOT NULL,
    fecha date NOT NULL,
    es_coordinador boolean DEFAULT false
);





COMMENT ON COLUMN unidad_coordinacion_tiene_persona.fecha IS 'Fecha en que la persona se une a la coordinacion';



CREATE TABLE unidad_medida (
    id integer NOT NULL,
    medida character varying(100) NOT NULL,
    cantidad_unidades numeric(10,2),
    activo boolean DEFAULT true
);





CREATE SEQUENCE unidad_medida_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE unidad_medida_id_seq OWNED BY unidad_medida.id;



CREATE TABLE usuario (
    id integer NOT NULL,
    usuario character varying(15),
    clave character varying,
    preferencias character varying,
    id_persona_responsable integer,
    activo boolean DEFAULT true
);





CREATE SEQUENCE usuario_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE usuario_id_seq OWNED BY usuario.id;



CREATE TABLE usuario_perfil (
    anio integer NOT NULL,
    id_usuario integer NOT NULL,
    perfil text NOT NULL,
    activo boolean DEFAULT true
);





CREATE TABLE usuario_perfil_acceso (
    perfil character varying(40) NOT NULL,
    acceso text
);




SET search_path = modulo_inventario, pg_catalog;


CREATE TABLE movimiento_material (
    id integer NOT NULL,
    tipo character(1) NOT NULL,
    correlativo integer NOT NULL,
    fecha date NOT NULL,
    concepto character varying(300) NOT NULL,
    id_persona bigint
);





COMMENT ON COLUMN movimiento_material.tipo IS 'E=ENTRADA, S=SALIDA';



CREATE SEQUENCE movimiento_material_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE movimiento_material_id_seq OWNED BY movimiento_material.id;



CREATE TABLE movimiento_material_tiene_factura (
    id_movimiento_material integer NOT NULL,
    id_factura integer NOT NULL
);





CREATE TABLE movimiento_material_tiene_item (
    id_movimiento_material integer NOT NULL,
    id_item integer NOT NULL,
    cantidad numeric(10,2),
    id_unidad_medida integer NOT NULL
);





CREATE TABLE movimiento_material_tiene_requisicion (
    id_movimiento_material integer NOT NULL,
    id_requisicion integer NOT NULL
);





CREATE TABLE requisicion (
    id integer NOT NULL,
    id_item_tipo integer NOT NULL,
    correlativo integer NOT NULL,
    fecha date NOT NULL,
    id_unidad_coordinacion integer NOT NULL,
    concepto character varying,
    estado character(1) DEFAULT 'B'::bpchar NOT NULL,
    id_persona bigint
);





COMMENT ON COLUMN requisicion.estado IS 'B=Borrador E=Enviado R=Rechazado C=Corregir D=Despachado';



CREATE SEQUENCE requisicion_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE requisicion_id_seq OWNED BY requisicion.id;



CREATE TABLE requisicion_observacion (
    id_requisicion integer NOT NULL,
    fecha timestamp without time zone DEFAULT now() NOT NULL,
    observacion character varying(250),
    id_persona_observacion integer
);





CREATE TABLE requisicion_tiene_item (
    id_requisicion integer NOT NULL,
    id_item integer NOT NULL,
    cantidad numeric(10,2)
);





COMMENT ON COLUMN requisicion_tiene_item.cantidad IS 'siempre en unidades';


SET search_path = modulo_nomina, pg_catalog;


CREATE TABLE grupo_familiar (
    id integer NOT NULL,
    id_ficha integer NOT NULL,
    id_parentesco integer NOT NULL,
    id_persona bigint NULL,
    nacionalidad character varying(1),
    cedula bigint,
    nombres_apellidos character varying(100),
    genero character varying(1),
    fecha_nacimiento date
);

CREATE SEQUENCE grupo_familiar_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


CREATE TABLE grupo_familiar_parentesco (
    id integer NOT NULL,
    parentesco character varying(30),
    activo boolean DEFAULT true
);


CREATE SEQUENCE grupo_familiar_parentesco_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


CREATE TABLE cargo (
    id integer NOT NULL,
    cargo character varying(20),
    denominacion character varying(50),
    orden integer DEFAULT 0,
    activo boolean DEFAULT true
);





CREATE SEQUENCE cargo_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE cargo_id_seq OWNED BY cargo.id;



CREATE TABLE concepto (
    id integer NOT NULL,
    codigo character varying(10),
    concepto character varying(80),
    identificador character varying(40),
    tipo character varying(2),
    orden integer DEFAULT 0,
    activo boolean DEFAULT true
);





COMMENT ON COLUMN concepto.tipo IS 'A=Asignación, D=Deducción, AP=Aporte Patronal, Vacio=Solo para realizar cálculos, _=Solo para calculos pero visibles';



CREATE TABLE concepto_cuenta (
    id_concepto integer NOT NULL,
    id_nomina integer NOT NULL,
    fecha date NOT NULL,
    id_cuenta_presupuestaria character varying(9),
    id_cuenta_presupuestaria_ap character varying(9),
    id_cuenta_contable character varying(15),
    id_cuenta_contable_ap character varying(15)
);





CREATE TABLE concepto_formula (
    id_concepto integer NOT NULL,
    fecha date NOT NULL,
    definicion character varying(1000),
    definicion_ap character varying(1000)
);





CREATE SEQUENCE concepto_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE concepto_id_seq OWNED BY concepto.id;



CREATE TABLE concepto_periodo (
    id_periodo integer NOT NULL,
    id_concepto integer NOT NULL,
    id_nomina integer NOT NULL
);





CREATE TABLE concepto_presupuesto_contabilidad (
    id_nomina integer NOT NULL,
    id_concepto integer NOT NULL,
    fecha date NOT NULL,
    id_cuenta_presupuestaria character varying(9),
    id_cuenta_presupuestaria_ap character varying(9),
    id_cuenta_contable character varying(15),
    id_cuenta_contable_ap character varying(15)
);





CREATE TABLE escala_salarial (
    id integer NOT NULL,
    escala character varying(50),
    sueldo_basico numeric(20,2)
);





CREATE TABLE escala_salarial_configuracion (
    campo character varying(20) NOT NULL,
    definicion character varying(250)
);





CREATE SEQUENCE escala_salarial_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE escala_salarial_id_seq OWNED BY escala_salarial.id;



CREATE TABLE ficha (
    id integer NOT NULL,
    id_persona bigint,
    fecha_ingreso date[],
    fecha_egreso date[],
    codigo character varying(10),
    extension character varying(10),
    activo boolean DEFAULT true,
    cuenta_nomina character varying(20),
    id_escala_salarial integer,
    antiguedad_apn integer default NULL,
    profesionalizacion_porcentaje integer default NULL
);





COMMENT ON COLUMN ficha.codigo IS 'Código en el sistema de asistencia';



CREATE TABLE ficha_cargo (
    id_ficha integer NOT NULL,
    id_cargo integer NOT NULL,
    fecha date NOT NULL
);





CREATE TABLE ficha_concepto (
    id_ficha integer NOT NULL,
    id_concepto integer NOT NULL,
    id_periodo integer NOT NULL,
    valor numeric(10,2),
    id_nomina integer NOT NULL
);





COMMENT ON COLUMN ficha_concepto.valor IS 'Si el concepto no es formula, lleva el valor';



CREATE TABLE ficha_cuenta_nomina (
    id_nomina integer NOT NULL,
    id_ficha integer NOT NULL,
    numero_cuenta character varying(20)
);





CREATE TABLE ficha_estructura_presupuestaria (
    id_ficha integer NOT NULL,
    fecha date NOT NULL,
    id_accion_subespecifica integer
);





CREATE SEQUENCE ficha_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE ficha_id_seq OWNED BY ficha.id;



CREATE TABLE nomina (
    id integer NOT NULL,
    codigo character varying(10),
    nomina character varying(100),
    tipo character varying(1),
    activo boolean DEFAULT true
);





COMMENT ON COLUMN nomina.tipo IS 'Q=Quincenal, M=Mensual, T=Trimestral, S=Semestral, A=Anual';



CREATE TABLE nomina_banco_cuenta (
    id_nomina integer NOT NULL,
    id_banco_cuenta integer NOT NULL
);





CREATE SEQUENCE nomina_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE nomina_id_seq OWNED BY nomina.id;



CREATE TABLE periodo (
    id integer NOT NULL,
    codigo character varying(10),
    fecha_inicio date,
    fecha_culminacion date,
    cerrado boolean DEFAULT false,
    contabilizado bigint,
    tipo character varying(1),
    descripcion character varying(100)
);





CREATE SEQUENCE periodo_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE periodo_id_seq OWNED BY periodo.id;



CREATE TABLE periodo_nota (
    id_periodo integer NOT NULL,
    id_nomina integer NOT NULL,
    nota character varying
);





CREATE TABLE periodo_tipo (
    tipo character varying(1) NOT NULL,
    denominacion character varying(50),
    activo boolean DEFAULT true
);





CREATE TABLE prestamo (
    id integer NOT NULL,
    id_nomina integer,
    id_ficha integer,
    id_concepto integer,
    tipo character varying(1),
    fecha date,
    monto numeric(20,2),
    cuotas integer,
    cuotas_definicion numeric(20,2)[],
    cancelado boolean DEFAULT false
);





CREATE TABLE prestamo_cuota (
    id_prestamo integer NOT NULL,
    id_periodo integer NOT NULL,
    cuota_numero integer
);





CREATE SEQUENCE prestamo_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE prestamo_id_seq OWNED BY prestamo.id;


SET search_path = modulo_planificacion, pg_catalog;


CREATE TABLE meta_fisica (
    id integer NOT NULL,
    id_tipo integer,
    id_accion_especifica integer,
    id_unidad_coordinacion integer,
    id_responsable integer,
    id_unidad_medida integer,
    codigo character varying(15),
    actividad character varying,
    meta integer[]
);





CREATE SEQUENCE meta_fisica_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE meta_fisica_id_seq OWNED BY meta_fisica.id;



CREATE TABLE meta_fisica_informe (
    id_meta_fisica integer NOT NULL,
    mes integer NOT NULL,
    cantidad integer,
    cantidad_masculino integer,
    cantidad_femenino integer,
    comunidad character varying,
    logros character varying,
    obstaculos character varying,
    adjunto character varying[]
);





CREATE TABLE meta_fisica_tipo (
    id integer NOT NULL,
    tipo character varying(10)
);





CREATE SEQUENCE meta_fisica_tipo_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE meta_fisica_tipo_id_seq OWNED BY meta_fisica_tipo.id;



CREATE TABLE unidad_medida (
    id integer NOT NULL,
    unidad_medida character varying,
    activo boolean DEFAULT true
);





CREATE SEQUENCE unidad_medida_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE unidad_medida_id_seq OWNED BY unidad_medida.id;


SET search_path = modulo_planificador, pg_catalog;


CREATE TABLE tarea (
    id_unidad_coordinacion integer,
    id bigint NOT NULL,
    titulo character varying,
    descripcion character varying,
    fecha_inicio timestamp without time zone,
    duracion integer,
    progreso double precision,
    orden double precision,
    padre bigint
);





CREATE TABLE tarea_enlace (
    id bigint NOT NULL,
    origen bigint,
    destino bigint,
    tipo character varying
);





CREATE SEQUENCE tarea_enlace_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE tarea_enlace_id_seq OWNED BY tarea_enlace.id;



CREATE SEQUENCE tarea_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;





ALTER SEQUENCE tarea_id_seq OWNED BY tarea.id;


SET search_path = modulo_asistencia, pg_catalog;


ALTER TABLE ONLY asistencia ALTER COLUMN id SET DEFAULT nextval('asistencia_id_seq'::regclass);



ALTER TABLE ONLY asistencia_nota ALTER COLUMN id SET DEFAULT nextval('asistencia_nota_id_seq'::regclass);



ALTER TABLE ONLY asistencia_visitante ALTER COLUMN id SET DEFAULT nextval('asistencia_visitante_id_seq'::regclass);


SET search_path = modulo_asl, pg_catalog;


ALTER TABLE ONLY curso ALTER COLUMN id SET DEFAULT nextval('curso_id_seq'::regclass);



ALTER TABLE ONLY curso_aperturado ALTER COLUMN id SET DEFAULT nextval('curso_aperturado_id_seq'::regclass);



ALTER TABLE ONLY estado ALTER COLUMN id SET DEFAULT nextval('estado_id_seq'::regclass);



ALTER TABLE ONLY inscrito ALTER COLUMN id SET DEFAULT nextval('inscrito_id_seq'::regclass);



ALTER TABLE ONLY institucion ALTER COLUMN id SET DEFAULT nextval('institucion_id_seq'::regclass);



ALTER TABLE ONLY instructor ALTER COLUMN id SET DEFAULT nextval('instructor_id_seq'::regclass);



ALTER TABLE ONLY preinscrito ALTER COLUMN id SET DEFAULT nextval('preinscrito_id_seq'::regclass);



ALTER TABLE ONLY sala ALTER COLUMN id SET DEFAULT nextval('sala_id_seq'::regclass);



ALTER TABLE ONLY turno_curso ALTER COLUMN id SET DEFAULT nextval('turno_curso_id_seq'::regclass);


SET search_path = modulo_base, pg_catalog;


ALTER TABLE ONLY accion_centralizada ALTER COLUMN id SET DEFAULT nextval('accion_centratizada_id_seq'::regclass);



ALTER TABLE ONLY accion_especifica ALTER COLUMN id SET DEFAULT nextval('accion_especifica_id_seq'::regclass);



ALTER TABLE ONLY accion_subespecifica ALTER COLUMN id SET DEFAULT nextval('accion_subespecifica_id_seq'::regclass);



ALTER TABLE ONLY banco ALTER COLUMN id SET DEFAULT nextval('banco_id_seq'::regclass);



ALTER TABLE ONLY banco_cuenta ALTER COLUMN id SET DEFAULT nextval('banco_cuenta_id_seq'::regclass);



ALTER TABLE ONLY banco_cuenta_tipo ALTER COLUMN id SET DEFAULT nextval('banco_cuenta_tipo_id_seq'::regclass);



ALTER TABLE ONLY cargo ALTER COLUMN id SET DEFAULT nextval('cargo_id_seq'::regclass);



ALTER TABLE ONLY comprobante ALTER COLUMN id SET DEFAULT nextval('comprobante_id_seq'::regclass);



ALTER TABLE ONLY formulacion ALTER COLUMN id SET DEFAULT nextval('formulacion_id_seq'::regclass);



ALTER TABLE ONLY fuente_recursos ALTER COLUMN id SET DEFAULT nextval('fuente_recursos_id_seq'::regclass);



ALTER TABLE ONLY item ALTER COLUMN id SET DEFAULT nextval('item_id_seq'::regclass);



ALTER TABLE ONLY item_tipo ALTER COLUMN id SET DEFAULT nextval('item_tipo_id_seq'::regclass);



ALTER TABLE ONLY marca ALTER COLUMN id SET DEFAULT nextval('marca_id_seq'::regclass);



ALTER TABLE ONLY persona ALTER COLUMN id SET DEFAULT nextval('persona_id_seq'::regclass);



ALTER TABLE ONLY requisicion_externa ALTER COLUMN id SET DEFAULT nextval('requisicion_externa_id_seq'::regclass);



ALTER TABLE ONLY retencion ALTER COLUMN id SET DEFAULT nextval('retencion_id_seq'::regclass);



ALTER TABLE ONLY retencion_comprobante ALTER COLUMN id SET DEFAULT nextval('retencion_comprobante_id_seq'::regclass);



ALTER TABLE ONLY retencion_tipo ALTER COLUMN id SET DEFAULT nextval('retencion_tipo_id_seq'::regclass);



ALTER TABLE ONLY unidad_coordinacion ALTER COLUMN id SET DEFAULT nextval('unidad_coordinacion_id_seq'::regclass);



ALTER TABLE ONLY unidad_medida ALTER COLUMN id SET DEFAULT nextval('unidad_medida_id_seq'::regclass);



ALTER TABLE ONLY usuario ALTER COLUMN id SET DEFAULT nextval('usuario_id_seq'::regclass);


SET search_path = modulo_inventario, pg_catalog;


ALTER TABLE ONLY movimiento_material ALTER COLUMN id SET DEFAULT nextval('movimiento_material_id_seq'::regclass);



ALTER TABLE ONLY requisicion ALTER COLUMN id SET DEFAULT nextval('requisicion_id_seq'::regclass);


SET search_path = modulo_nomina, pg_catalog;


ALTER TABLE ONLY grupo_familiar ALTER COLUMN id SET DEFAULT nextval('grupo_familiar_id_seq'::regclass);



ALTER TABLE ONLY grupo_familiar_parentesco ALTER COLUMN id SET DEFAULT nextval('grupo_familiar_parentesco_id_seq'::regclass);



ALTER TABLE ONLY cargo ALTER COLUMN id SET DEFAULT nextval('cargo_id_seq'::regclass);



ALTER TABLE ONLY concepto ALTER COLUMN id SET DEFAULT nextval('concepto_id_seq'::regclass);



ALTER TABLE ONLY escala_salarial ALTER COLUMN id SET DEFAULT nextval('escala_salarial_id_seq'::regclass);



ALTER TABLE ONLY ficha ALTER COLUMN id SET DEFAULT nextval('ficha_id_seq'::regclass);



ALTER TABLE ONLY nomina ALTER COLUMN id SET DEFAULT nextval('nomina_id_seq'::regclass);



ALTER TABLE ONLY periodo ALTER COLUMN id SET DEFAULT nextval('periodo_id_seq'::regclass);



ALTER TABLE ONLY prestamo ALTER COLUMN id SET DEFAULT nextval('prestamo_id_seq'::regclass);


SET search_path = modulo_planificacion, pg_catalog;


ALTER TABLE ONLY meta_fisica ALTER COLUMN id SET DEFAULT nextval('meta_fisica_id_seq'::regclass);



ALTER TABLE ONLY meta_fisica_tipo ALTER COLUMN id SET DEFAULT nextval('meta_fisica_tipo_id_seq'::regclass);



ALTER TABLE ONLY unidad_medida ALTER COLUMN id SET DEFAULT nextval('unidad_medida_id_seq'::regclass);


SET search_path = modulo_planificador, pg_catalog;


ALTER TABLE ONLY tarea ALTER COLUMN id SET DEFAULT nextval('tarea_id_seq'::regclass);



ALTER TABLE ONLY tarea_enlace ALTER COLUMN id SET DEFAULT nextval('tarea_enlace_id_seq'::regclass);


SET search_path = modulo_asistencia, pg_catalog;


ALTER TABLE ONLY asistencia_nota
    ADD CONSTRAINT asistencia_nota_pkey PRIMARY KEY (id);



ALTER TABLE ONLY asistencia
    ADD CONSTRAINT asistencia_pkey PRIMARY KEY (id);



ALTER TABLE ONLY asistencia_visitante
    ADD CONSTRAINT asistencia_visitante_pkey PRIMARY KEY (id);


SET search_path = modulo_asl, pg_catalog;


ALTER TABLE ONLY contenido_curso
    ADD CONSTRAINT contenido_curso_pkey PRIMARY KEY (id_curso, orden);



ALTER TABLE ONLY curso_aperturado
    ADD CONSTRAINT curso_aperturado_codigo_key UNIQUE (codigo);



ALTER TABLE ONLY curso_aperturado
    ADD CONSTRAINT curso_aperturado_pkey PRIMARY KEY (id);



ALTER TABLE ONLY curso_encuesta
    ADD CONSTRAINT curso_encuesta_pkey PRIMARY KEY (id_curso_aperturado, id_persona);



ALTER TABLE ONLY curso
    ADD CONSTRAINT curso_pkey PRIMARY KEY (id);



ALTER TABLE ONLY estado
    ADD CONSTRAINT estado_pkey PRIMARY KEY (id);



ALTER TABLE ONLY inscrito
    ADD CONSTRAINT inscrito_pkey PRIMARY KEY (id);



ALTER TABLE ONLY institucion
    ADD CONSTRAINT institucion_pkey PRIMARY KEY (id);



ALTER TABLE ONLY institucion_tipo
    ADD CONSTRAINT institucion_tipo_pkey PRIMARY KEY (id);



ALTER TABLE ONLY instructor
    ADD CONSTRAINT instructor_pkey PRIMARY KEY (id);



ALTER TABLE ONLY preinscrito_curso
    ADD CONSTRAINT preinscrito_curso_pkey PRIMARY KEY (id_preinscrito, id_curso);



ALTER TABLE ONLY preinscrito
    ADD CONSTRAINT preinscrito_fecha_nacionalidad_cedula_key UNIQUE (fecha, nacionalidad, cedula);



ALTER TABLE ONLY preinscrito
    ADD CONSTRAINT preinscrito_pkey PRIMARY KEY (id);



ALTER TABLE ONLY sala
    ADD CONSTRAINT sala_pkey PRIMARY KEY (id);



ALTER TABLE ONLY turno_curso
    ADD CONSTRAINT turno_curso_pkey PRIMARY KEY (id);


SET search_path = modulo_base, pg_catalog;


ALTER TABLE ONLY accion_centralizada
    ADD CONSTRAINT accion_centratizada_pkey PRIMARY KEY (id);



ALTER TABLE ONLY accion_especifica
    ADD CONSTRAINT accion_especifica_pkey PRIMARY KEY (id);



ALTER TABLE ONLY accion_subespecifica
    ADD CONSTRAINT accion_subespecifica_pkey PRIMARY KEY (id);



ALTER TABLE ONLY anio_detalle
    ADD CONSTRAINT anio_detalle_pkey PRIMARY KEY (anio);



ALTER TABLE ONLY banco_cuenta
    ADD CONSTRAINT banco_cuenta_pkey PRIMARY KEY (id);



ALTER TABLE ONLY banco_cuenta_tipo
    ADD CONSTRAINT banco_cuenta_tipo_pkey PRIMARY KEY (id);



ALTER TABLE ONLY banco_movimiento_tipo
    ADD CONSTRAINT banco_movimiento_tipo_codigo_key UNIQUE (codigo);



ALTER TABLE ONLY banco_movimiento_tipo
    ADD CONSTRAINT banco_movimiento_tipo_pkey PRIMARY KEY (id);



ALTER TABLE ONLY banco
    ADD CONSTRAINT banco_pkey PRIMARY KEY (id);



ALTER TABLE ONLY cargo
    ADD CONSTRAINT cargo_pkey PRIMARY KEY (id);



ALTER TABLE ONLY comprobante_bancario
    ADD CONSTRAINT comprobante_bancario_pkey PRIMARY KEY (id_comprobante);



ALTER TABLE ONLY comprobante_tiene_cargo
    ADD CONSTRAINT comprobante_cargo_pkey PRIMARY KEY (id_comprobante, id_cargo);



ALTER TABLE ONLY comprobante_datos
    ADD CONSTRAINT comprobante_data_pkey PRIMARY KEY (id_comprobante, dato);



ALTER TABLE ONLY comprobante_tiene_item
    ADD CONSTRAINT comprobante_item_pkey PRIMARY KEY (id_comprobante, id_item);



ALTER TABLE ONLY comprobante
    ADD CONSTRAINT comprobante_pkey PRIMARY KEY (id);



ALTER TABLE ONLY comprobante_previo_monto_pagado
    ADD CONSTRAINT comprobante_previo_monto_pagado_pkey PRIMARY KEY (id_comprobante, id_comprobante_previo);



ALTER TABLE ONLY comprobante_previo
    ADD CONSTRAINT comprobante_previo_pkey PRIMARY KEY (id_comprobante, id_comprobante_previo);



ALTER TABLE ONLY comprobante_tiene_requisicion_externa
    ADD CONSTRAINT comprobante_tiene_requisicion_externa_pkey PRIMARY KEY (id_comprobante, id_requisicion_externa);



ALTER TABLE ONLY comprobante_tiene_retencion
    ADD CONSTRAINT comprobante_tiene_retencion_pkey PRIMARY KEY (id_comprobante, id_retencion);



ALTER TABLE ONLY comprobante_tipo
    ADD CONSTRAINT comprobante_tipo_pkey PRIMARY KEY (tipo);



ALTER TABLE ONLY configuracion
    ADD CONSTRAINT configuracion_pkey PRIMARY KEY (dato);



ALTER TABLE ONLY convertidor
    ADD CONSTRAINT convertidor_pkey PRIMARY KEY (id_cuenta_contable, id_cuenta_presupuestaria);



ALTER TABLE ONLY cuenta_contable
    ADD CONSTRAINT cuenta_contable_pkey PRIMARY KEY (id_cuenta_contable);



ALTER TABLE ONLY cuenta_presupuestaria
    ADD CONSTRAINT cuenta_presupuestaria_pkey PRIMARY KEY (id_cuenta_presupuestaria);



ALTER TABLE ONLY detalle_contable
    ADD CONSTRAINT detalle_contable_pkey PRIMARY KEY (id_comprobante, id_cuenta_contable, operacion);



ALTER TABLE ONLY detalle_presupuestario_operacion
    ADD CONSTRAINT detalle_presupuestario_operacion_pkey PRIMARY KEY (operacion);



ALTER TABLE ONLY estado
    ADD CONSTRAINT estado_pkey PRIMARY KEY (codigo);



ALTER TABLE ONLY factura
    ADD CONSTRAINT factura_pkey PRIMARY KEY (id);



ALTER TABLE ONLY factura_tiene_cheque
    ADD CONSTRAINT factura_tiene_cheque_pkey PRIMARY KEY (id_factura, id_comprobante_cheque);



ALTER TABLE ONLY formulacion_detalle
    ADD CONSTRAINT formulacion_detalle_pkey PRIMARY KEY (id_formulacion, id_cuenta_presupuestaria);



ALTER TABLE ONLY formulacion
    ADD CONSTRAINT formulacion_pkey PRIMARY KEY (id);



ALTER TABLE ONLY fuente_recursos
    ADD CONSTRAINT fuente_recursos_pkey PRIMARY KEY (id);



ALTER TABLE ONLY item
    ADD CONSTRAINT item_id_item_tipo_codigo_key UNIQUE (id_item_tipo, codigo);



ALTER TABLE ONLY item
    ADD CONSTRAINT item_pkey PRIMARY KEY (id);



ALTER TABLE ONLY item_tipo
    ADD CONSTRAINT item_tipo_pkey PRIMARY KEY (id);



ALTER TABLE ONLY marca
    ADD CONSTRAINT marca_pkey PRIMARY KEY (id);



ALTER TABLE ONLY municipio
    ADD CONSTRAINT municipio_pkey PRIMARY KEY (codigo_estado, codigo);



ALTER TABLE ONLY organismo
    ADD CONSTRAINT organismo_pkey PRIMARY KEY (id_persona);



ALTER TABLE ONLY parroquia
    ADD CONSTRAINT parroquia_pkey PRIMARY KEY (codigo_estado, codigo_municipio, codigo);



ALTER TABLE ONLY persona_tipo
    ADD CONSTRAINT persona_cuenta_contable_pkey PRIMARY KEY (tipo);



ALTER TABLE ONLY persona
    ADD CONSTRAINT persona_identificacion_tipo_identificacion_numero_key UNIQUE (identificacion_tipo, identificacion_numero);



ALTER TABLE ONLY persona_juridica
    ADD CONSTRAINT persona_juridica_pkey PRIMARY KEY (id_persona);



ALTER TABLE ONLY persona_natural
    ADD CONSTRAINT persona_natural_pkey PRIMARY KEY (id_persona);



ALTER TABLE ONLY persona
    ADD CONSTRAINT persona_pkey PRIMARY KEY (id);



ALTER TABLE ONLY requisicion_externa
    ADD CONSTRAINT requisicion_externa_pkey PRIMARY KEY (id);



ALTER TABLE ONLY requisicion_externa_tiene_item
    ADD CONSTRAINT requisicion_externa_tiene_item_pkey PRIMARY KEY (id_requisicion_externa, id_item);



ALTER TABLE ONLY retencion_comprobante
    ADD CONSTRAINT retencion_comprobante_pkey PRIMARY KEY (id);



ALTER TABLE ONLY retencion_comprobante_tiene_factura
    ADD CONSTRAINT retencion_comprobante_tiene_factura_pkey PRIMARY KEY (id_retencion_comprobante, id_factura);



ALTER TABLE ONLY retencion
    ADD CONSTRAINT retencion_pkey PRIMARY KEY (id);



ALTER TABLE ONLY retencion_tipo
    ADD CONSTRAINT retencion_tipo_pkey PRIMARY KEY (id);



ALTER TABLE ONLY unidad_coordinacion
    ADD CONSTRAINT unidad_coordinacion_pkey PRIMARY KEY (id);



ALTER TABLE ONLY unidad_coordinacion_tiene_persona
    ADD CONSTRAINT unidad_coordinacion_tiene_persona_pkey PRIMARY KEY (id_unidad_coordinacion, id_persona, fecha);



ALTER TABLE ONLY unidad_medida
    ADD CONSTRAINT unidad_medida_pkey PRIMARY KEY (id);



ALTER TABLE ONLY usuario_perfil_acceso
    ADD CONSTRAINT usuario_acceso_perfil_pkey PRIMARY KEY (perfil);



ALTER TABLE ONLY usuario_perfil
    ADD CONSTRAINT usuario_acceso_pkey PRIMARY KEY (anio, id_usuario);



ALTER TABLE ONLY usuario
    ADD CONSTRAINT usuario_pkey PRIMARY KEY (id);

ALTER TABLE usuario CLUSTER ON usuario_pkey;



ALTER TABLE ONLY usuario
    ADD CONSTRAINT usuario_usuario_key UNIQUE (usuario);


SET search_path = modulo_inventario, pg_catalog;


ALTER TABLE ONLY movimiento_material
    ADD CONSTRAINT movimiento_material_pkey PRIMARY KEY (id);



ALTER TABLE ONLY movimiento_material_tiene_factura
    ADD CONSTRAINT movimiento_material_tiene_factura_pkey PRIMARY KEY (id_movimiento_material, id_factura);



ALTER TABLE ONLY movimiento_material_tiene_item
    ADD CONSTRAINT movimiento_material_tiene_item_pkey PRIMARY KEY (id_movimiento_material, id_item, id_unidad_medida);



ALTER TABLE ONLY movimiento_material_tiene_requisicion
    ADD CONSTRAINT movimiento_material_tiene_requisicion_pkey PRIMARY KEY (id_movimiento_material, id_requisicion);



ALTER TABLE ONLY requisicion_tiene_item
    ADD CONSTRAINT requisicion_item_pkey PRIMARY KEY (id_requisicion, id_item);



ALTER TABLE ONLY requisicion_observacion
    ADD CONSTRAINT requisicion_observacion_pkey PRIMARY KEY (id_requisicion, fecha);



ALTER TABLE ONLY requisicion
    ADD CONSTRAINT requisicion_pkey PRIMARY KEY (id);


SET search_path = modulo_nomina, pg_catalog;


ALTER TABLE ONLY grupo_familiar_parentesco
    ADD CONSTRAINT grupo_familiar_parentesco_pkey PRIMARY KEY (id);



ALTER TABLE ONLY grupo_familiar
    ADD CONSTRAINT grupo_familiar_pkey PRIMARY KEY (id_ficha);



ALTER TABLE ONLY cargo
    ADD CONSTRAINT cargo_pkey PRIMARY KEY (id);



ALTER TABLE ONLY concepto_formula
    ADD CONSTRAINT concepto_formula_pkey PRIMARY KEY (id_concepto, fecha);



ALTER TABLE ONLY concepto_cuenta
    ADD CONSTRAINT concepto_nomina_cuenta_pkey PRIMARY KEY (id_concepto, id_nomina, fecha);



ALTER TABLE ONLY concepto_periodo
    ADD CONSTRAINT concepto_periodo_pkey PRIMARY KEY (id_periodo, id_concepto, id_nomina);



ALTER TABLE ONLY concepto
    ADD CONSTRAINT concepto_pkey PRIMARY KEY (id);



ALTER TABLE ONLY concepto_presupuesto_contabilidad
    ADD CONSTRAINT concepto_presupuesto_contabilidad_pkey PRIMARY KEY (id_nomina, id_concepto, fecha);



ALTER TABLE ONLY escala_salarial_configuracion
    ADD CONSTRAINT escala_salarial_configuracion_pkey PRIMARY KEY (campo);



ALTER TABLE ONLY escala_salarial
    ADD CONSTRAINT escala_salarial_pkey PRIMARY KEY (id);



ALTER TABLE ONLY ficha_cargo
    ADD CONSTRAINT ficha_cargo_pkey PRIMARY KEY (id_ficha, id_cargo, fecha);



ALTER TABLE ONLY ficha
    ADD CONSTRAINT ficha_codigo_key UNIQUE (codigo);



ALTER TABLE ONLY ficha_concepto
    ADD CONSTRAINT ficha_concepto_pkey PRIMARY KEY (id_ficha, id_concepto, id_periodo, id_nomina);



ALTER TABLE ONLY ficha_estructura_presupuestaria
    ADD CONSTRAINT ficha_estructura_presupuestaria_pkey PRIMARY KEY (id_ficha, fecha);



ALTER TABLE ONLY ficha
    ADD CONSTRAINT ficha_id_persona_key UNIQUE (id_persona);



ALTER TABLE ONLY ficha
    ADD CONSTRAINT ficha_pkey PRIMARY KEY (id);



ALTER TABLE ONLY nomina_banco_cuenta
    ADD CONSTRAINT nomina_banco_cuenta_pkey PRIMARY KEY (id_nomina, id_banco_cuenta);



ALTER TABLE ONLY nomina
    ADD CONSTRAINT nomina_codigo_key UNIQUE (codigo);



ALTER TABLE ONLY ficha_cuenta_nomina
    ADD CONSTRAINT nomina_ficha_pkey PRIMARY KEY (id_nomina, id_ficha);



ALTER TABLE ONLY nomina
    ADD CONSTRAINT nomina_pkey PRIMARY KEY (id);



ALTER TABLE ONLY periodo_nota
    ADD CONSTRAINT periodo_nota_pkey PRIMARY KEY (id_periodo, id_nomina);



ALTER TABLE ONLY periodo
    ADD CONSTRAINT periodo_pkey PRIMARY KEY (id);



ALTER TABLE ONLY periodo_tipo
    ADD CONSTRAINT periodo_tipo_pkey PRIMARY KEY (tipo);



ALTER TABLE ONLY prestamo_cuota
    ADD CONSTRAINT prestamo_cuota_pkey PRIMARY KEY (id_prestamo, id_periodo);



ALTER TABLE ONLY prestamo
    ADD CONSTRAINT prestamo_pkey PRIMARY KEY (id);


SET search_path = modulo_planificacion, pg_catalog;


ALTER TABLE ONLY meta_fisica_informe
    ADD CONSTRAINT meta_fisica_ejecutado_pkey PRIMARY KEY (id_meta_fisica, mes);



ALTER TABLE ONLY meta_fisica
    ADD CONSTRAINT meta_fisica_pkey PRIMARY KEY (id);



ALTER TABLE ONLY meta_fisica_tipo
    ADD CONSTRAINT meta_fisica_tipo_pkey PRIMARY KEY (id);



ALTER TABLE ONLY unidad_medida
    ADD CONSTRAINT unidad_medida_pkey PRIMARY KEY (id);


SET search_path = modulo_planificador, pg_catalog;


ALTER TABLE ONLY tarea_enlace
    ADD CONSTRAINT tarea_enlace_pkey PRIMARY KEY (id);



ALTER TABLE ONLY tarea
    ADD CONSTRAINT tarea_pkey PRIMARY KEY (id);


SET search_path = modulo_base, pg_catalog;


CREATE TRIGGER _comprobante_bancario_delete BEFORE DELETE ON comprobante_bancario FOR EACH ROW EXECUTE PROCEDURE public._comprobante_bancario_delete();



CREATE TRIGGER _comprobante_bancario_insert BEFORE INSERT ON comprobante_bancario FOR EACH ROW EXECUTE PROCEDURE public._comprobante_bancario_insert();



CREATE TRIGGER _comprobante_bancario_update AFTER UPDATE ON comprobante_bancario FOR EACH ROW EXECUTE PROCEDURE public._comprobante_bancario_update();



CREATE TRIGGER _comprobante_insert_update_delete BEFORE INSERT OR DELETE OR UPDATE ON comprobante FOR EACH ROW EXECUTE PROCEDURE public._comprobante_insert_update_delete();



CREATE TRIGGER _detalle_contable_insert_update_delete BEFORE INSERT OR DELETE OR UPDATE ON detalle_contable FOR EACH ROW EXECUTE PROCEDURE public._detalle_contable_insert_update_delete();



CREATE TRIGGER _detalle_presupuestario_delete BEFORE DELETE ON detalle_presupuestario FOR EACH ROW EXECUTE PROCEDURE public._detalle_presupuestario_delete();



CREATE TRIGGER _detalle_presupuestario_insert BEFORE INSERT ON detalle_presupuestario FOR EACH ROW EXECUTE PROCEDURE public._detalle_presupuestario_insert();



CREATE TRIGGER _detalle_presupuestario_update AFTER UPDATE ON detalle_presupuestario FOR EACH ROW EXECUTE PROCEDURE public._detalle_presupuestario_update();


SET search_path = modulo_inventario, pg_catalog;


CREATE TRIGGER movimiento_material_delete BEFORE DELETE ON movimiento_material_tiene_item FOR EACH ROW EXECUTE PROCEDURE movimiento_material_delete();



CREATE TRIGGER movimiento_material_insert BEFORE INSERT ON movimiento_material_tiene_item FOR EACH ROW EXECUTE PROCEDURE movimiento_material_insert();



CREATE TRIGGER movimiento_material_update BEFORE UPDATE ON movimiento_material_tiene_item FOR EACH ROW EXECUTE PROCEDURE movimiento_material_update();


SET search_path = modulo_asistencia, pg_catalog;


ALTER TABLE ONLY asistencia
    ADD CONSTRAINT asistencia_id_persona_fkey FOREIGN KEY (id_persona) REFERENCES modulo_base.persona(id);



ALTER TABLE ONLY asistencia_nota
    ADD CONSTRAINT asistencia_nota_id_persona_fkey FOREIGN KEY (id_persona) REFERENCES modulo_base.persona(id);



ALTER TABLE ONLY asistencia_visitante
    ADD CONSTRAINT asistencia_visitante_id_persona_fkey FOREIGN KEY (id_persona) REFERENCES modulo_base.persona(id);



ALTER TABLE ONLY asistencia_visitante
    ADD CONSTRAINT asistencia_visitante_id_unidad_coordinacion_fkey FOREIGN KEY (id_unidad_coordinacion) REFERENCES modulo_base.unidad_coordinacion(id);


SET search_path = modulo_asl, pg_catalog;


ALTER TABLE ONLY curso_aperturado
    ADD CONSTRAINT curso_aperturado_id_curso_fkey FOREIGN KEY (id_curso) REFERENCES curso(id);



ALTER TABLE ONLY curso_aperturado
    ADD CONSTRAINT curso_aperturado_id_instructor_fkey FOREIGN KEY (id_instructor) REFERENCES instructor(id);



ALTER TABLE ONLY curso_aperturado
    ADD CONSTRAINT curso_aperturado_id_instructor_secundario_fkey FOREIGN KEY (id_instructor_secundario) REFERENCES instructor(id);



ALTER TABLE ONLY curso_aperturado
    ADD CONSTRAINT curso_aperturado_id_sala_fkey FOREIGN KEY (id_sala) REFERENCES sala(id);



ALTER TABLE ONLY curso_aperturado
    ADD CONSTRAINT curso_aperturado_id_turno_fkey FOREIGN KEY (id_turno) REFERENCES turno_curso(id);



ALTER TABLE ONLY inscrito
    ADD CONSTRAINT inscrito_id_curso_aperturado_fkey FOREIGN KEY (id_curso_aperturado) REFERENCES curso_aperturado(id);



ALTER TABLE ONLY inscrito
    ADD CONSTRAINT inscrito_id_estado_fkey FOREIGN KEY (id_estado) REFERENCES estado(id);



ALTER TABLE ONLY inscrito
    ADD CONSTRAINT inscrito_id_institucion_fkey FOREIGN KEY (id_institucion) REFERENCES institucion(id);



ALTER TABLE ONLY inscrito
    ADD CONSTRAINT inscrito_id_persona_fkey FOREIGN KEY (id_persona) REFERENCES modulo_base.persona(id);



ALTER TABLE ONLY instructor
    ADD CONSTRAINT instructor_id_persona_fkey FOREIGN KEY (id_persona) REFERENCES modulo_base.persona(id);



ALTER TABLE ONLY preinscrito_curso
    ADD CONSTRAINT preinscrito_curso_id_curso_fkey FOREIGN KEY (id_curso) REFERENCES curso(id);



ALTER TABLE ONLY preinscrito_curso
    ADD CONSTRAINT preinscrito_curso_id_preinscrito_fkey FOREIGN KEY (id_preinscrito) REFERENCES preinscrito(id);



ALTER TABLE ONLY preinscrito
    ADD CONSTRAINT preinscrito_id_institucion_fkey FOREIGN KEY (id_institucion) REFERENCES institucion(id);


SET search_path = modulo_base, pg_catalog;


ALTER TABLE ONLY accion_especifica
    ADD CONSTRAINT accion_especifica_id_accion_centralizada_fkey FOREIGN KEY (id_accion_centralizada) REFERENCES accion_centralizada(id);



ALTER TABLE ONLY accion_subespecifica
    ADD CONSTRAINT accion_subespecifica_id_accion_especifica_fkey FOREIGN KEY (id_accion_especifica) REFERENCES accion_especifica(id);



ALTER TABLE ONLY banco_cuenta
    ADD CONSTRAINT banco_cuenta_id_banco_cuenta_tipo_fkey FOREIGN KEY (id_banco_cuenta_tipo) REFERENCES banco_cuenta_tipo(id);



ALTER TABLE ONLY banco_cuenta
    ADD CONSTRAINT banco_cuenta_id_banco_fkey FOREIGN KEY (id_banco) REFERENCES banco(id);



ALTER TABLE ONLY banco_cuenta
    ADD CONSTRAINT banco_cuenta_id_cuenta_contable_fkey FOREIGN KEY (id_cuenta_contable) REFERENCES cuenta_contable(id_cuenta_contable);



ALTER TABLE ONLY cargo
    ADD CONSTRAINT cargo_id_cuenta_presupuestaria_fkey FOREIGN KEY (id_cuenta_presupuestaria) REFERENCES cuenta_presupuestaria(id_cuenta_presupuestaria);



ALTER TABLE ONLY comprobante_bancario
    ADD CONSTRAINT comprobante_bancario_id_banco_cuenta_fkey FOREIGN KEY (id_banco_cuenta) REFERENCES banco_cuenta(id);



ALTER TABLE ONLY comprobante_bancario
    ADD CONSTRAINT comprobante_bancario_id_banco_movimiento_tipo_fkey FOREIGN KEY (id_banco_movimiento_tipo) REFERENCES banco_movimiento_tipo(id);



ALTER TABLE ONLY comprobante_bancario
    ADD CONSTRAINT comprobante_bancario_id_comprobante_fkey FOREIGN KEY (id_comprobante) REFERENCES comprobante(id) ON UPDATE CASCADE ON DELETE CASCADE;



ALTER TABLE ONLY comprobante_tiene_cargo
    ADD CONSTRAINT comprobante_cargo_id_cargo_fkey FOREIGN KEY (id_cargo) REFERENCES cargo(id);



ALTER TABLE ONLY comprobante_tiene_cargo
    ADD CONSTRAINT comprobante_cargo_id_comprobante_fkey FOREIGN KEY (id_comprobante) REFERENCES comprobante(id);



ALTER TABLE ONLY comprobante_datos
    ADD CONSTRAINT comprobante_data_id_comprobante_fkey FOREIGN KEY (id_comprobante) REFERENCES comprobante(id);



ALTER TABLE ONLY comprobante
    ADD CONSTRAINT comprobante_id_persona_fkey FOREIGN KEY (id_persona) REFERENCES persona(id);



ALTER TABLE ONLY comprobante_tiene_item
    ADD CONSTRAINT comprobante_item_id_comprobante_fkey FOREIGN KEY (id_comprobante) REFERENCES comprobante(id);



ALTER TABLE ONLY comprobante_tiene_item
    ADD CONSTRAINT comprobante_item_id_item_fkey FOREIGN KEY (id_item) REFERENCES item(id);



ALTER TABLE ONLY comprobante_previo
    ADD CONSTRAINT comprobante_previo_id_comprobante_fkey FOREIGN KEY (id_comprobante) REFERENCES comprobante(id);



ALTER TABLE ONLY comprobante_previo
    ADD CONSTRAINT comprobante_previo_id_comprobante_previo_fkey FOREIGN KEY (id_comprobante_previo) REFERENCES comprobante(id);



ALTER TABLE ONLY comprobante_previo_monto_pagado
    ADD CONSTRAINT comprobante_previo_monto_pagado_id_comprobante_fkey FOREIGN KEY (id_comprobante, id_comprobante_previo) REFERENCES comprobante_previo(id_comprobante, id_comprobante_previo);



ALTER TABLE ONLY comprobante_tiene_item
    ADD CONSTRAINT comprobante_tiene_item_id_unidad_medida_fkey FOREIGN KEY (id_unidad_medida) REFERENCES unidad_medida(id);



ALTER TABLE ONLY comprobante_tiene_retencion
    ADD CONSTRAINT comprobante_tiene_retencion_id_comprobante_fkey FOREIGN KEY (id_comprobante) REFERENCES comprobante(id);



ALTER TABLE ONLY comprobante_tiene_retencion
    ADD CONSTRAINT comprobante_tiene_retencion_id_retencion_fkey FOREIGN KEY (id_retencion) REFERENCES retencion(id);



ALTER TABLE ONLY comprobante
    ADD CONSTRAINT comprobante_tipo_fkey FOREIGN KEY (tipo) REFERENCES comprobante_tipo(tipo);



ALTER TABLE ONLY comprobante
    ADD CONSTRAINT comprobante_usuario_fkey FOREIGN KEY (usuario) REFERENCES usuario(usuario);



ALTER TABLE ONLY convertidor
    ADD CONSTRAINT convertidor_id_cuenta_contable_fkey FOREIGN KEY (id_cuenta_contable) REFERENCES cuenta_contable(id_cuenta_contable);



ALTER TABLE ONLY convertidor
    ADD CONSTRAINT convertidor_id_cuenta_presupuestaria_fkey FOREIGN KEY (id_cuenta_presupuestaria) REFERENCES cuenta_presupuestaria(id_cuenta_presupuestaria);



ALTER TABLE ONLY detalle_contable
    ADD CONSTRAINT detalle_contable_id_comprobante_fkey FOREIGN KEY (id_comprobante) REFERENCES comprobante(id);



ALTER TABLE ONLY detalle_contable
    ADD CONSTRAINT detalle_contable_id_cuenta_contable_fkey FOREIGN KEY (id_cuenta_contable) REFERENCES cuenta_contable(id_cuenta_contable);



ALTER TABLE ONLY detalle_presupuestario
    ADD CONSTRAINT detalle_presupuestario_id_comprobante_fkey FOREIGN KEY (id_comprobante) REFERENCES comprobante(id);



ALTER TABLE ONLY detalle_presupuestario
    ADD CONSTRAINT detalle_presupuestario_id_cuenta_presupuestaria_fkey FOREIGN KEY (id_cuenta_presupuestaria) REFERENCES cuenta_presupuestaria(id_cuenta_presupuestaria);



ALTER TABLE ONLY detalle_presupuestario
    ADD CONSTRAINT detalle_presupuestario_operacion_fkey FOREIGN KEY (operacion) REFERENCES detalle_presupuestario_operacion(operacion);



ALTER TABLE ONLY detalle_presupuestario
    ADD CONSTRAINT etalle_presupuestario_id_accion_subespecifica_fkey FOREIGN KEY (id_accion_subespecifica) REFERENCES accion_subespecifica(id);



ALTER TABLE ONLY factura
    ADD CONSTRAINT factura_id_persona_fkey FOREIGN KEY (id_persona) REFERENCES persona(id);



ALTER TABLE ONLY factura_tiene_cheque
    ADD CONSTRAINT factura_tiene_cheque_id_comprobante_cheque_fkey FOREIGN KEY (id_comprobante_cheque) REFERENCES comprobante(id);



ALTER TABLE ONLY factura_tiene_cheque
    ADD CONSTRAINT factura_tiene_cheque_id_factura_fkey FOREIGN KEY (id_factura) REFERENCES factura(id);



ALTER TABLE ONLY formulacion_detalle
    ADD CONSTRAINT formulacion_detalle_id_cuenta_presupuestaria_fkey FOREIGN KEY (id_cuenta_presupuestaria) REFERENCES cuenta_presupuestaria(id_cuenta_presupuestaria);



ALTER TABLE ONLY formulacion_detalle
    ADD CONSTRAINT formulacion_detalle_id_formulacion_fkey FOREIGN KEY (id_formulacion) REFERENCES formulacion(id) ON UPDATE CASCADE ON DELETE CASCADE;



ALTER TABLE ONLY formulacion
    ADD CONSTRAINT formulacion_id_accion_subespecifica_fkey FOREIGN KEY (id_accion_subespecifica) REFERENCES accion_subespecifica(id);



ALTER TABLE ONLY formulacion
    ADD CONSTRAINT formulacion_id_comprobante_apertura_fkey FOREIGN KEY (id_comprobante_apertura) REFERENCES comprobante(id);



ALTER TABLE ONLY item
    ADD CONSTRAINT item_id_cuenta_presupuestaria_fkey FOREIGN KEY (id_cuenta_presupuestaria) REFERENCES cuenta_presupuestaria(id_cuenta_presupuestaria);



ALTER TABLE ONLY item
    ADD CONSTRAINT item_id_item_tipo_fkey FOREIGN KEY (id_item_tipo) REFERENCES item_tipo(id);



ALTER TABLE ONLY municipio
    ADD CONSTRAINT municipio_codigo_estado_fkey FOREIGN KEY (codigo_estado) REFERENCES estado(codigo);



ALTER TABLE ONLY organismo
    ADD CONSTRAINT organismo_id_persona_fkey FOREIGN KEY (id_persona) REFERENCES persona(id);



ALTER TABLE ONLY persona_tipo
    ADD CONSTRAINT persona_tipo_cuenta_contable_fkey FOREIGN KEY (id_cuenta_contable) REFERENCES cuenta_contable(id_cuenta_contable);



ALTER TABLE ONLY requisicion_externa
    ADD CONSTRAINT requisicion_externa_id_accion_subespecifica_fkey FOREIGN KEY (id_accion_subespecifica) REFERENCES accion_subespecifica(id);



ALTER TABLE ONLY requisicion_externa_tiene_item
    ADD CONSTRAINT requisicion_externa_tiene_item_id_unidad_medida_fkey FOREIGN KEY (id_unidad_medida) REFERENCES unidad_medida(id);



ALTER TABLE ONLY retencion_comprobante
    ADD CONSTRAINT retencion_comprobante_id_persona_fkey FOREIGN KEY (id_persona) REFERENCES persona(id);



ALTER TABLE ONLY retencion_comprobante
    ADD CONSTRAINT retencion_comprobante_id_retencion_tipo_fkey FOREIGN KEY (id_retencion_tipo) REFERENCES retencion_tipo(id);



ALTER TABLE ONLY retencion_comprobante_tiene_factura
    ADD CONSTRAINT retencion_comprobante_tiene_factu_id_retencion_comprobante_fkey FOREIGN KEY (id_retencion_comprobante) REFERENCES retencion_comprobante(id);



ALTER TABLE ONLY retencion_comprobante_tiene_factura
    ADD CONSTRAINT retencion_comprobante_tiene_factura_id_factura_fkey FOREIGN KEY (id_factura) REFERENCES factura(id);



ALTER TABLE ONLY retencion
    ADD CONSTRAINT retencion_id_cuenta_contable_fkey FOREIGN KEY (id_cuenta_contable) REFERENCES cuenta_contable(id_cuenta_contable);



ALTER TABLE ONLY retencion
    ADD CONSTRAINT retencion_id_retencion_tipo_fkey FOREIGN KEY (id_retencion_tipo) REFERENCES retencion_tipo(id);



ALTER TABLE ONLY unidad_coordinacion_tiene_persona
    ADD CONSTRAINT unidad_coordinacion_tiene_persona_id_persona_fkey FOREIGN KEY (id_persona) REFERENCES persona(id);



ALTER TABLE ONLY unidad_coordinacion_tiene_persona
    ADD CONSTRAINT unidad_coordinacion_tiene_persona_id_unidad_coordinacion_fkey FOREIGN KEY (id_unidad_coordinacion) REFERENCES unidad_coordinacion(id) ON UPDATE CASCADE ON DELETE CASCADE;



ALTER TABLE ONLY usuario_perfil
    ADD CONSTRAINT usuario_acceso_id_usuario_fkey FOREIGN KEY (id_usuario) REFERENCES usuario(id);


SET search_path = modulo_inventario, pg_catalog;


ALTER TABLE ONLY movimiento_material
    ADD CONSTRAINT movimiento_material_id_persona_fkey FOREIGN KEY (id_persona) REFERENCES modulo_base.persona(id);



ALTER TABLE ONLY movimiento_material_tiene_factura
    ADD CONSTRAINT movimiento_material_tiene_factura_factura_id_fkey FOREIGN KEY (id_factura) REFERENCES modulo_base.factura(id) ON UPDATE CASCADE ON DELETE CASCADE;



ALTER TABLE ONLY movimiento_material_tiene_factura
    ADD CONSTRAINT movimiento_material_tiene_factura_movimiento_material_id_fkey FOREIGN KEY (id_movimiento_material) REFERENCES movimiento_material(id) ON UPDATE CASCADE ON DELETE CASCADE;



ALTER TABLE ONLY movimiento_material_tiene_item
    ADD CONSTRAINT movimiento_material_tiene_item_id_item_fkey FOREIGN KEY (id_item) REFERENCES modulo_base.item(id);



ALTER TABLE ONLY movimiento_material_tiene_item
    ADD CONSTRAINT movimiento_material_tiene_item_id_movimiento_material_fkey FOREIGN KEY (id_movimiento_material) REFERENCES movimiento_material(id);



ALTER TABLE ONLY movimiento_material_tiene_item
    ADD CONSTRAINT movimiento_material_tiene_item_id_unidad_medida_fkey FOREIGN KEY (id_unidad_medida) REFERENCES modulo_base.unidad_medida(id);



ALTER TABLE ONLY movimiento_material_tiene_requisicion
    ADD CONSTRAINT movimiento_material_tiene_requisici_movimiento_material_id_fkey FOREIGN KEY (id_movimiento_material) REFERENCES movimiento_material(id) ON UPDATE CASCADE ON DELETE CASCADE;



ALTER TABLE ONLY movimiento_material_tiene_requisicion
    ADD CONSTRAINT movimiento_material_tiene_requisicion_requisicion_id_fkey FOREIGN KEY (id_requisicion) REFERENCES requisicion(id) ON UPDATE CASCADE ON DELETE CASCADE;



ALTER TABLE ONLY requisicion
    ADD CONSTRAINT requisicion_id_item_tipo_fkey FOREIGN KEY (id_item_tipo) REFERENCES modulo_base.item_tipo(id);



ALTER TABLE ONLY requisicion
    ADD CONSTRAINT requisicion_id_persona_fkey FOREIGN KEY (id_persona) REFERENCES modulo_base.persona(id);



ALTER TABLE ONLY requisicion
    ADD CONSTRAINT requisicion_id_unidad_coordinacion_fkey FOREIGN KEY (id_unidad_coordinacion) REFERENCES modulo_base.unidad_coordinacion(id);



ALTER TABLE ONLY requisicion_observacion
    ADD CONSTRAINT requisicion_observacion_id_persona_observacion_fkey FOREIGN KEY (id_persona_observacion) REFERENCES modulo_base.persona(id);



ALTER TABLE ONLY requisicion_observacion
    ADD CONSTRAINT requisicion_observacion_id_requisicion_fkey FOREIGN KEY (id_requisicion) REFERENCES requisicion(id) ON UPDATE CASCADE ON DELETE CASCADE;



ALTER TABLE ONLY requisicion_tiene_item
    ADD CONSTRAINT requisicion_tiene_item_item_id_fkey FOREIGN KEY (id_item) REFERENCES modulo_base.item(id) ON UPDATE CASCADE ON DELETE CASCADE;



ALTER TABLE ONLY requisicion_tiene_item
    ADD CONSTRAINT requisicion_tiene_item_requisicion_id_fkey FOREIGN KEY (id_requisicion) REFERENCES requisicion(id) ON UPDATE CASCADE ON DELETE CASCADE;


SET search_path = modulo_nomina, pg_catalog;


ALTER TABLE ONLY grupo_familiar
    ADD CONSTRAINT grupo_familiar_id_ficha_fkey FOREIGN KEY (id_ficha) REFERENCES ficha(id);



ALTER TABLE ONLY grupo_familiar
    ADD CONSTRAINT grupo_familiar_id_parentesco_fkey FOREIGN KEY (id_parentesco) REFERENCES grupo_familiar_parentesco(id);



ALTER TABLE ONLY grupo_familiar
    ADD CONSTRAINT grupo_familiar_id_persona_fkey FOREIGN KEY (id_persona) REFERENCES modulo_base.persona(id);



ALTER TABLE ONLY concepto_cuenta
    ADD CONSTRAINT concepto_cuenta_id_concepto_fkey FOREIGN KEY (id_concepto) REFERENCES concepto(id);



ALTER TABLE ONLY concepto_cuenta
    ADD CONSTRAINT concepto_cuenta_id_nomina_fkey FOREIGN KEY (id_nomina) REFERENCES nomina(id);



ALTER TABLE ONLY concepto_formula
    ADD CONSTRAINT concepto_formula_id_concepto_fkey FOREIGN KEY (id_concepto) REFERENCES concepto(id);



ALTER TABLE ONLY concepto_periodo
    ADD CONSTRAINT concepto_nomina_id_concepto_fkey FOREIGN KEY (id_concepto) REFERENCES concepto(id);



ALTER TABLE ONLY concepto_periodo
    ADD CONSTRAINT concepto_nomina_id_periodo_fkey FOREIGN KEY (id_periodo) REFERENCES periodo(id);



ALTER TABLE ONLY concepto_periodo
    ADD CONSTRAINT concepto_periodo_id_nomina_fkey FOREIGN KEY (id_nomina) REFERENCES nomina(id);



ALTER TABLE ONLY concepto_presupuesto_contabilidad
    ADD CONSTRAINT concepto_presupuesto_contabili_id_cuenta_presupuestaria_ap_fkey FOREIGN KEY (id_cuenta_presupuestaria_ap) REFERENCES modulo_base.cuenta_presupuestaria(id_cuenta_presupuestaria);



ALTER TABLE ONLY concepto_presupuesto_contabilidad
    ADD CONSTRAINT concepto_presupuesto_contabilidad_id_concepto_fkey FOREIGN KEY (id_concepto) REFERENCES concepto(id);



ALTER TABLE ONLY concepto_presupuesto_contabilidad
    ADD CONSTRAINT concepto_presupuesto_contabilidad_id_cuenta_contable_ap_fkey FOREIGN KEY (id_cuenta_contable_ap) REFERENCES modulo_base.cuenta_contable(id_cuenta_contable);



ALTER TABLE ONLY concepto_presupuesto_contabilidad
    ADD CONSTRAINT concepto_presupuesto_contabilidad_id_cuenta_contable_fkey FOREIGN KEY (id_cuenta_contable) REFERENCES modulo_base.cuenta_contable(id_cuenta_contable);



ALTER TABLE ONLY concepto_presupuesto_contabilidad
    ADD CONSTRAINT concepto_presupuesto_contabilidad_id_cuenta_presupuestaria_fkey FOREIGN KEY (id_cuenta_presupuestaria) REFERENCES modulo_base.cuenta_presupuestaria(id_cuenta_presupuestaria);



ALTER TABLE ONLY concepto_presupuesto_contabilidad
    ADD CONSTRAINT concepto_presupuesto_contabilidad_id_nomina_fkey FOREIGN KEY (id_nomina) REFERENCES nomina(id);



ALTER TABLE ONLY ficha_estructura_presupuestaria
    ADD CONSTRAINT ficha_accion_presupuestaria_id_ficha_fkey FOREIGN KEY (id_ficha) REFERENCES ficha(id);



ALTER TABLE ONLY ficha_cargo
    ADD CONSTRAINT ficha_cargo_id_cargo_fkey FOREIGN KEY (id_cargo) REFERENCES cargo(id);



ALTER TABLE ONLY ficha_cargo
    ADD CONSTRAINT ficha_cargo_id_ficha_fkey FOREIGN KEY (id_ficha) REFERENCES ficha(id);



ALTER TABLE ONLY ficha_concepto
    ADD CONSTRAINT ficha_concepto_id_concepto_fkey FOREIGN KEY (id_concepto) REFERENCES concepto(id);



ALTER TABLE ONLY ficha_concepto
    ADD CONSTRAINT ficha_concepto_id_ficha_fkey FOREIGN KEY (id_ficha) REFERENCES ficha(id);



ALTER TABLE ONLY ficha_concepto
    ADD CONSTRAINT ficha_concepto_id_nomina_fkey FOREIGN KEY (id_nomina) REFERENCES nomina(id);



ALTER TABLE ONLY ficha_concepto
    ADD CONSTRAINT ficha_concepto_id_periodo_fkey FOREIGN KEY (id_periodo) REFERENCES periodo(id);



ALTER TABLE ONLY ficha_estructura_presupuestaria
    ADD CONSTRAINT ficha_estructura_presupuestaria_id_accion_subespecifica_fkey FOREIGN KEY (id_accion_subespecifica) REFERENCES modulo_base.accion_subespecifica(id);



ALTER TABLE ONLY ficha
    ADD CONSTRAINT ficha_id_escala_salarial_fkey FOREIGN KEY (id_escala_salarial) REFERENCES escala_salarial(id);



ALTER TABLE ONLY ficha
    ADD CONSTRAINT ficha_id_persona_fkey FOREIGN KEY (id_persona) REFERENCES modulo_base.persona(id);



ALTER TABLE ONLY nomina_banco_cuenta
    ADD CONSTRAINT nomina_banco_cuenta_id_banco_cuenta_fkey FOREIGN KEY (id_banco_cuenta) REFERENCES modulo_base.banco_cuenta(id);



ALTER TABLE ONLY nomina_banco_cuenta
    ADD CONSTRAINT nomina_banco_cuenta_id_nomina_fkey FOREIGN KEY (id_nomina) REFERENCES nomina(id);



ALTER TABLE ONLY ficha_cuenta_nomina
    ADD CONSTRAINT nomina_ficha_id_ficha_fkey FOREIGN KEY (id_ficha) REFERENCES ficha(id);



ALTER TABLE ONLY ficha_cuenta_nomina
    ADD CONSTRAINT nomina_ficha_id_nomina_fkey FOREIGN KEY (id_nomina) REFERENCES nomina(id);



ALTER TABLE ONLY periodo_nota
    ADD CONSTRAINT periodo_nota_id_nomina_fkey FOREIGN KEY (id_nomina) REFERENCES nomina(id);



ALTER TABLE ONLY periodo_nota
    ADD CONSTRAINT periodo_nota_id_periodo_fkey FOREIGN KEY (id_periodo) REFERENCES periodo(id);



ALTER TABLE ONLY prestamo_cuota
    ADD CONSTRAINT prestamo_cuota_id_periodo_fkey FOREIGN KEY (id_periodo) REFERENCES periodo(id);



ALTER TABLE ONLY prestamo_cuota
    ADD CONSTRAINT prestamo_cuota_id_prestamo_fkey FOREIGN KEY (id_prestamo) REFERENCES prestamo(id);



ALTER TABLE ONLY prestamo
    ADD CONSTRAINT prestamo_id_concepto_fkey FOREIGN KEY (id_concepto) REFERENCES concepto(id);



ALTER TABLE ONLY prestamo
    ADD CONSTRAINT prestamo_id_ficha_fkey FOREIGN KEY (id_ficha) REFERENCES ficha(id);



ALTER TABLE ONLY prestamo
    ADD CONSTRAINT prestamo_id_nomina_fkey FOREIGN KEY (id_nomina) REFERENCES nomina(id);


SET search_path = modulo_planificacion, pg_catalog;


ALTER TABLE ONLY meta_fisica
    ADD CONSTRAINT meta_fisica_id_accion_fkey FOREIGN KEY (id_accion_especifica) REFERENCES modulo_base.accion_especifica(id);



ALTER TABLE ONLY meta_fisica
    ADD CONSTRAINT meta_fisica_id_responsable_fkey FOREIGN KEY (id_responsable) REFERENCES modulo_nomina.ficha(id);



ALTER TABLE ONLY meta_fisica
    ADD CONSTRAINT meta_fisica_id_tipo_fkey FOREIGN KEY (id_tipo) REFERENCES meta_fisica_tipo(id);



ALTER TABLE ONLY meta_fisica
    ADD CONSTRAINT meta_fisica_id_unidad_coordinacion_fkey FOREIGN KEY (id_unidad_coordinacion) REFERENCES modulo_base.unidad_coordinacion(id);



ALTER TABLE ONLY meta_fisica
    ADD CONSTRAINT meta_fisica_id_unidad_medida_fkey FOREIGN KEY (id_unidad_medida) REFERENCES unidad_medida(id);


SET search_path = modulo_planificador, pg_catalog;


ALTER TABLE ONLY tarea
    ADD CONSTRAINT tarea_id_unidad_coordinacion_fkey FOREIGN KEY (id_unidad_coordinacion) REFERENCES modulo_base.unidad_coordinacion(id);



