# Correspondencia ANTIGUO (anitguo/) -> NUEVO (backend/ + frontend/)
Generado 2026-10-06. Regla: NO modificar diseño visual; refactor solo backend + capa HTTP React.

## backend/ (lógica PHP, prioridad 1)
| Origen en anitguo/ | Destino en backend/ | Notas |
|---|---|---|
| app/api/*.php (45 endpoints) | legacy/app/api/ + routes/*.php + controllers/*.php | cobrarCredito.php (600L, core) -> PaymentController+Service |
| app/apiMobile/*.php (4) | legacy/app/apiMobile/ | cobranza campo -> routes/cobros |
| app/Models/*.php (20) | models/*.php + legacy/app/Models/ | User(tusuario), Customer(tclie_general), Credit(tprestamo), Installment, Transaction, CashOffice... |
| app/Controllers/*.php (4) | legacy/app/Controllers/ | patrón procedural a refactorizar |
| app/Helpers/Delay.php, Holiday.php | utils/Delay.php, utils/Holiday.php | cálculo mora (feriados/domingos/condonaciones) |
| app/Request/, app/Session/ | legacy + middleware/ | Request 319L -> DTOs con symfony/validator |
| app/database/database.php | config/database.php | Eloquent Capsule + Dotenv, sin cambios de credenciales |
| app/exel/*.php (9) | legacy/app/exel/ | phpspreadsheet -> futuro job/cola |
| app/pdf/*.php (30) | legacy/app/pdf/ | dompdf -> PdfService |
| admin/*.php (128) + add/ ajax/ api/ listar/ modal/ conection/ | legacy/admin_full/ | vistas mysqli mezcladas; head.php/footer.php = futuro Layout React |
| admin/conection/db.php, conex.php | ELIMINAR a futuro, usar config/database.php | credenciales hardcoded -> .env |
| pdf/ (html2pdf/tcpdf) | legacy/pdf_lib/ | librería legacy |
| storage/ | backend/storage/ | uploads/attachments/profiles/customers |
| composer.json/.lock, .env, .htaccess | backend/composer.json + config/ | vendor/ NO copiado: reinstalar con composer install |
| pki.*/ test/ webhook.*/ .well-known/ wp-cron.php home.html error_log | NO MIGRADOS (clones/stubs) | quedan solo en anitguo/ como archivo histórico |

## frontend/ (React, mismo diseño visual)
| Origen en anitguo/ | Destino en frontend/ | Notas |
|---|---|---|
| CSS/*.css (8) | public/CSS/ intacto | estilos landing, NO modificar |
| JS/*.js + leaflet/ chartjs alpine tailwind | public/JS/ intacto | login.js -> src/services/authService.js (mismo contrato) |
| HTML/*.html + PRODUCTOS/ | public/HTML/ intacto | landing estática |
| img/ (85) + FONT/ + favicon.ico | public/img/, public/FONT/ | logos, productos, fotos oficina |
| modal/*.php (login + simuladores crédito) | public/modal/ intacto | contCredidiario/Semanal/Prendario/Transportista, dni.php |
| resource/ (INSPINIA bootstrap/font-awesome) | public/resource/ intacto | css/style, js/inspinia, jquery-3.1.1 |
| locales/es.json, en.json | public/locales/ intacto | i18n tema |
| index.html (landing 263L) | public/index.legacy.html | referencia visual para React |
| portal.../assets + index.html (build Vite) | public/portal_assets/ + portal.legacy.index.html | base Vite reutilizable |
| admin/modal/ + admin/js/ | public/admin_modal_ref/ + admin_js_ref/ | referencia para components/Modals y DataTable |
| JS/login.js | src/services/authService.js + src/hooks/useAuth.js | POST /app/api/login.php -> "1" => admin/iniOperaciones.php |

## Contratos que NO se rompen (compatibilidad)
- POST /app/api/login.php {usu,pas} -> "1"/"0" + cookies user1/nombre_U/tuser/tofi
- Tablas legacy tusuario/tprestamo/tpresta_detalle/tcaja_*/tahorro_* se mantienen hasta migración con migrations
- Roles cookie tuser: 1 GERENTE 2 ADMIN 3 OPERADOR 4 ASESOR 5 JEFE_OP
