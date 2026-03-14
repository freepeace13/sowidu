<template>
    <div class="items-grid-row">
        <v-layout row>
            <v-flex xs1>
                <TextField
                    type="number"
                    :readonly="readonly"
                    :value="item.quantity"
                    @input="$emit('update-quantity', {
                        index: $vnode.key,
                        value: $event
                    })"
                    hide-details
                    min="1"
                />
            </v-flex>
            <v-flex xs1>
                <TextField
                    :value="item.unit"
                    hide-details
                    readonly
                />
            </v-flex>
            <v-flex :xs4="!readonly">
                <ItemSelector
                    :readonly="readonly"
                    :value="item"
                    :hide-no-data="!enableCreateItem"
                    @change="$emit('update-item', {
                        index: $vnode.key,
                        value: $event
                    })"
                    hide-details
                    classes="mb-0"
                />
            </v-flex>
            <v-flex xs2>
                <TextField
                    :value="item.retailPrice"
                    hide-details
                    readonly
                />
            </v-flex>
            <v-flex xs2>
                <TextField
                    :value="item.subtotal"
                    hide-details
                    readonly
                />
            </v-flex>
            <v-flex xs2 v-if="!readonly">
                <v-btn
                    flat
                    color="red lighten-1"
                    class="remove-row-button"
                    @click="$emit('delete-slot', item)"
                    small
                >
                    <v-icon color="red lighten-1" left>close</v-icon>
                    {{ $t('buttons.remove') }}
                </v-btn>
            </v-flex>
        </v-layout>
    </div>
</template>

<script>
import { Item } from '~/services/models';
import ItemSelector from '@features/product/components/ItemSelector';

export default {
    name: 'ItemsGridRow',

    components: {
        ItemSelector
    },

    props: {
        readonly: {
            type: Boolean,
            default: false
        },

        item: {
            type: Item,
            required: true
        },

        enableCreateItem: {
            type: Boolean,
            default: false
        }
    }
}
</script>
