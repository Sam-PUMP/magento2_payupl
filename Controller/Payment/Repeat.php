<?php
/**
 * @copyright Copyright (c) 2015 Orba Sp. z o.o. (http://orba.pl)
 */

namespace Orba\Payupl\Controller\Payment;

class Repeat extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\Action\Context
     */
    protected $_context;

    /**
     * @var \Orba\Payupl\Helper\Payment
     */
    protected $_paymentHelper;

    /**
     * @var \Orba\Payupl\Model\Session
     */
    protected $_session;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Orba\Payupl\Helper\Payment $paymentHelper,
        \Orba\Payupl\Model\Session $session
    )
    {
        parent::__construct($context);
        $this->_context = $context;
        $this->_paymentHelper = $paymentHelper;
        $this->_session = $session;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $payuplOrderId = $this->_context->getRequest()->getParam('id');
        $orderId = $this->_paymentHelper->getOrderIdIfCanRepeat($payuplOrderId);
        if ($orderId) {
            $resultRedirect->setPath('orba_payupl/payment/repeat_start');
            $this->_session->setLastOrderId($orderId);
        } else {
            $resultRedirect->setPath('orba_payupl/payment/repeat_error');
            $this->messageManager->addError(__('The repeat payment link is invalid.'));
        }
        return $resultRedirect;
    }
}