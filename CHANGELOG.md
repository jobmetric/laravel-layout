# Changelog

## 2.1.1

- Allow multiple placements of the same plugin in one layout position.
- For an existing installation, drop the legacy unique index
  `LAYOUT_PLUGIN_UNIQUE_KEY` from the configured layout plugins table.
  The owning migration now creates fresh tables without this index.
  On MySQL with the default table name, use
  `ALTER TABLE layout_plugins DROP INDEX LAYOUT_PLUGIN_UNIQUE_KEY;`.
