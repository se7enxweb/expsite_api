Exponential Site API
===================

General description
-------------------

Exponential Site API (extension name: `expsite_api`) is a Site API for
Exponential CMS (eZ Publish Legacy), ported from `netgen/ibexa-site-api`.
It wraps the native content tree in a small, modern PHP service API that
Exponential Layouts and site templates can use, and provides the following
capabilities:

- Content loading - Load content by object ID, remote ID or location ID
  through a clean service interface instead of raw kernel calls.
- Location loading - Load tree nodes, children, parents and main locations
  with optional limit, offset and class filtering.
- Filtering - Fetch content or locations under a parent with a simple
  query-array format (class filter, limit, offset, sorting, depth).
- Value objects - Work with `expSiteApiContent` and `expSiteApiLocation`
  wrappers that expose simple accessors and `toArray()` for JSON output.
- Static facade - Reach every service through one entry point,
  `expSiteApi`, with no service container or configuration required.

The service/value-object split mirrors `netgen/ibexa-site-api` (LoadService,
FilterService, Content and Location values), reimplemented directly on
`eZContentObject` and `eZContentObjectTreeNode` with no Symfony
dependencies.

Features
--------

Exponential Site API provides the following features in detail:

Key classes

| Class | File | Purpose |
| --- | --- | --- |
| `expSiteApi` | `classes/expsiteapi.php` | Static facade: `content()`, `location()`, `filter()` |
| `expSiteApiContentService` | `classes/expsiteapicontentservice.php` | Load content by ID, remote ID, location ID; find |
| `expSiteApiLocationService` | `classes/expsiteapilocationservice.php` | Load locations, children, parents; find |
| `expSiteApiFilterService` | `classes/expsiteapifilterservice.php` | `findContent()` / `findLocations()` with a query array |
| `expSiteApiContent` | `classes/expsiteapicontent.php` | Value object wrapping `eZContentObject` |
| `expSiteApiLocation` | `classes/expsiteapilocation.php` | Value object wrapping `eZContentObjectTreeNode` |

ContentService

- `load( $contentId )` - load by content object ID
- `loadByRemoteId( $remoteId )` - load by remote ID
- `loadByLocationId( $nodeId )` - load the content for a node
- `loadMainLocationContent( $contentId )` - load the main location of a content item
- `find( $query )` - shortcut to `FilterService::findContent`

LocationService

- `load( $nodeId )` - load a tree node
- `loadByContentId( $contentId )` - load the main location for a content object
- `loadChildren( $nodeId, $params )` - load direct children with optional limit/offset/class filter
- `loadParent( $nodeId )` - load the parent node
- `find( $query )` - shortcut to `FilterService::findLocations`

FilterService

- `findContent( $query )` - fetch content under a parent, optionally filtered by class
- `findLocations( $query )` - fetch nodes under a parent, optionally filtered by class and depth

Query format

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

Query key defaults: `parent_id` 2, `limit` 25, `offset` 0, `sort_by`
`array( 'name', true )`. `content_type_identifier` becomes
`ClassFilterType=include` + `ClassFilterArray`. `findContent()` always uses
depth 1; only `findLocations()` honours `depth`. `sort_by` is passed
straight through as a `subTreeByNodeID` `SortBy` value, so any kernel sort
field works (`name`, `published`, `priority`, ...).

Value objects

- `expSiteApiContent` wraps `eZContentObject` and exposes `id()`,
  `remoteId()`, `name()`, `contentTypeIdentifier()`, `mainLocationId()`,
  `published()`, `modified()`, `ownerId()`, `dataMap()`, `field()`,
  `isAvailable()`, `toArray()` and `getObject()`.
- `expSiteApiLocation` wraps `eZContentObjectTreeNode` and exposes `id()`,
  `contentId()`, `name()`, `path()`, `urlAlias()`, `depth()`,
  `isMainLocation()`, `childrenCount()`, `content()`, `isAvailable()`,
  `toArray()` and `getNode()`.

Both provide simple accessor methods and `toArray()` for JSON output. All
loaders return `null` when nothing is found.

Version
-------

- The current version of Exponential Site API is 1.0.0
- Last Major update: July 30, 2026

Copyright
---------

- Exponential Site API is copyright 1998 - 2026 7x
- See: [LICENSE.md](LICENSE.md) for more information on the terms of the copyright and license

License
-------

Exponential Site API is licensed under the GNU General Public License.

The complete license agreement is included in the LICENSE.md file.

Exponential Site API is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License or at your
option a later version.

