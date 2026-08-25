<?php
class expSiteApiContent
{
    protected $object;

    public function __construct( eZContentObject $object = null )
    {
        $this->object = $object;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function id()
    {
        return $this->object ? (int)$this->object->attribute( 'id' ) : 0;
    }

    public function remoteId()
    {
        return $this->object ? (string)$this->object->attribute( 'remote_id' ) : '';
    }

    public function name()
    {
        return $this->object ? (string)$this->object->attribute( 'name' ) : '';
    }

    public function contentTypeIdentifier()
    {
        return $this->object ? (string)$this->object->attribute( 'class_identifier' ) : '';
    }

    public function mainLocationId()
    {
        return $this->object ? (int)$this->object->attribute( 'main_node_id' ) : 0;
    }

    public function published()
    {
        return $this->object ? (int)$this->object->attribute( 'published' ) : 0;
    }

    public function modified()
    {
        return $this->object ? (int)$this->object->attribute( 'modified' ) : 0;
    }

    public function ownerId()
    {
        return $this->object ? (int)$this->object->attribute( 'owner_id' ) : 0;
    }

    public function dataMap()
    {
        return $this->object ? $this->object->dataMap() : array();
    }

    public function field( $identifier )
    {
        $dataMap = $this->dataMap();
        return isset( $dataMap[$identifier] ) ? $dataMap[$identifier] : null;
    }

    public function isAvailable()
    {
        return $this->object instanceof eZContentObject;
    }

    public function hasAttribute( $name )
    {
        return in_array( $name, array(
            'id', 'name', 'remoteId', 'contentTypeIdentifier', 'mainLocationId',
            'published', 'modified', 'ownerId', 'contentInfo', 'fields'
        ) );
    }

    public function attribute( $name )
    {
        switch ( $name )
        {
            case 'id':
                return $this->id();
            case 'name':
                return $this->name();
            case 'remoteId':
                return $this->remoteId();
            case 'contentTypeIdentifier':
                return $this->contentTypeIdentifier();
            case 'mainLocationId':
                return $this->mainLocationId();
            case 'published':
                return $this->published();
            case 'modified':
                return $this->modified();
            case 'ownerId':
                return $this->ownerId();
            case 'contentInfo':
                return $this->contentInfo();
            case 'fields':
                return $this->fields();
        }
        return null;
    }

    protected function contentInfo()
    {
        if ( !$this->object )
            return array();

        $class = $this->object->attribute( 'content_class' );
        return array(
            'id' => $this->id(),
            'contentTypeIdentifier' => $this->contentTypeIdentifier(),
            'contentTypeName' => $class ? (string)$class->attribute( 'name' ) : '',
            'publishedDate' => $this->published(),
            'mainLocationId' => $this->mainLocationId(),
        );
    }

    protected function fields()
    {
        $fields = array();
        $dataMap = $this->dataMap();

        if ( class_exists( 'sevenxThemesMediaOperators' ) )
        {
            $operators = new sevenxThemesMediaOperators();
            foreach ( $dataMap as $identifier => $attr )
            {
                if ( $attr instanceof eZContentObjectAttribute )
                    $fields[$identifier] = $operators->wrapField( $attr );
            }
        }
        else
        {
            foreach ( $dataMap as $identifier => $attr )
            {
                $fields[$identifier] = $attr;
            }
        }

        return $fields;
    }

    public function toArray()
    {
        $data = array();
        foreach ( $this->dataMap() as $identifier => $attribute )
        {
            $data[$identifier] = $attribute ? $attribute->toString() : null;
        }

        return array(
            'id' => $this->id(),
            'remote_id' => $this->remoteId(),
            'name' => $this->name(),
            'content_type_identifier' => $this->contentTypeIdentifier(),
            'main_location_id' => $this->mainLocationId(),
            'published' => $this->published(),
            'modified' => $this->modified(),
            'owner_id' => $this->ownerId(),
            'fields' => $data,
        );
    }
}
