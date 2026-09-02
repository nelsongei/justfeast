<?php

namespace App\Services\Loop;

final readonly class PaybillPaymentData
{
    public function __construct(
        public string $merchantReference,
        public string $idempotencyKey,
        public string $paybillNumber,
        public string $accountReference,
        public string $amount,
        public string $currency,
        public ?string $narration = null,
    ) {}
}
