<p align="center">
  <a href="https://digital.gob.es/ministerio/organigrama_organos/SEDIA.html" target="_blank" rel="noreferrer noopener"><img alt="Secretaría de Estado de Digitalización e Inteligencia Artificial" src="https://raw.githubusercontent.com/datosgobes/DCAT-AP-ES/a3830db83a1ed5de0b347eeaf9d05eede75f620f/docs/img/sedia-red-es.jpg" height="44" /></a>
  &nbsp;&nbsp;&nbsp;
  <a href="https://datos.gob.es/" target="_blank" rel="noreferrer noopener"><img alt="datos.gob.es" src="https://raw.githubusercontent.com/datosgobes/DCAT-AP-ES/a3830db83a1ed5de0b347eeaf9d05eede75f620f/docs/img/dge_logo_2025.svg" height="44" /></a>
  &nbsp;&nbsp;&nbsp;
  <a href="https://datos.gob.es/acerca-de-la-iniciativa-aporta" target="_blank" rel="noreferrer noopener"><img alt="Iniciativa Aporta" src="https://raw.githubusercontent.com/datosgobes/DCAT-AP-ES/a3830db83a1ed5de0b347eeaf9d05eede75f620f/docs/img/iniciativa_aporta.svg" height="44" /></a>
</p>

# Módulos Drupal - [datos.gob.es](https://datos.gob.es/)

Se incluyen en esta sección los módulos *contrib* desarrollados para el proyecto, que deben activarse en Drupal según el modelo de despliegue definido.

> [!TIP]
> Para más información el repositorio base está disponible en [github.com/datosgobes/datos.gob.es](https://github.com/datosgobes/datos.gob.es)

### Dependencias

Requiere [Drupal versión 10.x](https://www.drupal.org/project/drupal/releases?version=10)

### Instalación

El proceso de instalación debe seguir el modelo de despliegue de Drupal para *modules* *contrib*, activación de *features* e integración de *themes*, aplicando las recomendaciones de su [documentación oficial](https://www.drupal.org/docs/). Para cualquier consulta, puede utilizar el [punto de contacto](https://datos.gob.es/es/form/contact) de datos.gob.es.


## Módulos

| Extensión | Nombre | Descripción |
|---|---|---|
| `dge_access` | DGE Access | Funciones de control de acceso |
| `dge_basic_settings` | DGE Basic settings | Página de configuración básica personalizada |
| `dge_bigpipe_facets` | DGE Disable BigPipe for Facets | Módulo que deshabilita *BigPipe* para los bloques de *Facets*. |
| `dge_broken_links_ckan` | DGE Broken Links CKAN | Enlace de menú: enlaces rotos de CKAN |
| `dge_ckan` | DGE Ckan | Conexión con CKAN |
| `dge_ckan_blocks` | DGE Ckan Blocks | Bloques con información de CKAN |
| `dge_comments` | DGE Comments | Formulario y visualización de comentarios personalizados |
| `dge_custom_permissions` | DGE Custom Permissions | Módulo personalizado para la gestión de permisos |
| `dge_custom_powerbi_report_iframe` | DGE Custom PowerBI Report Iframe | *URL* personalizada con id de usuario para *iframe* |
| `dge_dashboard_querys` | DGE Dashboard querys | Obtiene consultas de la base de datos de CKAN para generar ficheros CSV en el *dashboard* |
| `dge_data_request` | DGE Data Request | Acciones personalizadas para solicitudes de datos |
| `dge_email_custom` | DGE Email Custom | Módulo para configurar un campo de tipo *email* |
| `dge_email_delete_confirmation` | DGE Email Delete Confirmation | Confirmación de borrado personalizada para añadir notificación por *email* |
| `dge_email_footer_header` | DGE Custom header and footer email | Cabecera y pie de *email* personalizados |
| `dge_last_login` | DGE Last Login | Muestra la fecha del último inicio de sesión del usuario autenticado |
| `dge_login_redirect` | DGE Login Redirect | Redirige al usuario a la página de inicio tras el *login* |
| `dge_migrate` | DGE Migrate | Módulo personalizado para migrar contenido de Drupal 7 a Drupal 10 |
| `dge_more_view_custom_url_block` | DGE Custom URL Block More View | *URL* personalizada para bloques de sector: la *view* tiene un límite de tamaño y se necesita generar la *URL* de forma personalizada |
| `dge_node_add_restricted` | DGE NODE ADD restricted | Módulo que Restringe el acceso a node/add para usuarios anonimos. |
| `dge_ocultar_ponentes_aporta` | DGE Ocultar Ponentes Aporta | Módulo que oculta el acceso por url al tipo de contenido Ponentes Aporta. |
| `dge_password_simple_form` | DGE Password Simple Form | Simplifica el formulario de configuración o restablecimiento de contraseña |
| `dge_reset_password_ldap` | DGE Reset password LDAP | Cambia el *email* que se envía a usuarios LDAP al restablecer contraseñas |
| `dge_search` | DGE Search | Personaliza el formulario de búsqueda |
| `dge_semantic` | DGE Semantics | Añade información semántica a las páginas web |
| `dge_sendinblue` | DGE SendinBlue | Conexión con la API de Sendinblue y gestión de suscripciones |
| `dge_social_media_share` | DGE Social media share | Compartir en redes sociales personalizado |
| `dge_tfa` | DGE TFA | Personalización del módulo *TFA* |
| `dge_unique_role_name` | DGE Unique Role Name | Hace que el nombre del rol sea único |
| `dge_unpublish_confirmation` | DGE Unpublish Confirmation | DGE Unpublish Confirmation |
| `dge_user` | DGE User | Funciones de usuario |
| `dge_user_report_inactivity` | DGE USER REPORT INACTIVITY | Añade una acción para reportar inactividad del usuario |
| `dge_views_filter_dates` | DGE Views Filter Dates | Módulo que altera los filtros de fecha en Views sumando 1 día a los filtros "Es menor o igual que". |
| `dge_webform_email_reply` | DGE Webform Email Reply | Módulo auxiliar de *webform* que permite enviar una respuesta por *email* a los envíos de contacto |
| `dge_widget` | DGE Widget | Módulo DGE para generación de widget |
