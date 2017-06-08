<?php

namespace Mypos\IPC;

/**
 * Process IPC method: IPCPurchase.
 * Collect, validate and send API params
 */
class Purchase extends Base
{

    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var Customer
     */
    private $customer;
    private $url_ok, $url_cancel, $url_notify;
    private $currency = 'EUR', $note, $orderID, $cardTokenRequest, $paymentParametersRequired;

    /**
     * Return purchase object
     * @param Config $cnf
     */
    public function __construct(Config $cnf)
    {
        $this->setCnf($cnf);
    }

    /**
     * Purchase identifier - must be unique
     * @param string $orderID
     * @return Purchase
     */
    public function setOrderID($orderID)
    {
        $this->orderID = $orderID;
        return $this;
    }

    /**
     * Purchase identifier
     * @return string
     */
    public function getOrderID()
    {
        return $this->orderID;
    }

    /**
     * Optional note to purchase
     * @param string $note
     * @return Purchase
     */
    public function setNote($note)
    {
        $this->note = $note;
        return $this;
    }

    /**
     * Optional note to purchase
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * ISO-4217 Three letter currency code
     * @param string $currency
     * @return Purchase
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * ISO-4217 Three letter currency code
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Cart object
     * @param Cart $cart
     * @return Purchase
     */
    public function setCart(Cart $cart)
    {
        $this->cart = $cart;
        return $this;
    }

    /**
     * Cart object
     * @return Cart
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * Customer object
     * @param Customer $customer
     * @return Purchase
     */
    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Merchant Site URL where client comes after successful payment
     * @param string $urlOk
     * @return Config
     */
    public function setUrlOk($urlOk)
    {
        $this->url_ok = $urlOk;
        return $this;
    }

    /**
     * Merchant Site URL where client comes after successful payment
     * @return string
     */
    public function getUrlOk()
    {
        return $this->url_ok;
    }

    /**
     * Merchant Site URL where client comes after unsuccessful payment
     * @param string $urlCancel
     * @return Config
     */
    public function setUrlCancel($urlCancel)
    {
        $this->url_cancel = $urlCancel;
        return $this;
    }

    /**
     * Merchant Site URL where client comes after unsuccessful payment
     * @return string
     */
    public function getUrlCancel()
    {
        return $this->url_cancel;
    }

    /**
     * Merchant Site URL where IPC posts Purchase Notify requests
     * @param string $urlNotify
     * @return Config
     */
    public function setUrlNotify($urlNotify)
    {
        $this->url_notify = $urlNotify;
        return $this;
    }

    /**
     * Merchant Site URL where IPC posts Purchase Notify requests
     * @var string
     */
    public function getUrlNotify()
    {
        return $this->url_notify;
    }

    /**
     * Whether to return Card Token for current client card
     * @return integer
     */
    public function getCardTokenRequest()
    {
        return $this->cardTokenRequest;
    }

    /**
     * Whether to return Card Token for current client card
     * @param integer $cardTokenRequest
     */
    public function setCardTokenRequest($cardTokenRequest)
    {
        $this->cardTokenRequest = $cardTokenRequest;
    }

    /**
     * Defines the packet of details needed from merchant and client to make payment
     * @return integer
     */
    public function getPaymentParametersRequired()
    {
        return $this->paymentParametersRequired;
    }

    /**
     * Defines the packet of details needed from merchant and client to make payment
     * @param integer $paymentParametersRequired
     */
    public function setPaymentParametersRequired($paymentParametersRequired)
    {
        $this->paymentParametersRequired = $paymentParametersRequired;
    }


