<template>
    <div>
        <template v-if="readonly">
            <TextField :label="label" :value="String(choice)" readonly />
        </template>
        <template v-else>
            <h4 class="font-weight-bold mb-1 text-uppercase" v-if="label" v-html="label"></h4>
            <v-autocomplete
                v-model="choice"
                v-bind="$attrs"
                v-on="$listeners"
                :items="selections"
                :menu-props="{ closeOnContentClick: true }"
                item-text="name"
                item-value="id"
                hide-selected
                return-object
                hide-details
                solo
            >
                <template slot="no-data">
                    <v-btn color="primary" block @click="createItem">
                        <v-icon>add</v-icon> Create Item
                    </v-btn>
                </template>

                <template slot="item" slot-scope="{ item }">
                    <v-list-tile-content>
                        <v-list-tile-title>{{ item.name }}</v-list-tile-title>
                        <v-list-tile-sub-title>{{ item.longDescription }}</v-list-tile-sub-title>
                </v-list-tile-content>
            </template>
        </v-autocomplete>
        </template>
    </div>
</template>


<script>
import { showItemModal } from '~/services/events/modal'
import { Item } from '~/services/models';
import { isNullOrUndefined } from '~/support/helpers';
import UsesItemStore from '../../mixins/UsesItemStore';
import { DispatchesQueue } from '~/components/Mixins';

export default {
    name: 'ItemSelector',

    mixins: [
        UsesItemStore(),
    ],

    props: {
        label: String,

        readonly: {
            type: Boolean,
            default: false
        },

        selected: {
            type: Array,
            default: () => ([]),
            validator(prop) {
                return prop.every((v) => v instanceof Item);
            }
        },

        value: {
            required: true,
            validator(v) {
                return isNullOrUndefined(v) || v instanceof Item;
            }
        },
    },

    data: () => ({
        search: null
    }),

    computed: {
        choice: {
            set(value) {
                this.$emit('input', value);
                this.$emit('change', value);
            },
            get() {
                return this.value;
            }
        },

        selections() {
            const filtered = this.selected
                .filter((v) => !v.equals(this.value))
                .map((v) => v.id);

            return this.products.filter((v) => !filtered.includes(v.id));
        },
    },

    methods: {
        createItem() {
            showItemModal({ onSuccess: (response) => {
                this.choice = response.value;
                response.close();
            }}, true);
        },
    }
}
</script>
