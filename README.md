### Instalación
1. Ubicar la ruta donde vamos a ejecutarlo, para efectos demostrativo usaremos:\
`/home/usuario/docker-siga-php8`\
No crear esta carpeta.

3. Clonar el repositorio desde github con el siguiente comando:\
`cd /home/usuario/`\
`git clone https://github.com/pintocar83/siga_php8 docker-siga-php8`

	>Clonando en 'docker-siga-php8'...\
remote: Enumerating objects: 11407, done.\
remote: Counting objects: 100% (4652/4652), done.\
remote: Compressing objects: 100% (3277/3277), done.\
remote: Total 11407 (delta 1455), reused 4199 (delta 1288), pack-reused 6755 (from 1)\
Recibiendo objetos: 100% (11407/11407), 77.89 MiB | 22.93 MiB/s, listo.\
Resolviendo deltas: 100% (2519/2519), listo.\
Actualizando archivos: 100% (9189/9189), listo.

5. Ingresar a la carpeta docker-siga-php8\
`cd docker-siga-php8`

7. Copiar (desde la plantilla) el archivo con la configuración de la conexón a la base de datos.\
`cp library/siga.config.example.php siga.config.php`\
	Posteriormente (una vez funcional) editar este archivo para modificar el nombre de la base de datos y los parametros de configuración para la conexión.

9. Levantar los contenedores\
`docker compose up`

	La primera vez que se ejecute este comando, se descargará desde dockerhub las imágenes necesarias para ejecutar el sistema. Este comando levantaran 2 servicios: web y database. Para consultar el estatus de estos, ejecutamos:\
	`docker compose ps`

	Consultar la documentación de docker para mas información.

10. Restaurar la estructura de la base de datos e información inicial. Ingresar al phpPgAdmin que se encuentra en el sistema:
http://localhost:8080/module/administrador/phppgadmin/ \
Ingresamos con:\
**Usuario:** siga\
**Contraseña:** siga

	Seleccionamos la base de datos: **siga_fundacite_sucre** y restauramos los `scripts database_estructura.sql` y `database_data_inicial.sql`, ambos ubicados en la carpeta `database/` del sistema.

11. Ya podemos ingresar al sistema desde:\
http://localhost:8080/ \
**Usuario:** admin\
**Contraseña:** admin

12. El manual de los módulos se encuentra ubicado en:\
http://localhost:8080/help/

	El manual se encuentra realizado con dokuwiki y podemos entrar en modo edición desde la siguiente ruta:\
	http://localhost:8080/help/core/



<br>
<br>
<br>

### Creación de la Base de Datos `registro_civil_ve.sqlite` (opcional)
Esta base de datos contiene información básica de la persona:
- [x] Cedula
- [x] Nombres
- [x] Apellidos
- [ ] Fecha de nacimiento (información disponible pero pendiente de adjuntar al csv)

Esta base de datos es usada por los modulos ficha y beneficiarios a la hora de registrar nuevas personas,
al ingresar la cedula la información restante es precargada. La configurada se encuentra en `library/siga.config.php` en `"base"`.

El proceso de restauracion es lento y debe ejecutarse en un terminal. La ejecucion desde el
navegador arroja timeout al superar los 10min.

1. Extrar en la carpeta actual `registro_civil_ve.part01.rar`. Esto arrojará `registro_civil_ve.csv` (este archivo es una compilación de una serie de csv publicados en la página del CNE).

2. Entramos en la consola del contenedor web:
```
docker compose exec web /bin/bash
```

4. Una vez dentro del contenedor, ingresamos a la carpeta `data/` para ejecutar la rutina `regsitro_civil_ve.php`
```
cd /app/data/
php registro_civil.ve.php
```

5. Una vez culminado el proceso, mostrará:
```
-Creando tabla
-Importando...
-Finalizo
-Restaurados 18903143 registros
[Tiempo de ejecución 830 segundos]
```

Por ultimo verificamos la creacion del archivo `registro_civil_ve.sqlite` (tamaño aproximado: 1.1Gb).

Ingresamos al sistema y podemos verificar la funcionalidad desde cualquiera de los modulos (ficha o beneficiarios), ingresamos la cedula y pulsamos buscar.

