---
okf_version: "0.2"
---

# microscrap/uart Knowledge Bundle

Package knowledge for `microscrap/uart` (UART / serial bindings over **ext-posi** `Posi\Termios` + **microscrap/posix**, v0.7.0).
Read this index first; open only the concepts needed for the task.

**Trust rule:** Prefer `status: stable`. Treat `deprecated` as historical only. New agent-written concepts stay `status: draft` until a human verifies them.
**Placement:** This bundle lives at the **package root** only — never under `src/`.
**Links:** Concept cross-links use paths relative to each file.
**Scope:** Document the bindings-only package (helpers + facades + enums + `UARTPort`). Do **not** invent ServiceProviders, Chassis/Core coupling, or Fabricate remaps here.
**Dist note:** `.okf/` and root `AGENTS.md` are `export-ignore` in `.gitattributes` so Composer dist packages do not ship this bundle.

# Orientation

* [Package (0.7)](orientation/package.md) - Composer identity, namespace, helpers/facades over Posi\Termios + posix.
* [Ecosystem docs](orientation/ecosystem-docs.md) - Published 0.7.x overview and docs site entrypoint.
* [Pair with protocol peers](orientation/pairing-protocol-peers.md) - posix below; gpio / i2c / spi beside; gpio-framework above.

# Architecture

* [Helpers → Termios / posix](architecture/helpers-termios-ext.md) - Call stack: helpers → Serial / Termios → Posi\Termios + posix_*.

# Conventions

* [1:1 extension wrap](conventions/one-to-one-extension-wrap.md) - Helpers → Serial / Termios only; no parallel APIs.
* [Enums for termios flags](conventions/enums-termios-flags.md) - BaudRate / flag enums; int-backed, FULLY UPPERCASE.

# Traps

* [BaudRate value vs numeric](traps/baudrate-value-vs-numeric.md) - `BaudRate::value` is speed_t; `UARTPort::$baud` is numeric rate.
* [Serial device permissions](traps/serial-device-permissions.md) - `/dev/tty*` often needs `dialout` (or equivalent) group membership.

# Log

* [Directory update log](log.md)
