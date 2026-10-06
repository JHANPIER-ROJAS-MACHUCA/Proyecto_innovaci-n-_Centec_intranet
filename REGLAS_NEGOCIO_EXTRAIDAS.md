# Reglas de Negocio Críticas — CREDISOPORTE

Documento de extracción previa a eliminación de archivos.

---

## 1. CreditoService.php (`app/Modules/Credito/models/`)

### Qué hace
Servicio central de crédito: cálculo de cuotas, creación de préstamos, gestión completa de clientes (titular + cónyuge + avales), y consulta de ubigeo.

### Fórmulas de cálculo

**Cuota mensual (French system / amortización constante):**
```
tasaMensual = (tasaAnual / 100) / 12
Si tasaMensual == 0: cuota = monto / plazoMeses
Si no: cuota = monto * (tasaMensual * (1 + tasaMensual)^plazoMeses) / ((1 + tasaMensual)^plazoMeses - 1)
Resultado redondeado a 2 decimales
```

### Reglas de negocio

| ID | Regla |
|----|-------|
| BR-CRED-01 | Al crear préstamo, `capital` = `monto` (capital inicial igual al monto solicitado) |
| BR-CRED-02 | Las cuotas se generan automáticamente al crear el préstamo vía `Movimiento::generarCuotas()` |
| BR-CRED-03 | Fecha de desembolso por defecto: `date('Y-m-d')` (hoy) |
| BR-CRED-04 | **Préstamo Prendario**: solo guarda Titular + Vivienda + Bien en Prenda. Ignora negocio, transporte, cónyuge y avales |
| BR-CRED-05 | Al guardar cliente completo, el campo `comentario` de `tclie_general` almacena un JSON con datos adicionales (vivienda, negocio, conyuge, aval, fotos, transporte, bienPrenda, numDependientes) |
| BR-CRED-06 | La dirección (`tclie_direccion`) solo se inserta si no existe previamente para ese `idCG` |
| BR-CRED-07 | El negocio (`tclie_negocio`) se elimina si no hay datos; se hace upsert (inserta si no existe, actualiza si existe) |
| BR-CRED-08 | Vinculación cónyuge/aval: no duplica si ya existe la relación en `tvinculacion` |
| BR-CRED-09 | Al crear solicitud completa, el préstamo se crea con valores en cero (monto=0, cuota=0, tasa=0, plazo=0) y `estado=1` (solicitud pendiente) |
| BR-CRED-10 | `credit_type_id` por defecto = 1, `modality_id` por defecto = 1, `payment_period` = 'mensual', `number_installments` = 12 |
| BR-CRED-11 | Aprobar préstamo → `estado = 4`; Rechazar préstamo → `estado = 3` |
| BR-CRED-12 | En detalle de préstamo: `totalPagado` = suma de `montoPagado` donde `estado='2'`; `saldoPendiente` = suma de `cuota` donde `estado != '2'` |
| BR-CRED-13 | IDs se generan manualmente: `MAX(columna) + 1` (no autoincrement) |
| BR-CRED-14 | Transacciones: `crearSolicitudCompleta` y `guardarSoloCliente` usan transacciones con rollback |

### Tablas dependientes

| Tabla | Uso |
|-------|-----|
| `tclie_general` | Clientes (titular, cónyuge, avales) |
| `tclie_direccion` | Direcciones de clientes |
| `tclie_negocio` | Negocios de clientes |
| `tvinculacion` | Relaciones titular-conyugue y titular-aval |
| `ubigeo_departments` | Catálogo de departamentos |
| `ubigeo_provinces` | Catálogo de provincias |
| `ubigeo_districts` | Catálogo de distritos |
| `prestamo` (vía Prestamo model) | Préstamos |
| `movimiento` (vía Movimiento model) | Cuotas/movimientos |

---

## 2. Evaluacion.php (`app/Modules/Evaluacion/models/`)

### Qué hace
Modelo de evaluación crediticia: CRUD de evaluaciones, indicadores financieros, actividades, costos, deudas, gastos, ingresos, activos, y control de permisos de edición.

### Reglas de negocio

