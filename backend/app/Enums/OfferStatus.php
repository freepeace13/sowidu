<?php

namespace App\Enums;

use App\Enums\MetaProperties\Color;
use App\Enums\MetaProperties\Icon;
use App\Enums\MetaProperties\Trans;
use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Meta\Meta;
use ArchTech\Enums\Metadata;
use ArchTech\Enums\Options;
use ArchTech\Enums\Values;

/**
 * @method color()
 * @method icon()
 * @method trans()
 */
#[Meta(Color::class, Icon::class, Trans::class)]
enum OfferStatus: string
{
    use InvokableCases;
    use Metadata;
    use Options;
    use Values;

    #[Color('#BDBDBD'), Icon('edit_note'), Trans('offer.labels.status.draft')]
    case DRAFT = 'draft';

    #[Color('#2196F3'), Icon('pending_actions'), Trans('offer.labels.status.pending')]
    case PENDING = 'pending';

    #[Color('#4caf50'), Icon('recommend'), Trans('offer.labels.status.accepted')]
    case ACCEPTED = 'accepted';

    #[Color('#EF6C00'), Icon('back_hand'), Trans('offer.labels.status.rejected')]
    case REJECTED = 'rejected';

    #[Color('#F44336'), Icon('cancel'), Trans('offer.labels.status.cancelled')]
    case CANCELLED = 'cancelled';

    #[Color('#FFC107'), Icon('check_circle'), Trans('offer.labels.status.completed')]
    case COMPLETED = 'completed';
}
