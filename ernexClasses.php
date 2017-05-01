
<?php

#################### ernexGlobals ###########################################


class ernexGlobals{

var $Globals=array(
                  MONERIS_PROTOCOL => 'https',

                  //FOR US
                  //MONERIS_HOST => 'esplusqa.moneris.com',

                  MONERIS_HOST => 'esqa.moneris.com',

                  MONERIS_PORT =>'443',

                  //FOR US
                  //MONERIS_FILE => '/gateway_us/servlet/MpgRequest',

                  MONERIS_FILE => '/gateway2/servlet/MpgRequest',

                  API_VERSION  =>'PHP - 3.1.0 - ERNEX',

                  CLIENT_TIMEOUT => '60'
                 );

 function ernexGlobals()
 {
  // default
 }

 function getGlobals()
 {
  return($this->Globals);
 }

}//end class ernexGlobals



###################### ernexHttpsPost #########################################

class ernexHttpsPost{

 var $api_token;
 var $store_id;
 var $ernexRequest;
 var $ernexResponse;

 function ernexHttpsPost($storeid,$apitoken,$ernexRequestOBJ)
 {

  $this->store_id=$storeid;
  $this->api_token= $apitoken;
  $this->ernexRequest=$ernexRequestOBJ;
  $dataToSend=$this->toXML();

  //echo "DATA TO SEND: $dataToSend\n";
  //do post

  $g=new ernexGlobals();
  $gArray=$g->getGlobals();

  $url=$gArray[MONERIS_PROTOCOL]."://".
       $gArray[MONERIS_HOST].":".
       $gArray[MONERIS_PORT].
       $gArray[MONERIS_FILE];

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
  curl_setopt ($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$dataToSend);
  curl_setopt($ch,CURLOPT_TIMEOUT,$gArray[CLIENT_TIMEOUT]);
  curl_setopt($ch,CURLOPT_USERAGENT,$gArray[API_VERSION]);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

  $response=curl_exec ($ch);

  //echo "RESPONSE: $response\n";

  curl_close ($ch);

  if(!$response)
  {

     $response="<?xml version=\"1.0\"?><response><receipt>".
          "<ReceiptId>Global Error Receipt</ReceiptId>".
          "<ResponseCode>null</ResponseCode>".
          "<AuthCode>null</AuthCode><TransTime>null</TransTime>".
          "<TransDate>null</TransDate><TransType>null</TransType><Complete>false</Complete>".
          "<Message>null</Message><TransAmount>null</TransAmount>".
          "<CardType>null</CardType>".
          "<TransID>null</TransID><TimedOut>null</TimedOut>".
          "</receipt></response>";
  }

    //print "Got a xml response of: \n$response\n";
  	$this->ernexResponse=new ernexResponse($response);

 }



 function getErnexResponse()
 {
  return $this->ernexResponse;

 }

 function toXML( )
 {

  $req=$this->ernexRequest ;
  $reqXMLString=$req->toXML();

  $xmlString .="<?xml version=\"1.0\"?>".
               "<request>".
               "<store_id>$this->store_id</store_id>".
               "<api_token>$this->api_token</api_token>".
                $reqXMLString.
                "</request>";

  return ($xmlString);

 }

}//end class ernexHttpsPost



############# ernexResponse #####################################################


class ernexResponse{

 var $responseData;

 var $p; //parser

 var $currentTag;
 var $isBatchTotals;
 var $isInitData;
 var $isText;
 var $CardCode;
 var $RecordType;
 var $term_id;
 var $ecrs = array();
 var $ecrHash = array();
 var $Cards = array();
 var $CardsHash = array();
 var $Text = array();
 var $TextHash = array();
 var $termIds = array();
 var $termIdHash = array();

 function ernexResponse($xmlString)
 {

  $this->p = xml_parser_create();
  xml_parser_set_option($this->p,XML_OPTION_CASE_FOLDING,0);
  xml_parser_set_option($this->p,XML_OPTION_TARGET_ENCODING,"UTF-8");
  xml_set_object($this->p,&$this);
  xml_set_element_handler($this->p,"startHandler","endHandler");
  xml_set_character_data_handler($this->p,"characterHandler");
  xml_parse($this->p,$xmlString);
  xml_parser_free($this->p);

 }//end of constructor


 function getErnexResponseData(){
 	 return($this->responseData);
 }

