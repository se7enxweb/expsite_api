# expsite_api TODO

Code-observed gaps; no promises attached.

- `expSiteApiFilterService::findContent()` hardcodes `Depth => 1`; a `depth` query key is only honoured by `findLocations()`.
- No pagination metadata: list methods return plain arrays without total counts, so callers must issue their own count fetches for pagers.
- The query format has no visibility, section, language or date filters — only `parent_id`, `content_type_identifier`, `limit`, `offset`, `sort_by` (and `depth` for locations).
- `expSiteApi` facade instantiates services with `new` on every call; there is no way to inject service subclasses without wrapping the facade.
- `settings/design.ini.append.php` registers a design extension slot, but the extension ships no `design/` directory.
- No search integration; text filtering must be layered on via `expquery_translator` or the kernel search API.
