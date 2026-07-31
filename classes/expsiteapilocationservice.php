<?php
class expSiteApiLocationService
{
    public function load( $nodeId )
    {
        $node = eZContentObjectTreeNode::fetch( (int)$nodeId );
        return $node instanceof eZContentObjectTreeNode ? new expSiteApiLocation( $node ) : null;
    }

    public function loadByContentId( $contentId )
    {
        $object = eZContentObject::fetch( (int)$contentId );
        if ( !$object instanceof eZContentObject )
            return null;

        $nodeId = (int)$object->attribute( 'main_node_id' );
        if ( $nodeId <= 0 )
            return null;

        $node = eZContentObjectTreeNode::fetch( $nodeId );
        return $node instanceof eZContentObjectTreeNode ? new expSiteApiLocation( $node ) : null;
    }

    public function loadChildren( $nodeId, $params = array() )
    {
        $parent = eZContentObjectTreeNode::fetch( (int)$nodeId );
        if ( !$parent instanceof eZContentObjectTreeNode )
            return array();

        $limit = isset( $params['limit'] ) ? (int)$params['limit'] : 25;
        $offset = isset( $params['offset'] ) ? (int)$params['offset'] : 0;
        $classFilter = isset( $params['content_type_identifier'] ) ? (array)$params['content_type_identifier'] : array();

        $params = array(
            'Depth' => 1,
            'Limit' => $limit,
            'Offset' => $offset,
            'SortBy' => isset( $params['sort_by'] ) ? $params['sort_by'] : array( 'name', true ),
        );

        if ( !empty( $classFilter ) )
        {
            $params['ClassFilterType'] = 'include';
            $params['ClassFilterArray'] = $classFilter;
        }

        $nodes = eZContentObjectTreeNode::subTreeByNodeID( $params, (int)$parent->attribute( 'node_id' ) );
        if ( !is_array( $nodes ) )
            return array();

        return array_map( function( $node ) { return new expSiteApiLocation( $node ); }, $nodes );
    }

    public function loadParent( $nodeId )
    {
        $node = eZContentObjectTreeNode::fetch( (int)$nodeId );
        if ( !$node instanceof eZContentObjectTreeNode )
            return null;

        $parent = $node->attribute( 'parent' );
        if ( !$parent instanceof eZContentObjectTreeNode )
            return null;

        return new expSiteApiLocation( $parent );
    }

    public function find( $query = array() )
    {
        $filter = new expSiteApiFilterService();
        return $filter->findLocations( $query );
    }
}
