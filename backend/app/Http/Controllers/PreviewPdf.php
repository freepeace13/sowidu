<?php

namespace App\Http\Controllers;

// use Barryvdh\DomPDF\Facade\Pdf;

use App\Contracts\Actions\SavesInvoiceAsPdfs;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Packages\Invoice\Address;
use Packages\Invoice\BankRemittance;
use Packages\Invoice\Biller;
use Packages\Invoice\Customer;
use Packages\Invoice\Invoice as InvoiceBuilder;
use Packages\Invoice\LineItem;
use Packages\PdfGenerator\MpdfFactory;
use Packages\Urn\UrnManager;
use Sowidu\PdfCraft\Data\InvoiceData;
use Sowidu\PdfCraft\Data\Item;
use Sowidu\PdfCraft\Data\ItemBag;
use Sowidu\PdfCraft\Data\Metadata;
use Sowidu\PdfCraft\Data\Recipient;
use Sowidu\PdfCraft\Data\Sender;
use Sowidu\PdfCraft\MpdfWrapper;
use Sowidu\PdfCraft\Support\StringHelper;

class PreviewPdf extends Controller
{
    public function __construct(
        protected SavesInvoiceAsPdfs $action,
    ) {}

    public function __invoke()
    {
        $pdf = new MpdfWrapper([
            // 'mode' => 's',
            // 'format' => 'A4',
            // 'debug' => true,
            // 'allow_output_buffering' => true,
            // 'default_font' => 'chelvetica',
            // 'default_font_size' => 11,
        ]);

        return $pdf
            ->template('invoice', [
                'invoice' => $this->getData(),
            ])
            ->output();

        // $html = view('pdfcraft::templates.invoice', [])->render();

        // dd($html);
    }

    // protected function getData()
    // {
    //     $invoice = new InvoiceData(
    //         bankName: 'Stadtsparkasse München',
    //         iban: 'DE23 7015 0000 1001 9012 12',
    //         bic: 'SSKMDEMMXXX',
    //         hrbNr: '170 444',
    //         vat: 'DE256569809',
    //         managingDirector: 'Sebastian Goebel',
    //         invoiceNumber: 'BND-RE - 2024 321 042024',
    //         invoiceDate: '03.04.2024',
    //         invoiceOrderNumber: '257-256-3',
    //         constructionSite: 'Pestalozzistrasse-16-80469-München',
    //         executionPeriod: '05.03.2024 - 07.03.2024',
    //         serviceRecipient: 'Bernd Schmücker Stephansplatz 1 80337 München',
    //         description: 'Sehr geehrter Herr Schmücker, anbei erhalten Sie die Rechnung für den Austausch des UP Spülkastens. Dafür',
    //         sender: new Sender(
    //             externalId: 1,
    //             name: 'John Doe',
    //             address: '123 Main St, Anytown, USA',
    //             photoUrl: 'https://doodleipsum.com/700x700/avatar-2?i=3cc4fa41128b79780dc9739c4cd92e80',
    //             email: 'john.doe@example.com',
    //             website: 'https://www.example.com',
    //         ),
    //         recipient: new Recipient(
    //             externalId: 1,
    //             name: 'Jane Doe',
    //             address: '123 Main St, Anytown, USA',
    //             photoUrl: 'https://doodleipsum.com/700/flat?bg=6392D9&i=982653932eabcb86283b22f6dee0aec2',
    //             email: 'jane.doe@example.com',
    //         ),
    //         items: new ItemBag([
    //             Item::create([
    //                 'externalId' => 1,
    //                 'description' => 'Item 1',
    //                 'price' => StringHelper::parseMoneyAny('65,00 €'),
    //                 'unit' => 'HJ',
    //                 'quantity' => StringHelper::parseNumber('23,50'),
    //             ]),
    //             Item::create([
    //                 'externalId' => 2,
    //                 'description' => 'Item 2',
    //                 'price' => StringHelper::parseMoneyAny('448,00 €'),
    //                 'unit' => 'HJ',
    //                 'quantity' => StringHelper::parseNumber('1.00'),
    //             ]),
    //             Item::create([
    //                 'externalId' => 3,
    //                 'description' => 'Item 3',
    //                 'price' => StringHelper::parseMoneyAny('661,00 €'),
    //                 'unit' => 'HJ',
    //                 'quantity' => StringHelper::parseNumber('2,00'),
    //             ]),
    //             Item::create([
    //                 'externalId' => 4,
    //                 'description' => 'Item 4',
    //                 'price' => StringHelper::parseMoneyAny('273,00 €'),
    //                 'unit' => 'HJ',
    //                 'quantity' => StringHelper::parseNumber('2,50'),
    //             ]),
    //         ]),
    //         meta: new Metadata(
    //             title: 'Invoice',
    //             author: 'John Doe',
    //         ),
    //     );

