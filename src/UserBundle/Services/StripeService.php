<?php

namespace UserBundle\Services;

use Exception;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\Plan;
use Stripe\Product;
use Stripe\Subscription;
use Stripe\SubscriptionItem;
use Stripe\PaymentMethod;
use Stripe\Invoice;
use Stripe\Card;
use Stripe\Price;

class StripeService
{


    /**
     * @var string
     */
    private $stripe_auth_api_secret_key;
    /**
     * @var string
     */
    private $stripe_auth_api_publish_key;

    public function __construct(string $stripe_auth_api_secret_key, string $stripe_auth_api_publish_key)
    {

        $this->stripe_auth_api_secret_key = $stripe_auth_api_secret_key;
        $this->stripe_auth_api_publish_key = $stripe_auth_api_publish_key;
    }

    public function setApiKey(){
        Stripe::setApiKey($this->stripe_auth_api_secret_key);
    }

    public function createSource($parentId, $params = null, $opts = null){
        try {
            return Customer::createSource($parentId, $params, $opts);
        } catch (ApiErrorException $e) {
            return $e;
        }
    }

    public function paymentMethodAttachToCustomer($parentId, $params = null, $opts = null){
        try {
            $paymentMethod = new PaymentMethod($parentId);
            return $paymentMethod->attach($params, $opts);
        } catch (ApiErrorException $e) {
            return $e;
        }
    }

    public function getPaymentMethodById($params = null, $opts = null){
        try {
            $paymentMethod = new PaymentMethod();
            return $paymentMethod->retrieve($params, $opts);
        } catch (ApiErrorException $e) {
            return $e;
        }
    }

    public function getAllPaymentMethodUpdateByCustomerId($parentId, $params = null, $opts = null){
        try {
            $paymentMethod = new PaymentMethod();
            return $paymentMethod->update($parentId, $params, $opts);
        } catch (ApiErrorException $e) {
            return $e;
        }
    }

    public function paymentMethodDetachToCustomer($parentId, $params = null, $opts = null){
        try {
            $paymentMethod = new PaymentMethod($parentId);
            return $paymentMethod->detach($params, $opts);
        } catch (ApiErrorException $e) {
            return $e;
        }
    }
    

    public function addProduct($params = null, $opts = null){
        try {
            return Product::create($params, $opts);
        } catch (ApiErrorException $e) {
            return $e;
        }
    }

    public function getProduct($id = null, $opts = null){
        try {
            return Product::retrieve($id, $opts);
        } catch (ApiErrorException $e) {
            return $e;
        }
    }

    public function getProducts($params = null, $opts = null){
        try {
            return Product::all($params, $opts);
        } catch (ApiErrorException $e) {
            return $e;
        }
    }

    public function getPlans($params = null, $opts = null){
        try {
            return Plan::all($params, $opts);
        } catch (ApiErrorException $e) {
            return $e;
        }
    }

    public function addPlan($params = null, $opts = null){
        try {
            return Plan::create($params, $opts);
        } catch (ApiErrorException $e) {
            return $e;
        }
    }

    public function getPlan($params = null, $opts = null){
        try {
            return Plan::retrieve($params, $opts);
        } catch (ApiErrorException $e) {
            return $e;
        }
    }

    public function addPrice($params = null, $opts = null){
        try {
            return Price::create($params, $opts);
        } catch (ApiErrorException $e) {
            return $e;
        }
    }

    public function getPrice($params = null, $opts = null){
        try {
            return Price::retrieve($params, $opts);
        } catch (ApiErrorException $e) {
            return $e;
        }
    }

    public function removePrice($params = null, $opts = null){
        try {
            return Price::retrieve($params, $opts);
        } catch (ApiErrorException $e) {
            return $e;
        }
    }

    public function getUpdatePrice($id, $params = null, $opts = null){
        try {
            return Price::update($id, $params, $opts);
        } catch (ApiErrorException $e) {
            return $e;
        }
    }


    public function createCustomer($params = null, $options = null)
    {
        try {
            return Customer::create($params, $options);
        } catch (ApiErrorException $e) {
            return $e;
        }
    }

    public function updateCustomer($id, $params = null, $options = null)
    {
        try {
            return Customer::update($id, $params, $options);
        } catch (ApiErrorException $e) {
            return $e;
        }
    }

    public function getCustomer($params = null, $options = null)
    {
        try {
            return Customer::retrieve($params, $options);
        } catch (ApiErrorException $e) {
            return $e;
        }
    }

    public function deleteCustomer($params = null, $options = null)
    {
        try {
            $customer = Customer::retrieve($params, $options);
            return $customer->delete();
        } catch (ApiErrorException $e) {
            return $e;
        }
    }

    public function createSubscription($params = null, $options = null){
        try {
            return Subscription::create($params, $options);
        } catch (ApiErrorException $e) {
            return $e;
        }

    }

    public function getSubscription($id, $params = null, $options = null){
        try {
            return Subscription::retrieve($id, $params, $options);
        } catch (ApiErrorException $e) {
            return $e;
        }

    }

    public function createSubscriptionItem($params = null, $options = null){
        try {
            return SubscriptionItem::create($params, $options);
        } catch (ApiErrorException $e) {
            return $e;
        }

    }

    public function updateSubscription($id, $params = null, $options = null){
        try {
            return Subscription::update($id, $params, $options);
        } catch (ApiErrorException $e) {
            return $e;
        }

    }

    public function updateSubscriptionItem($id, $params = null, $options = null){
        try {
            return SubscriptionItem::update($id, $params, $options);
        } catch (ApiErrorException $e) {
            return $e;
        }

    }

    public function cancelSubscription($id, $params = null, $options = null){
        try {
            $subscription = new Subscription($id);
            return $subscription->cancel($params, $options);
        } catch (ApiErrorException $e) {
            return $e;
        }

    }

    public function createUsageRecord($subscriptionItemId, $params = null, $opts = null){
        try {
            return SubscriptionItem::createUsageRecord($subscriptionItemId, $params, $opts);
        } catch (ApiErrorException $e) {
            return $e;
        }
    }

    public function getPaymentMethod($params = null, $opts = null){
        try {
            return PaymentMethod::all($params, $opts);
        } catch (ApiErrorException $e) {
            return $e;
        }
    }

    public function getUpcomingInvoice($params = null, $opts = null){
        try {
            return Invoice::upcoming($params, $opts);
        } catch (ApiErrorException $e) {
            return $e;
        }
    }

    public function getAllInvoice($params = null, $opts = null){
        try {
            return Invoice::all($params, $opts);
        } catch (ApiErrorException $e) {
            return $e;
        }
    }
}