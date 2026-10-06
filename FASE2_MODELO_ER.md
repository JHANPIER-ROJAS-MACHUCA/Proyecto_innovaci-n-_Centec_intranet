# FASE 2: MODELO ENTIDAD-RELACIÓN

## 1. DIAGRAMA ENTIDAD-RELACIÓN (MER)

### 1.1 Entidades principales y sus relaciones

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│    EMPRESA      │────<│   SUCURSAL      │────<│    USUARIO      │
│  (1)            │  1:N│  (N)            │  1:N│  (N)            │
└─────────────────┘     └─────────────────┘     └─────────────────┘
         │                       │                       │
         │                       │                       │
         │              ┌────────┴────────┐              │
         │              │                 │              │
         │              ▼                 ▼              │
         │       ┌─────────────┐   ┌─────────────┐       │
         │       │    CAJA     │   │  USUARIO_   │       │
         │       │  (N)        │   │  SUCURSAL   │       │
         │       └─────────────┘   └─────────────┘       │
         │              │                                 │
         │              │                                 │
         │              ▼                                 │
         │       ┌─────────────┐                          │
         │       │  MOVIMIENTO │                          │
         │       │  _CAJA (N)  │                          │
         │       └─────────────┘                          │
         │                                                │
         │       ┌─────────────┐                          │
         └──────<│   CLIENTE   │>─────────────────────────┘
              1:N│  (N)        │  1:N
                 └─────────────┘
                        │
         ┌──────────────┼──────────────┐
         │              │              │
         ▼              ▼              ▼
┌─────────────┐  ┌─────────────┐  ┌─────────────┐
│   CUENTA    │  │  SOLICITUD  │  │  PROSPECTO  │
│  _AHORRO(N) │  │  _CREDITO(N)│  │  (N)        │
└─────────────┘  └─────────────┘  └─────────────┘
                        │
                        ▼
                 ┌─────────────┐
                 │   CREDITO   │
                 │   (N)       │
                 └─────────────┘
                        │
         ┌──────────────┼──────────────┐
         │              │              │
         ▼              ▼              ▼
┌─────────────┐  ┌─────────────┐  ┌─────────────┐
│  CRONOGRAMA │  │   CUOTA     │  │ DESEMBOLSO  │
│   (N)       │  │   (N)       │  │   (N)       │
└─────────────┘  └─────────────┘  └─────────────┘
                        │
                        ▼
                 ┌─────────────┐
                 │  COBRANZA   │
                 │   (N)       │
                 └─────────────┘
