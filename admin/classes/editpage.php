<?php
class EditPage extends RunnerPage
{
	protected $cachedRecord = null;
	
	public $keys = array();
	public $oldKeys = array();
	protected $keysChanged = false;
	
	public $jsKeys = array();
	
	public $keyFields = array();
	
	/**
	 * An array of edit page's fields
	 */
	public $editFields = array();
	public $readEditValues = false;
	/**
	 *	Values to be displayed in Edit controls
	 */
	protected $controlsDisabled = false;

	public $action = "";

	
	public $lockingAction = "";
	public $lockingSid = null;
	public $lockingKeys = null;
	public $lockingStart = null;
	
	protected $lockingMessageStyle = "display:none;";
	protected $lockingMessageText = "";
	
	protected $message = "";
	protected $messageType = MESSAGE_ERROR;
	
	
	protected $auditObj = null;
	
	protected $oldRecordData = null;
	protected $newRecordData = array();
	protected $newRecordBlobFields = array();
	
	protected $updatedSuccessfully = false;

	/**
	 * It's set up in inline edit mode only
	 */
	public $screenWidth = 0;
	/**
	 * It's set up in inline edit mode only
	 */	
	public $screenHeight = 0;	
	/**
	 * It's set up in inline edit mode only
	 */	
	public $orientation = '';
	
	/**
	 * The name of the dashboard element where the page is displayed on
	 * It's set up correctly in dash mode only
	 */
	public $dashElementName = "";
	
	/**
	 * The corresponding dashboard name
	 * It's set up correctly in dash mode only	 
	 */
	public $dashTName = "";

	
	/**
	 * @constructor
	 */
	function EditPage(&$params)
	{

		parent::RunnerPage($params);
		
		$this->setKeysForJs();
		
		$this->auditObj = GetAuditObject($this->tName);			
		
		$this->editFields = $this->getPageFields();
		
		$this->formBricks["header"] = "editheader";
		$this->formBricks["footer"] = "editbuttons";
		$this->assignFormFooterAndHeaderBricks( true );		
	}
	
	/**
	 * Assign session prefix
	 */
	protected function assignSessionPrefix()
	{	
		if( $this->mode == EDIT_DASHBOARD || ( $this->mode == EDIT_POPUP || $this->mode == EDIT_INLINE ) && $this->dashTName )
		{
			$this->sessionPrefix = $this->dashTName."_".$this->tName;
			return;
		} 
		
		parent::assignSessionPrefix();
	}
	
	/**
	 * Set session variables
	 */
	public function setSessionVariables()
	{	
		parent::setSessionVariables();
		
		$_SESSION[ $this->sessionPrefix.'_advsearch' ] = serialize($this->searchClauseObj);
	}	

	/**
	 * Get the page's fields list
	 * @return Array
	 */
	protected function getPageFields()
	{
		if( $this->mode == EDIT_INLINE )
			return $this->pSet->getInlineEditFields();
			
		return $this->pSet->getEditFields();	
	}
	
	/**
	 * Set keys values
	 * @param Array keys
	 */	
	public function setKeys($keys)
	{
		$this->cachedRecord = null;
		$this->keys = $keys;
		$this->setKeysForJs();
	}
	
	public function setKeysForJs()
	{
		$i = 0;
		foreach($this->keys as $field => $value)
		{
			$this->keyFields[ $i ] = $field;
			$this->jsKeys[ $i++ ] = $value;
		}
	}	
	
	/**
	 * Tell whether the page was called to update locking state only
	 */
	public function isLockingRequest()
	{
		return $this->lockingObj && ($this->lockingAction != "");
	}

	/**
	 * Perform locking action the page was called for
	 */
	public function doLockingAction()
	{
		$arrkeys = explode("&", urldecode( $this->lockingKeys ));
			
		foreach(array_keys($arrkeys) as $ind)
			$arrkeys[$ind] = urldecode($arrkeys[$ind]);
		
		if($this->lockingAction == "unlock")
		{
			$this->lockingObj->UnlockRecord($this->tName, $arrkeys, $this->lockingSid);
		}
		else if($this->lockingAction == "lockadmin" && (IsAdmin() || $_SESSION["AccessLevel"] == ACCESS_LEVEL_ADMINGROUP))
		{
			$this->lockingObj->UnlockAdmin($this->tName, $arrkeys, $this->lockingStart == "yes");
			if($this->lockingStart == "no")
				echo "unlock";
			else if($this->lockingStart == "yes")
				echo "lock";
		}
		else if($this->lockingAction == "confirm")
		{
			$lockMessage = "";
			if( !$this->lockingObj->ConfirmLock($this->tName, $arrkeys, $lockMessage) )
				echo $lockMessage;
		}
	}
	
	/**
	 * Set template file if it empty
	 */
	public function setTemplateFile()
	{
		if($this->mode == EDIT_INLINE)
			$this->templatefile = GetTemplateName($this->shortTableName, "inline_edit");
		parent::setTemplateFile();
	}

	public function init()
	{
		parent::init(); 
		
		if( $this->eventsObject->exists("BeforeProcessEdit") )
			$this->eventsObject->BeforeProcessEdit( $this );
	}
	
