<template>
    <section class="purchasing-items-grid">
        <v-alert :value="errors.length">
            <ul>
                <li v-for="message in errors" :key="message">
                    {{ message }}
                </li>
            </ul>
        </v-alert>

        <v-layout>
            <v-flex xs1>Qty</v-flex>
            <v-flex xs1>Unit</v-flex>
            <v-flex :xs4="!readonly">Description</v-flex>
            <v-flex xs2>Unit Price</v-flex>
            <v-flex xs2>Total Price</v-flex>
        </v-layout>

        <ItemsGrid
            :readonly="readonly"
            :purchased-items="purchasedItems"
            @create-slot="$listeners['create-slot']"
        >
            <template v-slot="{ item, index }">
                <ItemsGridRow
                    :readonly="readonly"
                    :item="item"
                    :key="index"
                    :enable-create-item="enableCreateItem"
                    v-on="$listeners"
                />
            </template>
        </ItemsGrid>

        <h3 class="text-xs-right py-2">
            Total Amount: <b>{{ calculatedTotal }}</b>
        </h3>
    </section>
</template>

<script>
import { User, Company, Item, Employee } from '~/services/models';
import ItemsGrid from './ItemsGrid';
import ItemsGridRow from './ItemsGridRow';
import { isNullOrUndefined } from '~/support/helpers';
import ItemService from '~/services/ItemService';

export default {
    name: 'PurchasingItemsGrid',

    components: {
        ItemsGrid,
        ItemsGridRow
    },

    props: {
        readonly: {
            type: Boolean,
            default: false
        },

        purchasedItems: {
            type: Array,
            default: () => ([]),
            validator(prop) {
                return prop.every((v) => v instanceof Item);
            }
        },

        enableCreateItem: {
            type: Boolean,
            default: true
        },
        
        errors: {
            type: Array,
            default: () => ([])
        }
    },

    computed: {
        calculatedTotal() {
            return this.purchasedItems.reduce((acc, cur) => acc + cur.subtotal, 0);
        }
    },
}
</script>
