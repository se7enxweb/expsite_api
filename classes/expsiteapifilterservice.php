<?php
class expSiteApiFilterService
{
    public function findContent( $query = array() )
    {
        $parentId = isset( $query['parent_id'] ) ? (int)$query['parent_id'] : 2;
        $limit = isset( $query['limit'] ) ? (int)$query['limit'] : 25;
        $offset = isset( $query['offset'] ) ? (int)$query['offset'] : 0;
        $contentType = isset( $query['content_type_identifier'] ) ? (array)$query['content_type_identifier'] : array();

        $params = array(
            'Depth' => 1,
            'Limit' => $limit,
            'Offset' => $offset,
            'SortBy' => isset( $query['sort_by'] ) ? $query['sort_by'] : array( 'name', true ),
        );

        if ( !empty( $contentType ) )
        {
            $params['ClassFilterType'] = 'include';
            $params['ClassFilterArray'] = $contentType;
        }

        $nodes = eZContentObjectTreeNode::subTreeByNodeID( $params, $parentId );
        if ( !is_array( $nodes ) )
            return array();

        $contents = array();
        foreach ( $nodes as $node )
        {
            $object = $node->attribute( 'object' );
            if ( $object instanceof eZContentObject )
                $contents[] = new expSiteApiContent( $object );
        }

        return $contents;
    }

    public function findLocations( $query = array() )
    {
        $parentId = isset( $query['parent_id'] ) ? (int)$query['parent_id'] : 2;
        $limit = isset( $query['limit'] ) ? (int)$query['limit'] : 25;
        $offset = isset( $query['offset'] ) ? (int)$query['offset'] : 0;
        $contentType = isset( $query['content_type_identifier'] ) ? (array)$query['content_type_identifier'] : array();
        $depth = isset( $query['depth'] ) ? (int)$query['depth'] : 1;

        $params = array(
            'Depth' => $depth,
            'Limit' => $limit,
            'Offset' => $offset,
            'SortBy' => isset( $query['sort_by'] ) ? $query['sort_by'] : array( 'name', true ),
        );

        if ( !empty( $contentType ) )
        {
            $params['ClassFilterType'] = 'include';
            $params['ClassFilterArray'] = $contentType;
        }

        $nodes = eZContentObjectTreeNode::subTreeByNodeID( $params, $parentId );
        if ( !is_array( $nodes ) )
            return array();

        return array_map( function( $node ) { return new expSiteApiLocation( $node ); }, $nodes );
    }
}