	public function process()
	{
		if( $this->captchaExists() )
		{
			$this->doCaptchaCode();
		}
		
		if( $this->action == "edited" )
		{
			$this->processDataInput();
			
			$this->readEditValues = !$this->updatedSuccessfully;
			
			if( $this->mode == EDIT_INLINE || $this->mode == EDIT_POPUP )
			{
				$this->reportInlineSaveStatus();
				return;
			}
			
			if( $this->prgRedirect() )
				return;
		}
		$this->prgReadMessage();
		
		//	get the record to edit
		if( !$this->readRecord() )
			return;

		if( !$this->IsRecordEditable( false ) )
			return $this->SecurityRedirect();

		if( !$this->lockRecord() )
			return;
		
		$this->prepareReadonlyFields();
	
		$this->doCommonAssignments();
		$this->prepareButtons();
		$this->prepareEditControls();
		$this->fillCntrlTabGroups();
		
		$this->prepareJsSettings();
		
		$this->prepareDetailsTables();

		if( $this->mode != EDIT_INLINE )
			$this->addButtonHandlers();		
		
		$this->addCommonJs();

		$this->fillSetCntrlMaps();
		
		$this->displayEditPage();	
	}
	
	/**
	 * Add table settings
	 */	
	protected function prepareJsSettings()
	{
		$this->jsSettings['tableSettings'][ $this->tName ]["keys"] = $this->jsKeys;
		$this->jsSettings['tableSettings'][ $this->tName ]['keyFields'] = $this->keyFields;
		
		if($this->lockingObj)
		{
			// $keys, $savedKeys could not be set properly if editid params were not passed, so use $this->keys instead
			$this->jsSettings['tableSettings'][ $this->tName ]["sKeys"] = implode("&", $this->keys);
			$this->jsSettings['tableSettings'][ $this->tName ]["enableCtrls"] = !$this->controlsDisabled;
			$this->jsSettings['tableSettings'][ $this->tName ]["confirmTime"] = $this->lockingObj->ConfirmTime;
		}		
	}
	
	/**
	 * Assign basic page's xt variables
	 */	
	protected function doCommonAssignments()
	{
		$this->xt->assign( "id", $this->id );
		
		//	display message
		$this->xt->assign("message_block", true);
		if( strlen($this->message) )
		{
			$mesClass = $this->messageType == MESSAGE_ERROR ? "message rnr-error" : "message" ;
			$this->xt->assign("message", "<div class='".$mesClass."'>" . $this->message . "</div>" );
		}
		else
		{
			$this->xt->displayBrickHidden("message");
		}

		
		//	display legacy page caption - key values
		$data = $this->getCurrentRecordInternal();
		foreach( $this->keyFields as $i => $k )
		{
			$viewFormat = $this->pSet->getViewFormat( $k );
			if( $viewFormat == FORMAT_HTML || $viewFormat == FORMAT_FILE_IMAGE || $viewFormat == FORMAT_FILE || 
				$viewFormat == FORMAT_HYPERLINK || $viewFormat == FORMAT_HYPERLINK || $viewFormat == FORMAT_EMAILHYPERLINK || 
				$viewFormat == FORMAT_CHECKBOX )
			{
				$this->xt->assign( "show_key" . ($i+1), runner_htmlspecialchars( $data[ $k ] ) );
			}
			else
			{
				$this->xt->assign( "show_key" . ($i+1), $this->showDBValue( $k, $data ) );
			}
		}
		//	labels		
		$this->assignEditFieldsBlocksAndLabels();

		//	body["end"]	- this assignment is very important
		if($this->mode == EDIT_SIMPLE)
		{
			$this->assignBody();
			// assign body end
			$this->xt->assign("flybody", true);
		}
	}
	
	/**
	 * Display the edit page
	 */		
	protected function displayEditPage()
	{
		// beforeshow event		
		$templateFile = $this->templatefile;
		if( $this->eventsObject->exists("BeforeShowEdit") )
			$this->eventsObject->BeforeShowEdit($this->xt, $templateFile, $this->getCurrentrecordInternal(), $this);
		
		if( $this->mode == EDIT_SIMPLE )
		{
			$this->display($templateFile);
			return;
		}
		
		if( $this->mode == EDIT_POPUP || $this->mode == EDIT_DASHBOARD )
		{
			$this->xt->assign("footer", false);
			$this->xt->assign("header", false);
			$this->xt->assign("body", $this->body);
			$this->displayAJAX($templateFile, $this->flyId + 1);
			exit();
		}
		
		if( $this->mode == EDIT_INLINE )
		{
			$returnJSON = array();
			$returnJSON["settings"] = $this->jsSettings;				
			$returnJSON["controlsMap"] = $this->controlsHTMLMap;
			$returnJSON["viewControlsMap"] = $this->viewControlsHTMLMap;
			
			$this->xt->load_template($templateFile);
			$returnJSON["html"] = array();
			foreach($this->editFields as $f)
			{
				if( $this->detailKeysByM && in_array($f, $this->detailKeysByM) )
					continue;
				$returnJSON["html"][ $f ] = $this->xt->fetchVar(GoodFieldName($f)."_editcontrol");
			}
			$returnJSON["additionalJS"] = $this->grabAllJsFiles();
			$returnJSON["additionalCSS"] = $this->grabAllCSSFiles();
			echo printJSON($returnJSON); 
			exit();
		}
	}

	/**
	 * Get extra JSON params to display the page on AJAX-like request	
	 * @return Array
	 */
	protected function getExtraAjaxPageParams()
	{
		return $this->getSaveStatusJSON();
	}
	
	/**
	 * Set details preview on the edit master page 
	 */		
	protected function prepareDetailsTables()
	{
		if( !$this->isShowDetailTables || $this->mode == EDIT_DASHBOARD || $this->mode == EDIT_INLINE )
			return;
			
		$dpParams = $this->getDetailsParams( $this->id ); 
		$this->jsSettings['tableSettings'][ $this->tName ]['dpParams'] = array('tableNames' => $dpParams['strTableNames'], 'ids' => $dpParams['ids']);	
		
		if( !count($dpParams['ids']) )
			return;

		$this->xt->assign("detail_tables", true);	
		$this->flyId = $dpParams['ids'][ count($dpParams['ids']) - 1 ] + 1;
		for($d = 0; $d < count($dpParams['ids']); $d++)
		{
			$this->setDetailPreview( $dpParams['type'][ $d ], $dpParams['strTableNames'][ $d ], $dpParams['ids'][ $d ], $this->getCurrentRecordInternal() );
		}
	}
	