| ID | Regla |
|----|-------|
| BR-EVAL-01 | El ID de evaluación (`grupo`) se genera como `(int)(microtime(true) * 1000)` — timestamp en milisegundos |
| BR-EVAL-02 | Al crear evaluación: si `finalizar` está presente → estado `'completado'`, si no → `'borrador'` |
| BR-EVAL-03 | **Notificación automática**: si el resultado es `'rechazado'`, `'riesgo_medio'`, `'no_aprobado'` o `'no aprobado'`, se inserta notificación en `notificaciones_cliente` |
| BR-EVAL-04 | **Permiso de edición**: roles 1 (Super Admin) y 5 siempre pueden editar. Otros roles solo si son el creador o dueño del cliente, y si está en borrador o tiene `editar_permiso=1` |
| BR-EVAL-05 | `finalizar()`: marca `estado='completado'` y `editar_permiso=0` (bloquea edición) |
| BR-EVAL-06 | `permitirEditar()`: activa `editar_permiso=1` y resetea `editado_una_vez=0` |
| BR-EVAL-07 | Soft delete: `eliminado=1` + `eliminado_por` + `eliminado_at` (no borra físicamente) |
| BR-EVAL-08 | Eliminación definitiva: borra archivos físicos de `eval_activos.ruta_imagen` y luego elimina registros de todas las tablas `eval_*` |
| BR-EVAL-09 | **Exclusión de vinculados**: listados globales excluyen clientes que son cónyuge o aval de otro cliente (`NOT EXISTS tvinculacion`) |
| BR-EVAL-10 | **Filtro por sucursal**: usa COALESCE entre `idSucursal` directo y la primera sucursal vigente del asesor (`usuario_sucursal` con `estado=1` y `fecha_fin >= CURRENT_DATE`) |
| BR-EVAL-11 | **Próximos a vencer**: evaluaciones con `fecha_caducidad` entre hoy y +3 meses |
| BR-EVAL-12 | Última evaluación de un cliente: se obtiene con `ORDER BY grupo DESC, id_indicador ASC LIMIT 1` |
| BR-EVAL-13 | Detalles (actividades, costos, deudas, gastos, ingresos, activos) se guardan con patrón DELETE + INSERT (reemplazo completo) |
| BR-EVAL-14 | `resolverUbigeo()`: convierte códigos numéricos (2=dpto, 4=prov, 6=dist) a nombres usando tablas ubigeo |
| BR-EVAL-15 | Estadísticas de asesor: total clientes, evaluados, sin evaluar, próximos a vencer (3 meses) |

### Tablas dependientes

| Tabla | Uso |
|-------|-----|
| `eval_credito` | Cabecera de evaluación (monto, TEA, plazo, frecuencia, caducidad) |
| `eval_indicadores` | Indicadores financieros (excedente, capacidad_pago, endeudamiento, capital_trabajo, resultado) |
| `eval_actividades` | Actividades económicas del negocio |
| `eval_costos` | Costos del negocio |
| `eval_deudas` | Deudas financieras |
| `eval_gastos` | Gastos familiares |
| `eval_ingresos` | Ingresos extraordinarios |
| `eval_activos` | Activos del cliente |
| `tclie_general` | Datos del cliente |
| `sucursales` | Sucursal del cliente |
| `usuario_sucursal` | Asignación asesor-sucursal |
| `tvinculacion` | Vinculaciones cónyuge/aval |
| `notificaciones_cliente` | Notificaciones al cliente |
| `referencias_frecuencias` | Catálogo de frecuencias |
| `referencias_periodos` | Catálogo de periodos |
| `referencias_moras` | Catálogo de moras |
| `empresas` | Datos de la empresa |
| `ubigeo_departments/provinces/districts` | Catálogo geográfico |

---

## 3. SimuladorSolicitud.php (`app/Modules/Simulador/models/`)

### Qué hace
Registro de simulaciones y solicitudes de crédito desde el simulador público. Incluye anti-duplicados, notificaciones a administradores, y gestión de estados.

### Reglas de negocio

