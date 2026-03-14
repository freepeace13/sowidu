<div class="invoice-header sticky">
    <div class="header-container">
        <div class="tw-w-full payer ">
            <div class="address">
                <div>

                </div>
            </div>
            <div class=" tw-text-base">
            </div>
            <div class="tw-text-base payer-info">
                <br>
            </div>
            <div class="tw-flex tw-flex-grow tw-items-end tw-font-semibold tw-text-base invoice-kind">
            </div>
        </div>
        <div class="tw-text-left pt-0 contractor tw-w-[18rem]">
            <div class="tw-flex tw-justify-end contractor-logo-container">
                <img src="{{ $invoice->company->photo }}" class="contractor-logo">
            </div>
            <div class="tw-font-semibold tw-text-base" style="visibility: hidden;">
                {{ $invoice->company->name }}
                {{ $invoice->company->legal_form->legal_form }}
            </div>
            <div class="tw-font-semibold tw-text-sm">
                <div> </div>
                <div> </div>
            </div>
            <div class="tw-font-semibold tw-text-[10px] tw-underline contractor-contact-info">
                <div>
                    <span>

                    </span>
                </div>
                <div>
                    <span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>