	/**
	 * Assign buttons xt variables
	 */	
	protected function prepareButtons()
	{
		if( $this->mode == EDIT_INLINE )
			return;
		
		$this->prepareNextPrevButtons();

		if( $this->mode == EDIT_SIMPLE)
		{
			//	back to list/menu buttons
			if( $this->pSet->hasListPage() )
			{
				$this->xt->assign("back_button", true);
				$this->xt->assign("backbutton_attrs", "id=\"backButton".$this->id."\"");
				$this->xt->assign("mbackbutton_attrs", "id=\"extraBackButton".$this->id."\"");
			}
			else if( $this->isShowMenu() )
			{		
				$this->xt->assign("back_button", true);
				$this->xt->assign("backbutton_attrs", "id=\"backToMenuButton".$this->id."\"");
			}
		}
		
		if($this->mode == EDIT_POPUP)
		{
			$this->xt->assign("close_button", true);
			$this->xt->assign("closebutton_attrs", "id=\"closeButton".$this->id."\"");
		}

		$this->xt->assign("save_button", true);
		if( $this->controlsDisabled )
			$this->xt->assign("savebutton_attrs", "id=\"saveButton".$this->id."\" type=\"disabled\" ");
		else
			$this->xt->assign("savebutton_attrs", "id=\"saveButton".$this->id."\"");
		
		$this->xt->assign("resetbutton_attrs", 'id="resetButton'.$this->id.'"');		
		$this->xt->assign("reset_button", true);

		if( $this->mode == EDIT_DASHBOARD )
			return;
		
		if( $this->pSet->hasViewPage() && $this->permis[ $this->tName ]['search'] )
		{
			$this->xt->assign("view_page_button", true);
			$this->xt->assign("view_page_button_attrs", "id=\"viewPageButton".$this->id."\"");
		}		
	}
	
	protected function prepareNextPrevButtons()
	{
		if( !$this->pSet->useMoveNext() )
			return;
			
		$next = array();
		$prev = array();
		
		$this->getNextPrevRecordKeys( $this->getCurrentRecordInternal(), "Edit", $next, $prev, $this->mode == EDIT_DASHBOARD );
		
		//show Prev/Next buttons
		$this->assignPrevNextButtons( count( $next ) > 0, count( $prev ) > 0 );
		
		$this->jsSettings['tableSettings'][ $this->tName] ["prevKeys"] = $prev;
		$this->jsSettings['tableSettings'][ $this->tName ]["nextKeys"] = $next; 			
	}

	protected function readRecord()
	{
		if( $this->getCurrentRecordInternal() )
			return true;
		if($this->mode == EDIT_SIMPLE)
		{
			HeaderRedirect($this->pSet->getShortTableName(), "list", "a=return");
			exit();
		}
		//	nothing to edit.
		//	TODO: add some report or message
		exit();	
		return false;
	}
	
	/**
	 *	Format and prepare readonly field values
	 */
	protected function prepareReadonlyFields()
	{
		$fields = $this->pSet->getFieldsList();
		$data = $this->getCurrentRecordInternal();

		//	prepare field values
		//	keys
		$keyParams = array();
		foreach( $this->keyFields as $i => $k )
		{
			$keyParams[] = "key" . ($i + 1) . "=" . rawurldecode( $this->keys[ $k ] );
		}
		$keylink = "&" . implode("&", $keyParams);
		
		foreach( $fields as $f )
		{
			if( $this->pSet->getEditFormat( $f ) == EDIT_FORMAT_READONLY && 
				( $this->pSet->appearOnEditPage( $f ) || $this->pSet->appearOnInlineEdit( $f ) ) )
				$this->readOnlyFields[ $f ] = $this->showDBValue( $f , $data, $keylink );
		}
	}
	
	/**
	 *	Locks record for editing. 
	 * Returns false if the page can not continue processing. True otherwise.
	 */
	protected function lockRecord()
	{
		if( !$this->lockingObj )
			return true;

		//	locked OK
		if( $this->lockingObj->LockRecord( $this->tName, $this->keys) )
		{
			$this->body["begin"].= '<div class="rnr-locking" style="' .$this->lockingMessageStyle. '">' .$this->lockingMessageText. '</div>';
			return true;
		}
		
		//	NOT locked
		//	inline mode
		if($this->mode == EDIT_INLINE)
		{
			if(IsAdmin() || $_SESSION["AccessLevel"] == ACCESS_LEVEL_ADMINGROUP)
				$lockmessage = $this->lockingObj->GetLockInfo($this->tName, $this->keys, false, $this->id);
			else
				$lockmessage = $this->lockingObj->LockUser;
				
			$returnJSON = array();
			$returnJSON['success'] = false;
			$returnJSON['message'] = $lockmessage;
			$returnJSON['enableCtrls'] = false;
			$returnJSON['confirmTime'] = $this->lockingObj->ConfirmTime;
			echo printJSON($returnJSON);
			exit();
		}
	
		//	other modes
		$this->controlsDisabled = true;
		$this->lockingMessageStyle = "style='display:block;'";
		$this->lockingMessageText = $this->lockingObj->LockUser;
		
		if(IsAdmin() || $_SESSION["AccessLevel"] == ACCESS_LEVEL_ADMINGROUP)
		{
			$ribbonMessage = $this->lockingObj->GetLockInfo($this->tName, $this->keys, true, $this->id);
			if($ribbonMessage != "")
				$this->lockingMessageText = $ribbonMessage;
		}

		$this->body["begin"].= '<div class="rnr-locking" style="' .$this->lockingMessageStyle. '">' . 
			$this->lockingMessageText . 
			'</div>';
		return true;
	}
	
