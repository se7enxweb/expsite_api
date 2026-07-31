# expsite_api FAQ

## How is this different from calling eZContentObject / eZContentObjectTreeNode directly?

It is a thin, consistent wrapper: services always return `expSiteApiContent` / `expSiteApiLocation` value objects (or `null` / empty arrays), queries are plain arrays with sane defaults, and `toArray()` gives JSON-ready output. Under the hood everything is `eZContentObject::fetch()`, `eZContentObjectTreeNode::fetch()` and `subTreeByNodeID()`.

## Why does findContent() ignore my 'depth' key?

Only `findLocations()` honours `depth`. `findContent()` fetches with a fixed `Depth => 1` (direct children of `parent_id`). See `TODO.md`.

## What does a service return when nothing matches?

Single-item loaders (`load()`, `loadByRemoteId()`, `loadParent()`, ...) return `null`; list methods (`loadChildren()`, `findContent()`, `findLocations()`) return an empty array. No exceptions are thrown.

## What values can 'sort_by' take?

Anything `eZContentObjectTreeNode::subTreeByNodeID()` accepts as `SortBy`, e.g. `array( 'name', true )`, `array( 'published', false )`, or an array of such pairs. The default is `array( 'name', true )`.

## Is there full-text search?

No. Filtering is parent + class-identifier based. For query-string filtering over fetched nodes, combine this extension with `expquery_translator`'s `filterNodes()`.

## Does the API check permissions or visibility?

It relies on the kernel fetch functions' default behaviour; there are no extra `Language`, visibility or section filters in the query format yet (see `TODO.md`).
