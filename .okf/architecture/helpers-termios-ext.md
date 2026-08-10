---
type: Architecture
title: "Helpers → Termios / posix"
description: "Global uart_* helpers call Serial / Termios facades; those call Posi\\Termios and posix_* FD helpers."
resource: src/
tags: [architecture, bindings, uart, helpers, termios, posix]
generated: { by: "okf-documentation-generator/cursor", at: "2026-08-10T22:03:00Z" }
status: draft
sources:
  - id: helpers-serial
    resource: src/Helpers/uart-serial.php
    title: Serial helpers → Serial facade
  - id: helpers-termios
    resource: src/Helpers/uart-termios.php
    title: Termios helpers → Termios facade
  - id: serial
    resource: src/Serial.php
    title: Serial facade (Posi\\Termios + posix_*)
  - id: termios-facade
    resource: src/Termios.php
    title: Termios facade (Posi\\Termios)
  - id: composer
    resource: composer.json
    title: Autoload files list
  - id: readme
    resource: README.md
    title: Helper API tables and defaults
  - id: agents
    resource: AGENTS.md
    title: Helpers call Serial / Termios only
---

# Call stack

```
app / peers / tests
    │
    ├─ uart_open / uart_read / uart_write / …   # uart-serial.php
    │       └─► Serial::*
    │               ├─► posix_open / posix_read / posix_write / posix_close
    │               └─► Posi\Termios::*  (tcgetattr, tcsetattr, setBaudRate, tcdrain, …)
    │
    └─ uart_tcgetattr / uart_cfsetispeed / …   # uart-termios.php
            └─► Termios::*
                    └─► Posi\Termios::*  (tcgetattr, tcsetattr, cfset*/cfget*)
```

Rules:[^agents][^readme][^helpers-serial]

1. Helpers call `Serial` / `Termios` only.
2. Facades call `Posi\Termios` and (for I/O) `posix_*` from **microscrap/posix** — do not invent parallel APIs.
3. Keep 1:1 coverage with helpers already in `uart-serial.php` / `uart-termios.php`; document drift in README / ecosystem docs.[^agents]

# Autoload

Composer `autoload.files` registers:[^composer]

- `src/Helpers/uart-serial.php`
- `src/Helpers/uart-termios.php`

Each function is wrapped in `if (! function_exists(...))` so a prior definition wins.[^helpers-serial][^helpers-termios]

# Helper groups (0.7 surface)

| Group | Helpers | Facade | Downstream |
|-------|---------|--------|------------|
| Lifecycle / I/O | `uart_open`, `uart_set_baud_rate`, `uart_close`, `uart_read`, `uart_write` | `Serial` | `posix_*` + `Posi\Termios::setBaudRate` / raw setup[^serial] |
| Queue / flow | `uart_drain`, `uart_flush`, `uart_flow` | `Serial` | `Posi\Termios::{tcdrain,tcflush,tcflow}`[^serial] |
| Raw mode | `uart_make_raw` | `Serial::makeRaw` | PHP-side `cfmakeraw` semantics on a termios array[^serial] |
| Attributes | `uart_tcgetattr`, `uart_tcsetattr` | `Termios` | `Posi\Termios::{tcgetattr,tcsetattr}`[^termios-facade] |
| Speeds | `uart_cfsetispeed`, `uart_cfsetospeed`, `uart_cfgetispeed`, `uart_cfgetospeed` | `Termios` | `Posi\Termios::cf*` + `BaudRate`[^termios-facade] |

# `uart_open` defaults

Automatic configuration (override via `uart_tcgetattr` / mutate / `uart_tcsetattr`):[^readme][^serial]

- Open flags: `O_RDWR | O_NOCTTY` (from `microscrap/posix` `FileControlFlag`)
- `cfmakeraw` semantics via `Serial::makeRaw`
- `VMIN=1`, `VTIME=0`
- Apply with `TCSANOW`
- Baud via `Posi\Termios::setBaudRate` (enum cases → numeric rate from case name; plain `int` uses Linux BOTHER path)

# Baud handling note

`uart_open` / `uart_set_baud_rate` accept `BaudRate|int`. Enum cases are converted to a **numeric** rate by stripping the leading `B` from the case name — **not** by using `BaudRate::value` (`speed_t`). See [BaudRate value vs numeric](../traps/baudrate-value-vs-numeric.md).[^serial]

`uart_cfsetispeed` / `uart_cfsetospeed` pass `BaudRate::value` (`speed_t`) into `Posi\Termios::cfset*`.[^termios-facade]

# Errors / style

- C-style return codes (`null` / `-1` / `false` on failure) — no exceptions from helpers in this package’s wrap.[^readme]
- Prefer `is_null($var)` over `$var === null` in package code.[^agents]

# Related

* [1:1 extension wrap](../conventions/one-to-one-extension-wrap.md)
* [Enums for termios flags](../conventions/enums-termios-flags.md)
* [BaudRate value vs numeric](../traps/baudrate-value-vs-numeric.md)

[^helpers-serial]: Serial helpers → Serial facade
[^helpers-termios]: Termios helpers → Termios facade
[^serial]: Serial facade (Posi\Termios + posix_*)
[^termios-facade]: Termios facade (Posi\Termios)
[^composer]: Autoload files list
[^readme]: Helper API tables and defaults
[^agents]: Helpers call Serial / Termios only