| ID | Regla |
|----|-------|
| BR-SIM-01 | **Anti-duplicados**: ventana de 60 segundos para SOLICITUD, 10 segundos para SIMULACION. Compara tipo + dni + ruc + razon_social + monto + cuotas + tipo_pago |
| BR-SIM-02 | **Notificación a admins**: al registrar, notifica a usuarios con `idRol IN (1, 5)` y `idEstado = 1` |
| BR-SIM-03 | **Asignación de asesor**: al asignar, si estaba PENDIENTE → pasa a EN_GESTION |
| BR-SIM-04 | **Estados**: PENDIENTE → EN_GESTION → ATENDIDO (flujo lineal) |
| BR-SIM-05 | **Rango de edad permitido**: 18 a 70 años (validado en `fechaNacimientoPermitida`) |
| BR-SIM-06 | **Cálculo de edad**: soporta formatos `YYYY-MM-DD` y `DD/MM/YYYY`; rechaza fechas futuras |
| BR-SIM-07 | `tipo_persona` por defecto: `'JURIDICA'` |
| BR-SIM-08 | `tipo_credito` por defecto: `'comercio'` |
| BR-SIM-09 | El cronograma se almacena como JSON en campo LONGTEXT |
| BR-SIM-10 | Al actualizar estado a ATENDIDO, se registra `atendido_at = NOW()` |
| BR-SIM-11 | `atendido_por` solo se actualiza si se provee un usuario (COALESCE) |
| BR-SIM-12 | Búsqueda con filtros: texto (dni, ruc, nombres, apellidos, razon_social, celular), estado, tipo_pago, rango de fechas, rango de monto, geo (con/sin coordenadas), asesor (con/sin) |
| BR-SIM-13 | Ordenamientos disponibles: recientes, antiguos, monto_desc, monto_asc |
| BR-SIM-14 | La tabla se auto-crea si no existe (`asegurarEsquema()`) — DDL idempotente |

### Tablas dependientes

| Tabla | Uso |
|-------|-----|
| `simulador_solicitudes` | Registro de simulaciones/solicitudes |
| `tusuarios` | Administradores a notificar |
| `tdatosu` | Datos del asesor asignado |
| `notificaciones` | Notificaciones del sistema |

---

## 4. Authorization.php (`app/Core/`) — Solo lógica de permisos y alcance

### Qué hace
Servicio central de autorización: verificación de permisos por rol, control de alcance (todas/sucursal/propio/ninguna), y contexto de sucursal.

### Reglas de negocio

| ID | Regla |
|----|-------|
| BR-AUTH-01 | **Roles**: 1=Super Admin, 2=Asesor, 3=Cliente, 4=Postulante, 5=admin_personal, 6=Soporte Web, 7=Plataforma, 8=Gerente |
| BR-AUTH-02 | **Super Admin**: bypass total de permisos y alcance. Se detecta por `idRol=1` O flag `esSuperAdmin=1` |
| BR-AUTH-03 | **Alcances válidos**: `'todas'`, `'sucursal'`, `'propio'`, `'ninguna'`. Si un permiso tiene alcance inválido, se asume `'todas'` |
| BR-AUTH-04 | **Permisos**: se cargan de `rol_permisos` JOIN `permisos` (solo `estado=1`). Formato: `modulo.accion` → alcance |
| BR-AUTH-05 | **Cache en sesión**: permisos se cachean en `$_SESSION['auth_permissions']` y `$_SESSION['auth_super_admin']` |
| BR-AUTH-06 | **Vigencia de asignación**: `usuario_sucursal` solo cuenta si `estado=1` AND (`fecha_fin IS NULL` OR `fecha_fin >= CURRENT_DATE`) |
| BR-AUTH-07 | **Contexto de sucursal**: si el usuario tiene 1 sola sucursal → se auto-selecciona. Si tiene varias → debe elegir vía `$_GET['sucursal']`. Si no elige → `blocked=true` |
| BR-AUTH-08 | **Gate de sucursal**: antes de mostrar datos, el usuario debe tener sucursal asignada o elegirla |
| BR-AUTH-09 | `canAccessBranch()`: Super Admin siempre true; si permiso tiene alcance `'todas'` → true; si no, verifica que la sucursal esté en sus asignaciones |
| BR-AUTH-10 | `requirePermission()`: redirige a URL base si no hay permiso (guard para controllers) |
| BR-AUTH-11 | `getUserCompanyIds()`: obtiene empresas de las sucursales asignadas (para multi-empresa) |

### Tablas dependientes

| Tabla | Uso |
|-------|-----|
| `tusuarios` | Usuarios (idRol, esSuperAdmin, idEstado) |
| `rol_permisos` | Asignación rol → permiso con alcance |
| `permisos` | Catálogo de permisos (modulo, accion, estado) |
| `usuario_sucursal` | Asignación usuario → sucursal con vigencia |
| `sucursales` | Sucursales (estado, tipo, empresa_id) |

---

## 5. MLService.php (`app/Services/`)

### Qué hace
Cliente HTTP para el servicio de Machine Learning (Python/Flask) que evalúa crédito y predice morosidad.

### Reglas de negocio

