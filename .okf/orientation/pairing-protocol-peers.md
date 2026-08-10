---
type: Orientation
title: Pair with protocol peers
description: "posix below for FDs; gpio / i2c / spi sit beside; ftdi/mpsse beside for USB; gpio-framework above."
resource: .
tags: [orientation, gpio, uart, i2c, spi, ftdi, composition]
generated: { by: "okf-documentation-generator/cursor", at: "2026-08-10T22:03:00Z" }
status: draft
sources:
  - id: readme
    resource: README.md
    title: Bindings role; requires posix + ext-posi
  - id: agents
    resource: AGENTS.md
    title: Bindings-only role; no Chassis
  - id: serial
    resource: src/Serial.php
    title: Uses posix_open / posix_read / posix_write / posix_close
---

# Composition boundary

`microscrap/uart` is **bindings only** — serial open/configure/read/write and termios helpers. It does not implement GPIO chip APIs, I2C/SPI transfers, USB MPSSE, or Chassis registration.[^readme][^agents]

| Concern | Package |
|---------|---------|
| POSIX FD / syscall helpers | `microscrap/posix` `^0.7` (below — used for open/read/write/close)[^serial] |
| Native termios | **ext-posi** `Posi\Termios` `^0.7` |
| UART / serial (this package) | `microscrap/uart` |
| GPIO | `microscrap/gpio` `^0.7` |
| I2C | `microscrap/i2c` `^0.7` |
| SPI | `microscrap/spi` `^0.7` |
| USB MPSSE / FTDI | `microscrap/ftdi` (sits **beside** — USB path, not a uart child) |
| Higher GPIO orchestration | `scrapyard-io/gpio-framework` `^0.7` (above the microscrap protocol packages) |

# Typical flow

1. Depend on this package (pulls **ext-posi** + **microscrap/posix** `^0.7.0`).
2. Open `/dev/tty*` via `uart_open` (internally `posix_open` + `Posi\Termios`).
3. Application / `gpio-framework` may compose uart with other peers — do not invent framework providers inside this package.[^agents]

# Caveats

- `UARTPort::$baud` is a numeric rate — see [BaudRate value vs numeric](../traps/baudrate-value-vs-numeric.md).
- Device node access often needs group membership — see [Serial device permissions](../traps/serial-device-permissions.md).

# Related

* [Package (0.7)](package.md)
* [Helpers → Termios / posix](../architecture/helpers-termios-ext.md)

[^readme]: Bindings role; requires posix + ext-posi
[^agents]: Bindings-only role; no Chassis
[^serial]: Uses posix_open / posix_read / posix_write / posix_close
