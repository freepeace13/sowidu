<template>
    <DisplayCard
        :title="name"
        :photos="photos"
        @click:photo="$emit('click:photo')"
        @click:title="$emit('click:title')"
    >
        <template slot="subtitle">
            <span class="grey--text text-lighten-5">{{ description }}</span>
        </template>

        <template slot="menu" v-if="menuable">
            <v-list light>
                <v-list-tile v-if="relations['contact:connected']">
                    <v-list-tile-content @click="$emit('menu:edit')">
                        Edit
                    </v-list-tile-content>
                </v-list-tile>

                <v-list-tile v-if="employeable">
                    <v-list-tile-content @click="$emit('menu:employment')">
                        Send Employment Request
                    </v-list-tile-content>
                </v-list-tile>

                <v-list-tile v-if="addressbookable">
                    <v-list-tile-content @click="$emit('menu:add')">
                        Add To Addressbook
                    </v-list-tile-content>
                </v-list-tile>

                <v-list-tile v-if="$listeners['menu:remove'] !== undefined">
                    <v-list-tile-content @click="$emit('menu:remove')">
                        Remove
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>
        </template>

        <v-layout column justify-space-between>
            <v-flex class="grey--text py-0 d-flex" align-items-center>
                <v-list two-line dense class="transparent py-0 info-list">
                    <v-list-tile>
                        <v-list-tile-action>
                            <v-icon>location_on</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>
                                {{ (address || 'Unknown') | truncate(35) }}
                            </v-list-tile-title>

                            <v-list-tile-sub-title>
                                Address
                            </v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </v-list>
            </v-flex>

            <v-flex class="d-flex py-0" shrink>
                <div :class="{ 'green--text': registered }">
                    {{ accountState.toUpperCase() }}
                </div>

                <v-spacer></v-spacer>

                <div class="text-xs-right">
                    {{ connectionState.toUpperCase() }}
                </div>
            </v-flex>
        </v-layout>
    </DisplayCard>
</template>

<script>
import DisplayCard from '~/components/layouts/DisplayCard';
import { CONTACT_TYPES } from '~/support/constants';
import { Company } from '~/services/models';
import { isNullOrUndefined, isObject } from '~/support/helpers';

export default {
    name: 'ContactCard',
    
    components: {
        DisplayCard
    },

    props: {
        name: {
            type: String,
            default: null
        },

        type: {
            type: String,
            required: true,
            validator(v) {
                return Object.values(CONTACT_TYPES).includes(v);
            }
        },

        employer: {
            validator(v) {
                return v instanceof Company || v === undefined;
            }
        },

        address: {
            validator(v) {
                return typeof(v) === 'string' || v === undefined;
            }
        },

        registered: {
            type: Boolean,
            default: false
        },

        relations: {
            default: () => ({}),
            validator(prop) {
                return isNullOrUndefined(prop) || isObject(prop);
            }
        },

        avatar: {
            type: String,
            required: true
        }
    },

    computed: {
        employeable() {
            return this.relations['employment:applicable']
                && ! this.relations['employment:invited']
                && ! this.relations['employment:employed'];
        },

        addressbookable() {
            return this.relations['contact:applicable']
                && ! this.relations['contact:connected']
                && ! this.relations['contact:invited']
                && ! this.relations['employment:invited'];
        },

        description() {
            const { capitalize, singular } = this.$options.filters;
            const type = capitalize(singular(this.type));

            if (this.employer instanceof Company) {
                return `${type} of ${this.employer.name}`;
            }

            return type;
        },

        accountState() {
            return this.registered
                ? 'Registered'
                : 'Unregistered';
        },

        connectionState() {
            return this.relations['contact:connected']
                ? 'Added To Addressbook'
                : 'Not Added To Addressbook';
        },

        menuable() {
            return this.relations['contact:connected']
                || this.employeable
                || this.addressbookable;
        },

        photos() {
            const _photos = [this.avatar];

            if (this.employer instanceof Company) {
                _photos.push(this.employer.avatar);
            }

            return _photos;
        }
    }
}
</script>

<style scoped>
.info-list [role="listitem"] >>> .v-list__tile {
    padding-left: 0;
    padding-right: 0;
}
</style>