| ID | Regla |
|----|-------|
| BR-ML-01 | **Endpoint de evaluación**: `POST {ML_API_URL}/api/evaluar` con JSON `{documento, monto_solicitado, plazo, historial}` |
| BR-ML-02 | **Endpoint de predicción**: `GET {ML_API_URL}/api/prediccion/{clienteId}` |
| BR-ML-03 | **Timeout**: 15 segundos para evaluación, 10 segundos para predicción |
| BR-ML-04 | **Fallback de evaluación**: si falla → `score=50, recommendation='revisar_manual', risk='medio'` |
| BR-ML-05 | **Fallback de predicción**: si falla → `probabilidad_morosidad=0.5, nivel='medio'` |
| BR-ML-06 | **Autenticación**: header `X-API-Key` con `ML_API_KEY` |
| BR-ML-07 | **URL por defecto**: `http://localhost:5000` (si no está definida la constante) |

### Dependencias externas

| Servicio | Uso |
|----------|-----|
| ML API (Python) | Evaluación crediticia y predicción de morosidad |

---

## 6. EncryptionService.php (`app/Services/`)

### Qué hace
Servicio de cifrado/descifrado de datos sensibles usando AES-256-GCM.

### Reglas de negocio

| ID | Regla |
|----|-------|
| BR-ENC-01 | **Algoritmo**: AES-256-GCM (autenticado) |
| BR-ENC-02 | **Key**: derivada de `APP_KEY` (base64_decode). Si no es de 32 bytes, se aplica SHA-256 y se trunca a 32 bytes |
| BR-ENC-03 | **Formato cifrado**: `base64(IV + tag + ciphertext)` donde IV = 12 bytes, tag = 16 bytes |
| BR-ENC-04 | **IV aleatorio**: se genera con `openssl_random_pseudo_bytes()` en cada cifrado |
| BR-ENC-05 | **Hash**: HMAC-SHA256 para integridad de datos |
| BR-ENC-06 | **Token**: `bin2hex(random_bytes(32)))` = 64 caracteres hex |
| BR-ENC-07 | **Excepciones**: lanza `RuntimeException` en error de cifrado/descifrado/datos inválidos |

### Dependencias

| Constante | Uso |
|-----------|-----|
| `APP_KEY` | Clave maestra de cifrado |

---

## 7. Cliente.php (`app/Admin/models/`)

### Qué hace
Modelo de administración de clientes: CRUD completo, gestión de direcciones, negocios, vínculos (cónyuge/aval), código de cliente, y borrado definitivo con limpieza de archivos.

### Reglas de negocio

| ID | Regla |
|----|-------|
| BR-CLI-01 | **Código de cliente**: formato `{PREFIJO_SUCURSAL}-{NUMERO}` (ej: `SUC-0001`). Se genera automáticamente al asignar sucursal |
| BR-CLI-02 | **Prefijo de sucursal**: mayúsculas del campo `codigo` de `sucursales`. Si está vacío → `'SUC'` |
| BR-CLI-03 | **Solo titulares**: listados excluyen clientes que son cónyuge o aval de otro (`NOT EXISTS tvinculacion`) |
| BR-CLI-04 | **Estado por defecto**: solo muestra `status='ACTIVE'` o `NULL` si no se filtra |
| BR-CLI-05 | **Filtro "nuevos"**: clientes con `fecha_registro >= NOW() - INTERVAL N DAY` |
| BR-CLI-06 | **Filtro sucursal**: usa COALESCE entre `idSucursal` directo y primera sucursal vigente del asesor |
| BR-CLI-07 | **Soft delete**: `status='INACTIVE'` (no borra físicamente) |
| BR-CLI-08 | **Borrado definitivo**: elimina archivos físicos (imágenes en `public/uploads/credito/`, `assets/img/`), luego elimina registros de `tclie_general`, `tclie_negocio`, `tclie_direccion`, `tvinculacion`, y todas las tablas `eval_*` |
| BR-CLI-09 | **Vinculación 1:N**: un aval puede estar en múltiples titulares (campo `aval` es CSV en `tvinculacion`) |
| BR-CLI-10 | **Cónyuge 1:1**: solo un cónyuge por titular (campo `conyugue` es ID único) |
| BR-CLI-11 | **IDs manuales**: `MAX(idCG) + 1` para `tclie_general`, `tclie_direccion`, `tclie_negocio`, `tvinculacion` |
| BR-CLI-12 | **Avatar**: campo `imgC` se actualiza con la foto `cliente_solo` o `dni_frente` |
| BR-CLI-13 | **Comentario JSON**: campo `comentario` almacena datos estructurados (vivienda, negocio, conyuge, aval, fotos) |
| BR-CLI-14 | **Sincronización de código**: al cambiar `idSucursal`, se regenera el `codigo_cliente` si el prefijo no coincide |
| BR-CLI-15 | **Asesores**: roles 2 (Asesor) y 5 (admin_personal) son considerados asesores |
| BR-CLI-16 | **Búsqueda**: por dni, nom, ap, am, teléfono, o nombre completo. Límite 50 resultados |

