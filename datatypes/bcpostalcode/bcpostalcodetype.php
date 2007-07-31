<?php
//
// Definition of BcPostalCodeType class
//
// Created on: <01-Jun-2007 08:00:00 gb>
//
// COPYRIGHT NOTICE: Copyright (C) 1999-2007 Brookins Consulting
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
// 
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
// 
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//

/*!
  \class BcPostalCodeType bcpostalcodetype.php
  \ingroup eZDatatype
  \brief A content datatype which handles text lines

  It provides the functionality to work as a text line and handles
  class definition input, object definition input and object viewing.

  It uses the spare field data_text in a content object attribute for storing
  the attribute data.
*/

include_once( 'kernel/classes/ezdatatype.php' );
include_once( 'lib/ezutils/classes/ezintegervalidator.php' );
include_once( 'kernel/common/i18n.php' );

define( 'EZ_DATATYPEPOSTALCODE_STRING', 'bcpostalcode' );
define( 'EZ_DATATYPEPOSTALCODE_MAX_LEN_FIELD', 'data_int1' );
define( 'EZ_DATATYPEPOSTALCODE_MAX_LEN_VARIABLE', '_bcpostalcode_max_string_length_' );
define( "EZ_DATATYPEPOSTALCODE_DEFAULT_STRING_FIELD", "data_text1" );
define( "EZ_DATATYPEPOSTALCODE_DEFAULT_STRING_VARIABLE", "_bcpostalcode_default_value_" );

class BcPostalCodeType extends eZDataType
{
    /*!
     Initializes with a string id and a description.
    */
    function BcPostalCodeType()
    {
        $this->eZDataType( EZ_DATATYPEPOSTALCODE_STRING, ezi18n( 'kernel/classes/datatypes', 'Postal Code', 'Datatype name' ),
                           array( 'serialize_supported' => true,
                                  'object_serialize_map' => array( 'data_text' => 'text' ) ) );
        $this->MaxLenValidator = new eZIntegerValidator();
    }

