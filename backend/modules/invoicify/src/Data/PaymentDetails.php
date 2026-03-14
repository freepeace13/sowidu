<?php

namespace Modules\Invoicify\Data;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Modules\Invoicify\Data\Concerns\ArrayAccessible;

class PaymentDetails implements Arrayable, ArrayAccess
{
    use ArrayAccessible;

    public function __construct(
        protected string $bankName,
        protected string $iban,
        protected string $bic,
        protected string $vat,
        protected string $hrb,
        protected string $managingDirector,
    ) {}

    public function toArray(): array
    {
        return [
            'bankName' => $this->bankName,
            'iban' => $this->iban,
            'bic' => $this->bic,
            'hrbNr' => $this->hrbNr,
            'vat' => $this->vat,
            'managingDirector' => $this->managingDirector,
            'paymentDate' => $this->paymentDate,
            'totalWageCosts' => $this->totalWageCosts,
            'note' => $this->note,
        ];
    }
}