	/**
	 * Print JSON containing a saved record data on ajax-like request
	 */
	protected function reportInlineSaveStatus()
	{			
		echo printJSON( $this->getSaveStatusJSON() );
		exit();
	}
	
	/**
	 * Get an array containing the record save status
	 * @return Array
	 */
	protected function getSaveStatusJSON()
	{	
		$returnJSON = array();

		if( $this->action != "edited" || $this->mode == EDIT_SIMPLE )
			return $returnJSON;
		
		$returnJSON['success'] = $this->updatedSuccessfully;
		$returnJSON['message'] = $this->message;
		$returnJSON['lockMessage'] = $this->lockingMessageText;

		if( !$this->isCaptchaOk )
			$returnJSON['captcha'] = false;
			
		if( !$this->updatedSuccessfully )
			return $returnJSON;

		//	successful update. Return new keys and field values
		$data = $this->getCurrentRecordInternal();
		if( !$data )
			$data = $this->newRecordData;

		//	details tables keys
		$returnJSON['detKeys'] = array();
		foreach( $this->pSet->getDetailTablesArr() as $dt )
		{
			$dkeys = array();
			foreach( $dt["masterKeys"] as $idx => $mk )
			{
				$dkeys[ "masterkey".($idx + 1) ] = $data[ $mk ];		
			}
			$returnJSON['detKeys'][ $dt['dDataSourceTable'] ] = $dkeys;
		}
		
		//	prepare field values
		//	keys
		$keyParams = array();
		foreach( $this->keyFields as $i => $k )
		{
			$keyParams[] = "key" . ($i + 1) . "=" . rawurldecode( $this->keys[ $k ] );
		}
		$keylink = "&" . implode("&", $keyParams);
		
		//	values		
		$values = array();
		$rawValues = array();
		$fields = $this->pSet->getFieldsList();
		foreach( $fields as $f )
		{
			$value = $this->showDBValue( $f, $data, $keylink );
			$values[ $f ] = $value;
			if( IsBinaryType( $this->pSet->getFieldType( $f ) ) )
				$rawValues[ $f ] = "";
			else
				$rawValues[ $f ] = substr($data[ $f ], 0, 100);
		
		}
		
		$returnJSON['keys'] = $this->jsKeys;
		$returnJSON['keyFields'] = $this->keyFields;
		$returnJSON['vals'] = $values;
		$returnJSON['fields'] = $fields;
		$returnJSON['rawVals'] = $rawValues;
		$returnJSON['hrefs'] = $this->buildDetailGridLinks( $returnJSON['detKeys'] );

		//	the record might become non-editable after updating
		if( !$this->IsRecordEditable( false ) )
			$returnJSON['nonEditable'] = true;

		return $returnJSON;	
	}
	
	/**
	 *	POST-REDIRECT-GET 
	 *	Redirect after saving the data to avoid saving again on refresh.
	 */
	protected function prgRedirect()
	{
		if( !$this->updatedSuccessfully || $this->mode != EDIT_SIMPLE || !no_output_done() )
			return false;

		$_SESSION["message_edit"] = $this->message . "";
		$keyParams = array();
		foreach( $this->keyFields as $i => $k )
		{
			$keyParams[] = "editid" . ($i + 1) . "=" . rawurldecode( $this->keys[ $k ] );
		}
		HeaderRedirect( $this->pSet->getShortTableName(), $this->getPageType(), implode("&", $keyParams) );
		exit();
		return true;
	}

	/**
	 *	POST-REDIRECT-GET 
	 *	Read the saved message on the GET step.
	 */
	protected function prgReadMessage()
	{
		if( $this->mode != EDIT_SIMPLE || ! isset($_SESSION["message_edit"]) )
			return;
		$this->setMessage( $_SESSION["message_edit"] );
		$this->messageType = MESSAGE_INFO;
		unset($_SESSION["message_edit"]);
	}
	
	/**
	 * @return Array
	 */ 
	public function getCurrentRecord()
	{
		$data = $this->getCurrentRecordInternal();
		$newData = array();
		
		foreach($data as $fName => $val)
		{
			$editFormat = $this->pSet->getEditFormat($fName);
			if( $editFormat == EDIT_FORMAT_DATABASE_FILE || $editFormat==EDIT_FORMAT_DATABASE_IMAGE )
			{
				if( $data[ $fName ] )
					$newData[ $fName ] = true;
				else
					$newData[ $fName ] = false;
			}	
		}
		
		foreach($newData as $fName => $val) // .net compatibility issue
		{
			$data[ $fName ] = $val;
		}
		
		return $data;
	}
	
	/**
	 * @param Boolean useOldKeys
	 * @return String
	 */
	public function getWhereClause( $useOldKeys )
	{
		$strWhereClause = "";
		
		if( $useOldKeys )
			$strWhereClause = KeyWhere( $this->oldKeys );
		else if( $this->checkKeysSet() )
			$strWhereClause = KeyWhere( $this->keys );
		elseif( $this->mode == EDIT_DASHBOARD )
		{
			$whereComponents = $this->getWhereComponents();
			$strWhereClause = $whereComponents["searchWhere"];
		}			
		else
		{
			$strWhereClause = $_SESSION[ $this->sessionPrefix."_where" ];
		}
		
		if( $this->pSet->getAdvancedSecurityType() != ADVSECURITY_ALL )
		{
			// select only owned records
			$strWhereClause = whereAdd($strWhereClause, SecuritySQL("Edit", $this->tName));
		}
		return $strWhereClause;
	}
	
