insert into modulo_base.detalle_presupuestario
               SELECT  
                  8103 as id_comprobante,
                  DP.id_accion_subespecifica,
                  DP.id_cuenta_presupuestaria,
                  'DI' as operacion,
                  sum(case when DP.operacion='AP' or DP.operacion='AU' then DP.monto else -DP.monto end) as monto
               FROM
                  modulo_base.comprobante as C,
                  modulo_base.detalle_presupuestario as DP            
               WHERE
                  EXTRACT(YEAR FROM C.fecha)=2018 AND
                  C.contabilizado AND
                  C.id=DP.id_comprobante AND
                  (DP.operacion='AP' OR DP.operacion='AU' OR DP.operacion='DI' OR DP.operacion='C' OR DP.operacion='CC' OR DP.operacion='CCP')
  
               GROUP BY
                  id_accion_subespecifica,
                  id_cuenta_presupuestaria;



insert into modulo_base.detalle_presupuestario
select 8104 as id_comprobante, id_accion_subespecifica, id_cuenta_presupuestaria, 'AU' as operacion, (monto/100000.00)::numeric(20,2) as monto from modulo_base.detalle_presupuestario where id_comprobante=8103;
            