```

---

## 2. ENTIDADES DETALLADAS

### 2.1 EMPRESA
| Atributo | Tipo | Descripción |
|----------|------|-------------|
| id | INT PK | Identificador único |
| nombre | VARCHAR(200) | Nombre comercial |
| razon_social | VARCHAR(200) | Razón social |
| ruc | VARCHAR(11) | RUC |
| direccion | VARCHAR(300) | Dirección principal |
| telefono | VARCHAR(20) | Teléfono |
| email | VARCHAR(100) | Email |
| logo | VARCHAR(255) | Ruta del logo |
| estado | ENUM('activo','inactivo') | Estado |
| created_at | TIMESTAMP | Fecha creación |
| updated_at | TIMESTAMP | Fecha modificación |

**Relaciones:**
- 1:N → SUCURSAL
- 1:N → CAJA (caja general)

---

### 2.2 SUCURSAL
| Atributo | Tipo | Descripción |
|----------|------|-------------|
| id | INT PK | Identificador único |
| empresa_id | INT FK | → EMPRESA.id |
| codigo | VARCHAR(10) UNIQUE | SUC-001, SUC-002... |
| nombre | VARCHAR(200) | Nombre de sucursal |
| direccion | VARCHAR(300) | Dirección |
| telefono | VARCHAR(20) | Teléfono |
| email | VARCHAR(100) | Email |
| departamento | VARCHAR(50) | Departamento |
| provincia | VARCHAR(50) | Provincia |
| distrito | VARCHAR(50) | Distrito |
| latitud | DECIMAL(10,8) | GPS |
| longitud | DECIMAL(11,8) | GPS |
| estado | ENUM('activo','inactivo') | Estado |
| created_at | TIMESTAMP | Fecha creación |
| updated_at | TIMESTAMP | Fecha modificación |

**Relaciones:**
- N:1 → EMPRESA
- 1:N → USUARIO_SUCURSAL
- 1:N → CAJA
- 1:N → CLIENTE
- 1:N → CUENTA_AHORRO
- 1:N → CREDITO
- 1:N → MOVIMIENTO_CAJA

---

### 2.3 ROL
| Atributo | Tipo | Descripción |
|----------|------|-------------|
| id | INT PK | Identificador único |
| nombre | VARCHAR(50) UNIQUE | TI, GERENCIA, ADMIN_SUCURSAL, PLATAFORMA, ASESOR, SEGUIMIENTO |
| descripcion | VARCHAR(255) | Descripción |
| nivel | INT | Jerarquía (1=TI, 2=GERENCIA, 3=ADMIN, 4=OPERATIVO) |
| estado | ENUM('activo','inactivo') | Estado |

**Roles del sistema:**
1. TI
2. GERENCIA
3. ADMINISTRADOR_SUCURSAL
4. PLATAFORMA
5. ASESOR
6. SEGUIMIENTO

---

### 2.4 PERMISO
| Atributo | Tipo | Descripción |
|----------|------|-------------|
| id | INT PK | Identificador único |
| modulo | VARCHAR(50) | Módulo (clientes, creditos, cajas...) |
| accion | VARCHAR(50) | Acción (crear, editar, eliminar, ver) |
| descripcion | VARCHAR(255) | Descripción |

---

### 2.5 ROL_PERMISO
| Atributo | Tipo | Descripción |
|----------|------|-------------|
| id | INT PK | Identificador único |
| rol_id | INT FK | → ROL.id |
| permiso_id | INT FK | → PERMISO.id |
| alcance | ENUM('todas','sucursal','propio','ninguna') | Alcance del permiso |

---

### 2.6 USUARIO
| Atributo | Tipo | Descripción |
|----------|------|-------------|
| id | INT PK | Identificador único |
| username | VARCHAR(50) UNIQUE | Login |
| password | VARCHAR(255) | Password bcrypt |
| email | VARCHAR(100) UNIQUE | Email |
| nombre | VARCHAR(100) | Nombre |
| apellido_paterno | VARCHAR(100) | Apellido paterno |
| apellido_materno | VARCHAR(100) | Apellido materno |
| dni | VARCHAR(8) UNIQUE | DNI |
| telefono | VARCHAR(20) | Teléfono |
| direccion | VARCHAR(300) | Dirección |
| rol_id | INT FK | → ROL.id |
| estado | ENUM('activo','inactivo','bloqueado') | Estado |
| ultimo_login | DATETIME | Último acceso |
| created_at | TIMESTAMP | Fecha creación |
| updated_at | TIMESTAMP | Fecha modificación |

---

### 2.7 USUARIO_SUCURSAL
| Atributo | Tipo | Descripción |
|----------|------|-------------|
| id | INT PK | Identificador único |
| usuario_id | INT FK | → USUARIO.id |
| sucursal_id | INT FK | → SUCURSAL.id |
| tipo_asignacion | ENUM('principal','secundaria') | Tipo |
| estado | ENUM('activo','inactivo') | Estado |
| fecha_inicio | DATE | Fecha inicio |
| fecha_fin | DATE | Fecha fin |

---

### 2.8 CLIENTE
| Atributo | Tipo | Descripción |
|----------|------|-------------|
| id | INT PK | Identificador único |
| codigo_cliente | VARCHAR(20) UNIQUE | CLI-000001 |
| sucursal_id | INT FK | → SUCURSAL.id |
| tipo_persona | ENUM('natural','juridica') | Tipo |
| dni | VARCHAR(8) | DNI (natural) |
| ruc | VARCHAR(11) | RUC (jurídica) |
| nombre | VARCHAR(100) | Nombre / Razón social |
| apellido_paterno | VARCHAR(100) | Apellido paterno |
| apellido_materno | VARCHAR(100) | Apellido materno |
| fecha_nacimiento | DATE | Fecha nacimiento |
| sexo | ENUM('M','F') | Sexo |
| estado_civil | VARCHAR(20) | Estado civil |
| direccion | VARCHAR(300) | Dirección |
| telefono | VARCHAR(20) | Teléfono |
| email | VARCHAR(100) | Email |
| ocupacion | VARCHAR(100) | Ocupación |
| ingreso_mensual | DECIMAL(12,2) | Ingreso mensual |
| estado | ENUM('pendiente','inactivo','activo','bloqueado','cancelado') | Estado |
| usuario_creador_id | INT FK | → USUARIO.id |
| created_at | TIMESTAMP | Fecha creación |
| updated_at | TIMESTAMP | Fecha modificación |

---

### 2.9 PROSPECTO
| Atributo | Tipo | Descripción |
|----------|------|-------------|
| id | INT PK | Identificador único |
| sucursal_id | INT FK | → SUCURSAL.id |
| asesor_id | INT FK | → USUARIO.id |
| dni | VARCHAR(8) | DNI |
| nombre | VARCHAR(100) | Nombre |
| apellido_paterno | VARCHAR(100) | Apellido paterno |
| apellido_materno | VARCHAR(100) | Apellido materno |
| telefono | VARCHAR(20) | Teléfono |
| email | VARCHAR(100) | Email |
| direccion | VARCHAR(300) | Dirección |
| interes | ENUM('credito','ahorro','ambos') | Interés |
| estado | ENUM('prospecto','evaluacion','convertido','descartado') | Estado |
| observaciones | TEXT | Observaciones |
| created_at | TIMESTAMP | Fecha creación |
| updated_at | TIMESTAMP | Fecha modificación |

---

### 2.10 TIPO_AHORRO
| Atributo | Tipo | Descripción |
|----------|------|-------------|
| id | INT PK | Identificador único |
| nombre | VARCHAR(100) | Nombre |
| slug | VARCHAR(100) UNIQUE | URL amigable |
| descripcion | TEXT | Descripción |
| tasa_anual | DECIMAL(5,2) | TEA % |
| tasa_mensual | DECIMAL(5,2) | TEM % |
| saldo_minimo | DECIMAL(12,2) | Saldo mínimo |
| estado | ENUM('activo','inactivo') | Estado |

---

### 2.11 CUENTA_AHORRO
| Atributo | Tipo | Descripción |
|----------|------|-------------|
| id | INT PK | Identificador único |
| numero_cuenta | VARCHAR(20) UNIQUE | 001-000000001 |
| cliente_id | INT FK | → CLIENTE.id |
| sucursal_id | INT FK | → SUCURSAL.id |
| tipo_ahorro_id | INT FK | → TIPO_AHORRO.id |
| saldo | DECIMAL(12,2) DEFAULT 0 | Saldo actual |
| estado | ENUM('activa','inactiva','bloqueada','cancelada') | Estado |
| fecha_apertura | DATE | Fecha apertura |
| usuario_creador_id | INT FK | → USUARIO.id |
| created_at | TIMESTAMP | Fecha creación |
| updated_at | TIMESTAMP | Fecha modificación |

---

### 2.12 TIPO_CREDITO
| Atributo | Tipo | Descripción |
|----------|------|-------------|
| id | INT PK | Identificador único |
| nombre | VARCHAR(100) | Nombre |
| slug | VARCHAR(100) UNIQUE | URL amigable |
| descripcion | TEXT | Descripción |
| categoria | ENUM('personal','vehicular','prendario','empresarial') | Categoría |
| tasa_anual | DECIMAL(5,2) | TEA % |
| tasa_mensual | DECIMAL(5,2) | TEM % |
| monto_minimo | DECIMAL(12,2) | Monto mínimo |
| monto_maximo | DECIMAL(12,2) | Monto máximo |
| plazo_minimo | INT | Plazo mínimo (meses) |
| plazo_maximo | INT | Plazo máximo (meses) |
| comision | DECIMAL(5,2) | Comisión % |
| seguro_desgravamen | DECIMAL(5,2) | Seguro % |
| requisitos | TEXT | Requisitos |
| estado | ENUM('activo','inactivo') | Estado |

---

### 2.13 SOLICITUD_CREDITO
| Atributo | Tipo | Descripción |
|----------|------|-------------|
| id | INT PK | Identificador único |
| numero_solicitud | VARCHAR(20) UNIQUE | SOL-000001 |
| cliente_id | INT FK | → CLIENTE.id |
| sucursal_id | INT FK | → SUCURSAL.id |
| asesor_id | INT FK | → USUARIO.id |
| tipo_credito_id | INT FK | → TIPO_CREDITO.id |
| monto_solicitado | DECIMAL(12,2) | Monto solicitado |
| plazo | INT | Plazo (meses) |
| frecuencia_pago | ENUM('diario','semanal','quincenal','mensual') | Frecuencia |
| estado | ENUM('pendiente','evaluacion','aprobado','desaprobado','observado','desembolsado') | Estado |
| observaciones | TEXT | Observaciones |
| created_at | TIMESTAMP | Fecha creación |
| updated_at | TIMESTAMP | Fecha modificación |

---

### 2.14 CREDITO
| Atributo | Tipo | Descripción |
|----------|------|-------------|
| id | INT PK | Identificador único |
| numero_credito | VARCHAR(20) UNIQUE | CRE-000001 |
| solicitud_id | INT FK | → SOLICITUD_CREDITO.id |
| cliente_id | INT FK | → CLIENTE.id |
| sucursal_id | INT FK | → SUCURSAL.id |
| tipo_credito_id | INT FK | → TIPO_CREDITO.id |
| monto_aprobado | DECIMAL(12,2) | Monto aprobado |
| monto_desembolsado | DECIMAL(12,2) | Monto desembolsado |
| tasa_anual | DECIMAL(5,2) | TEA % |
| tasa_mensual | DECIMAL(5,2) | TEM % |
| plazo | INT | Plazo (meses) |
| frecuencia_pago | ENUM('diario','semanal','quincenal','mensual') | Frecuencia |
| fecha_desembolso | DATE | Fecha desembolso |
| fecha_vencimiento | DATE | Fecha vencimiento |
| estado | ENUM('vigente','cancelado','liquidado','refinanciado','cobro_judicial') | Estado |
| created_at | TIMESTAMP | Fecha creación |
| updated_at | TIMESTAMP | Fecha modificación |

---

### 2.15 CRONOGRAMA
| Atributo | Tipo | Descripción |
|----------|------|-------------|
| id | INT PK | Identificador único |
| credito_id | INT FK | → CREDITO.id |
| numero_cuota | INT | Número de cuota |
| fecha_pago | DATE | Fecha programada |
| monto_cuota | DECIMAL(12,2) | Monto cuota |
| capital | DECIMAL(12,2) | Amortización capital |
| interes | DECIMAL(12,2) | Interés |
| saldo_capital | DECIMAL(12,2) | Saldo capital |
| estado | ENUM('pendiente','pagada','vencida','condonada') | Estado |

---

### 2.16 CUOTA
| Atributo | Tipo | Descripción |
|----------|------|-------------|
| id | INT PK | Identificador único |
| credito_id | INT FK | → CREDITO.id |
| cronograma_id | INT FK | → CRONOGRAMA.id |
| numero_cuota | INT | Número de cuota |
| fecha_vencimiento | DATE | Fecha vencimiento |
| monto_cuota | DECIMAL(12,2) | Monto cuota |
| capital | DECIMAL(12,2) | Capital |
| interes | DECIMAL(12,2) | Interés |
| mora | DECIMAL(12,2) | Mora |
| estado | ENUM('pendiente','pagada','vencida','parcial') | Estado |
| created_at | TIMESTAMP | Fecha creación |
| updated_at | TIMESTAMP | Fecha modificación |

---

### 2.17 DESEMBOLSO
| Atributo | Tipo | Descripción |
|----------|------|-------------|
| id | INT PK | Identificador único |
| numero_desembolso | VARCHAR(20) UNIQUE | DES-000001 |
| credito_id | INT FK | → CREDITO.id |
| cliente_id | INT FK | → CLIENTE.id |
| sucursal_id | INT FK | → SUCURSAL.id |
| caja_id | INT FK | → CAJA.id |
| usuario_id | INT FK | → USUARIO.id |
| monto | DECIMAL(12,2) | Monto desembolsado |
| metodo_desembolso | ENUM('efectivo','transferencia','cheque') | Método |
| fecha | DATE | Fecha |
| estado | ENUM('pendiente','realizado','anulado') | Estado |
| created_at | TIMESTAMP | Fecha creación |

---

### 2.18 CAJA
| Atributo | Tipo | Descripción |
|----------|------|-------------|
| id | INT PK | Identificador único |
| codigo | VARCHAR(20) UNIQUE | CAJ-001 |
| tipo | ENUM('general','ti','sucursal','operativa') | Tipo |
| nivel | INT | Jerarquía (1=Gerencia, 2=TI, 3=Sucursal, 4=Operativa) |
| empresa_id | INT FK NULL | → EMPRESA.id (caja general) |
| sucursal_id | INT FK NULL | → SUCURSAL.id |
| usuario_id | INT FK NULL | → USUARIO.id (cajas operativas) |
| saldo_inicial | DECIMAL(12,2) DEFAULT 0 | Saldo inicial |
| saldo_actual | DECIMAL(12,2) DEFAULT 0 | Saldo actual |
| estado | ENUM('abierta','cerrada','bloqueada') | Estado |
| fecha_apertura | DATETIME | Fecha apertura |
| fecha_cierre | DATETIME | Fecha cierre |
| created_at | TIMESTAMP | Fecha creación |
| updated_at | TIMESTAMP | Fecha modificación |

---

### 2.19 APERTURA_CAJA
| Atributo | Tipo | Descripción |
|----------|------|-------------|
| id | INT PK | Identificador único |
| caja_id | INT FK | → CAJA.id |
| usuario_id | INT FK | → USUARIO.id |
| monto_inicial | DECIMAL(12,2) | Monto inicial |
| fecha | DATETIME | Fecha apertura |
| observaciones | TEXT | Observaciones |

---

### 2.20 CIERRE_CAJA
| Atributo | Tipo | Descripción |
|----------|------|-------------|
| id | INT PK | Identificador único |
| caja_id | INT FK | → CAJA.id |
| usuario_id | INT FK | → USUARIO.id |
| saldo_sistema | DECIMAL(12,2) | Saldo según sistema |
| saldo_fisico | DECIMAL(12,2) | Dinero contado |
| faltante | DECIMAL(12,2) | Faltante |
| sobrante | DECIMAL(12,2) | Sobrante |
| fecha | DATETIME | Fecha cierre |
| observaciones | TEXT | Observaciones |

---

### 2.21 MOVIMIENTO_CAJA
| Atributo | Tipo | Descripción |
|----------|------|-------------|
| id | INT PK | Identificador único |
| sucursal_id | INT FK | → SUCURSAL.id |
| caja_id | INT FK | → CAJA.id |
| usuario_id | INT FK | → USUARIO.id |
| tipo | ENUM('apertura','deposito','retiro','cobro','desembolso','transferencia_entrada','transferencia_salida','ajuste','cierre') | Tipo |
| concepto | VARCHAR(255) | Concepto |
| monto | DECIMAL(12,2) | Monto |
| saldo_anterior | DECIMAL(12,2) | Saldo anterior |
| saldo_posterior | DECIMAL(12,2) | Saldo posterior |
| referencia | VARCHAR(100) | Referencia |
| estado | ENUM('completado','anulado') | Estado |
| fecha | DATETIME | Fecha |
| created_at | TIMESTAMP | Fecha creación |

---

### 2.22 TRANSFERENCIA
| Atributo | Tipo | Descripción |
|----------|------|-------------|
| id | INT PK | Identificador único |
| caja_origen_id | INT FK | → CAJA.id |
| caja_destino_id | INT FK | → CAJA.id |
| usuario_id | INT FK | → USUARIO.id |
| monto | DECIMAL(12,2) | Monto transferido |
| concepto | VARCHAR(255) | Concepto |
| fecha | DATETIME | Fecha |
| estado | ENUM('completada','anulada') | Estado |

---

### 2.23 COBRANZA
| Atributo | Tipo | Descripción |
|----------|------|-------------|
| id | INT PK | Identificador único |
| numero_cobranza | VARCHAR(20) UNIQUE | COB-000001 |
| cliente_id | INT FK | → CLIENTE.id |
| credito_id | INT FK | → CREDITO.id |
| cuota_id | INT FK | → CUOTA.id |
| caja_id | INT FK | → CAJA.id |
| usuario_id | INT FK | → USUARIO.id |
| sucursal_id | INT FK | → SUCURSAL.id |
| monto | DECIMAL(12,2) | Monto cobrado |
| metodo_pago | ENUM('efectivo','transferencia','yape','plin') | Método |
| comprobante | VARCHAR(100) | N° comprobante |
| fecha | DATETIME | Fecha |
| estado | ENUM('completada','anulada') | Estado |
| created_at | TIMESTAMP | Fecha creación |

---

### 2.24 VISITA
| Atributo | Tipo | Descripción |
|----------|------|-------------|
| id | INT PK | Identificador único |
| cliente_id | INT FK | → CLIENTE.id |
| prospecto_id | INT FK NULL | → PROSPECTO.id |
| usuario_id | INT FK | → USUARIO.id |
| tipo | ENUM('visita','llamada','whatsapp','email') | Tipo |
| fecha | DATETIME | Fecha |
| resultado | TEXT | Resultado |
| observaciones | TEXT | Observaciones |
| created_at | TIMESTAMP | Fecha creación |

---

### 2.25 SEGUIMIENTO
| Atributo | Tipo | Descripción |
|----------|------|-------------|
| id | INT PK | Identificador único |
| cliente_id | INT FK | → CLIENTE.id |
| credito_id | INT FK NULL | → CREDITO.id |
| usuario_id | INT FK | → USUARIO.id |
| tipo | ENUM('cobranza','seguimiento','reclamo') | Tipo |
| descripcion | TEXT | Descripción |
| acuerdo | TEXT | Acuerdo |
| fecha | DATETIME | Fecha |
| created_at | TIMESTAMP | Fecha creación |

---

### 2.26 AUDITORIA
| Atributo | Tipo | Descripción |
|----------|------|-------------|
| id | INT PK | Identificador único |
| usuario_id | INT FK | → USUARIO.id |
| accion | VARCHAR(50) | Acción (CREATE, UPDATE, DELETE, LOGIN...) |
| tabla | VARCHAR(50) | Tabla afectada |
| registro_id | INT | ID del registro |
| valor_anterior | TEXT NULL | Valor anterior (JSON) |
| valor_nuevo | TEXT NULL | Valor nuevo (JSON) |
| ip | VARCHAR(45) | IP del usuario |
| user_agent | VARCHAR(255) | Navegador |
| fecha | DATETIME | Fecha |

---

### 2.27 DOCUMENTO
| Atributo | Tipo | Descripción |
|----------|------|-------------|
| id | INT PK | Identificador único |
| tipo | ENUM('comprobante','recibo','contrato','estado_cuenta','reporte') | Tipo |
| numero | VARCHAR(20) UNIQUE | Número documento |
| cliente_id | INT FK NULL | → CLIENTE.id |
| credito_id | INT FK NULL | → CREDITO.id |
| cuenta_id | INT FK NULL | → CUENTA_AHORRO.id |
| sucursal_id | INT FK | → SUCURSAL.id |
| usuario_id | INT FK | → USUARIO.id |
| archivo | VARCHAR(255) | Ruta del archivo |
| fecha | DATETIME | Fecha |
| created_at | TIMESTAMP | Fecha creación |

---

## 3. RESUMEN DE RELACIONES PRINCIPALES

| Entidad A | Relación | Entidad B | Cardinalidad | Descripción |
|-----------|-----------|-----------|--------------|-------------|
| EMPRESA | tiene | SUCURSAL | 1:N | Una empresa tiene muchas sucursales |
| SUCURSAL | tiene | USUARIO_SUCURSAL | 1:N | Una sucursal tiene muchos usuarios asignados |
| USUARIO | tiene | USUARIO_SUCURSAL | 1:N | Un usuario puede estar en varias sucursales |
| ROL | tiene | ROL_PERMISO | 1:N | Un rol tiene muchos permisos |
| PERMISO | tiene | ROL_PERMISO | 1:N | Un permiso está en muchos roles |
| SUCURSAL | tiene | CLIENTE | 1:N | Una sucursal tiene muchos clientes |
| CLIENTE | tiene | CUENTA_AHORRO | 1:N | Un cliente tiene muchas cuentas |
| CLIENTE | tiene | SOLICITUD_CREDITO | 1:N | Un cliente tiene muchas solicitudes |
| SOLICITUD_CREDITO | genera | CREDITO | 1:1 | Una solicitud genera un crédito |
| CREDITO | tiene | CRONOGRAMA | 1:N | Un crédito tiene muchas cuotas programadas |
| CREDITO | tiene | CUOTA | 1:N | Un crédito tiene muchas cuotas |
| CUOTA | tiene | COBRANZA | 1:N | Una cuota tiene muchas cobranzas (parciales) |
| CREDITO | tiene | DESEMBOLSO | 1:1 | Un crédito tiene un desembolso |
| CAJA | tiene | MOVIMIENTO_CAJA | 1:N | Una caja tiene muchos movimientos |
| CAJA | tiene | APERTURA_CAJA | 1:N | Una caja tiene muchas aperturas |
| CAJA | tiene | CIERRE_CAJA | 1:N | Una caja tiene muchos cierres |
| CAJA | transfiere | CAJA | N:M | Transferencias entre cajas |
| CLIENTE | tiene | VISITA | 1:N | Un cliente tiene muchas visitas |
| CLIENTE | tiene | SEGUIMIENTO | 1:N | Un cliente tiene muchos seguimientos |
| USUARIO | realiza | AUDITORIA | 1:N | Un usuario tiene muchas acciones auditadas |

---

## 4. REGLAS DE NEGOCIO DEL MER

1. **Cliente INACTIVO** no puede realizar operaciones financieras
2. **Número de cuenta** único con formato `{sucursal}-{correlativo}`
3. **Código de cliente** único con formato `CLI-{correlativo}`
4. **Código de sucursal** único con formato `SUC-{correlativo}`
5. **Cierre de caja** debe ser de abajo hacia arriba
6. **Todo movimiento** debe registrar saldo anterior y posterior
7. **Transferencias** generan dos movimientos (salida + entrada)
8. **Desembolso** genera movimiento de egreso automático
9. **Cobro** genera movimiento de ingreso automático
10. **Auditoría** registra todas las operaciones importantes
