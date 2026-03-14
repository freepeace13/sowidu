<template>
    <v-container grid-list-sm fluid class="pt-0">
        <v-layout row wrap>
            <v-flex xs2>
                <h4 class="font-weight-bold mb-1 text-uppercase">qty</h4>
            </v-flex>
            <v-flex xs5>
                <h4 class="font-weight-bold mb-1 text-uppercase">item</h4>
            </v-flex>
            <v-flex xs2>
                <h4 class="font-weight-bold mb-1 text-uppercase">Price</h4>
            </v-flex>
            <v-flex xs3>
                <h4 class="font-weight-bold mb-1 text-uppercase">Subtotal</h4>
            </v-flex>
            <template v-for="(item, index) in items">
                <v-flex xs2 :key="`${index}_qty`">
                    <TextField
                        :value="item.quantity"
                        type="number"
                        min="1"
                        hide-details
                        @input="(value) => $emit('change:quantity', { index, value })">
                    </TextField>
                </v-flex>
                <v-flex xs5 :key="`${index}_item`">
                    <ItemSelector
                        :value="item"
                        :selected="items"
                        @change="(value) => $emit('change:item', { index, value })"
                        hide-details
                    />
                </v-flex>
                <v-flex xs2 :key="`${index}_price`">
                    <TextField
                        :value="item.retailPrice"
                        type="number"
                        hide-details
                        readonly
                    />
                </v-flex>
                <v-flex xs2 :key="`${index}_subtotal`">
                    <TextField :value="item.subtotal" type="number" hide-details readonly />
                </v-flex>
                <v-flex
                    xs1 align-self-center
                    class="text-xs-right"
                    :key="`${index}_remove`"
                >
                    <v-btn color="red" class="ma-0" small icon @click="$emit('remove', item)">
                        <v-icon>remove</v-icon>
                    </v-btn>
                </v-flex>
            </template>
        </v-layout>

        <v-btn block color="secondary" @click="$emit('slot:new')">
            Add new item
        </v-btn>

        <div class="error--text" v-if="errors.length">
            Please select item(s)
        </div>
    </v-container>
</template>

<script>
import { Item } from '~/services/models';
import ItemSelector from '@features/product/components/ItemSelector';

export default {
    components: {
        ItemSelector
    },

    props: {
        items: {
            type: Array,
            required: true,
            validator(prop) {
                return prop.map((v) => v instanceof Item);
            }
        },

        errors: {
            type: Array,
            default: () => ([])
        }
    }
}
</script>
