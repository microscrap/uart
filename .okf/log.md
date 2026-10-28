## 2026-10-28

* **Version**: 0.11.0 with the Venusian 0.11 ecosystem: `version` 0.11.0, `dev-main` → `0.11.x-dev`, `ext-posi` `^0.11.0`, PHP `~8.4.27||^8.5.12`, docs URLs `0.11.x`, `scrapyard-io/framework` `^0.11.0`. README, AGENTS and the bundle relabelled; CI installs `posi` `^0.11` where it has a workflow. No API change. 7 passed on the Pi (8.4 ZTS); all 7 skip on macOS.

## 2026-10-03
* **Update**: ported to **0.10.0**. `ext-posi` 0.10 is a C rewrite: `Posi\System` / `Posi\Termios` / `Posi\Memory` are gone, replaced by global functions (`posix_*`, `tcgetattr`, `tcsetattr`, `cf*speed`, `tcdrain` / `tcflush` / `tcflow`, `ioctl`, `posi_set_baud_rate`). `microscrap/posix` dropped (absorbed by ext-posi); the package requires only `php` and `ext-posi` `^0.10.0`. `Serial` opens with `O_RDWR | O_NOCTTY | O_CLOEXEC` constants; modem-line methods use `ioctl(..., ['value' => ...], $value)`. Termios arrays carry `c_ispeed` / `c_ospeed` (all seven keys required). Docs URLs and branch alias moved to 0.10.x; suggest `scrapyard-io/framework` `^0.10.0`. README, AGENTS.md and the bundle updated.

## 2026-09-24
* **Update**: relabeled 0.8.0 → 0.9.0 with `ext-posi` / `microscrap/posix` ^0.9.0. No code change.

## 2026-09-14
* **Update**: relabeled 0.7.0 → 0.8.0 with `ext-posi` / `ext-ftdi` 0.8.0. No code change.

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
