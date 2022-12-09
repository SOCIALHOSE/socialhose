<?php

namespace UserBundle\Controller\Security;

use ApiBundle\Controller\AbstractApiController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class CostCalculationController
 * @package UserBundle\Controller\Security
 *
 * @Route("/cost_calculation", service="user.controller.cost_calculation")
 */
class CostCalculationController extends AbstractApiController
{

    /**
     * @Route("", methods={ "POST" })
     *
     * @return array
     */
    public function costCalculationAction(Request $request, $isCallContoller = false)
    {
        $data = $request->request->all();
        $mtPrice = 0;
        $totalPrice = 0;
        $responseData = [];
        //echo '<pre>'; print_r($data); die;
        if(!empty($data)){
            if(isset(
                    $data['news'],
                    $data['blog'],
                    $data['reddit'],
                    $data['instagram'],
                    $data['twitter'],
                    $data['searchesPerDay'],
                    $data['savedFeeds'],
                    $data['subscriberAccounts'],
                    $data['webFeeds'],
                    $data['alerts'],
                    $data['analytics']
                )
            ){
                //die('hello');
                $mtPrice += ($data['news'] == true) ? 20 : 0;
                $mtPrice += ($data['blog'] == true) ? 15 : 0;
                $mtPrice += ($data['reddit'] == true) ? 1 : 0;
                $mtPrice += ($data['instagram'] == true) ? 3 : 0;
                $mtPrice += ($data['twitter'] == true) ? 3 : 0;
                $responseData['selectedMediaTypeCost'] = $mtPrice;
                $searchPerDayPrice = ($data['searchesPerDay'] > 10) ? ($data['searchesPerDay'] - 10)/10 : 0;
                $searchPerDayPrice = $searchPerDayPrice ? ($searchPerDayPrice * $mtPrice) : 0;
                $responseData['searchPerDayPrice'] = $searchPerDayPrice;

                $savedFeedsPrice = $data['savedFeeds'] ? ($data['savedFeeds'] * $mtPrice) : 0;
                $responseData['savedFeedsPrice'] = $savedFeedsPrice;

                $subscriberAccountsPrice = $data['subscriberAccounts'] > 1 ? (($data['subscriberAccounts'] - 1) * 15) : 0; // Fixed price $15 per account
                $responseData['subscriberAccountsPrice'] = $subscriberAccountsPrice;

                $webFeedsPrice = $data['webFeeds'] > 0 ? ($data['webFeeds'] * 5) : 0; // Fixed price $5 per export/webFeeds
                $responseData['webFeedsPrice'] = $webFeedsPrice;

                $alertsPrice = $data['alerts'] ? ($data['alerts'] * 5) : 0; // Fixed price $5 per alerts
                $responseData['alertsPrice'] = $alertsPrice;

                $analyticsPrice = $data['analytics'] ? ($data['savedFeeds'] * 15) : 0; // Fixed price $15 if analytics field comes true in request
                $responseData['analyticsPrice'] = $analyticsPrice;

                $totalPrice = $searchPerDayPrice + $savedFeedsPrice + $subscriberAccountsPrice + $webFeedsPrice + $alertsPrice + $analyticsPrice;
                $responseData['totalPrice'] = $totalPrice;
                if ($isCallContoller) {
                    return ['price' => $totalPrice];
                }
                return $this->generateResponse($responseData, 200);
            } else {
                return $this->generateResponse("Invalid request", 400);
            }
        } else {
            return $this->generateResponse("Something went wrong in the request.", 400);
        }
    }
}