	/**
	 * Read current values from the database
	 * @return Array 		The current record data
	 */
	public function getCurrentRecordInternal()
	{		
		if( !is_null($this->cachedRecord) )
			return $this->cachedRecord;
		
		$keysSet = $this->checkKeysSet();	
		
		$orderClause = "";
		$havingClause = "";	
		if( !$keysSet )
		{
			$orderClause = $this->getOrderByClause();
			if( $this->mode == EDIT_DASHBOARD )
			{
				$whereComponents = $this->getWhereComponents();
				$havingClause = $whereComponents["searchHaving"];
			}			
		}
		
		$strWhereClause = $this->getWhereClause( false );
		$strSQL = $this->gQuery->gSQLWhere( $strWhereClause, $havingClause );
		
		if( !$keysSet )
			$strSQL = applyDBrecordLimit($strSQL.$orderClause, 1, $this->connection->dbType);
		
		$strSQLbak = $strSQL;
		$strWhereClauseBak = $strWhereClause;
		//	Before Query event
		if( $this->eventsObject->exists("BeforeQueryEdit") )
			$this->eventsObject->BeforeQueryEdit($strSQL, $strWhereClause, $this);
		
		if( $strSQLbak == $strSQL && $strWhereClauseBak != $strWhereClause )
		{
			$strSQL = $this->gQuery->gSQLWhere( $strWhereClause, $havingClause );
			if( !$keysSet )
				$strSQL = applyDBrecordLimit($strSQL.$orderClause, 1, $this->connection->dbType);
		}
		
		LogInfo($strSQL);
		
		$fetchedArray = $this->connection->query( $strSQL )->fetchAssoc();
		$this->cachedRecord = $this->cipherer->DecryptFetchedArray( $fetchedArray );

		if( !$keysSet )
		{
			$this->keys = $this->getKeysFromData( $this->cachedRecord );
			$this->setKeysForJs();
		}
		
		if( !$this->cachedRecord && $this->mode == EDIT_SIMPLE )
			return $this->cachedRecord;
			
		foreach($this->getPageFields() as $fName)
		{
			if( @$_POST["a"]!= "edited" && $this->pSet->getAutoUpdateValue($fName) !== "" )
				$this->cachedRecord[ $fName ] = $this->pSet->getAutoUpdateValue($fName);	
		}
		
		if($this->readEditValues)
		{
			foreach($this->getPageFields() as $fName)
			{
				$editFormat = $this->pSet->getEditFormat($fName);
				if( $editFormat == EDIT_FORMAT_DATABASE_FILE && $editFormat != EDIT_FORMAT_DATABASE_IMAGE && $editFormat != EDIT_FORMAT_FILE && !$this->pSet->isReadonly($fName) )
					$this->cachedRecord[ $fName ] = $this->newRecordData[ $fName ];
			}	
		}
		
		if( $this->eventsObject->exists("ProcessValuesEdit") )
			$this->eventsObject->ProcessValuesEdit($this->cachedRecord, $this);
		
		return $this->cachedRecord;
	}

	/**
	 * Check if the keys values were set through GET/POST 'editid' params
	 * or by using the setKeys method directly
	 * @return Boolean
	 */
	protected function checkKeysSet()
	{
		foreach($this->keys as $kValue)
		{
			if( strlen($kValue) )
				return true;
		}
		return false;
	}
	
