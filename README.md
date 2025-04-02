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
Actualizando archivos: 100% (9189/9189), listo.\

5. Ingresar a la carpeta docker-siga-php8\
`cd docker-siga-php8`

7. Copiar (desde la plantilla) el archivo con la configuración de la conexón a la base de datos.\
`cp library/siga.config.example.php siga.config.php`\
	Posteriormente (una vez funcional) editar este archivo para modificar el nombre de la base de datos y los parametros de configuración para la conexión.

9. Levantar los contenedores\
`docker compose up`

	La primera vez que se ejecute este comando, se descargará desde dockerhub las imágenes necesarios para ejecutar el sistema. Este comando levantaran 2 servicios: web y database. Para consultar el estatus de estos, ejecutamos:\
	`docker compose ps`

	Consultar la documentación de docker para mas información.

10. Restaurar la estructura de la base de datos e información inicial. Ingresar al phpPgAdmin que se encuentra en el sistema:
http://localhost:8080/module/administrador/phppgadmin/ \
Ingresamos con:\
**Usuario:** siga\
**Contraseña:** siga

	Seleccionamos la base de datos: **siga_fundacite_sucre** y restauramos los `scripts database_estructura.sql` y `database_data_inicial.sql`, ambos ubicados en la carpeta `database/` del sistema.\

11. Ya podemos ingresar al sistema desde:\
http://localhost:8080/ \
**Usuario:** admin\
**Contraseña:** admin

12. El manual de los módulos se encuentra ubicado en:\
http://localhost:8080/help/

	El manual se encuentra realizado con dokuwiki y podemos entrar en modo edición desde la siguiente ruta:\
	http://localhost:8080/help/core/
