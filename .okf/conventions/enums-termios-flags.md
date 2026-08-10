---
type: Convention
title: Enums for termios flags
description: "BaudRate and termios flag/action enums are int-backed with FULLY UPPERCASE cases; CSIZE omitted (same int as CS8)."
resource: src/Enums/
tags: [convention, enums, uart, termios, linux]
generated: { by: "okf-documentation-generator/cursor", at: "2026-08-10T22:03:00Z" }
status: draft
sources:
  - id: readme
    resource: README.md
    title: Enum quick reference and FULLY UPPERCASE rule
  - id: agents
    resource: AGENTS.md
    title: Enum case naming rule
  - id: baud
    resource: src/Enums/BaudRate.php
    title: BaudRate enum (speed_t values)
  - id: control-flag
    resource: src/Enums/ControlFlag.php
    title: ControlFlag; CSIZE omitted
---

# Why enums live here

POSIX / Linux termios uses integer `#define`s for baud (`speed_t`), flag bitmasks, `tcsetattr` actions, flush queues, and `c_cc` indices. This package ships typed int-backed enums so callers avoid raw magic numbers for the usual cases.[^readme]

# Rules

- Use **int-backed** enums under `Microscrap\Bindings\UART\Enums\`.[^readme]
- Case names are **FULLY UPPERCASE** (e.g. `BaudRate::B115200`, `TermiosAction::TCSANOW`).[^agents][^readme]
- No class-level constants in `src/` — prefer enums.[^agents]
- Know which path wants **numeric rate** vs **`BaudRate::value` (`speed_t`)** — see [BaudRate value vs numeric](../traps/baudrate-value-vs-numeric.md).

# Enum inventory (0.7.0)

| Enum | Purpose | Notes |
|------|---------|-------|
| `BaudRate` | POSIX / Linux `speed_t` constants (`B0`…`B4000000`) | `::value` is **not** the numeric bps for `setBaudRate`[^baud][^readme] |
| `TermiosAction` | `tcsetattr` when-to-apply (`TCSANOW`, `TCSADRAIN`, `TCSAFLUSH`) | |
| `TermiosQueue` | `tcflush` queue select (`TCIFLUSH`, `TCOFLUSH`, `TCIOFLUSH`) | |
| `TermiosFlow` | `tcflow` action (`TCOOFF`…`TCION`) | |
| `InputFlag` | `c_iflag` bits | |
| `OutputFlag` | `c_oflag` bits | |
| `ControlFlag` | `c_cflag` bits | **`CSIZE` omitted** — same int as `CS8`; use `CS8->value` to clear/set size[^control-flag][^readme] |
| `LocalFlag` | `c_lflag` bits | |
| `ControlChar` | `c_cc` indices (`VMIN`, `VTIME`, …) | |

# Related

* [1:1 extension wrap](one-to-one-extension-wrap.md)
* [BaudRate value vs numeric](../traps/baudrate-value-vs-numeric.md)
* [Helpers → Termios / posix](../architecture/helpers-termios-ext.md)

[^readme]: Enum quick reference and FULLY UPPERCASE rule
[^agents]: Enum case naming rule
[^baud]: BaudRate enum (speed_t values)
[^control-flag]: ControlFlag; CSIZE omitted
