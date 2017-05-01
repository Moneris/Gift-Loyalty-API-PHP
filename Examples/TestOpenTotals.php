<?php

$store_id =$argv[1];
$api_token=$argv[2];
$ecrnumber=$argv[3];

include("../ernexClasses.php");

        $txnArray=array(
            type=>'ernex_opentotals',
            ecr_number=>$ecrnumber
           );

$ernexTxn = new ernexTransaction($txnArray);
$ernexRequest = new ernexRequest($ernexTxn);
$ernexHttpPost  = new ernexHttpsPost($store_id,$api_token,$ernexRequest);
$ernexResponse =$ernexHttpPost->getErnexResponse();

$ecrs = $ernexResponse->getTerminalIds();

for($i=0; $i < count($ecrs); $i++)
{
        print "\nTerminal ID = $ecrs[$i]\n";

		$CardCodes = $ernexResponse->getEcrCardCodes($ecrs[$i]);

        for($j=0; $j < count($CardCodes); $j++)
        {
                print "\nCardCode = $CardCodes[$j]\n";

                print "\nPurchaseCount = " . $ernexResponse->getPurchaseCount($ecrs[$i],$CardCodes[$j]);
                print "\nPurchaseTotal = " . $ernexResponse->getPurchaseTotal($ecrs[$i],$CardCodes[$j]);
                print "\nPurchaseBenefitTotal = " . $ernexResponse->getPurchaseBenefitTotal($ecrs[$i],$CardCodes[$j]);
        		print "\nRefundCount = " . $ernexResponse->getRefundCount($ecrs[$i],$CardCodes[$j]);
                print "\nRefundTotal = " . $ernexResponse->getRefundTotal($ecrs[$i],$CardCodes[$j]);
                print "\nRefundBenefitTotal = " . $ernexResponse->getRefundBenefitTotal($ecrs[$i],$CardCodes[$j]);
				print "\nRedemptionCount = " . $ernexResponse->getRedemptionCount($ecrs[$i],$CardCodes[$j]);
                print "\nRedemptionTotal = " . $ernexResponse->getRedemptionTotal($ecrs[$i],$CardCodes[$j]);
                print "\nRedemptionBenefitTotal = " . $ernexResponse->getRedemptionBenefitTotal($ecrs[$i],$CardCodes[$j]);
				print "\nActivationCount = " . $ernexResponse->getActivationCount($ecrs[$i],$CardCodes[$j]);
                print "\nActivationTotal = " . $ernexResponse->getActivationTotal($ecrs[$i],$CardCodes[$j]);
                print "\nCorrectionCount = " . $ernexResponse->getCorrectionCount($ecrs[$i],$CardCodes[$j]);
                print "\nCorrectionTotal = " . $ernexResponse->getCorrectionTotal($ecrs[$i],$CardCodes[$j])."\n";
		}
}
?>
