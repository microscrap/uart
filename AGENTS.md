# AGENTS.md — microscrap/uart

**Always read `.okf/index.md` first** before changing this package. Open only the concepts needed for the task; prefer `status: stable` when present. When you learn a durable package fact, update `.okf/` and append `.okf/log.md`.

## Role

Bindings-only Composer package over **ext-posi** 0.10 global functions (`posix_*`, `tcgetattr`, `tcsetattr`, `ioctl`, `posi_set_baud_rate`, ...). Requires only `php` and `ext-posi` `^0.10.0`. Global `uart_*` helpers, facades (`Serial`, `Termios`), enums, and `UARTPort`. No ServiceProvider, no Chassis/Core coupling.

## Rules

* Helpers call `Serial` / `Termios` only; those call ext-posi global functions — do not invent parallel APIs.
* Keep 1:1 coverage with helpers already in `src/Helpers/uart-serial.php` and `uart-termios.php`; document drift in README / ecosystem docs.
* Enums in `src/Enums/*` are int-backed with **FULLY UPPERCASE** cases.
* Prefer `is_null($var)` over `$var === null`.
* No class-level constants; no Fabricate remaps in this package.
* `UARTPort::$baud` is a numeric rate (`int`); `BaudRate::value` is POSIX `speed_t`.

## Quick OKF map

| Need | Concept |
|------|---------|
| Identity / scope | `.okf/orientation/package.md` |
| Docs site | `.okf/orientation/ecosystem-docs.md` |
| Call stack | `.okf/architecture/helpers-termios-ext.md` |
| Wrap rules | `.okf/conventions/one-to-one-extension-wrap.md` |
| Enums | `.okf/conventions/enums-termios-flags.md` |
| Peer stack | `.okf/orientation/pairing-protocol-peers.md` |
| Baud / speed_t trap | `.okf/traps/baudrate-value-vs-numeric.md` |
| Device permissions | `.okf/traps/serial-device-permissions.md` |
