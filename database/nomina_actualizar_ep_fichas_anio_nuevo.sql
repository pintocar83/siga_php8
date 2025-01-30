INSERT INTO modulo_nomina.ficha_estructura_presupuestaria(id_ficha,fecha,id_accion_subespecifica) 
SELECT DISTINCT id_ficha, '2025-01-01'::date, 76 FROM modulo_nomina.ficha_estructura_presupuestaria WHERE text(fecha) ILIKE '2024-%'