 //-----------------  Receipt Variables  ---------------------------------------------------------//

 function getReceiptId(){
  	return ($this->responseData['ReceiptId']);
 }

 function getResponseCode(){
 	return ($this->responseData['ResponseCode']);
 }

 function getCardCode(){
     return ($this->CardCode);
 }

 function getHostReferenceNum(){
  	return ($this->responseData['HostReferenceNum']);
 }

 function getTransTime(){
 	return ($this->responseData['TransTime']);
 }

 function getTransDate(){
 	return ($this->responseData['TransDate']);
 }

 function getActivationCharge(){
     return ($this->responseData['ActivationCharge']);
 }

 function getTransType(){
 	return ($this->responseData['TransType']);
 }

 function getComplete(){
 	return ($this->responseData['Complete']);
 }

 function getMessage(){
 	return ($this->responseData['Message']);
 }

 function getTransCardCode(){
 	return ($this->responseData['TransCardCode']);
 }

 function getTransCardType(){
 	return ($this->responseData['TransCardType']);
 }

 function getTxnNumber(){
 	return ($this->responseData['TransID']);
 }

 function getTimedOut(){
 	return ($this->responseData['TimedOut']);
 }

 function getHostTotals(){
        return ($this->responseData['HostTotals']);
 }

 function getInitData(){
        return ($this->responseData['InitData']);
 }

 function getDisplayText(){
 	return ($this->responseData['DisplayText']);
 }

 function getReceiptText(){
  	return (base64_decode($this->responseData['ReceiptText']));
 }

 function getCardHolderName(){
        return ($this->responseData['CardHolderName']);
 }

 function getVoucherType(){
  	return ($this->responseData['VoucherType']);
 }

 function getVoucherText(){
   	return (base64_decode($this->responseData['VoucherText']));
 }

 function getCardStatus(){
 	return ($this->responseData['CardStatus']);
 }

 function getRemainingBalance(){
     return ($this->responseData['RemainingBalance']);
 }

 function getInitialAmount(){
 	return ($this->responseData['InitialAmount']);
 }

 function getInitialBalance(){
  	return ($this->responseData['InitialBalance']);
 }

 function getBatchNo(){
 	return ($this->responseData['BatchNo']);
 }

 function getCurrentBalance(){
  	return ($this->responseData['CurrentBalance']);
 }

 function getLifetimeBalance(){
   	return ($this->responseData['LifetimeBalance']);
 }

 function getBenefit(){
   	return ($this->responseData['Benefit']);
 }

 function getLanguage(){
        return ($this->responseData['Language']);
 }

 function getErrorCode(){
        return ($this->responseData['ErrorCode']);
 }

 function getErrorMessage(){
        return ($this->responseData['ErrorMessage']);
 }

 //-----------------  Card Variables  ---------------------------------------------------------//

 function getCardCodes(){
 	return ($this->Cards);
 }

 function getCardString($CardCode)
 {
 	if ($this->CardsHash[$CardCode]['CardType'] == '0')
 	{
 		return 'loyalty';
 	}
 	elseif ($this->CardsHash[$CardCode]['CardType'] == '1')
 	{
 		return 'gift';
 	}
 }

 function getCardType($CardCode){
 	return ($this->CardsHash[$CardCode]['CardType']);
 }

 function getReserved2($CardCode){
  	return ($this->CardsHash[$CardCode]['Reserved2']);
 }

 function getCheckMod10($CardCode){
  	return ($this->CardsHash[$CardCode]['CheckMod10']);
 }

 function getCheckLanguage($CardCode){
  	return ($this->CardsHash[$CardCode]['CheckLanguage']);
 }

 function getAmountPrompt($CardCode){
  	return ($this->CardsHash[$CardCode]['AmountPrompt']);
 }

 function getBenefitPrompt($CardCode){
  	return ($this->CardsHash[$CardCode]['BenefitPrompt']);
 }

 function getCVCPrompt($CardCode){
  	return ($this->CardsHash[$CardCode]['CVCPrompt']);
 }

 function getProductPrompt($CardCode){
  	return ($this->CardsHash[$CardCode]['ProductPrompt']);
 }

 function getRedemptionType($CardCode){
  	return ($this->CardsHash[$CardCode]['RedemptionType']);
 }

 function getRedemptionAmount($CardCode){
     return ($this->CardsHash[$CardCode]['RedemptionAmount']);
 }

