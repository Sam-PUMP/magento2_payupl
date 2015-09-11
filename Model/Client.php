<?php
/**
 * @copyright Copyright (c) 2015 Orba Sp. z o.o. (http://orba.pl)
 */

namespace Orba\Payupl\Model;

use Orba\Payupl\Model\Client\Exception;

class Client
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var Client\Order
     */
    protected $_orderHelper;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param Client\Config $configHelper
     * @param Client\Order $orderHelper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Orba\Payupl\Model\Client\Config $configHelper,
        \Orba\Payupl\Model\Client\Order $orderHelper
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_orderHelper = $orderHelper;
        $configHelper->setConfig();
    }

    /**
     * @param array $data
     * @return bool|\OpenPayU_Result
     * @throws Exception
     */
    public function orderCreate(array $data = [])
    {
        if (!$this->_orderHelper->validateCreate($data)) {
            throw new Exception('Order request data array is invalid.');
        }
        $data = $this->_orderHelper->addSpecialData($data);
        $result = $this->_orderHelper->create($data);
        if (!$result) {
            throw new Exception('There was a problem while processing order create request.');
        }
        return $result;
    }

    /**
     * @param string $id
     * @return bool|\OpenPayU_Result
     * @throws Exception
     */
    public function orderRetrieve($id)
    {
        if (!$this->_orderHelper->validateRetrieve($id)) {
            throw new Exception('ID of order to retrieve is empty.');
        }
        $result = $this->_orderHelper->retrieve($id);
        if (!$result) {
            throw new Exception('There was a problem while processing order retrieve request.');
        }
        return $result;
    }

    /**
     * @param string $id
     * @return bool|\OpenPayU_Result
     * @throws Exception
     */
    public function orderCancel($id)
    {
        if (!$this->_orderHelper->validateCancel($id)) {
            throw new Exception('ID of order to cancel is empty.');
        }
        $result = $this->_orderHelper->cancel($id);
        if (!$result) {
            throw new Exception('There was a problem while processing order cancel request.');
        }
        return $result;
    }

    public function orderStatusUpdate(array $data = [])
    {
        if (!$this->_orderHelper->validateStatusUpdate($data)) {
            throw new Exception('Order status update request data array is invalid.');
        }
        $result = $this->_orderHelper->statusUpdate($data);
        if (!$result) {
            throw new Exception('There was a problem while processing order status update request.');
        }
        return $result;
    }
}