<?php
/**
 * @copyright Copyright (c) 2015 Orba Sp. z o.o. (http://orba.pl)
 */

namespace Orba\Payupl\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Orba\Payupl\Model\Client
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_scopeConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_orderHelper;

    /**
     * @var ObjectManager
     */
    protected $_objectManagerHelper;

    public function setUp()
    {
        $this->_objectManagerHelper = new ObjectManager($this);
        $this->_configHelper = $this->getMockBuilder(\Orba\Payupl\Model\Client\Config::class)->disableOriginalConstructor()->getMock();
        $this->_orderHelper = $this->getMockBuilder(\Orba\Payupl\Model\Client\Order::class)->disableOriginalConstructor()->getMock();
        $this->_model = $this->_getModel();
    }

    public function testSetConfigInConstructor()
    {
        $this->_configHelper->expects($this->once())->method('setConfig')->willReturn(true);
        // This will run constructor.
        $this->_getModel();
    }

    public function testOrderCreateInvalidData()
    {
        $this->setExpectedException(\Orba\Payupl\Model\Client\Exception::class, 'Order request data array is invalid.');
        $this->_orderHelper->expects($this->once())->method('validateCreate')->willReturn(false);
        $this->_model->orderCreate();
    }

    public function testOrderCreateFail()
    {
        $data = ['data'];
        $dataExtended = ['data_extended'];
        $this->setExpectedException(\Orba\Payupl\Model\Client\Exception::class, 'There was a problem while processing order create request.');
        $this->_orderHelper->expects($this->once())->method('validateCreate')->willReturn(true);
        $this->_orderHelper->expects($this->once())->method('addSpecialData')->with($this->equalTo($data))->willReturn($dataExtended);
        $this->_orderHelper->expects($this->once())->method('create')->with($this->equalTo($dataExtended))->willReturn(false);
        $this->_model->orderCreate($data);
    }

    public function testOrderCreateSuccess()
    {
        $data = ['data'];
        $dataExtended = ['data_extended'];
        $result = $this->_getResultMock();
        $this->_orderHelper->expects($this->once())->method('validateCreate')->with($this->equalTo($data))->willReturn(true);
        $this->_orderHelper->expects($this->once())->method('addSpecialData')->with($this->equalTo($data))->willReturn($dataExtended);
        $this->_orderHelper->expects($this->once())->method('create')->with($this->equalTo($dataExtended))->willReturn($result);
        $this->assertEquals($result, $this->_model->orderCreate($data));
    }
    
    public function testOrderRetrieveEmptyId()
    {
        $this->setExpectedException(\Orba\Payupl\Model\Client\Exception::class, 'ID of order to retrieve is empty.');
        $this->_orderHelper->expects($this->once())->method('validateRetrieve')->willReturn(false);
        $this->_model->orderRetrieve('');
    }

    public function testOrderRetrieveFail()
    {
        $id = '123456';
        $this->setExpectedException(\Orba\Payupl\Model\Client\Exception::class, 'There was a problem while processing order retrieve request.');
        $this->_orderHelper->expects($this->once())->method('validateRetrieve')->willReturn(true);
        $this->_orderHelper->expects($this->once())->method('retrieve')->with($this->equalTo($id))->willReturn(false);
        $this->_model->orderRetrieve($id);
    }

    public function testOrderRetrieveSuccess()
    {
        $id = '123456';
        $result = $this->_getResultMock();
        $this->_orderHelper->expects($this->once())->method('validateRetrieve')->with($this->equalTo($id))->willReturn(true);
        $this->_orderHelper->expects($this->once())->method('retrieve')->with($this->equalTo($id))->willReturn($result);
        $this->assertEquals($result, $this->_model->orderRetrieve($id));
    }

    public function testOrderCancelEmptyId()
    {
        $this->setExpectedException(\Orba\Payupl\Model\Client\Exception::class, 'ID of order to cancel is empty.');
        $this->_orderHelper->expects($this->once())->method('validateCancel')->willReturn(false);
        $this->_model->orderCancel('');
    }

    public function testOrderCancelFail()
    {
        $id = '123456';
        $this->setExpectedException(\Orba\Payupl\Model\Client\Exception::class, 'There was a problem while processing order cancel request.');
        $this->_orderHelper->expects($this->once())->method('validateCancel')->willReturn(true);
        $this->_orderHelper->expects($this->once())->method('cancel')->with($this->equalTo($id))->willReturn(false);
        $this->_model->orderCancel($id);
    }

    public function testOrderCancelSuccess()
    {
        $id = '123456';
        $result = $this->_getResultMock();
        $this->_orderHelper->expects($this->once())->method('validateCancel')->with($this->equalTo($id))->willReturn(true);
        $this->_orderHelper->expects($this->once())->method('cancel')->with($this->equalTo($id))->willReturn($result);
        $this->assertEquals($result, $this->_model->orderCancel($id));
    }

    public function testOrderStatusUpdateInvalidData()
    {
        $this->setExpectedException(\Orba\Payupl\Model\Client\Exception::class, 'Order status update request data array is invalid.');
        $this->_orderHelper->expects($this->once())->method('validateStatusUpdate')->willReturn(false);
        $this->_model->orderStatusUpdate();
    }

    public function testOrderStatusUpdateFail()
    {
        $data = ['data'];
        $this->setExpectedException(\Orba\Payupl\Model\Client\Exception::class, 'There was a problem while processing order status update request.');
        $this->_orderHelper->expects($this->once())->method('validateStatusUpdate')->willReturn(true);
        $this->_orderHelper->expects($this->once())->method('statusUpdate')->with($this->equalTo($data))->willReturn(false);
        $this->_model->orderStatusUpdate($data);
    }

    public function testOrderStatusUpdateSuccess()
    {
        $data = ['data'];
        $result = $this->_getResultMock();
        $this->_orderHelper->expects($this->once())->method('validateStatusUpdate')->with($this->equalTo($data))->willReturn(true);
        $this->_orderHelper->expects($this->once())->method('statusUpdate')->with($this->equalTo($data))->willReturn($result);
        $this->assertEquals($result, $this->_model->orderStatusUpdate($data));
    }

    /**
     * @return object
     */
    protected function _getModel()
    {
        return $this->_objectManagerHelper->getObject(
            \Orba\Payupl\Model\Client::class,
            [
                'configHelper' => $this->_configHelper,
                'orderHelper' => $this->_orderHelper
            ]
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getResultMock()
    {
        return $this->getMockBuilder(\OpenPayU_Result::class)->getMock();
    }
}