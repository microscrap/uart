# Log

## 2026-08-10

* **Update**: `composer.json` reconciled to **0.7.0** (`php` `^8.4|^8.5|^8.6`, `ext-posi` / `microscrap/posix` `^0.7.0`, `suggest` gpio-framework, homepage/support, branch-alias). README rewritten to open-gl style + `src/` drift fixes (`uart_set_baud_rate`, `BaudRate|int`, `UARTPort::$baud` int, `CSIZE` omission).
* **Creation**: Initial OKF v0.2 bundle for `microscrap/uart` **0.7.0** (gpio microscrap stack; shape mirrored from `microscrap/posix`) from package sources + `okf/SPEC.md` (GoogleCloudPlatform/knowledge-catalog).
* **Creation**: Orientation — [Package (0.7)](/orientation/package.md), [Ecosystem docs](/orientation/ecosystem-docs.md), [Pair with protocol peers](/orientation/pairing-protocol-peers.md).
* **Creation**: Architecture — [Helpers → Termios / posix](/architecture/helpers-termios-ext.md).
* **Creation**: Conventions — [1:1 extension wrap](/conventions/one-to-one-extension-wrap.md), [Enums for termios flags](/conventions/enums-termios-flags.md).
* **Creation**: Traps — [BaudRate value vs numeric](/traps/baudrate-value-vs-numeric.md), [Serial device permissions](/traps/serial-device-permissions.md).
* **Creation**: Subdirectory indexes under `orientation/`, `architecture/`, `conventions/`, `traps/`; root [index.md](/index.md).
* **Note**: Root `AGENTS.md` Quick OKF map already matches these concept paths (extra convention + dialout trap are progressive-disclosure additions); all concepts left `status: draft` pending human verification.