    /*!
     Sets the default value.
    */
    function initializeObjectAttribute( &$contentObjectAttribute, $currentVersion, &$originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
	  /*
             $contentObjectAttributeID = $contentObjectAttribute->attribute( "id" );
             $currentObjectAttribute = eZContentObjectAttribute::fetch( $contentObjectAttributeID,
                                                                         $currentVersion );
	  */
            $dataText = $originalContentObjectAttribute->attribute( "data_text" );
            $contentObjectAttribute->setAttribute( "data_text", $dataText );
        }
        else
        {
            $contentClassAttribute =& $contentObjectAttribute->contentClassAttribute();
            $default = $contentClassAttribute->attribute( "data_text1" );
            if ( $default !== "" )
            {
                $contentObjectAttribute->setAttribute( "data_text", $default );
            }
        }
    }

    /*
     Private method, only for using inside this class.
    */
    function validateStringHTTPInput( $data, &$contentObjectAttribute, &$classAttribute )
    {
        // $textCodec =& eZTextCodec::instance( false );
        // $maxLen = $classAttribute->attribute( EZ_DATATYPEPOSTALCODE_MAX_LEN_FIELD );
        $maxLen = 10;

	if ( strlen( $data ) == 5 ) {
	  if ( !preg_match( "/^\d\d\d\d\d$/", $data ) ) {
	    $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
								 'The input is a invalid format. The maximum number of digits allowed is 5.' ),
							 $maxLen );
	    
	    return EZ_INPUT_VALIDATOR_STATE_INVALID; // not valid 5 digit
	  }
	} elseif ( strlen( $data ) == 10 ) {
	  if ( !preg_match( "/^\d{5}(\-\d{4})$/", $data ) )
	  {
	    $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
								 'The input is a invalid format. The maximum number of digits allowed is 9.' ),
							 $maxLen );
	    return EZ_INPUT_VALIDATOR_STATE_INVALID; // not valid 9 digit
	  }
	} elseif ( strlen( $data ) > 5 && strlen( $data ) < 10 ) {
	    $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
								 'The input is a invalid format. The number of characters allowed is 5 or 10.' ),
							 $maxLen );
	    return EZ_INPUT_VALIDATOR_STATE_INVALID; // wasn't a 5 or 9 digit zip
	} elseif ( strlen( $data ) > 10 ) {
	    $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
								 'The input is a invalid format. The maximum number of characters allowed is 10.' ),
							 $maxLen );
	    return EZ_INPUT_VALIDATOR_STATE_INVALID; // wasn't a 5 or 9 digit zip
	} 

        return EZ_INPUT_VALIDATOR_STATE_ACCEPTED;
    }


    /*!
     \reimp
    */
    function validateObjectAttributeHTTPInput( &$http, $base, &$contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . '_bcpostalcode_data_text_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $data = $http->postVariable( $base . '_bcpostalcode_data_text_' . $contentObjectAttribute->attribute( 'id' ) );
            $classAttribute =& $contentObjectAttribute->contentClassAttribute();

            if ( $data == "" )
            {
                if ( !$classAttribute->attribute( 'is_information_collector' ) and
                     $contentObjectAttribute->validateIsRequired() )
                {
                    $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                         'Input required.' ) );
                    return EZ_INPUT_VALIDATOR_STATE_INVALID;
                }
            }
            else
            {
                return $this->validateStringHTTPInput( $data, $contentObjectAttribute, $classAttribute );
            }
        }
        return EZ_INPUT_VALIDATOR_STATE_ACCEPTED;
    }

    /*!
     \reimp
    */
    function validateCollectionAttributeHTTPInput( &$http, $base, &$contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . '_bcpostalcode_data_text_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $data = $http->postVariable( $base . '_bcpostalcode_data_text_' . $contentObjectAttribute->attribute( 'id' ) );
            $classAttribute =& $contentObjectAttribute->contentClassAttribute();

            if ( $data == "" )
            {
                if ( $contentObjectAttribute->validateIsRequired() )
                {
                    $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                         'Input required.' ) );
                    return EZ_INPUT_VALIDATOR_STATE_INVALID;
                }
                else
                    return EZ_INPUT_VALIDATOR_STATE_ACCEPTED;
            }
            else
            {
                return $this->validateStringHTTPInput( $data, $contentObjectAttribute, $classAttribute );
            }
        }
        else
            return EZ_INPUT_VALIDATOR_STATE_INVALID;
    }

    /*!
     Fetches the http post var string input and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( &$http, $base, &$contentObjectAttribute )
    {

      die('no1');
        if ( $http->hasPostVariable( $base . '_bcpostalcode_data_text_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $data = $http->postVariable( $base . '_bcpostalcode_data_text_' . $contentObjectAttribute->attribute( 'id' ) );
            $contentObjectAttribute->setAttribute( 'data_text', $data );
            return true;
        }
        return false;
    }

    /*!
     Fetches the http post variables for collected information
    */
    function fetchCollectionAttributeHTTPInput( &$collection, &$collectionAttribute, &$http, $base, &$contentObjectAttribute )
    {
       if ( $http->hasPostVariable( $base . "_bcpostalcode_data_text_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $dataText = $http->postVariable( $base . "_bcpostalcode_data_text_" . $contentObjectAttribute->attribute( "id" ) );
            $collectionAttribute->setAttribute( 'data_text', $dataText );

            return true;
        }
        return false;
    }

    /*!
     Does nothing since it uses the data_text field in the content object attribute.
     See fetchObjectAttributeHTTPInput for the actual storing.
    */
    function storeObjectAttribute( &$attribute )
    {
    }

    /*!
     \reimp
     Simple string insertion is supported.
    */
    function isSimpleStringInsertionSupported()
    {
        return true;
    }

    /*!
     \reimp
     Inserts the string \a $string in the \c 'data_text' database field.
    */
    function insertSimpleString( &$object, $objectVersion, $objectLanguage,
                                 &$objectAttribute, $string,
                                 &$result )
    {
        $result = array( 'errors' => array(),
                         'require_storage' => true );
        $objectAttribute->setContent( $string );
        $objectAttribute->setAttribute( 'data_text', $string );
        return true;
    }

    function storeClassAttribute( &$attribute, $version )
    {
    }

    function storeDefinedClassAttribute( &$attribute )
    {
    }

    /*!
     \reimp
    */
    function validateClassAttributeHTTPInput( &$http, $base, &$classAttribute )
    {
        $maxLenName = $base . EZ_DATATYPEPOSTALCODE_MAX_LEN_VARIABLE . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $maxLenName ) )
        {
            $maxLenValue = $http->postVariable( $maxLenName );
            $maxLenValue = str_replace(" ", "", $maxLenValue );
            if( ( $maxLenValue == "" ) ||  ( $maxLenValue == 0 ) )
            {
                $maxLenValue = 0;
                $http->setPostVariable( $maxLenName, $maxLenValue );
                return EZ_INPUT_VALIDATOR_STATE_ACCEPTED;
            }
            else
            {
                $this->MaxLenValidator->setRange( 1, false );
                return $this->MaxLenValidator->validate( $maxLenValue );
            }
        }
        return EZ_INPUT_VALIDATOR_STATE_INVALID;
    }

    /*!
     \reimp
    */
    function fixupClassAttributeHTTPInput( &$http, $base, &$classAttribute )
    {
        $maxLenName = $base . EZ_DATATYPEPOSTALCODE_MAX_LEN_VARIABLE . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $maxLenName ) )
        {
            $maxLenValue = $http->postVariable( $maxLenName );
            $this->MaxLenValidator->setRange( 1, false );
            $maxLenValue = $this->MaxLenValidator->fixup( $maxLenValue );
            $http->setPostVariable( $maxLenName, $maxLenValue );
        }
    }

    /*!
     \reimp
    */
    function fetchClassAttributeHTTPInput( &$http, $base, &$classAttribute )
    {
        $maxLenName = $base . EZ_DATATYPEPOSTALCODE_MAX_LEN_VARIABLE . $classAttribute->attribute( 'id' );
        $defaultValueName = $base . EZ_DATATYPEPOSTALCODE_DEFAULT_STRING_VARIABLE . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $maxLenName ) )
        {
            $maxLenValue = $http->postVariable( $maxLenName );
            $classAttribute->setAttribute( EZ_DATATYPEPOSTALCODE_MAX_LEN_FIELD, $maxLenValue );
        }
        if ( $http->hasPostVariable( $defaultValueName ) )
        {
            $defaultValueValue = $http->postVariable( $defaultValueName );

            $classAttribute->setAttribute( EZ_DATATYPEPOSTALCODE_DEFAULT_STRING_FIELD, $defaultValueValue );
        }
        return true;
    }

    /*!
     Returns the content.
    */
    function &objectAttributeContent( &$contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }

    /*!
     Returns the meta data used for storing search indeces.
    */
    function metaData( &$contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }

    /*!
     Returns the content of the string for use as a title
    */
    function title( &$contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }

    function hasObjectAttributeContent( &$contentObjectAttribute )
    {
        return trim( $contentObjectAttribute->attribute( 'data_text' ) ) != '';
    }

    /*!
     \reimp
    */
    function isIndexable()
    {
        return true;
    }

    /*!
     \reimp
    */
    function isInformationCollector()
    {
        return true;
    }

    /*!
     \reimp
    */
    function sortKey( &$contentObjectAttribute )
    {
        include_once( 'lib/ezi18n/classes/ezchartransform.php' );
        $trans =& eZCharTransform::instance();
        return $trans->transformByGroup( $contentObjectAttribute->attribute( 'data_text' ), 'lowercase' );
    }

    /*!
     \reimp
    */
    function sortKeyType()
    {
        return 'string';
    }

    /*!
     \reimp
    */
    function serializeContentClassAttribute( &$classAttribute, &$attributeNode, &$attributeParametersNode )
    {
        $maxLength = $classAttribute->attribute( EZ_DATATYPEPOSTALCODE_MAX_LEN_FIELD );
        $defaultString = $classAttribute->attribute( EZ_DATATYPEPOSTALCODE_DEFAULT_STRING_FIELD );
        $attributeParametersNode->appendChild( eZDOMDocument::createElementTextNode( 'max-length', $maxLength ) );
        if ( $defaultString )
            $attributeParametersNode->appendChild( eZDOMDocument::createElementTextNode( 'default-string', $defaultString ) );
        else
            $attributeParametersNode->appendChild( eZDOMDocument::createElementNode( 'default-string' ) );
    }

    /*!
     \reimp
    */
    function unserializeContentClassAttribute( &$classAttribute, &$attributeNode, &$attributeParametersNode )
    {
        $maxLength = $attributeParametersNode->elementTextContentByName( 'max-length' );
        $defaultString = $attributeParametersNode->elementTextContentByName( 'default-string' );
        $classAttribute->setAttribute( EZ_DATATYPEPOSTALCODE_MAX_LEN_FIELD, $maxLength );
        $classAttribute->setAttribute( EZ_DATATYPEPOSTALCODE_DEFAULT_STRING_FIELD, $defaultString );
    }

    /*!
      \reimp
    */
    function diff( $old, $new, $options = false )
    {
        include_once( 'lib/ezdiff/classes/ezdiff.php' );
        $diff = new eZDiff();
        $diff->setDiffEngineType( $diff->engineType( 'text' ) );
        $diff->initDiffEngine();
        $diffObject = $diff->diff( $old->content(), $new->content() );
        return $diffObject;
    }

    /// \privatesection
    /// The max len validator
    var $MaxLenValidator;
}

eZDataType::register( EZ_DATATYPEPOSTALCODE_STRING, 'bcpostalcodetype' );

?>
