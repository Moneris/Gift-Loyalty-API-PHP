<?php

$store_id =$argv[1];
$api_token=$argv[2];
$orderId = $argv[3];
$txn_number = $argv[4];
$group_ref_num = $argv[5];

include("../ernexClasses.php");

        $txnArray=array(
           	type=>'group',
            order_id=>$orderId,
			txn_number=>$txn_number,
			group_ref_num=>$group_ref_num,
			group_type=>"ernex"
           );


$ernexTxn = new ernexTransaction($txnArray);
$ernexRequest = new ernexRequest($ernexTxn);
$ernexHttpPost  = new ernexHttpsPost($store_id,$api_token,$ernexRequest);
$ernexResponse =$ernexHttpPost->getErnexResponse();

//-------------- Main Receipt Variables --------------------------------//

print("\nReceiptId = " . $ernexResponse->getReceiptId());
print("\nResponseCode = " . $ernexResponse->getResponseCode());
print("\nTransTime = " . $ernexResponse->getTransTime());
print("\nTransDate = " . $ernexResponse->getTransDate());
print("\nTransType = " . $ernexResponse->getTransType());
print("\nComplete = " . $ernexResponse->getComplete());
print("\nMessage = " . $ernexResponse->getMessage());
print("\nTxnNumber = " . $ernexResponse->getTxnNumber());
print("\nTimedOut = " . $ernexResponse->getTimedOut());

?>
