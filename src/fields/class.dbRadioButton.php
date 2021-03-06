<?php
/**
 * class dbRadioButton
 *
 * Create a Radio button field from records retrieved from the db
 *
 * @author Johan Wiegel
 * @package FormHandler
 * @subpackage Fields
 * @since 10-04-2008
 */

class dbRadioButton extends RadioButton
{
     /**
     * dbCheckBox::dbCheckBox()
     *
     * Public constructor: create a new db RadioButton object
     *
     * @param object &$oForm: the form where the datefield is located on
     * @param string $sName: the name of the datefield
     * @param object $oDb: object of the database handler
     * @param string $sTable: the table to get the fields from
     * @param mixed $mFields: array of string with the names of the fields which data we should get
     * @param string $sExtraSQL: extra SQL statements
     * @return dbRadioButton
     * @access public
     * @author Johan Wiegel
     */
	public function __construct( &$oForm, $sName, &$oDb, $sTable, $mFields, $sExtraSQL = null, $sMask = null )
	{
	    // call the constructor of the selectfield
		parent::__construct( $oForm, $sName, '' );

		// convert _aOptions to array as we cannot add elements to a string 
		if (is_string($this->_aOptions))
        	{
            		$this->_aOptions = array();			
        	}		
		
		// make sure that the fields are set in an array
		$aFields = !is_array($mFields) ? array( $mFields ) : $mFields;
		$this -> useArrayKeyAsValue( sizeof( $aFields) == 2 );

		// generate the query to retrieve the records
		$sQuery =
		  'SELECT '. implode(', ', $aFields).
		  ' FROM '. $oDb->quote( $sTable).' '.$sExtraSQL;

		// get the records and load the options
		//$this->_aOptions = is_array($aMergeArray) ? $aMergeArray : array();

		
		// execute the query
		$sql = $oDb->query( $sQuery );

		// query succeeded
		if( $sql )
		{
    		while( $row = $oDb->getRecord( $sql ) )
    		{
    			if( sizeof( $row ) == 1 )
    			{
    				$this->_aOptions[] = array_shift( $row );
    			}
    			else
    			{
    		       	$this->_aOptions[array_shift( $row )] = array_shift( $row );
    		    }
    		}
		}
		// query failed
		else
		{
		    trigger_error(
		      "Error, could not retrieve records.<br '. FH_XHTML_CLOSE .'>\n".
		      "Error message: ". $oDb->getError()."<br '. FH_XHTML_CLOSE .'>\n".
		      "Query: ". $sQuery,
		      E_USER_WARNING
		    );
		}
 	}
}

?>
