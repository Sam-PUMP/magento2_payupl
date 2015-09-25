<?php
/**
 * @copyright Copyright (c) 2015 Orba Sp. z o.o. (http://orba.pl)
 */

namespace Orba\Payupl\Controller\Payment\Repeat;

class Start extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Orba\Payupl\Model\Session
     */
    protected $_session;

    /**
     * @var \Orba\Payupl\Model\Client
     */
    protected $_client;

    /**
     * @var \Orba\Payupl\Model\Order
     */
    protected $_orderHelper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Orba\Payupl\Model\Session $session
     * @param \Orba\Payupl\Model\ClientInterface $client
     * @param \Orba\Payupl\Model\Order $orderHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Orba\Payupl\Model\Session $session,
        \Orba\Payupl\Model\ClientInterface $client,
        \Orba\Payupl\Model\Order $orderHelper
    )
    {
        parent::__construct($context);
        $this->_session = $session;
        $this->_client = $client;
        $this->_orderHelper = $orderHelper;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /**
         * @var $clientOrderHelper \Orba\Payupl\Model\Client\OrderInterface
         */
        $resultRedirect = $this->resultRedirectFactory->create();
        $orderId = $this->_session->getLastOrderId();
        if ($orderId) {
            $clientOrderHelper = $this->_client->getOrderHelper();
            $order = $clientOrderHelper->loadOrderById($orderId);
            $orderData = $clientOrderHelper->getDataForOrderCreate($order);
            $result = $this->_client->orderCreate($orderData);
            $this->_orderHelper->saveNewTransaction(
                $orderId,
                $result['orderId'],
                $result['extOrderId'],
                $clientOrderHelper->getNewStatus()
            );
            $clientOrderHelper->setNewOrderStatus($order);
            $redirectUrl = $result['redirectUri'];
        } else {
            $redirectUrl = 'orba_payupl/payment/repeat_error';
        }
        $resultRedirect->setPath($redirectUrl);
        return $resultRedirect;
    }
}