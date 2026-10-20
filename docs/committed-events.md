# Committed business outcomes

Classes are registered by the package provider alongside legacy events. Keys ending in `.committed` are safe automation outcomes; earlier event signatures, payloads and timing are preserved. Only scalar identifiers are exposed.

| Event class | Key | Payload properties |
| --- | --- | --- |
| `JobMetric\Layout\Events\Layout\LayoutStoreCommittedEvent` | `layout.stored.committed` | `id: int|string` |
| `JobMetric\Layout\Events\Layout\LayoutUpdateCommittedEvent` | `layout.updated.committed` | `id: int|string` |
| `JobMetric\Layout\Events\Layout\LayoutDeleteCommittedEvent` | `layout.deleted.committed` | `id: int|string` |
| `JobMetric\Layout\Events\Layout\LayoutRestoreCommittedEvent` | `layout.restored.committed` | `id: int|string` |
| `JobMetric\Layout\Events\Layout\LayoutForceDeleteCommittedEvent` | `layout.force_deleted.committed` | `id: int|string` |
| `JobMetric\Layout\Events\LayoutRelation\LayoutRelationStoreCommittedEvent` | `layout.layout_relation.stored.committed` | `id: int|string` |
| `JobMetric\Layout\Events\LayoutRelation\LayoutRelationDeleteCommittedEvent` | `layout.layout_relation.deleted.committed` | `id: int|string` |
| `JobMetric\Layout\Events\LayoutPages\LayoutPagesSyncCommittedEvent` | `layout.layout_pages.synced.committed` | `id: int|string` |
| `JobMetric\Layout\Events\LayoutPlugins\LayoutPluginsSyncCommittedEvent` | `layout.layout_plugins.synced.committed` | `id: int|string` |

Schedule from the owning operation after successful persistence. Delivery waits for the owning connection outer commit and is discarded on rollback. No-op operations emit nothing. Native Laravel listeners are isolated individually; failures are logged with event key and exception class only. No models, raw inputs, credentials or translated content are included. This is synchronous delivery without durable outbox guarantees.
