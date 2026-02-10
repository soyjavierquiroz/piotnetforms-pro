# PiotnetForms Pro: análisis del campo teléfono internacional

Este documento resume cómo el plugin implementa el input de teléfono con bandera y la autodetección de país.

## Archivos clave
- `inc/widgets/field.php`: define la opción **International Telephone Input** (`field_dial_code`) y agrega `data-piotnetforms-tel-field` al input `type=tel`.
- `piotnetforms-pro.php`: registra/enqueue de `assets/js/minify/frontend/intlTelInput-jquery.min.js` y `assets/css/minify/intl-tel-input.min.css`, además de cargar `inc/forms/ajax-intl-get-country-code.php`.
- `inc/forms/ajax-intl-get-country-code.php`: endpoint AJAX `piotnetforms_get_country_code` que consulta `http://ip-api.com/php/{ip}` y devuelve un ISO country code o `error`.
- `inc/shortcode/shortcode-widget.php`: imprime `<div data-piotnetforms-ajax-url=".../admin-ajax.php">` para llamadas AJAX frontend.
- `assets/js/minify/frontend.min.js`: inicializa `intlTelInput` sobre `[data-piotnetforms-tel-field]`, llama AJAX `piotnetforms_get_country_code`, usa fallback `us` y serializa con `.intlTelInput("getNumber")`.

## Flujo resumido
1. En el builder, si `field_type=tel` y `field_dial_code=true`, se marca el input con `data-piotnetforms-tel-field`.
2. Se encolan JS/CSS de `intl-tel-input`.
3. En `$(document).ready` (y también en `elementor/popup/show`), `frontend.min.js` busca los inputs tel marcados.
4. Hace POST AJAX a `admin-ajax.php` con `action=piotnetforms_get_country_code`.
5. El endpoint PHP resuelve IP del visitante y consulta `ip-api.com`.
6. Si responde OK, se usa ese país como `initialCountry`; si falla, fallback `us`.
7. Inicializa `intlTelInput` con `separateDialCode:true`, `nationalMode:false`, `hiddenInput:"full_number"`.
8. Al submit, para campos tel se usa `$(input).intlTelInput("getNumber")` para enviar número internacional.

## Observaciones
- No hay `geoIpLookup` nativo de intl-tel-input; la geolocalización es propia vía AJAX WordPress.
- No se observó uso de `navigator.language` ni timezone para país.
- `utilsScript` se inicializa vacío, por lo que el formateo/validación avanzada depende de capacidades embebidas y del patrón HTML.
- Dependencia externa: `ip-api.com` por HTTP (no HTTPS), con timeout 5s.