 function getInfoPrompt($CardCode){
  	return ($this->CardsHash[$CardCode]['InfoPrompt']);
 }

 function getInitialAmountPrompt($CardCode){
  	return ($this->CardsHash[$CardCode]['InitialAmountPrompt']);
 }

 function getReserved3($CardCode){
  	return ($this->CardsHash[$CardCode]['Reserved3']);
 }

 function getRefundAllowed($CardCode){
  	return ($this->CardsHash[$CardCode]['RefundAllowed']);
 }

 function getCardLengthMinimum($CardCode){
  	return ($this->CardsHash[$CardCode]['CardLengthMinimum']);
 }

 function getCardLengthMaximum($CardCode){
  	return ($this->CardsHash[$CardCode]['CardLengthMaximum']);
 }

 function getLowBIN1($CardCode){
  	return ($this->CardsHash[$CardCode]['LowBIN1']);
 }

 function getHighBIN1($CardCode){
  	return ($this->CardsHash[$CardCode]['HighBIN1']);
 }

 function getLowBIN2($CardCode){
  	return ($this->CardsHash[$CardCode]['LowBIN2']);
 }

 function getHighBIN2($CardCode){
  	return ($this->CardsHash[$CardCode]['HighBIN2']);
 }

 function getLowBIN3($CardCode){
  	return ($this->CardsHash[$CardCode]['LowBIN3']);
 }

 function getHighBIN3($CardCode){
  	return ($this->CardsHash[$CardCode]['HighBIN3']);
 }

 function getLowBIN4($CardCode){
  	return ($this->CardsHash[$CardCode]['LowBIN4']);
 }

 function getHighBIN4($CardCode){
  	return ($this->CardsHash[$CardCode]['HighBIN4']);
 }

 //-----------------  Text Variables  ---------------------------------------------------------//

 function getRecordTypes($CardCode){
  	return ($this->TextHash[$CardCode]);
 }

 function getCardDescription($CardCode,$RecordType){
  	return ($this->CardsHash[$CardCode]['Text'][$RecordType]['CardDescription']);
 }

 function getBenefitDescription($CardCode,$RecordType){
   	return ($this->CardsHash[$CardCode]['Text'][$RecordType]['BenefitDescription']);
 }

 function getBenefitPromptText($CardCode,$RecordType){
   	return ($this->CardsHash[$CardCode]['Text'][$RecordType]['BenefitPromptText']);
 }

 function getAmountPromptText($CardCode,$RecordType){
   	return ($this->CardsHash[$CardCode]['Text'][$RecordType]['AmountPromptText']);
 }

 function getInfoPromptText($CardCode,$RecordType){
   	return ($this->CardsHash[$CardCode]['Text'][$RecordType]['InfoPromptText']);
 }

