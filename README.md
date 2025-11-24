# IrpfBundle

Este plugin para Kimai añade la funcionalidad de retención de IRPF a las facturas.

## Características

- **Configuración Flexible**: Permite activar el IRPF y definir el porcentaje tanto en las plantillas de factura como en facturas individuales.
- **Cálculo Automático**: Calcula automáticamente el importe de la retención y lo deduce del total de la factura.
- **Integración Nativa**: Se integra directamente en los formularios de edición de facturas y plantillas de Kimai.

## Instalación

1. Clona este repositorio en el directorio `plugins/` de tu instalación de Kimai:
   ```bash
   cd /ruta/a/kimai/plugins/
   git clone https://github.com/tu-usuario/IrpfBundle.git
   ```

2. Limpia la caché de Kimai para que el sistema reconozca el nuevo plugin:
   ```bash
   bin/console cache:clear
   bin/console kimai:reload
   ```

## Uso

Una vez instalado, verás dos nuevos campos en la configuración de tus facturas y plantillas de factura:

1. **Apply IRPF**: Una casilla para activar la retención de IRPF.
2. **IRPF Rate**: Un campo porcentual para definir la tasa de retención (por defecto 15%).

El sistema calculará el IRPF basándose en la base imponible y restará este importe del total a pagar.

## Requisitos

- Kimai 2
- PHP compatible con tu versión de Kimai
