<?php

namespace App\Models;

use App\Models\Relations\AuthoredByUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class DeliveryTicketDocument extends Model
{
    use AuthoredByUser;
    use HasFactory;

    /** @return BelongsTo|DeliveryTicket */
    public function deliveryTicket(): BelongsTo
    {
        return $this->belongsTo(DeliveryTicket::class);
    }

    /** @return BelongsTo|Media[] */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_file_id');
    }
}
