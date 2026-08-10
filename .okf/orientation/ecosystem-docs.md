---
type: Reference
title: Ecosystem docs
description: "Published ScrapyardIO ecosystem docs for microscrap/uart 0.7.x."
resource: "https://scrapyard-io.projectsaturnstudios.com/ecosystem/microscrap/uart/0.7.x/overview"
tags: [orientation, docs, ecosystem, 0.7]
generated: { by: "okf-documentation-generator/cursor", at: "2026-08-10T22:03:00Z" }
status: draft
sources:
  - id: readme
    resource: README.md
    title: README production docs link and badges
  - id: overview
    resource: "https://scrapyard-io.projectsaturnstudios.com/ecosystem/microscrap/uart/0.7.x/overview"
    title: Ecosystem overview page
---

# Entrypoint

Human-facing package docs live on the ScrapyardIO ecosystem site:[^overview][^readme]

[https://scrapyard-io.projectsaturnstudios.com/ecosystem/microscrap/uart/0.7.x/overview](https://scrapyard-io.projectsaturnstudios.com/ecosystem/microscrap/uart/0.7.x/overview)

README badges and the production docs banner point at that overview.[^readme]

# How agents should use it

- Prefer this OKF bundle for **in-repo** agent rules (call stack, enums, baud trap, peer pairing).
- Prefer the ecosystem site for **published** narrative docs aimed at humans.
- When either drifts from `src/` or README helper tables, update the stale side and note it in [log.md](../log.md).

# Related

* [Package (0.7)](package.md)

[^readme]: README production docs link and badges
[^overview]: Ecosystem overview page
