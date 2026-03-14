<template>
    <section id="deliveries" class="mb-3">
        <v-divider></v-divider>
        <v-subheader class="px-4">Deliveries</v-subheader>

        <v-container fluid class="py-0">
            <DeliveryCard
                :min-height="100"
                v-for="delivery in deliveries"
                :delivery="delivery"
                :key="delivery.id"
                hide-members
                class="mb-3"
            >
                <template slot="menu">
                    <RemoveableCardMenuList @remove="$emit('remove', delivery)" />
                </template>
            </DeliveryCard>
        </v-container>
    </section>
</template>

<script>
import RemoveableCardMenuList from './RemoveableCardMenuList';
import DeliveryCard from '../../Cards/DeliveryCard';
import { Delivery } from '~/services/models';

export default {
    name: 'FormsDeliveriesList',

    components: {
        DeliveryCard,
        RemoveableCardMenuList
    },

    props: {
        deliveries: {
            type: Array,
            validator(prop) {
                return prop.every((v) => v instanceof Delivery);
            }
        }
    }
}
</script>