### Tablas dependientes

| Tabla | Uso |
|-------|-----|
| `tclie_general` | Clientes |
| `tclie_direccion` | Direcciones |
| `tclie_negocio` | Negocios |
| `tvinculacion` | Vinculaciones |
| `sucursales` | Sucursales |
| `usuario_sucursal` | Asignación asesor-sucursal |
| `tusuarios` | Usuarios/asesores |
| `tdatosu` | Datos personales del asesor |
| `ubigeo_departments/provinces/districts` | Catálogo geográfico |
| `eval_*` (7 tablas) | Evaluaciones crediticias |

---

## 8. Sucursal.php (`app/Admin/models/`)

### Qué hace
Modelo de administración de sucursales: CRUD, generación de códigos, gestión de asignaciones de asesores, y trabajadores.

### Reglas de negocio

| ID | Regla |
|----|-------|
| BR-SUC-01 | **Tipos de sucursal**: `'principal'` y `'sucursal'`. Orden: principal primero, luego por nombre |
| BR-SUC-02 | **Generación de código**: mayúsculas, sin acentos, sin vocales, primeros 3 caracteres. Ej: "Oficina Megan" → `FCN` |
| BR-SUC-03 | **Código único**: si ya existe, agrega sufijo numérico (STP → STP2 → STP3...) |
| BR-SUC-04 | **Asignación 1:1**: un trabajador pertenece a UNA sola sucursal. Al asignar, se elimina de otras |
| BR-SUC-05 | **Roles gestionables**: solo roles 2, 5, 6, 7, 8 (excluye 1=Super Admin, 3=Cliente, 4=Postulante) |
| BR-SUC-06 | **Clientes/postulantes**: sus asignaciones a sucursal se conservan (no se eliminan en `guardarAsesoresSucursal`) |
| BR-SUC-07 | **Contrato**: fechas `fecha_inicio` y `fecha_fin` por asignación |
| BR-SUC-08 | **Actualización de contrato**: `actualizarContratoAsesor` NO desasigna de otras sucursales (a diferencia de `guardarAsesoresSucursal`) |
| BR-SUC-09 | **IDs manuales**: `MAX(idS) + 1` |
| BR-SUC-10 | **Campos JSON**: `tipos_credito`, `tipos_ahorro`, `tipos_seguro`, `servicios` se almacenan como JSON arrays |

### Tablas dependientes

| Tabla | Uso |
|-------|-----|
| `sucursales` | Sucursales |
| `usuario_sucursal` | Asignación usuario-sucursal |
| `tusuarios` | Usuarios/trabajadores |
| `tdatosu` | Datos personales |
| `rol` | Roles |
| `ubigeo_departments` | Catálogo geográfico |

---

## 9. TipoCredito.php (`app/Admin/models/`)

### Qué hace
Modelo de catálogo de tipos de crédito: CRUD con parámetros financieros y de configuración.

### Reglas de negocio

| ID | Regla |
|----|-------|
| BR-TIP-01 | **Ordenamiento**: por `categoria ASC, orden ASC` |
| BR-TIP-02 | **Categoría por defecto**: `'personal'` |
| BR-TIP-03 | **Estado por defecto**: `1` (activo) |
| BR-TIP-04 | **Parámetros financieros**: `tasa_anual`, `tasa_mensual`, `monto_minimo`, `monto_maximo`, `plazo_min/max_dias`, `plazo_min/max_semanas`, `cuota_inicial`, `comision`, `seguro_desgravamen` |
| BR-TIP-05 | **Campos de contenido**: `requisitos`, `beneficios`, `caracteristicas` (texto largo) |
| BR-TIP-06 | **Campos visuales**: `icono`, `imagen`, `orden` |
| BR-TIP-07 | **IDs manuales**: `MAX(idTC) + 1` |
| BR-TIP-08 | **Delete físico**: `DELETE FROM tipos_credito` (no soft delete) |

### Tablas dependientes

