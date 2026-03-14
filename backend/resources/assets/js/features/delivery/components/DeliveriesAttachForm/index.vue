<template>
    <section id="deliveries" class="mb-3">
        <v-divider></v-divider>
        <v-subheader class="px-4">{{ $t('labels.deliveries') }}</v-subheader>

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
                    <v-list light>
                        <v-list-tile>
                            <v-list-tile-title @click="$emit('remove', delivery)">
                                Remove
                            </v-list-tile-title>
                        </v-list-tile>
                    </v-list>
                </template>
            </DeliveryCard>
        </v-container>
    </section>
</template>

<script>
import DeliveryCard from '../DeliveryCard';
import { Delivery } from '~/services/models';

export default {
    name: 'FormsDeliveriesList',

    components: {
        DeliveryCard,
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