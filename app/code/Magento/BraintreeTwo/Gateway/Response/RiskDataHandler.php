<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\BraintreeTwo\Gateway\Response;

use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\BraintreeTwo\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;

/**
 * Class RiskDataHandler
 */
class RiskDataHandler implements HandlerInterface
{
    /**
     * Risk data id
     */
    const RISK_DATA_ID = 'riskDataId';

    /**
     * The possible values of the risk decision are Not Evaluated, Approve, Review, and Decline
     */
    const RISK_DATA_DECISION = 'riskDataDecision';

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * Constructor
     *
     * @param SubjectReader $subjectReader
     */
    public function __construct(SubjectReader $subjectReader)
    {
        $this->subjectReader = $subjectReader;
    }

    /**
     * Handles response
     *
     * @param array $handlingSubject
     * @param array $response
     * @return void
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = $this->subjectReader->readPayment($handlingSubject);

        /** @var \Braintree\Transaction $transaction */
        $transaction = $this->subjectReader->readTransaction($response);

        if (!isset($transaction->riskData)) {
            return;
        }

        $payment = $paymentDO->getPayment();
        ContextHelper::assertOrderPayment($payment);

        $payment->setAdditionalInformation(self::RISK_DATA_ID, $transaction->riskData->id);
        $payment->setAdditionalInformation(self::RISK_DATA_DECISION, $transaction->riskData->decision);
    }
}
