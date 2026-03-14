<template>
    <v-autocomplete
        v-model="selected"
        item-value="id"
        :items="everyone"
        :readonly="readonly"
        :error="errors.length > 0"
        :error-messages="errors"
        return-object
        hide-selected
        solo
        :label="label"
    >
        <template slot="no-data">
            <v-btn
                @click="$modals.showUserContact()"
                color="primary" block
            >
                Create New User
            </v-btn>

            <v-btn
                @click="$modals.showCompanyContact()"
                color="primary"
                block
            >
                Create New Company
            </v-btn>

            <v-btn
                @click="$modals.showEmployeeContact()"
                color="primary" block
            >
                Create New Employee
            </v-btn>

            <v-btn @click="browseContacts" color="primary" block>
                Choose From Contact
            </v-btn>
        </template>

        <template slot="selection" slot-scope="{ item, selected }">
            <v-chip :selected="selected" color="green darken-3">
                <v-avatar>
                    <v-img :src="item.avatar.url" :alt="item.name"/>
                </v-avatar>
                {{ item.name }} ({{ item.entity | capitalize | singular }})
            </v-chip>
        </template>

        <template slot="item" slot-scope="{ item }">
            <v-list-tile-avatar
                color="indigo"
                class="headline font-weight-light white--text"
            >
                <v-img :src="item.avatar.url"/>
            </v-list-tile-avatar>

            <v-list-tile-content>
                <v-list-tile-title>
                    {{ item.name }}
                </v-list-tile-title>
                <v-list-tile-sub-title>
                    {{ item.entity | singular | capitalize }}
                </v-list-tile-sub-title>
            </v-list-tile-content>
        </template>
    </v-autocomplete>
</template>

<script>
import axios from 'axios'
import { mapActions } from 'vuex'
import * as apiCalls from '~/services/api/contact'
import { createResource } from 'vue-async-manager'
import UsesContactStore from '../../mixins/UsesContactStore';
import { DispatchesQueue } from '~/components/Mixins';
import {
    showCompanyContact,
    showUserContact,
    showEmployeeContact,
    showContactSelector
} from '~/services/events/modal'

export default {
    name: 'AddressbookSelector',

    mixins: [
        UsesContactStore(),
        DispatchesQueue()
    ],

    props: {
        label: {
            type: String,
            default: 'Choose Addressbook'
        },

        readonly: {
            type: Boolean,
            default: false
        },

        value: {
            required: true
        },

        errors: {
            type: Array,
            default: () => {
                return []
            }
        },
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
        browseContacts() {
            showContactSelector({
                onSelect: (response) => {
                    this.selected = response.value;
                    response.close();
                }
            });
        }
    },

    created() {
        this.$modals = {
            showCompanyContact,
            showUserContact,
            showEmployeeContact
        }
    }
}
</script>