	/**
	 * Prepare edit controls
	 */
	public function prepareEditControls( )
	{
		global $locale_info;
		if( $this->mode == EDIT_INLINE )
		{
			$fields = $this->editFields;
			$this->removeHiddenColumnsFromInlineFields( $fields, $this->screenWidth, $this->screenHeight, $this->orientation );
			$this->editFields = $fields;
		}

		//	prepare values
		$data = $this->getCurrentRecordInternal();
		if( $this->readEditValues )
		{
			foreach($this->editFields as $f)
			{
				if( !isset( $this->newRecordData[ $f ] ) )
					continue;
				$editFormat = $this->pSet->getEditFormat( $f );
				if( $editFormat != EDIT_FORMAT_DATABASE_FILE && 
					$editFormat != EDIT_FORMAT_DATABASE_IMAGE &&
					$editFormat != EDIT_FORMAT_READONLY )

					$data[ $f ] = $this->newRecordData[ $f ];
			}
		}
		
		$control = array();

		foreach($this->editFields as $fName)
		{
			$gfName = GoodFieldName($fName);
			$isDetKeyField = in_array($fName, $this->detailKeysByM);

			$control[ $gfName ] = array();
			
			$controls = array();
			$controls["controls"] = array();
			$controls["controls"]['ctrlInd'] = 0;
			$controls["controls"]['id'] = $this->id;
			$controls["controls"]['fieldName'] = $fName;			

			$parameters = array();
			$parameters["id"] = $this->id;
			$parameters["ptype"] = PAGE_EDIT;
			$parameters["field"] = $fName;
			$parameters["pageObj"] = $this;
			$parameters["value"] = @$data[ $fName ];
			
			if( !$isDetKeyField )
			{
				if( IsFloatType( $this->pSet->getFieldType($fName) ) && !is_null( @$data[ $fName ] ) )
				{
					if( $this->pSet->getHTML5InputType( $fName ) == "number" )
					{
						//	no thousand delimiters, only dot as decimal delimiter
						$parameters["value"] = formatNumberForHTML5( @$data[ $fName ] );
					}
					else
						$parameters["value"] = str_replace(".", $locale_info["LOCALE_SDECIMAL"], @$data[ $fName ]);
				}
				
				$parameters["validate"] = $this->pSet->getValidation($fName);

				$additionalCtrlParams = array();
				$additionalCtrlParams["disabled"] = $this->controlsDisabled;
				$parameters["additionalCtrlParams"] = $additionalCtrlParams;
			}
		
			$controlMode = $this->mode == EDIT_INLINE ? "inline_edit" : "edit";
			$parameters["mode"] = $controlMode;			
			$controls["controls"]['mode'] = $controlMode;

			if( $this->pSet->isUseRTE($fName) && $this->pSet->isAutoUpdatable($fName) )
			{
				$_SESSION[ $this->sessionPrefix."_".$fName."_rte" ] = GetAutoUpdateValue($fName, PAGE_EDIT);
				$control[ $gfName ]["params"]["mode"] = "add";
			}
			
			if( $isDetKeyField )
			{
				$controls["controls"]['value'] = @$data[ $fName ];
				
				$parameters["extraParams"] = array();
				$parameters["extraParams"]["getDetKeyReadOnlyCtrl"] = true;
			
				// to the ReadOnly control show the detail key cotnrol's value	
				$this->readOnlyFields[ $fName ] = $this->showDBValue($fName, $data);				
			}

			AssignFunction($control[ $gfName ], "xt_buildeditcontrol", $parameters);
			$this->xt->assignbyref($gfName."_editcontrol", $control[ $gfName ]);
			
			// category control field
			$strCategoryControl = $this->getMainLookupFieldNameForDependant($fName);
			
			if( strlen($strCategoryControl) && in_array($strCategoryControl, $this->editFields) )
				$vals = array($fName => @$data[ $fName ], $strCategoryControl => @$data[ $strCategoryControl ]);
			else
				$vals = array($fName => @$data[ $fName ]);
				
			$preload = $this->fillPreload($fName, $vals);
			if( $preload !== false )
				$controls["controls"]['preloadData'] = $preload;
			
			$this->fillControlsMap( $controls );
			$this->fillFieldToolTips( $fName );
			
			// fill special settings for timepicker
			if( $this->pSet->getEditFormat($fName) == 'Time' )	
				$this->fillTimePickSettings($fName, $data[ $fName ]);
			
			if( $this->pSet->getViewFormat($fName) == FORMAT_MAP )	
				$this->googleMapCfg['isUseGoogleMap'] = true;
		}		
	}
	
	/**
	 * Assign edit fields' blocks and labels variables
	 */
	public function assignEditFieldsBlocksAndLabels()
	{
		$editFields = $this->pSet->getEditFields();
		
		foreach($editFields as $fName)
		{
			$gfName = GoodFieldName($fName);
			
			if( !$this->isAppearOnTabs($fName) )
				$this->xt->assign($gfName."_fieldblock", true);
			else
				$this->xt->assign($gfName."_tabfieldblock", true);
				
			$this->xt->assign($gfName."_label", true);
			if( $this->is508 )
				$this->xt->assign_section($gfName."_label","<label for=\"" . $this->getInputElementId( $fName ) . "\">","</label>");
		}
	}
	
	public static function readEditModeFromRequest()
	{
		if(postvalue("editType") == "inline")
			return EDIT_INLINE;
		elseif(postvalue("editType") == EDIT_POPUP)
			return EDIT_POPUP;
		elseif(postvalue("mode") == "dashrecord")
			return EDIT_DASHBOARD;
		else
			return EDIT_SIMPLE;
	
	}
	
	public static function processEditPageSecurity( $table )
	{
		//	user has necessary permissions
		if( Security::checkPagePermissions( $table, "E" ) )
			return true;
			
		// display entered data. Give the user chance to relogin. Do nothing for now.
		if( postvalue("a") == "edited" )
			return true;
		
		//	page can not be displayed. Redirect or return error
		
		$pageMode = EditPage::readEditModeFromRequest();
		
		//	return error if the page is requested by AJAX
		if( $pageMode != EDIT_SIMPLE )
		{
			Security::sendPermissionError();
			return false;
		}
		
		// The user is logged in but lacks necessary permissions
		// redirect to List page or Menu.
		if( isLogged() && !isLoggedAsGuest() )
		{
			Security::redirectToList( $table );
			return false;
		}

		//	Not logged in
		// 	redirect to Login
		//	Save current URL in session
		$keyParams = array();
		$i = 1;
		while( postvalue("editid".$i) )
		{
			$keyParams[] = "editid".$i."=".rawurlencode( postvalue("editid".$i) );
			$i++;
		}
		$_SESSION["MyURL"] = $_SERVER["SCRIPT_NAME"]."?".implode("&", $keyParams);
		redirectToLogin();
		return false;
	}
	
	/**
	 * Handle broken POST request.
	 */
	public static function handleBrokenRequest()
	{
		if( sizeof($_POST) != 0 || !postvalue('submit') ) 
			return;
		if( !postvalue("editid1") )
		{
			$returnJSON = array();
			$returnJSON['success'] = false;
			$returnJSON['message'] = "Error occurred";
			$returnJSON['fatalError'] = true;
			echo printJSON($returnJSON);
			exit();
		} 
		else
		{
			if( postvalue('fly') )
			{
				echo -1;
				exit();
			}
			else 
				$_SESSION["message_edit"] = "<< "."Error occurred"." >>";
		}
	}
	
