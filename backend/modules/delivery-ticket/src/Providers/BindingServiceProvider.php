<?php

namespace Modules\DeliveryTicket\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\DeliveryTicket\Actions\AddDocumentToDeliveryTicket;
use Modules\DeliveryTicket\Actions\AddMaterialToDeliveryTicket;
use Modules\DeliveryTicket\Actions\CreateDeliveryTicket;
use Modules\DeliveryTicket\Actions\DeleteDeliveryTicket;
use Modules\DeliveryTicket\Actions\RemoveDocumentToDeliveryTicket;
use Modules\DeliveryTicket\Actions\RemoveMaterialOnDeliveryTicket;
use Modules\DeliveryTicket\Actions\UpdateDelivererTicket;
use Modules\DeliveryTicket\Actions\UpdateDeliveryAddressTicket;
use Modules\DeliveryTicket\Actions\UpdateDeliveryTicket;
use Modules\DeliveryTicket\Actions\UpdateDeliveryTicketMaterial;
use Modules\DeliveryTicket\Contracts\Actions\AddDocumentToDeliveryTicketContract;
use Modules\DeliveryTicket\Contracts\Actions\AddMaterialToDeliveryTicketContract;
use Modules\DeliveryTicket\Contracts\Actions\CreateDeliveryTicketContract;
use Modules\DeliveryTicket\Contracts\Actions\DeleteDeliveryTicketContract;
use Modules\DeliveryTicket\Contracts\Actions\RemoveDocumentToDeliveryTicketContract;
use Modules\DeliveryTicket\Contracts\Actions\RemoveMaterialOnDeliveryTicketContract;
use Modules\DeliveryTicket\Contracts\Actions\UpdateDelivererTicketContract;
use Modules\DeliveryTicket\Contracts\Actions\UpdateDeliveryAddressTicketContract;
use Modules\DeliveryTicket\Contracts\Actions\UpdateDeliveryTicketContract;
use Modules\DeliveryTicket\Contracts\Actions\UpdateDeliveryTicketMaterialContract;
use Modules\DeliveryTicket\Contracts\Services\DeliveryTicketMaterialServiceContract;
use Modules\DeliveryTicket\Contracts\Services\DeliveryTicketsServiceContract;
use Modules\DeliveryTicket\Services\DeliveryTicketMaterialService;
use Modules\DeliveryTicket\Services\DeliveryTicketsService;

class BindingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->services();
        $this->repositories();
        $this->actions();
    }

    protected function services(): void
    {
        $this->app->bind(DeliveryTicketsServiceContract::class, DeliveryTicketsService::class);
        $this->app->bind(DeliveryTicketMaterialServiceContract::class, DeliveryTicketMaterialService::class);
    }

    protected function repositories(): void
    {
        //
    }

    protected function actions(): void
    {
        $this->app->bind(AddDocumentToDeliveryTicketContract::class, AddDocumentToDeliveryTicket::class);
        $this->app->bind(AddMaterialToDeliveryTicketContract::class, AddMaterialToDeliveryTicket::class);
        $this->app->bind(CreateDeliveryTicketContract::class, CreateDeliveryTicket::class);
        $this->app->bind(DeleteDeliveryTicketContract::class, DeleteDeliveryTicket::class);
        $this->app->bind(RemoveDocumentToDeliveryTicketContract::class, RemoveDocumentToDeliveryTicket::class);
        $this->app->bind(RemoveMaterialOnDeliveryTicketContract::class, RemoveMaterialOnDeliveryTicket::class);
        $this->app->bind(UpdateDeliveryAddressTicketContract::class, UpdateDeliveryAddressTicket::class);
        $this->app->bind(UpdateDelivererTicketContract::class, UpdateDelivererTicket::class);
        $this->app->bind(UpdateDeliveryTicketContract::class, UpdateDeliveryTicket::class);
        $this->app->bind(UpdateDeliveryTicketMaterialContract::class, UpdateDeliveryTicketMaterial::class);
    }
}
