---
type: Convention
title: "1:1 extension wrap"
description: "Helpers delegate to Serial / Termios only; keep coverage aligned with uart-serial.php and uart-termios.php."
resource: src/
tags: [convention, bindings, uart, termios, posi]
generated: { by: "okf-documentation-generator/cursor", at: "2026-08-10T22:03:00Z" }
status: draft
sources:
  - id: agents
    resource: AGENTS.md
    title: Agent wrap rules
  - id: readme
    resource: README.md
    title: Package README wrap description
  - id: helpers-serial
    resource: src/Helpers/uart-serial.php
    title: Helper → Serial delegation
  - id: helpers-termios
    resource: src/Helpers/uart-termios.php
    title: Helper → Termios delegation
---

# Rule

Match peer bindings packages (`microscrap/posix`, `microscrap/open-gl`, …) in spirit — thin wrap over the native / FD stack:[^agents][^readme]

1. Global helpers use the names callers expect (`uart_open`, `uart_tcgetattr`, …).
2. Helpers call **`Serial`** or **`Termios`** only — never invent parallel APIs.[^helpers-serial][^helpers-termios][^agents]
3. Facades call **`Posi\Termios`** and **`posix_*`** (from `microscrap/posix`) only for I/O / termios work.
4. Keep 1:1 coverage with helpers already in `src/Helpers/uart-serial.php` and `uart-termios.php`; document drift in README / ecosystem docs.[^agents]
5. Termios / baud integers live in backed enums — see [Enums for termios flags](enums-termios-flags.md).
6. No ServiceProvider, Chassis/Core coupling, or Fabricate remaps in this package.[^agents]
7. Prefer `is_null($var)` over `$var === null`; no class-level constants.[^agents]

# Architecture link

Full call-stack diagram: [Helpers → Termios / posix](../architecture/helpers-termios-ext.md).

[^agents]: Agent wrap rules
[^readme]: Package README wrap description
[^helpers-serial]: Helper → Serial delegation
[^helpers-termios]: Helper → Termios delegation
