<?php
/**
 * @copyright Copyright (c) 2015 Orba Sp. z o.o. (http://orba.pl)
 */

namespace Orba\Payupl\Block\Payment\Repeat;

class FailTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_session;

    /**
     * @var Fail
     */
    protected $_block;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_paymentHelper;

    public function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->_session = $this->getMockBuilder(\Orba\Payupl\Model\Session::class)->disableOriginalConstructor()->setMethods(['getLastOrderId'])->getMock();
        $this->_paymentHelper = $this->getMockBuilder(\Orba\Payupl\Helper\Payment::class)->disableOriginalConstructor()->getMock();
        $this->_block = $objectManager->getObject(Fail::class, [
            'session' => $this->_session,
            'paymentHelper' => $this->_paymentHelper
        ]);
    }

    public function testGetRepeatPaymentUrlFail()
    {
        $this->_session->expects($this->once())->method('getLastOrderId')->willReturn(null);
        $this->assertFalse($this->_block->getRepeatPaymentUrl());
    }

    public function testGetRepeatPaymentUrlSuccess()
    {
        $orderId = 1;
        $url = 'http://repeat.url';
        $this->_session->expects($this->once())->method('getLastOrderId')->willReturn($orderId);
        $this->_paymentHelper->expects($this->once())->method('getRepeatPaymentUrl')->with($this->equalTo($orderId))->willReturn($url);
        $this->assertEquals($url, $this->_block->getRepeatPaymentUrl());
    }
}