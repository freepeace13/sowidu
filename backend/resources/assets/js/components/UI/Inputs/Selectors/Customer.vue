<template>
    <div>
        <h4 class="font-weight-bold mb-1 text-uppercase" v-if="label" v-html="label"></h4>

        <v-autocomplete
            v-model="selected"
            v-bind="$attrs"
            :items="customers"
            item-value="id"
            hide-selected
            return-object
            :menu-props="{ closeOnContentClick: true }"
            :error="errors.length > 0"
            :error-messages="errors"
            solo
        >
            <template slot="no-data">
                <v-btn @click="viewContactSelector" color="primary" block>
                    Choose From Contact
                </v-btn>
            </template>

            <template slot="selection" slot-scope="{ item, selected }">
                <v-chip :selected="selected" color="green darken-3">
                    <v-avatar>
                        <v-img :src="item.avatar.url" :alt="item.name" />
                    </v-avatar>

                    <div>
                        <span v-html="item.name" class="font-weight-bold"></span>
                        ({{ item.profile.entity | singular | capitalize }})
                    </div>

                    <!-- <div v-else>
                        {{ item.name }} : <span v-html="item.name" class="font-weight-bold"></span>
                    </div> -->
                </v-chip>
            </template>

            <template slot="item" slot-scope="{ item }">
                <v-list-tile-avatar
                    color="indigo"
                    class="headline font-weight-light white--text"
                >
                    <v-img :src="item.avatar.url" alt="" />
                </v-list-tile-avatar>
                <v-list-tile-content>
                    <v-list-tile-title>
                        {{ item.name }}
                    </v-list-tile-title>
                    <v-list-tile-sub-title class="grey--text">
                        {{ item.profile.entity | singular | capitalize }}
                    </v-list-tile-sub-title>
                </v-list-tile-content>
            </template>
        </v-autocomplete>
    </div>
</template>

<script>
import axios from 'axios'
import { showContactSelector } from '~/services/events/modal'
import { mapState } from 'vuex'
import { UsesCustomerStore, DispatchesQueue } from '~/components/Mixins';
import DispatchWhenTokenChanges from '@common/mixins/DispatchWhenTokenChanges';

export default {
    name: 'CustomerSelector',

    mixins: [
        UsesCustomerStore(),
        DispatchWhenTokenChanges('customer/all')
    ],

    props: {
        label: {
            type: String,
        },

        value: {
            required: true
        },

        errors: {
            type: Array,
            default: () => {
                return []
            }
        }
    },

    computed: {
        selected: {
            set(value) {
                this.$emit('input', value);
                this.$emit('change', value);
            },
            get() {
                return this.value;
            }
        }
    },

    methods: {
        viewContactSelector() {
            showContactSelector({
                onSelect: async (response) => {
                    this.selected = await this.$customers.create(response.value);
                    response.close();
                }
            }, true)
        }
    }
}
</script>