 function getTotalsDescription($CardCode,$RecordType){
   	return ($this->CardsHash[$CardCode]['Text'][$RecordType]['TotalsDescription']);
 }

//-----------------  Batch Totals Variables  --------------------------------------------------//

function getTerminalIds(){
        return ($this->ecrs);
}

function getEcrCardCodes($term_id){
        return ($this->termIds[$term_id]);
}

function getPurchaseCount($term_id,$CardCode){
	return ($this->termIdHash[$term_id][$CardCode]['PurchaseCount']=="" ? 0:$this->termIdHash[$term_id][$CardCode]['PurchaseCount']);
}

function getPurchaseTotal($term_id,$CardCode){
	return ($this->termIdHash[$term_id][$CardCode]['PurchaseTotal']=="" ? 0:$this->termIdHash[$term_id][$CardCode]['PurchaseTotal']);
}

function getPurchaseBenefitTotal($term_id,$CardCode){
	return ($this->termIdHash[$term_id][$CardCode]['PurchaseBenefitTotal']=="" ? 0:$this->termIdHash[$term_id][$CardCode]['PurchaseBenefitTotal']);
}

function getRefundCount($term_id,$CardCode){
	return ($this->termIdHash[$term_id][$CardCode]['RefundCount']=="" ? 0:$this->termIdHash[$term_id][$CardCode]['RefundCount']);
}

function getRefundTotal($term_id,$CardCode){
	return ($this->termIdHash[$term_id][$CardCode]['RefundTotal']=="" ? 0:$this->termIdHash[$term_id][$CardCode]['RefundTotal']);
}

function getRefundBenefitTotal($term_id,$CardCode){
	return ($this->termIdHash[$term_id][$CardCode]['RefundBenefitTotal']=="" ? 0:$this->termIdHash[$term_id][$CardCode]['RefundBenefitTotal']);
}

function getRedemptionCount($term_id,$CardCode){
	return ($this->termIdHash[$term_id][$CardCode]['RedemptionCount']=="" ? 0:$this->termIdHash[$term_id][$CardCode]['RedemptionCount']);
}

function getRedemptionTotal($term_id,$CardCode){
	return ($this->termIdHash[$term_id][$CardCode]['RedemptionTotal']=="" ? 0:$this->termIdHash[$term_id][$CardCode]['RedemptionTotal']);
}

function getRedemptionBenefitTotal($term_id,$CardCode){
	return ($this->termIdHash[$term_id][$CardCode]['RedemptionBenefitTotal']=="" ? 0:$this->termIdHash[$term_id][$CardCode]['RedemptionBenefitTotal']);
}

function getActivationCount($term_id,$CardCode){
	return ($this->termIdHash[$term_id][$CardCode]['ActivationCount']=="" ? 0:$this->termIdHash[$term_id][$CardCode]['ActivationCount']);
}

function getActivationTotal($term_id,$CardCode){
	return ($this->termIdHash[$term_id][$CardCode]['ActivationTotal']=="" ? 0:$this->termIdHash[$term_id][$CardCode]['ActivationTotal']);
}

function getCorrectionCount($term_id,$CardCode){
	return ($this->termIdHash[$term_id][$CardCode]['CorrectionCount']=="" ? 0:$this->termIdHash[$term_id][$CardCode]['CorrectionCount']);
}

function getCorrectionTotal($term_id,$CardCode){
	return ($this->termIdHash[$term_id][$CardCode]['CorrectionTotal']=="" ? 0:$this->termIdHash[$term_id][$CardCode]['CorrectionTotal']);
}

//-----------------  Parser Handlers  ---------------------------------------------------------//

function characterHandler($parser,$data)
{
	if($this->isBatchTotals && $this->currentTag != "HostTotals")
	{
		switch($this->currentTag)
		{
			case "term_id"    :
			{
				$this->term_id=$data;
				array_push($this->ecrs,$this->term_id);
				$this->termIdHash[$data]=array();
				$this->termIds[$data]=array();
				break;
			}

			case "closed"     :
			{
				$ecrHash=$this->ecrHash;
				$ecrHash[$this->term_id]=$data;
				$this->ecrHash = $ecrHash;
				break;
			}

			case "CardCode"   :
			{
				$this->CardCode=$data;
                array_push($this->Cards,$this->CardCode);
				array_push($this->termIds[$this->term_id],$data);
				break;
			}

			default     :
			{
				$termIdHash=$this->termIdHash;
               	$termIdHash[$this->term_id][$this->CardCode][$this->currentTag] = $data;
               	$this->termIdHash = $termIdHash;
			}
		}
	}
	elseif($this->isInitData && $this->currentTag != "InitData")
 	{
   		if($this->currentTag == "CardCode")
   		{
   			$this->CardCode=$data;
        	array_push($this->Cards,$this->CardCode);
        	$this->TextHash[$data] = array();
   		}
   		else if($this->isText)
   		{
   			if($this->currentTag == "RecordType")
			{
				$this->RecordType=$data;
				$this->Text[$data]=$data;
		    	array_push($this->TextHash[$this->CardCode],$data);
   			}
   			else
   			{
   				$CardsHash=$this->CardsHash;
				$CardsHash[$this->CardCode]['Text'][$this->RecordType][$this->currentTag] = $data;
        		$this->CardsHash = $CardsHash;
        	}
   		}
   		else
   		{
   			$CardsHash=$this->CardsHash;
			$CardsHash[$this->CardCode][$this->currentTag] = $data;
        	$this->CardsHash = $CardsHash;
   		}
 	}
 	else
 	{
    	@$this->responseData[$this->currentTag] .=$data;
 	}
}//end characterHandler



function startHandler($parser,$name,$attrs)
{
	$this->currentTag=$name;

	if($this->currentTag == "HostTotals")
   	{
    	$this->isBatchTotals=1;
   	}
	if($this->currentTag == "InitData")
	{
       	$this->isInitData=1;
   	}
  	else if($this->currentTag == "Text")
  	{
		$this->isText=1;
	}
} //end startHandler

function endHandler($parser,$name)
{
 	$this->currentTag=$name;

	if($name == "HostTotals")
   	{
    	$this->isBatchTotals=0;
   	}
 	if($name == "InitData")
	{
    	$this->isInitData=0;
    }
   	if($name == "Text")
	{
		$this->isText=0;
   	}

	$this->currentTag="/dev/null";
} //end endHandler



}//end class ernexResponse


