---
type: Trap
title: Serial device permissions
description: "uart_open returns null when posix_open fails — often /dev/tty* permission (dialout) or missing device."
resource: src/Serial.php
tags: [trap, permissions, dialout, tty, uart, linux]
generated: { by: "okf-documentation-generator/cursor", at: "2026-08-10T22:03:00Z" }
status: draft
sources:
  - id: serial
    resource: src/Serial.php
    title: uartOpen returns null when posix_open fd < 0
  - id: readme
    resource: README.md
    title: Linux /dev/tty* device examples and open failure pattern
  - id: helpers-serial
    resource: src/Helpers/uart-serial.php
    title: uart_open delegates to Serial::uartOpen
---

# Symptom

`uart_open('/dev/ttyUSB0', …)` (or `/dev/ttyAMA0`, …) returns `null` even though the device node exists and **ext-posi** / **microscrap/posix** are loaded.[^readme]

# Cause

`Serial::uartOpen` opens with `posix_open($path, O_RDWR | O_NOCTTY)`. If the FD is `< 0`, it returns `null` immediately — no exception.[^serial]

On typical Linux images, serial devices under `/dev/tty*` are owned by `root` and group **`dialout`** (or `uucp` / vendor-specific). A process whose user is not in that group gets `EACCES` from the open syscall, which surfaces here as a failed open / `null` port.

Other failure modes after a successful open (termios get/set / `setBaudRate` failure) also return `null` after closing the FD — permissions are only the most common first gate.[^serial]

# Mitigation

- Confirm the device path exists (`ls -l /dev/ttyUSB0`, …).
- Ensure the runtime user is in the serial group (commonly `dialout`), then re-login / restart the service so group membership applies.
- Prefer checking `is_null($port)` after `uart_open` before calling helpers that require `UARTPort`.[^readme]
- Distinguish permission failures from later configure failures by checking whether the node is readable/writable outside PHP when diagnosing.

# Related

* [Helpers → Termios / posix](../architecture/helpers-termios-ext.md)
* [Package (0.7)](../orientation/package.md)

[^serial]: uartOpen returns null when posix_open fd < 0
[^readme]: Linux /dev/tty* device examples and open failure pattern
[^helpers-serial]: uart_open delegates to Serial::uartOpen
