<template>
    <ModalLayout :title="$attrs.modal.options.title" :id="$attrs.modal.id">
        <v-tabs v-model="tabs" centered color="grey darken-4" slider-color="grey" grow>
            <v-tab :href="`#everyone`">
                Everyone
            </v-tab>
            <v-tab :href="`#contacts`">
                Addressbook
            </v-tab>
        </v-tabs>
        <v-divider></v-divider>

        <v-container grid-list-lg fluid>
            <v-list two-line class="mb-0 pa-0 grey darken-4">
                <template v-for="(item, index) in selections">
                    <v-divider v-if="index" :key="index"></v-divider>

                    <v-list-tile :key="`everyone_${item.id}`" @click="onSelectContact(item)">
                        <v-list-tile-avatar>
                            <v-img :src="item.avatar.url" />
                        </v-list-tile-avatar>
                        <v-list-tile-content class="ml-3">
                            <v-list-tile-title>
                                {{ item.name }}
                                <v-btn v-if="selected.indexOf(item.id) !== -1" icon ripple absolute right>
                                    <v-icon color="blue">check</v-icon>
                                </v-btn>
                            </v-list-tile-title>
                            <v-list-tile-sub-title>
                                {{ item.$alias | capitalize | singular }}
                            </v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </template>
            </v-list>
        </v-container>
        <v-speed-dial class="text-lg-right speed-dial-right" :fixed="false" direction="left" :right="true" :bottom="true" transition="scale">
            <v-btn color="primary" slot="activator" fab>
                <v-icon>add</v-icon>
            </v-btn>

            <v-btn
                color="primary"
                @click="$modals.showUserContact(undefined, true)"
            >
                ADD USER
            </v-btn>

            <v-btn
                color="primary"
                @click="$modals.showCompanyContact(undefined, true)"
            >
                ADD COMPANY
            </v-btn>

            <v-btn
                color="primary"
                @click="$modals.showEmployeeContact(undefined, true)"
            >
                ADD EMPLOYEE
            </v-btn>
        </v-speed-dial>
    </ModalLayout>
</template>

<script>
import { Response } from '~/services/events/modal';
import UsesContactStore from '../../mixins/UsesContactStore';
import DispatchWhenTokenChanges from '@common/mixins/DispatchWhenTokenChanges';

import {
    showUserContact,
    showEmployeeContact,
    showCompanyContact
} from '~/services/events/modal';

export default {
    name: 'ContactSelectorModal',

    mixins: [
        UsesContactStore(),
        DispatchWhenTokenChanges('contact/all')
    ],

    props: {
        onSelect: {
            type: Function,
            required: true
        },

        selected: {
            type: Array,
            default: () => ([])
        }
    },

    data: () => ({
        tabs: 'everyone',
    }),

    computed: {
        selections() {
            return this[this.tabs];
        }
    },

    methods: {
        onSelectContact(contact) {
            this.onSelect(new Response(this, contact))
        }
    },

    created() {
        this.$modals = {
            showCompanyContact,
            showUserContact,
            showEmployeeContact
        };
    }
}
</script>
