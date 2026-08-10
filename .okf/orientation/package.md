---
type: Orientation
title: Package (0.7)
description: "microscrap/uart 0.7.0 — UART/serial PHP helpers over Posi\\Termios + microscrap/posix; no ServiceProvider."
resource: .
tags: [orientation, uart, microscrap, bindings, 0.7]
generated: { by: "okf-documentation-generator/cursor", at: "2026-08-10T22:03:00Z" }
status: draft
sources:
  - id: composer
    resource: composer.json
    title: Package name, namespace, autoload helpers
  - id: readme
    resource: README.md
    title: Package README (0.7.x requirements and surface)
  - id: agents
    resource: AGENTS.md
    title: Agent rules for this package
  - id: serial
    resource: src/Serial.php
    title: Serial facade
  - id: termios-facade
    resource: src/Termios.php
    title: Termios facade
---

# What it is

Composer package `microscrap/uart` at **0.7.0** — PHP helpers, facades, enums, and `UARTPort` over [**php-io-extensions/posi**](https://github.com/php-io-extensions/posi) (`Posi\Termios`) plus [`microscrap/posix`](https://github.com/microscrap/posix) FD helpers.[^readme][^agents]

| Field | Value |
|-------|-------|
| Name | `microscrap/uart`[^composer] |
| Version | `0.7.0`[^composer] |
| PHP | `^8.4\|^8.5\|^8.6`[^composer] |
| Namespace | `Microscrap\Bindings\UART\` → `src/`[^composer] |
| Require | `ext-posi` `^0.7.0`; `microscrap/posix` `^0.7.0`[^composer] |
| Suggest | `scrapyard-io/gpio-framework` `^0.7` |
| Homepage | Ecosystem docs overview (see [Ecosystem docs](ecosystem-docs.md))[^readme] |
| Source | https://github.com/microscrap/uart |
| Discovery | **None** — no provider / Chassis registration in this package[^agents] |
| Role | Bindings layer only (global helpers + facades + enums + DTO)[^agents][^readme] |

Autoloads `src/Helpers/uart-serial.php` and `src/Helpers/uart-termios.php` (each helper guarded with `function_exists`).[^composer][^readme]

# What it is not

- Not `php-io-extensions/posi` (the native extension) — this package *wraps* `Posi\Termios` and uses `microscrap/posix` for FD I/O.[^readme]
- Not a ServiceProvider package — no Chassis / Core / Machine coupling; no Fabricate remaps.[^agents]
- Not the POSIX FD package — FD open/read/write live in `microscrap/posix` (see [Pair with protocol peers](pairing-protocol-peers.md)).
- Not GPIO / I2C / SPI — those are peer protocol packages.

# Public surface (summary)

| Layer | Location | Role |
|-------|----------|------|
| Helpers | `src/Helpers/uart-serial.php`, `uart-termios.php` | Global `uart_*` API |
| Facades | `src/Serial.php`, `src/Termios.php` | Static methods helpers call |
| Data object | `src/DataObjects/UARTPort.php` | `int $fd`, `string $path`, `int $baud` (numeric rate) |
| Enums | `src/Enums/*` | Baud / termios flag / action integers |
| Extension / peer | `Posi\Termios`, `posix_*` | Native + FD targets — not reimplemented here |

# Related

| Topic | Concept |
|-------|---------|
| Call stack | [Helpers → Termios / posix](../architecture/helpers-termios-ext.md) |
| Wrap rules | [1:1 extension wrap](../conventions/one-to-one-extension-wrap.md) |
| Enums | [Enums for termios flags](../conventions/enums-termios-flags.md) |
| Protocol peers | [Pair with protocol peers](pairing-protocol-peers.md) |
| Docs site | [Ecosystem docs](ecosystem-docs.md) |
| Baud trap | [BaudRate value vs numeric](../traps/baudrate-value-vs-numeric.md) |

[^composer]: Package name, namespace, autoload helpers
[^readme]: Package README (0.7.x requirements and surface)
[^agents]: Agent rules for this package