    //     return $invoice;
    // }

    // Using mPDF
    // public function __invoke()
    // {
    //     $start = microtime(true);

    //     $fakeUser = User::all()->random();
    //     /** @todo Transform $fakeInvoice into Packages\Invoice\Invoice instance */
    //     $fakeInvoice = Invoice::has('items', '>', 20)
    //         ->whereNotNull('send_date')
    //         ->get()
    //         ->random();

    //     $client = $fakeInvoice->order->client->loadMissing(['currentPlace']);
    //     $address = new Address(
    //         completeAddress: $client->currentPlace->complete_address,
    //         countryIso: 'PH',
    //         placeId: 1,
    //     );

    //     $customer = new Customer(
    //         urn: UrnManager::generate($client),
    //         name: $client->name ?? $client->full_name,
    //         address: $address,
    //     );

    //     $bankRemittance = new BankRemittance(
    //         bankName: 'Metrobank',
    //         iban: '22231-231-123-2',
    //         bic: '321-32134-54',
    //         vat: 'vat-332',
    //         hrbnr: 'hrbasd-2asd',
    //         managingDirector: 'Sebastian Goebel',
    //     );

    //     $biller = new Biller(
    //         urn: 'asdasd',
    //         name: 'john doe',
    //         logoPath: storage_path('app/public/companies.png'),
    //         website: 'www.example.org',
    //         emailAddress: 'jhondoe@example.org',
    //         address: new Address(
    //             completeAddress: 'ciudad de esperanza',
    //             countryIso: 'PH',
    //             placeId: 1,
    //         ),
    //     );

    //     $invoiceBuilder = new InvoiceBuilder(
    //         identifier: $fakeInvoice->id,
    //         orderNumberId: $fakeInvoice->order->id,
    //         currency: 'EUR',
    //         type: $fakeInvoice->type->value,
    //         customer: $customer,
    //         date: $fakeInvoice->send_date,
    //         paymentDate: $fakeInvoice->payment_date,
    //         biller: $biller,
    //         bankRemittance: $bankRemittance,
    //         location: 'herer',
    //         executionPeriod: [now(), now()->addMonth()],
    //     );

    //     foreach ($fakeInvoice->items as $index => $item) {
    //         $invoiceBuilder->addItem(new LineItem(
    //             id: $item->id,
    //             position: $index,
    //             quantity: $item->quantity,
    //             unit: 'HJ',
    //             description: $item->description,
    //             price: $item->price,
    //         ));
    //     }

    //     $pdf = MpdfFactory::make($invoiceBuilder);

    //     // $pdf = $this->action->saveAsPdf($invoiceBuilder, $fakeUser);

    //     Log::debug('pdf execution', ['seconds' => microtime(true) - $start]);

    //     return $pdf->Output();
    // }

    protected function getContextData()
    {
        return [
            'customer' => [
                'name' => 'john doe 1',
                'address' => 'somewhere down the road',
            ],
            'biller' => [
                'name' => 'john doe',
                'address' => 'somewhere down the road',
                'email' => 'test@example.org',
                'website' => 'example.org',
                'photo' => '/public/images/doodleipsum.png',
            ],
            'items' => array_map(fn ($num) => [
                'id' => $num,
                'description' => 'lorem' . $num,
                'quantity' => $num,
                'price' => $num,
            ], range(1, 35)),
        ];
    }
}
