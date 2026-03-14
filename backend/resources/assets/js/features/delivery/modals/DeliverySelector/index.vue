<template>
    <ModalLayout :title="$attrs.modal.options.title" :id="$attrs.modal.id">
        <v-container grid-list-lg fluid>
            <DeliveryCard
                hide-menu
                hide-members
                :min-height="100"
                v-for="delivery in selections"
                :key="delivery.id"
                :delivery="delivery"
                @click.native="select(delivery)"
                class="mb-2"
            />
        </v-container>
    </ModalLayout>
</template>

<script>
import { Response } from '~/services/events/modal';
import { Delivery } from '~/services/models';
import UsesDeliveryStore from '../../mixins/UsesDeliveryStore';
import DeliveryCard from '../../components/DeliveryCard';
import DispatchWhenTokenChanges from '@common/mixins/DispatchWhenTokenChanges';

export default {
    name: 'DeliverySelectorModal',

    mixins: [UsesDeliveryStore(),DispatchWhenTokenChanges('delivery/all')],

    components: { DeliveryCard },

    props: {
        selected: {
            type: Array,
            default: () => ([]),
        },

        onSelect: {
            type: Function,
            required: true
        }
    },

    computed: {
        selections() {
            const selected = Delivery.collection(this.selected);
            return this.deliveries.filter((delivery) =>
                !selected.includes(delivery)
            );
        }
    },

    methods: {
        select(delivery) {
            this.onSelect(new Response(this, delivery));
        },
    }
}
</script>
