# Using expsite_api

All examples use real class and method names from `classes/`.

## The facade

`expSiteApi` returns a fresh service instance per call:

```php
$contentService  = expSiteApi::content();   // expSiteApiContentService
$locationService = expSiteApi::location();  // expSiteApiLocationService
$filterService   = expSiteApi::filter();    // expSiteApiFilterService
```

## Loading content

```php
$content = expSiteApi::content()->load( 42 );                    // by content object ID
$content = expSiteApi::content()->loadByRemoteId( 'abc123' );    // by remote ID
$content = expSiteApi::content()->loadByLocationId( 60 );        // content shown at a node
$location = expSiteApi::content()->loadMainLocationContent( 42 ); // main location of a content item

if ( $content !== null )
{
    echo $content->name();
}
```

All loaders return `null` when nothing is found — always check before use.

## Loading locations

```php
$location = expSiteApi::location()->load( 2 );               // by node ID
$location = expSiteApi::location()->loadByContentId( 42 );   // main node of a content object
$parent   = expSiteApi::location()->loadParent( 60 );        // parent node

$children = expSiteApi::location()->loadChildren( 2, array(
    'limit'  => 10,
    'offset' => 0,
    'content_type_identifier' => array( 'article' ),
    'sort_by' => array( 'published', false ),
) );
```

`loadChildren()` always fetches depth 1 (direct children) and returns an array of `expSiteApiLocation`.

## Filtering

`findContent()` returns `expSiteApiContent[]`, `findLocations()` returns `expSiteApiLocation[]`:

```php
$articles = expSiteApi::filter()->findContent( array(
    'parent_id' => 2,
    'content_type_identifier' => array( 'article' ),
    'limit' => 10,
    'offset' => 0,
    'sort_by' => array( 'published', false ),
) );

$nodes = expSiteApi::filter()->findLocations( array(
    'parent_id' => 2,
    'depth' => 2,                       // only findLocations() honours depth
    'content_type_identifier' => array( 'folder' ),
) );
```

Query key defaults (from the code): `parent_id` 2, `limit` 25, `offset` 0, `sort_by` `array( 'name', true )`. `content_type_identifier` becomes `ClassFilterType=include` + `ClassFilterArray`. `findContent()` always uses depth 1. `sort_by` is passed straight through as a `subTreeByNodeID` `SortBy` value, so any kernel sort field works (`name`, `published`, `priority`, ...).

## Value objects

`expSiteApiContent` (wraps `eZContentObject`):

```php
$content->id();
$content->remoteId();
$content->name();
$content->contentTypeIdentifier();
$content->mainLocationId();
$content->published();   // timestamp
$content->modified();    // timestamp
$content->ownerId();
$content->dataMap();     // eZContentObjectAttribute[]
$content->field( 'title' ); // single attribute or null
$content->isAvailable();
$content->toArray();     // scalar summary for JSON output
$content->getObject();   // underlying eZContentObject
```

`expSiteApiLocation` (wraps `eZContentObjectTreeNode`):

```php
$location->id();          // node ID
$location->contentId();
$location->name();
$location->path();        // path string
$location->urlAlias();
$location->depth();
$location->isMainLocation();
$location->childrenCount();
$location->content();     // expSiteApiContent for this node
$location->isAvailable();
$location->toArray();
$location->getNode();     // underlying eZContentObjectTreeNode
```

## Scenario: build a JSON endpoint payload

```php
$items = expSiteApi::filter()->findLocations( array( 'parent_id' => $parentId, 'limit' => 20 ) );

$payload = array_map(
    function( $location ) { return $location->toArray(); },
    $items
);

header( 'Content-Type: application/json' );
echo json_encode( array( 'items' => $payload ) );
```

## Scenario: listing with drill-down in a module view

```php
$location = expSiteApi::location()->load( $Params['NodeID'] );
if ( $location === null )
    return $Params['Module']->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );

$children = expSiteApi::location()->loadChildren( $location->id(), array(
    'limit' => 12,
    'content_type_identifier' => array( 'article', 'folder' ),
) );

foreach ( $children as $child )
{
    $content = $child->content();
    // render $content->name(), $child->urlAlias(), ...
}
```

## Scenario: feed a Layouts block handler

```php
public function getValues( $block )
{
    $params = isset( $block['parameters'] ) ? $block['parameters'] : array();

    $items = expSiteApi::filter()->findContent( array(
        'parent_id' => isset( $params['parent_node_id'] ) ? (int)$params['parent_node_id'] : 2,
        'content_type_identifier' => array( 'article' ),
        'limit' => isset( $params['limit'] ) ? (int)$params['limit'] : 5,
        'sort_by' => array( 'published', false ),
    ) );

    return array( 'items' => $items );
}
```

## Customization

### Settings layer

The extension ships no INI file and reads no configuration. All tunables (parent node, limits, class filters, sorting) travel in the query arrays, so integrators define their own settings in their own extension's INI files and pass the values through. The standard INI cascade applies to those settings, not to this extension: extension defaults → `settings/siteaccess/<sa>/` → extension `settings/siteaccess/<sa>/` → `settings/override/`.

### Template layer

The extension ships no templates or design directory (`settings/design.ini.append.php` exists but the extension provides no `design/` tree). Rendering of the returned value objects is entirely up to the calling templates; there is nothing here to override in the design cascade.

### PHP layer

Safe extension points:

- Subclass the services (`expSiteApiContentService`, `expSiteApiLocationService`, `expSiteApiFilterService`) to add loaders or query keys; all methods are public and self-contained.
- Subclass the value objects (`expSiteApiContent`, `expSiteApiLocation`) to add accessors, and override `toArray()` for richer JSON payloads.
- The facade `expSiteApi` creates services with `new` and has no registry; if you need your subclasses returned by the facade, wrap or replace the facade in your own extension (for example a `mySiteApi` facade returning your service subclasses) rather than editing this one.

Register subclasses in your extension's `autoloads/` class map and run `php bin/php/ezpgenerateautoloads.php -e`.
