<?php

namespace Mypos\IPC;

/**
 * Process IPC method: IPCMandateManagement.
 * Collect, validate and send API params
 */
class MandateManagement extends Base
{

    const MANDATE_MANAGEMENT_ACTION_REGISTER = 1;
    const MANDATE_MANAGEMENT_ACTION_CANCEL = 2;


    private $mandateReferece, $customerWalletNumber, $action, $mandateText;

    /**
     * Return Refund object
     * @param Config $cnf
     */
    public function __construct(Config $cnf)
    {
        $this->setCnf($cnf);
    }

    /**
     * Unique identifier of the agreement (mandate) between the merchant and the client (debtor). Up to 127 characters.
     * @return string
     */
    public function getMandateReferece()
    {
        return $this->mandateReferece;
    }

    /**
     * Unique identifier of the agreement (mandate) between the merchant and the client (debtor). Up to 127 characters.
     * @param string $mandateReferece
     */
    public function setMandateReferece($mandateReferece)
    {
        $this->mandateReferece = $mandateReferece;
    }

    /**
     * Identifier of the client’s (debtor’s) myPOS account
     * @return string
     */
    public function getCustomerWalletNumber()
    {
        return $this->customerWalletNumber;
    }

    /**
     * Identifier of the client’s (debtor’s) myPOS account
     * @param string $customerWalletNumber
     */
    public function setCustomerWalletNumber($customerWalletNumber)
    {
        $this->customerWalletNumber = $customerWalletNumber;
    }

    /**
     * Registration / Cancellation of a MandateReference
     *
     * @return int
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Registration / Cancellation of a MandateReference
     * @param int $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Text supplied from the merchant, so the client can easily identify the Mandate.
     * @return string
     */
    public function getMandateText()
    {
        return $this->mandateText;
    }

    /**
     * Text supplied from the merchant, so the client can easily identify the Mandate.
     * @param string $mandateText
     */
    public function setMandateText($mandateText)
    {
        $this->mandateText = $mandateText;
    }

    /**
     * Initiate API request
     * @return Response
     */
    public function process()
    {
        $this->validate();
        $this->_addPostParam('IPCmethod', 'IPCMandateManagement');
        $this->_addPostParam('IPCVersion', $this->getCnf()->getVersion());
        $this->_addPostParam('IPCLanguage', $this->getCnf()->getLang());
        $this->_addPostParam('SID', $this->getCnf()->getSid());
        $this->_addPostParam('WalletNumber', $this->getCnf()->getWallet());
        $this->_addPostParam('KeyIndex', $this->getCnf()->getKeyIndex());
        $this->_addPostParam('Source', Defines::SOURCE_PARAM);
        $this->_addPostParam('MandateReference', $this->getMandateReferece());
        $this->_addPostParam('CustomerWalletNumber', $this->getCustomerWalletNumber());
        $this->_addPostParam('Action', $this->getAction());
        $this->_addPostParam('MandateText', $this->getMandateText());
        $this->_addPostParam('OutputFormat', $this->getOutputFormat());
        return $this->_processPost();
    }

    /**
     * Validate all set refund details
     * @return boolean
     * @throws IPC_Exception
     */
    public function validate()
    {
        try {
            $this->getCnf()->validate();
        } catch (\Exception $ex) {
            throw new IPC_Exception('Invalid Config details: ' . $ex->getMessage());
        }

        if ($this->getOutputFormat() == null || !Helper::isValidOutputFormat($this->getOutputFormat())) {
            throw new IPC_Exception('Invalid Output format');
        }

        return true;
    }
}