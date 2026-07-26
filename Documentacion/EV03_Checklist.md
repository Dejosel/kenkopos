# Lista de Chequeo de Cumplimiento Técnico
## Evidencia: GA7-220501096-AA5-EV03 - Proyecto

**Proyecto:** KenkoPOS  
**Aprendiz:** Jose Luis Hernandez  

Esta lista de chequeo evalúa la conformidad de los entregables desarrollados para la evidencia **GA7-220501096-AA5-EV03: Diseño y desarrollo de servicios web - proyecto** frente a las rúbricas e instrucciones de la Guía de Aprendizaje del SENA.

---

| N° | Criterio de Evaluación / Requerimiento | Estado | Evidencia y Ruta en el Proyecto |
|:---|:---|:---:|:---|
| **1** | **Desarrollo de Servicios Web del Proyecto**<br>Codificar los servicios necesarios para cumplir con las características del software a realizar. | **CUMPLE** | Implementado en la carpeta [api/](file:///Users/macbookproa1707dtctienda/Documents/websena/kenkopos/api/). Incluye autenticación (`register.php`, `login.php`) y CRUD de catálogo (`products.php`). |
| **2** | **Conexión a Base de Datos Mediante PDO**<br>Uso de conexiones seguras y parametrizadas a bases de datos relacionales en la API. | **CUMPLE** | Implementado en [config/database.php](file:///Users/macbookproa1707dtctienda/Documents/websena/kenkopos/config/database.php) y [api/config/database.php](file:///Users/macbookproa1707dtctienda/Documents/websena/kenkopos/api/config/database.php). Utiliza sentencias preparadas contra inyección SQL. |
| **3** | **Estándares REST y Códigos HTTP**<br>Utilizar los métodos HTTP correctos (GET, POST, PUT, DELETE) y códigos de estado REST estándar. | **CUMPLE** | Los scripts de API discriminan peticiones según `$_SERVER['REQUEST_METHOD']` y retornan códigos adecuados (200, 201, 400, 401, 404, 409, 405, 500). Ver [api/products.php](file:///Users/macbookproa1707dtctienda/Documents/websena/kenkopos/api/products.php). |
| **4** | **Documentación de los Servicios Web**<br>Elaborar documentación técnica con el diseño, endpoints, JSONs y pruebas. | **CUMPLE** | Documentado en [Documentacion/EV03_Informe.md](file:///Users/macbookproa1707dtctienda/Documents/websena/kenkopos/Documentacion/EV03_Informe.md) (Diseño y Especificaciones) y [Documentacion/EV03_Pruebas.md](file:///Users/macbookproa1707dtctienda/Documents/websena/kenkopos/Documentacion/EV03_Pruebas.md) (Informe de Pruebas). |
| **5** | **Uso de Herramientas de Versionamiento**<br>El proyecto debe estar versionado con Git y alojado en un repositorio remoto. | **CUMPLE** | Versionado en GitHub: `https://github.com/Dejosel/kenkopos`. Commits y tag de entrega creados. Ver [Documentacion/EV03_Repositorio.txt](file:///Users/macbookproa1707dtctienda/Documents/websena/kenkopos/Documentacion/EV03_Repositorio.txt). |
| **6** | **Colección de Pruebas en Postman**<br>Proveer un archivo de exportación de la colección de Postman con los requests estructurados. | **CUMPLE** | Colección actualizada y disponible en la raíz del proyecto: [KenkoPOS.postman_collection.json](file:///Users/macbookproa1707dtctienda/Documents/websena/kenkopos/KenkoPOS.postman_collection.json). |
| **7** | **Entregable Compreso con Nomenclatura**<br>Generar un archivo ZIP/RAR con el nombre `NOMBRE_APELLIDO_AA5_EV03` que incluya código, enlace y docs. | **CUMPLE** | Archivo comprimido generado en la raíz: `JOSE_LUIS_HERNANDEZ_AA5_EV03.zip`. |

---

### Observación Final del Aprendiz:
Los entregables cumplen al 100% con los requerimientos de la guía de aprendizaje. Las APIs de KenkoPOS son completamente funcionales, seguras frente a las vulnerabilidades más comunes (SQL Injection, XSS) y están listas para ser desplegadas y consumidas por cualquier aplicación frontend.