    /**
     * Initiate API request
     * @return boolean
     */
    public function process()
    {
        $this->validate();

        $this->_addPostParam('IPCmethod', 'IPCPurchase');
        $this->_addPostParam('IPCVersion', $this->getCnf()->getVersion());
        $this->_addPostParam('IPCLanguage', $this->getCnf()->getLang());
        $this->_addPostParam('SID', $this->getCnf()->getSid());
        $this->_addPostParam('WalletNumber', $this->getCnf()->getWallet());
        $this->_addPostParam('KeyIndex', $this->getCnf()->getKeyIndex());
        $this->_addPostParam('Source', Defines::SOURCE_PARAM);

        $this->_addPostParam('Currency', $this->getCurrency());
        if (!$this->isNoCartPurchase()) {
            $this->_addPostParam('Amount', $this->cart->getTotal());
        }

        $this->_addPostParam('OrderID', $this->getOrderID());
        $this->_addPostParam('URL_OK', $this->getUrlOk());
        $this->_addPostParam('URL_Cancel', $this->getUrlCancel());
        $this->_addPostParam('URL_Notify', $this->getUrlNotify());

        if ($this->getPaymentParametersRequired() == Defines::PURCHASE_TYPE_FULL) {
            $this->_addPostParam('customeremail', $this->getCustomer()->getEmail());
            $this->_addPostParam('customerphone', $this->getCustomer()->getPhone());
            $this->_addPostParam('customerfirstnames', $this->getCustomer()->getFirstName());
            $this->_addPostParam('customerfamilyname', $this->getCustomer()->getLastName());
            $this->_addPostParam('customercountry', $this->getCustomer()->getCountry());
            $this->_addPostParam('customercity', $this->getCustomer()->getCity());
            $this->_addPostParam('customerzipcode', $this->getCustomer()->getZip());
            $this->_addPostParam('customeraddress', $this->getCustomer()->getAddress());
        }


        if ($this->getPaymentParametersRequired() != Defines::PURCHASE_TYPE_SIMPLIFIED_CALL) {
            $this->_addPostParam('Note', $this->getNote());
            if (!$this->isNoCartPurchase()) {
                $this->_addPostParam('CartItems', $this->cart->getItemsCount());
                $items = $this->cart->getCart();

                $i = 1;
                foreach ($items as $v) {
                    $this->_addPostParam('Article_' . $i, $v['name']);
                    $this->_addPostParam('Quantity_' . $i, $v['quantity']);
                    $this->_addPostParam('Price_' . $i, $v['price']);
                    $this->_addPostParam('Amount_' . $i, $v['price'] * $v['quantity']);
                    $this->_addPostParam('Currency_' . $i, $this->getCurrency());
                    $i++;
                }
            }
        }

        $this->_addPostParam('CardTokenRequest', $this->getCardTokenRequest());
        $this->_addPostParam('PaymentParametersRequired', $this->getPaymentParametersRequired());

        $this->_processHtmlPost();
        return true;
    }

    /**
     * Validate all set purchase details
     * @return boolean
     * @throws IPC_Exception
     */
    public function validate()
    {

        if ($this->getUrlCancel() === null || !Helper::isValidURL($this->getUrlCancel())) {
            throw new IPC_Exception('Invalid Cancel URL');
        }

        if ($this->getUrlNotify() === null || !Helper::isValidURL($this->getUrlNotify())) {
            throw new IPC_Exception('Invalid Notify URL');
        }

        if ($this->getUrlOk() === null || !Helper::isValidURL($this->getUrlOk())) {
            throw new IPC_Exception('Invalid Success URL');
        }

        if ($this->getCardTokenRequest() === null || !in_array($this->getCardTokenRequest(), array(Defines::CARD_TOKEN_REQUSET_NONE, Defines::CARD_TOKEN_REQUSET_ONLY_STORE, Defines::CARD_TOKEN_REQUSET_PAY_AND_STORE))) {
            throw new IPC_Exception('Invalid value provided for CardTokenRequest params');
        }

        if ($this->getPaymentParametersRequired() === null || !in_array($this->getPaymentParametersRequired(), array(Defines::PURCHASE_TYPE_FULL, Defines::PURCHASE_TYPE_SIMPLIFIED_CALL, Defines::PURCHASE_TYPE_SIMPLIFIED_PAYMENT_PAGE))) {
            throw new IPC_Exception('Invalid value provided for PaymentParametersRequired params');
        }

        if ($this->getCurrency() === null || strpos(Defines::AVL_CURRENCIES, $this->getCurrency()) === false) {
            throw new IPC_Exception('Invalid currency');
        }

        try {
            $this->getCnf()->validate();
        } catch (Exception $ex) {
            throw new IPC_Exception('Invalid Config details: ' . $ex->getMessage());
        }

        if (!$this->isNoCartPurchase()) {
            try {
                $this->getCart()->validate();
            } catch (Exception $ex) {
                throw new IPC_Exception('Invalid Cart details: ' . $ex->getMessage());
            }
        }

        if ($this->getPaymentParametersRequired() == Defines::PURCHASE_TYPE_FULL) {
            try {
                $this->getCustomer()->validate($this->getPaymentParametersRequired());
            } catch (Exception $ex) {
                throw new IPC_Exception('Invalid Customer details: ' . $ex->getMessage());
            }
        }

        return true;
    }

    /**
     * If request is only for card token request without payment, the Amount and Cart params are not required
     * @return bool
     */
    private function isNoCartPurchase()
    {
        return $this->getCardTokenRequest() == Defines::CARD_TOKEN_REQUSET_ONLY_STORE;
    }
}
