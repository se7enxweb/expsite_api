# expsite_api

Site API for Exponential CMS (legacy), ported from `netgen/ibexa-site-api`. It wraps the native content tree in a small, modern PHP service API that Exponential Layouts and site templates can use.

## Key classes

| Class | File | Purpose |
| --- | --- | --- |
| `expSiteApi` | `classes/expsiteapi.php` | Static facade: `content()`, `location()`, `filter()` |
| `expSiteApiContentService` | `classes/expsiteapicontentservice.php` | Load content by ID, remote ID, location ID; find |
| `expSiteApiLocationService` | `classes/expsiteapilocationservice.php` | Load locations, children, parents; find |
| `expSiteApiFilterService` | `classes/expsiteapifilterservice.php` | `findContent()` / `findLocations()` with a query array |
| `expSiteApiContent` | `classes/expsiteapicontent.php` | Value object wrapping `eZContentObject` |
| `expSiteApiLocation` | `classes/expsiteapilocation.php` | Value object wrapping `eZContentObjectTreeNode` |

## Services

Get services through the static facade:

```php
$content  = expSiteApi::content()->load( 42 );
$location = expSiteApi::location()->load( 2 );
$children = expSiteApi::location()->loadChildren( 2, array( 'limit' => 10 ) );
$items    = expSiteApi::filter()->findContent( array(
    'parent_id' => 2,
    'content_type_identifier' => array( 'article' ),
) );
```

### ContentService

- `load( $contentId )` — load by content object ID
- `loadByRemoteId( $remoteId )` — load by remote ID
- `loadByLocationId( $nodeId )` — load the content for a node
- `loadMainLocationContent( $contentId )` — load the main location of a content item
- `find( $query )` — shortcut to `FilterService::findContent`

### LocationService

- `load( $nodeId )` — load a tree node
- `loadByContentId( $contentId )` — load the main location for a content object
- `loadChildren( $nodeId, $params )` — load direct children with optional limit/offset/class filter
- `loadParent( $nodeId )` — load the parent node
- `find( $query )` — shortcut to `FilterService::findLocations`

### FilterService

- `findContent( $query )` — fetch content under a parent, optionally filtered by class
- `findLocations( $query )` — fetch nodes under a parent, optionally filtered by class and depth

## Query format

```php
$query = array(
    'parent_id' => 2,
    'content_type_identifier' => array( 'article' ),
    'limit' => 10,
    'offset' => 0,
    'sort_by' => array( 'published', false ),
    'depth' => 2, // only for location queries
);
```

## Value objects

- `expSiteApiContent` wraps `eZContentObject`
- `expSiteApiLocation` wraps `eZContentObjectTreeNode`

Both provide simple accessor methods and `toArray()` for JSON output.

## Provenance

The service/value-object split mirrors `netgen/ibexa-site-api` (LoadService, FilterService, Content and Location values), reimplemented directly on `eZContentObject` and `eZContentObjectTreeNode` with no Symfony dependencies.

## Documentation

- `INSTALL.md` — activation
- `doc/USAGE.md` — verified code examples and customization guide
- `doc/FAQ.md` — common questions
- `doc/TODO.md` — known gaps
- `doc/SUPPORT.md` — how to get help
