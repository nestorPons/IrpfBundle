# IrpfBundle

This plugin for Kimai adds IRPF (Personal Income Tax) withholding functionality to invoices.

## Features

- **Flexible Configuration**: Allows you to activate IRPF and define the percentage in both invoice templates and individual invoices.

- **Automatic Calculation**: Automatically calculates the withholding amount and deducts it from the invoice total.

- **Native Integration**: Integrates directly into Kimai's invoice editing forms and templates.

## Installation

1. Clone this repository into the `plugins/` directory of your Kimai installation:

``bash

cd /path/to/kimai/plugins/

git clone https://github.com/your-username/IrpfBundle.git

``

2. Clear the Kimai cache so the system recognizes the new plugin:

``bash

bin/console cache:clear

bin/console kimai:reload

``

## Usage

Once installed, you will see two new fields in your invoice and invoice template settings:

1. **Apply IRPF**: A checkbox to activate IRPF withholding.

2. **IRPF Rate**: A percentage field to define the withholding rate (15% by default).

The system will calculate the IRPF based on the taxable income and subtract this amount from the total amount due.

## Requirements

- Kimai 2
- PHP compatible with your version of Kimai