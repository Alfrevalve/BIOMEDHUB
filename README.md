# Biomedhub Admin

Panel administrativo para la operacion logistica y comercial de dispositivos medicos. Construido con **Laravel 12** y **Filament 4**, con control de permisos (Spatie), bitacora de actividad, y modulos para inventario, pedidos, movimientos, cirugias, equipos, instituciones, usuarios y reportes de consumo.

## Caracteristicas clave
- CRUD completos en Filament para: Items, Kits, Pedidos, Movimientos, Equipos, Cirugias, Instituciones, Usuarios y Reportes de Cirugia.
- Tablas con badges y acciones rapidas (preparacion, despachado, entregado, devuelto, solicitar/confirmar recojo).
- Dashboard operativo con tarjetas de cirugias hoy, listos para despacho, recojos solicitados, consumidos sin facturar, pedidos atrasados, stock critico, equipos disponibles.
- Reportes de consumo con filtros (fecha, institucion, estado de pedido), evidencia descargable y exportacion CSV para facturacion/almacen.
- Importacion masiva de **Instituciones** via CSV con normalizacion de encabezados (acentos/guiones a ASCII), validacion de `nombre`, truncado de `ubigeo` (10 caracteres), normalizacion de `tipo` (Publica/Privada/Militar/ONG) y parseo de `inicio_actividad` (formatos Y-m-d, d/m/Y, d-m-Y, m/d/Y).
- Bitacora de cambios con Spatie Activity Log en recursos criticos.
- Roles y permisos con Spatie Permission: admin, logistica, auditoria, comercial, soporte_biomedico, almacen, facturacion, instrumentista.

## Modulos
- **Items & Kits**: catalogo con stock total/reservado; kits con composicion; acceso restringido por policy a inventario.
- **Pedidos**: flujo solicitado/preparacion/despachado/entregado/devuelto, detalle de materiales y equipos, badge de evidencia de consumo.
- **Movimientos**: transporte de equipos con transportista/contacto, recojos solicitados, antiguedad y acciones de recogido.
- **Equipos**: disponibilidad y estado operativo.
- **Cirugias**: agenda (hoy/proximas), responsables; reporte de consumo con evidencia y notificaciones separadas para logistica y facturacion.
- **Reportes de Cirugia**: vista de consumos con filtros y exportacion; solo lectura.
- **Instituciones**: maestro enriquecido (ubicacion, redes DISA/Red/Microrred/UE, categorizacion, contacto, georreferencia, camas); importacion CSV.
- **Usuarios**: gestion de roles, nombres legibles.

## Requisitos
- PHP 8.3+ (CLI en `laragon/bin/php/php-8.3.26-Win32-vs16-x64/php.exe`)
- Composer, Node 18+ y npm
- MySQL/MariaDB

## Instalacion rapida
```bash
composer install
cp .env.example .env    # configurar DB y MAIL
php artisan key:generate
php artisan migrate --seed
npm install
npm run build           # o npm run dev
```

## Scripts utiles
- `composer setup` — instala deps, genera .env, key, migra y construye assets.
- `composer dev` — levanta servidor, cola, logs (pail) y Vite en paralelo.
- `composer test` — limpia config y ejecuta tests.

## Flujo operativo resumido
1) Crear cirugia (instrumentista asignado).  
2) Pedido auto/manual: asignar kit + detalle de materiales/equipos.  
3) Marcar “Listo para despacho” (notifica logistica/soporte); asignar transportista y despachar.  
4) Entrega en institucion -> marcar entregado.  
5) Instrumentista carga consumo y evidencia (auto-notifica logistica/almacen/facturacion y solicita recojo).  
6) Recojo y devolucion de equipo/material desde movimientos/pedidos.

## Licencia
MIT (propia del esqueleto Laravel). Ver `LICENSE` si aplica.