Exponential Site API is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

The GNU GPL gives you the right to use, modify and redistribute
Exponential Site API under certain conditions. The GNU GPL license
is distributed with the software, see the file LICENSE.md.

It is also available at https://www.gnu.org/licenses/gpl.txt

You should have received a copy of the GNU General Public License
along with Exponential Site API in LICENSE.md.  If not, see https://www.gnu.org/licenses/.

Using Exponential Site API under the terms of the GNU GPL is free (as in freedom).

For more information or questions please contact info@se7enx.com

Requirements
------------

The following requirements exists for using Exponential Site API extension:

Exponential CMS / eZ Publish Legacy version
- Make sure you use Exponential 6 (eZ Publish Legacy) or higher.

PHP version
- Make sure you have PHP 8.1 or higher.

Other extensions
- No other extensions are required; the services use only kernel classes.

Installation
------------

Installation is the standard legacy extension procedure: place the extension
in `extension/expsite_api`, activate it via `[ExtensionSettings]`
`ActiveExtensions[]` (or `ActiveAccessExtensions[]` for a single siteaccess),
regenerate the extension autoloads (the class map lives in
`autoloads/expsite_api_autoload.php`) and clear all caches. There is no
configuration; the extension ships no INI settings.

See the complete step-by-step instructions in [INSTALL.md](INSTALL.md).

Usage
-----

Get services through the static facade; `expSiteApi` returns a fresh service
instance per call:

```php
$content  = expSiteApi::content()->load( 42 );
$location = expSiteApi::location()->load( 2 );
$children = expSiteApi::location()->loadChildren( 2, array( 'limit' => 10 ) );
$items    = expSiteApi::filter()->findContent( array(
    'parent_id' => 2,
    'content_type_identifier' => array( 'article' ),
) );
```

Loading content and locations:

```php
$content = expSiteApi::content()->load( 42 );                    // by content object ID
$content = expSiteApi::content()->loadByRemoteId( 'abc123' );    // by remote ID
$content = expSiteApi::content()->loadByLocationId( 60 );        // content shown at a node

$location = expSiteApi::location()->load( 2 );               // by node ID
$location = expSiteApi::location()->loadByContentId( 42 );   // main node of a content object
$parent   = expSiteApi::location()->loadParent( 60 );        // parent node
```

All loaders return `null` when nothing is found - always check before use.
`loadChildren()` always fetches depth 1 (direct children) and returns an
array of `expSiteApiLocation`.

Filtering - `findContent()` returns `expSiteApiContent[]`,
`findLocations()` returns `expSiteApiLocation[]`:

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
    'depth' => 2,
    'content_type_identifier' => array( 'folder' ),
) );
```

Building a JSON endpoint payload with the value objects:

```php
$items = expSiteApi::filter()->findLocations( array( 'parent_id' => $parentId, 'limit' => 20 ) );

$payload = array_map(
    function( $location ) { return $location->toArray(); },
    $items
);

header( 'Content-Type: application/json' );
echo json_encode( array( 'items' => $payload ) );
```

See [doc/USAGE.md](doc/USAGE.md) for the full verified examples - JSON
endpoints, module views with drill-down, Layouts block handlers - and for
the customization guide covering all three layers: the settings layer (no
INI shipped; tunables travel in the query arrays and belong in your own
extension's settings), the template layer (no templates shipped; rendering
of the value objects is owned by the calling templates) and the PHP layer
(subclassing the services and value objects, overriding `toArray()`, and
wrapping the facade when your subclasses should be returned).

Documentation
-------------

| Document | Description |
| --- | --- |
| [INSTALL.md](INSTALL.md) | Activation and verification instructions |
| [doc/USAGE.md](doc/USAGE.md) | Verified code examples plus the settings / template / PHP customization layers |
| [doc/FAQ.md](doc/FAQ.md) | Answers to the most common questions |
| [doc/TODO.md](doc/TODO.md) | Known gaps and planned improvements |
| [doc/SUPPORT.md](doc/SUPPORT.md) | How and where to get help |
| [LICENSE.md](LICENSE.md) | The complete GNU General Public License agreement |

Troubleshooting
---------------

Read the FAQ
- Some problems are more common than others. The most common ones are listed in the [doc/FAQ.md](doc/FAQ.md).

Use our support systems
- If you have any questions not handled by this document or the FAQ you can reach us through [se7enx.com](https://se7enx.com)
- If you find a bug or defect, please report it to the [Exponential Site API: Issue Tracker](https://github.com/se7enxweb/expsite_api/issues)