	protected function buildNewRecordData()
	{
		// define temporary arrays. These are required for ASP conversion
		$evalues = array();
		$efilename_values = array();
		$blobfields = array();	
		$keys = $this->keys;
		
		foreach($this->editFields as $f)
		{
			$control = $this->getControl( $f, $this->id);
			$control->readWebValue($evalues, $blobfields, NULL, NULL, $efilename_values);
			if( isset($keys[ $f ]) )
			{
				//	ASP conversion requires this separate "if".
				if( $keys[ $f ] != $control->getWebValue() )
				{
					$keys[ $f ] = $control->getWebValue();
					$this->keysChanged = true;
				}
			}
		}
		if( $this->keysChanged )
			$this->setKeys( $keys );
			
		foreach($efilename_values as $ekey => $value)
		{
			$evalues[ $ekey ] = $value;
		}
		$this->newRecordData = $evalues;
		$this->newRecordBlobFields = $blobfields;
	
	}
	
	/**
	 * Process user data input and save it to the database table
	 */
	public function processDataInput()
	{
		//	get prepared for the data saving
		$this->oldKeys = $this->keys;

		$this->buildNewRecordData();

		if( !$this->recheckUserPermissions() )
		{
			//	prevent the page from reading database values
			$this->oldRecordData = $this->newRecordData;
			$this->cachedRecord = $this->newRecordData;
			return false;
		}

		if( !$this->checkCaptcha() )
			return false;
		

		if( !$this->isRecordEditable( true ) )
			return $this->SecurityRedirect();

		if( !$this->callBeforeEditEvent() )
			return false;
		
		$this->addGeoValues();

		if( !$this->checkDeniedDuplicatedValues() )
			return false;

		if( !$this->confirmLockingBeforeSaving() )
			return false;

		//	do save the record
		if( $this->callCustomEditEvent() )
		{
			$this->updatedSuccessfully = DoUpdateRecord( $this );
		}

		$this->unlockNewRecord();

		if( !$this->updatedSuccessfully )
		{
			$this->setKeys( $this->oldKeys );
			return false;
		}
		//	after save steps
		
		$this->ProcessFiles();

		$this->setMessage( "&lt;&lt;&lt; "."Record updated". "&gt;&gt;&gt;" );
		$this->messageType = MESSAGE_INFO;
		
		$this->callAfterSuccessfulSave();
		
		$this->unlockOldRecord();
		
		$this->mergeNewRecordData();

		$this->auditLogEdit();

		$this->callAfterEditEvent();
		
		$this->setKeys( $this->keys );

		return true;
	}
	
	/**
	 * Check if updated data contains duplicated values
	 * @return Boolean
	 */
	protected function checkDeniedDuplicatedValues()
	{
		$oldData = $this->getOldRecordData();
			
		foreach($this->newRecordData as $f => $value)
		{
			if( $this->pSet->allowDuplicateValues($f) ) 
				continue;
			
			if( $oldData[ $f ] == $value )
				continue;
				
			if( !$this->hasDuplicateValue($f, $value) ) 
				continue;
			
			$this->setMessage( $this->pSet->label( $f ) . " " . "Field should not contain a duplicate value" );
			return false;		
		}
		return true;
	}
	
	protected function auditLogEdit()
	{
		if( $this->auditObj )
			$this->auditObj->LogEdit($this->tName, $this->newRecordData, $this->getOldRecordData(), $this->keys);
	}
	
	/**
	 *	Add missing values from oldRecordData to newRecordData
	 *	This is required for the Audit and the AfterEdit event
	 */
	protected function mergeNewRecordData()
	{
		if( !$this->auditObj && !$this->eventsObject->exists("AfterEdit") )
			return;
			
		foreach($this->getOldRecordData() as $f => $v)
		{
			if( !isset( $this->newRecordData[ $f ] ) )
				$this->newRecordData[ $f ] = $v;
		}	
	}
	
	/**
	 *	Call After Record Updated event
	 */
	protected function callAfterEditEvent()
	{
		if( !$this->eventsObject->exists("AfterEdit") )
			return;
		
		$this->eventsObject->AfterEdit( $this->newRecordData, 
			$this->getWhereClause( false ), 
			$this->getOldRecordData(), 
			$this->keys, 
			$this->mode == EDIT_INLINE, 
			$this );
	}
	
	/**
	 *	Unlock the record not existing anymore after successful updating.
	 */
	protected function unlockOldRecord() 
	{
		if( $this->lockingObj && $this->keysChanged )
			$this->lockingObj->UnlockRecord($this->tName, $this->oldKeys , "");
	}
	
	/**
	 *	Unlock the record to be created after unsuccessful updating.
	 */
	protected function unlockNewRecord() 
	{
		if( $this->lockingObj )
			$this->lockingObj->UnlockRecord($this->tName, $this->keys , "");
	}
	
	/**
	 *	Call each control's afterSuccessfulSave method
	 */
	protected function callAfterSuccessfulSave()
	{
		foreach($this->editFields as $f)
		{
			$this->getControl($f, $this->id)->afterSuccessfulSave();
		}
	}
	
	/**
	 *	Call Before Record Updated event
	 */
	protected function callBeforeEditEvent()
	{
		if( !$this->eventsObject->exists("BeforeEdit") )
			return true;
		$usermessage = "";
		$ret = $this->eventsObject->BeforeEdit( $this->newRecordData, 
			$this->getWhereClause( true ), 
			$this->getOldRecordData(), 
			$this->oldKeys, 
			$usermessage, 
			$this->mode == EDIT_INLINE, 
			$this );
		
		//	this is required for the ASP conversion
		if( !$ret )
			$this->setMessage( $usermessage );
		
		return $ret;
	}
	
