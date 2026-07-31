<?php
class expSiteApiContentService
{
    public function load( $contentId )
    {
        $object = eZContentObject::fetch( (int)$contentId );
        return $object instanceof eZContentObject ? new expSiteApiContent( $object ) : null;
    }

    public function loadByRemoteId( $remoteId )
    {
        $object = eZContentObject::fetchByRemoteID( (string)$remoteId );
        return $object instanceof eZContentObject ? new expSiteApiContent( $object ) : null;
    }

    public function loadByLocationId( $nodeId )
    {
        $node = eZContentObjectTreeNode::fetch( (int)$nodeId );
        if ( !$node instanceof eZContentObjectTreeNode )
            return null;

        $object = $node->attribute( 'object' );
        return $object instanceof eZContentObject ? new expSiteApiContent( $object ) : null;
    }

    public function loadMainLocationContent( $contentId )
    {
        $object = eZContentObject::fetch( (int)$contentId );
        if ( !$object instanceof eZContentObject )
            return null;

        $nodeId = (int)$object->attribute( 'main_node_id' );
        $node = $nodeId > 0 ? eZContentObjectTreeNode::fetch( $nodeId ) : null;
        if ( !$node instanceof eZContentObjectTreeNode )
            return null;

        return new expSiteApiLocation( $node );
    }

    public function find( $query = array() )
    {
        $filter = new expSiteApiFilterService();
        return $filter->findContent( $query );
    }
}
