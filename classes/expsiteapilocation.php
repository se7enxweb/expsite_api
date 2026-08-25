<?php
class expSiteApiLocation
{
    protected $node;

    public function __construct( eZContentObjectTreeNode $node = null )
    {
        $this->node = $node;
    }

    public function getNode()
    {
        return $this->node;
    }

    public function id()
    {
        return $this->node ? (int)$this->node->attribute( 'node_id' ) : 0;
    }

    public function contentId()
    {
        return $this->node ? (int)$this->node->attribute( 'contentobject_id' ) : 0;
    }

    public function name()
    {
        return $this->node ? (string)$this->node->attribute( 'name' ) : '';
    }

    public function path()
    {
        return $this->node ? (string)$this->node->attribute( 'path_string' ) : '';
    }

    public function urlAlias()
    {
        return $this->node ? (string)$this->node->attribute( 'url_alias' ) : '';
    }

    public function depth()
    {
        return $this->node ? (int)$this->node->attribute( 'depth' ) : 0;
    }

    public function isMainLocation()
    {
        if ( !$this->node )
            return false;

        return (int)$this->node->attribute( 'node_id' ) === (int)$this->node->attribute( 'main_node_id' );
    }

    public function childrenCount()
    {
        return $this->node ? (int)$this->node->attribute( 'children_count' ) : 0;
    }

    public function content()
    {
        if ( !$this->node )
            return null;

        $object = $this->node->attribute( 'object' );
        return $object instanceof eZContentObject ? new expSiteApiContent( $object ) : null;
    }

    public function isAvailable()
    {
        return $this->node instanceof eZContentObjectTreeNode;
    }

    public function hasAttribute( $name )
    {
        return in_array( $name, array(
            'id', 'contentId', 'name', 'path', 'urlAlias', 'depth',
            'isMainLocation', 'childrenCount', 'content'
        ) );
    }

    public function attribute( $name )
    {
        switch ( $name )
        {
            case 'id':
                return $this->id();
            case 'contentId':
                return $this->contentId();
            case 'name':
                return $this->name();
            case 'path':
                return $this->path();
            case 'urlAlias':
                return $this->urlAlias();
            case 'depth':
                return $this->depth();
            case 'isMainLocation':
                return $this->isMainLocation();
            case 'childrenCount':
                return $this->childrenCount();
            case 'content':
                return $this->content();
        }
        return null;
    }

    public function toArray()
    {
        $content = $this->content();

        return array(
            'id' => $this->id(),
            'content_id' => $this->contentId(),
            'name' => $this->name(),
            'path' => $this->path(),
            'url_alias' => $this->urlAlias(),
            'depth' => $this->depth(),
            'is_main_location' => $this->isMainLocation(),
            'children_count' => $this->childrenCount(),
            'content' => $content ? $content->toArray() : null,
        );
    }
}