	/**
	 *	Call Custom Edit event
	 */
	protected function callCustomEditEvent()
	{
		if( !$this->eventsObject->exists("CustomEdit") )
			return true;
		$usermessage = "";
		$ret = $this->eventsObject->CustomEdit( $this->newRecordData, 
			$this->getWhereClause( true ), 
			$this->getOldRecordData(), 
			$this->oldKeys, 
			$usermessage, 
			$this->mode == EDIT_INLINE, 
			$this );
		
		//	this is required for the ASP conversion
		if( !$ret )
		{
			$this->setMessage( $usermessage );
			$this->updatedSuccessfully = ( 0 == strlen( $this->message ) );
		}
		return $ret;
	}
	
	/**
	 * Check whether the user have passed CAPTCHA test
	 * @return Boolean
	 */
	protected function checkCaptcha()
	{
		if( $this->mode == EDIT_INLINE || !$this->captchaExists() )
			return true;
		
		if( !$this->isCaptchaOk )
			return false;
		
		$_SESSION[ $this->tName."_count_captcha" ] = $_SESSION[ $this->tName."_count_captcha" ] + 1;
		return true;
	}
	
	protected function addGeoValues()
	{
		if( $this->isTableGeoUpdatable() ) 			
			$this->setUpdatedLatLng( $this->getNewRecordData(), $this->getOldRecordData() );		
	}
	
	protected function recheckUserPermissions()
	{
		if( CheckTablePermissions($this->tName, "E") )
			return true;
		if( isLoggedAsGuest() || !isLogged() ) 
		{
			$this->setMessage( "Your session has expired." . 
				"<a href='#' id='loginButtonContinue" . $this->id . "'>" . 
				"Login" . "</a>" . 
				" to save data." );
		}
		else
		{
			$this->setMessage( 'You have no permissions to complete this action.' );
		}
		
		return false;
	}
	
	/**
	 *	Do locking stuff before saving the data.
	 *	Returns false if locking was unsuccessful and the saving action is impossible
	 */
	protected function confirmLockingBeforeSaving()
	{
		if( !$this->lockingObj )
			return true;
		$lockmessage = "";
		if( $this->keysChanged )
		{
			//	confirm and acquire locks on both old and new sets of keys
			$lockConfirmed = $this->lockingObj->ConfirmLock($this->tName, $this->oldKeys, $lockmessage);
			if( $lockConfirmed )
				$lockConfirmed = $this->lockingObj->LockRecord($this->tName, $this->keys);
		}
		else
		{
			//	confirm lock on the edited record
			$lockConfirmed = $this->lockingObj->ConfirmLock($this->tName, $this->oldKeys, $lockmessage);
		}
		
		if( !$lockConfirmed )
		{
			$this->lockingMessageStyle = "display:block";
			if( $this->mode == EDIT_INLINE )
			{
				if( IsAdmin() || $_SESSION["AccessLevel"] == ACCESS_LEVEL_ADMINGROUP )
					$lockmessage = $this->lockingObj->GetLockInfo($this->tName, $this->oldKeys, false, $this->id);
				
				$returnJSON['success'] = false;
				$returnJSON['message'] = $lockmessage;
				$returnJSON['enableCtrls'] = false;
				$returnJSON['confirmTime'] = $this->lockingObj->ConfirmTime;
				echo printJSON($returnJSON);
				exit();
			}
			else
			{
				if( IsAdmin() || $_SESSION["AccessLevel"] == ACCESS_LEVEL_ADMINGROUP )
					$this->lockingMessageText = $this->lockingObj->GetLockInfo($this->tName, $this->oldKeys, true, $id);
				else
					$this->lockingMessageText = $lockmessage;
			}
			$this->readEditValues = true;
			return false;
		}
		return true;
	}

	/**
	 *	Redirect the user from the page due to security reasons.
	 */
	protected function SecurityRedirect()
	{
		if($this->mode == EDIT_INLINE)
		{
			echo printJSON(array("success" => false, "message" => "The record is not editable"));
			exit();
		}
		Security::redirectToList( $this->tName );
		return;
	}
	
	/**
	 * @param Boolean useOldData
	 * @return Boolean
	 */
	protected function isRecordEditable( $useOldData )
	{
		global $globalEvents;
		if( $globalEvents->exists("IsRecordEditable", $this->tName) )
		{
			if( !$globalEvents->IsRecordEditable($useOldData ? $this->getOldRecordData() : $this->getCurrentRecordInternal(), true, $this->tName) )
				return false;
		}
		
		return true;
	}
	
	/**
	 * @return Array
	 */
	public function getOldRecordData()
	{
		if( $this->oldRecordData === null )
		{
			$strSQL = $this->gQuery->gSQLWhere( $this->getWhereClause( true ) );
			LogInfo($strSQL);
			$fetchedArray = $this->connection->query( $strSQL )->fetchAssoc();
			$this->oldRecordData = $this->cipherer->DecryptFetchedArray( $fetchedArray );
		}
		
		return $this->oldRecordData;
	}

	public function getBlobFields()
	{
		return $this->newRecordBlobFields;
	}
	
	public function & getNewRecordData()
	{
		return $this->newRecordData;
	}
	
	public function setMessage( $message )
	{
		$this->message = $message;
	}
	
	public function setDatabaseError( $message )
	{
		if( $this->mode != EDIT_INLINE )
		{
			$this->message = "&lt;&lt;&lt; "."Record was NOT edited"." &gt;&gt;&gt;<br><br>".$message;
		}
		else
			$this->message = "Record was NOT edited".". ".$message;
			
		$this->messageType = MESSAGE_ERROR;
	}
	
	/**
	 * Get the current record data to build correct edit controls (xt_buildeditcontrol)
	 * @return Array
	 */
	public function getFieldControlsData()
	{
		return $this->getCurrentRecordInternal();
	}	
}
?>