################## ernexRequest ###########################################################

class ernexRequest{

 var $txnTypes =array(
 		ernex_initialization => array('ecr_number'),
        ernex_opentotals => array('ecr_number'),
        ernex_batchclose => array('ecr_number'),
        ernex_card_data_inquiry => array('track2','pan','expdate'),
        ernex_gift_activation => array('order_id','cust_id','initial_amount','track2','pan','expdate','cvd_value','language_code','info'),
        ernex_gift_purchase => array('order_id','cust_id','total_amount','track2','pan','expdate','cvd_value','language_code','info','payment_type'),
        ernex_gift_refund => array('order_id','txn_number','cvd_value','info'),
		ernex_gift_ind_refund => array('order_id','cust_id','total_amount','track2','pan','expdate','cvd_value','language_code','info','reference_number'),
        ernex_gift_void => array('order_id','txn_number','cvd_value'),
        ernex_gift_card_inquiry => array('track2','pan','expdate','cvd_value','language_code'),
        ernex_gift_deactivation => array('order_id','cust_id','track2','pan','expdate','cvd_value','language_code','info'),
        ernex_loyalty_purchase => array('order_id','cust_id','total_amount','track2','pan','expdate','cvd_value','language_code','info','benefit_amount','payment_type'),
		ernex_loyalty_preauth => array('order_id','cust_id','total_amount','track2','pan','expdate','cvd_value','language_code','info','benefit_amount','payment_type'),
		ernex_loyalty_completion => array('order_id','txn_number','total_amount','benefit_amount'),
		ernex_loyalty_redemption => array('order_id','cust_id','total_amount','track2','pan','expdate','cvd_value','language_code','info','benefit'),
		ernex_loyalty_refund => array('order_id','txn_number','total_amount','benefit_amount'),
		ernex_loyalty_activation => array('order_id','cust_id','initial_amount','track2','pan','expdate','cvd_value','language_code','info'),
		ernex_loyalty_deactivation => array('order_id','cust_id','track2','pan','expdate','cvd_value','language_code','info'),
		ernex_loyalty_void => array('order_id','txn_number','cvd_value'),
		ernex_loyalty_ind_refund => array('order_id','cust_id','total_amount','benefit_amount','track2','pan','expdate','cvd_value','language_code','info','reference_number'),
		group => array('order_id','txn_number','group_ref_num','group_type')
     );

var $txnArray;

function ernexRequest($txn){

 if(is_array($txn))
 {
    $this->txnArray = $txn;
 }
 else
 {
    $temp[0]=$txn;
    $this->txnArray=$temp;
 }
}

function toXML(){

 $tmpTxnArray=$this->txnArray;

 $txnArrayLen=count($tmpTxnArray); //total number of transactions
 for($x=0;$x < $txnArrayLen;$x++)
 {
    $txnObj=$tmpTxnArray[$x];
    $txn=$txnObj->getTransaction();

    $txnType=array_shift($txn);
    $tmpTxnTypes=$this->txnTypes;
    $txnTypeArray=$tmpTxnTypes[$txnType];
    $txnTypeArrayLen=count($txnTypeArray); //length of a specific txn type

    $txnXMLString="";
    for($i=0;$i < $txnTypeArrayLen ;$i++)
    {
      $txnXMLString  .="<$txnTypeArray[$i]>"   //begin tag
                       .$txn[$txnTypeArray[$i]] // data
                       . "</$txnTypeArray[$i]>"; //end tag
    }

   $txnXMLString = "<$txnType>$txnXMLString</$txnType>";
   $xmlString .=$txnXMLString;

 }

 return $xmlString;

}//end toXML



}//end class

##################### ernexTransaction #######################################################

class ernexTransaction{

 var $txn;

function ernexTransaction($txn)
{
	$this->txn=$txn;
}

// new api
function getTransaction()
{
	return $this->txn;
}

}//end class


?>