| Tabla | Uso |
|-------|-----|
| `tipos_credito` | Catálogo de tipos de crédito |

---

## 10. MetodoPago.php (`app/Admin/models/`)

### Qué hace
Modelo de métodos de pago (cuentas bancarias): CRUD, vinculación con sucursales, ordenamiento, y gestión multi-empresa.

### Reglas de negocio

| ID | Regla |
|----|-------|
| BR-MET-01 | **Soft delete**: `estado=0` (no borra físicamente) |
| BR-MET-02 | **Eliminación definitiva**: borra de `sucursal_metodo_pago` y `bank_accounts`, retorna archivos (qr, icono) para limpieza |
| BR-MET-03 | **Vinculación con empresa**: al crear/editar, se vincula a TODAS las sucursales de la empresa (`INSERT IGNORE`) |
| BR-MET-04 | **Vinculación con sucursal específica**: `vincularASucursal` solo vincula a UNA sucursal (sin esparcir) |
| BR-MET-05 | **Orden por sucursal**: el campo `orden` en `sucursal_metodo_pago` es independiente por sucursal |
| BR-MET-06 | **Desplazamiento**: intercambia orden con el vecino activo más cercano (dentro de la misma sucursal o global) |
| BR-MET-07 | **Multi-empresa**: `getAll($empresaIds)` filta por empresa. Si no hay empresas asignadas → primera empresa activa |
| BR-MET-08 | **Empresas de usuario**: se obtienen vía `usuario_sucursal` → `sucursales.empresa_id` |
| BR-MET-09 | **Estado por sucursal**: `setEstadoSucursal` actualiza solo en la tabla intermedia |
| BR-MET-10 | **Estado global**: `setEstadoTodasSucursales` actualiza en todas las sucursales vinculadas |
| BR-MET-11 | **Campos**: `type`, `name`, `number`, `headline_name`, `destination_id`, `cci`, `qr`, `icono` |
| BR-MET-12 | **IDs manuales**: `MAX(id) + 1` para `bank_accounts` |

### Tablas dependientes

| Tabla | Uso |
|-------|-----|
| `bank_accounts` | Cuentas bancarias / métodos de pago |
| `sucursal_metodo_pago` | Tabla intermedia sucursal-método (con estado y orden) |
| `sucursales` | Sucursales |
| `empresas` | Empresas |
| `usuario_sucursal` | Asignación usuario-sucursal |

---

## Resumen de tablas únicas identificadas

| Tabla | Módulo(s) |
|-------|-----------|
| `tclie_general` | Credito, Evaluacion, Admin/Cliente |
| `tclie_direccion` | Credito, Admin/Cliente |
| `tclie_negocio` | Credito, Admin/Cliente |
| `tvinculacion` | Credito, Evaluacion, Admin/Cliente |
| `eval_credito` | Evaluacion |
| `eval_indicadores` | Evaluacion |
| `eval_actividades` | Evaluacion |
| `eval_costos` | Evaluacion |
| `eval_deudas` | Evaluacion |
| `eval_gastos` | Evaluacion |
| `eval_ingresos` | Evaluacion |
| `eval_activos` | Evaluacion |
| `simulador_solicitudes` | Simulador |
| `sucursales` | Evaluacion, Admin/Sucursal, Admin/MetodoPago |
| `usuario_sucursal` | Evaluacion, Admin/Sucursal, Admin/MetodoPago, Admin/Cliente |
| `tusuarios` | Authorization, Admin/Sucursal, Admin/Cliente, Simulador |
| `tdatosu` | Admin/Sucursal, Admin/Cliente, Simulador |
| `rol` | Admin/Sucursal |
| `rol_permisos` | Authorization |
| `permisos` | Authorization |
| `tipos_credito` | Admin/TipoCredito |
| `bank_accounts` | Admin/MetodoPago |
| `sucursal_metodo_pago` | Admin/MetodoPago |
| `empresas` | Evaluacion, Admin/MetodoPago |
| `ubigeo_departments` | Credito, Evaluacion, Admin/Cliente, Admin/Sucursal |
| `ubigeo_provinces` | Credito, Evaluacion, Admin/Cliente |
| `ubigeo_districts` | Credito, Evaluacion, Admin/Cliente |
| `notificaciones` | Simulador |
| `notificaciones_cliente` | Evaluacion |
| `referencias_frecuencias` | Evaluacion |
| `referencias_periodos` | Evaluacion |
| `referencias_moras` | Evaluacion |
