<template>
    <v-list dense two-line class="py-0 ma-0 grey darken-4">
        <template v-if="showSaveAndConfirmButtons">
            <div class="pa-2">
                <v-btn
                    color="primary"
                    v-if="orderCopy.policies.isModifiable()"
                    @click="$emit('save')"
                    :loading="saving"
                    :disabled="saving"
                    block
                >
                    Save Changes
                </v-btn>

                <v-btn
                    color="primary"
                    v-if="orderCopy.policies.isConfirmable()"
                    @click="$emit('confirm')"
                    :loading="saving"
                    :disabled="saving"
                    block
                >
                    Confirm
                </v-btn>

                <v-btn
                    v-if="orderCopy.policies.isConfirmable()"
                    color="error"
                    @click="$emit('cancel')"
                    :loading="saving"
                    :disabled="saving"
                    block
                >
                    Cancel
                </v-btn>
            </div>

            <v-divider></v-divider>
        </template>
        
        <v-subheader class="text-uppercase">Order State</v-subheader>

        <div class="px-2 pb-2">
            <ResourceStateSelector
                :items="selectableStates"
                :disabled="saving || !orderCopy.policies.isModifiable()"
                :value="order.states.state.current"
                :loading="saving"
                @change="$emit('update-state', $event)"
                block
            />
        </div>

        <v-divider></v-divider>

        <v-subheader class="text-uppercase">Order Sections</v-subheader>
        <v-list-tile exact :to="{
            name: 'orders.edit.summary',
            params: { id: $route.params.id }
        }">
            <v-list-tile-avatar>
                <v-icon>home</v-icon>
            </v-list-tile-avatar>
            <v-list-tile-title>General Information</v-list-tile-title>
        </v-list-tile>
        <v-list-tile :to="{
            name: 'orders.edit.media',
            params: { id: $route.params.id }
        }">
            <v-list-tile-avatar>
                <v-icon>image</v-icon>
            </v-list-tile-avatar>
            <v-list-tile-title>Media</v-list-tile-title>
        </v-list-tile>
        <v-list-tile :to="{
            name: 'orders.edit.deliveries',
            params: { id: $route.params.id }
        }">
            <v-list-tile-avatar>
                <v-icon>directions_bus</v-icon>
            </v-list-tile-avatar>
            <v-list-tile-title>Delivery Tickets</v-list-tile-title>
        </v-list-tile>
        <v-list-tile :to="{
            name: 'orders.edit.tasks',
            params: { id: $route.params.id }
        }">
            <v-list-tile-avatar>
                <v-icon>done</v-icon>
            </v-list-tile-avatar>
            <v-list-tile-title>Tasks</v-list-tile-title>
        </v-list-tile>
        <v-list-tile>
            <v-list-tile-avatar>
                <v-icon>receipt</v-icon>
            </v-list-tile-avatar>
            <v-list-tile-title>Invoices</v-list-tile-title>
        </v-list-tile>

        <template>
            <v-divider></v-divider>
            <v-subheader class="text-uppercase position-relative">
                MEMBERS

                <v-btn
                    color="grey lighten-2" flat icon
                    small absolute right
                    @click="selectMember"
                    v-if="orderCopy.policies.isModifiable()"
                >
                    <v-icon size="15">edit</v-icon>
                </v-btn>
            </v-subheader>
            <v-list-tile avatar v-for="member in order.members" :key="member.id">
                <v-list-tile-avatar>
                    <UserIcon :url="member.avatar.url" :status="member.status.authStatus">
                        {{ member.name }}
                    </UserIcon>
                </v-list-tile-avatar>
                <v-list-tile-title>{{ member.name }}</v-list-tile-title>
            </v-list-tile>
        </template>
    </v-list>
</template>

<script>
import { ProgressState } from '~/services/models/order/states';
import Order from '~/services/models/order';
import { showEmployeeSelector } from '~/services/events/modal'
import { UsesMiscStore, HandlesAuthentications } from '~/components/Mixins';
import PropTypes from '../mixins/PropTypes';
import ResourceStateSelector from '~/components/UI/Inputs/Selectors/ResourceStateSelector';

export default {
    name: 'SidebarMenu',

    mixins: [
        PropTypes,
        UsesMiscStore(),
        HandlesAuthentications()
    ],

    components: {
        ResourceStateSelector
    },

    props: {
        saving: {
            type: Boolean,
            required: true
        },

        orderCopy: {
            type: Order,
            required: true
        }
    },

    computed: {
        selectableStates() {
            return (new ProgressState).states.filter((state) => {
                const policies = this.order.policies.states('state');
                return policies.isTransitionableTo(state) || policies.is(state);
            });
        },

        showSaveAndConfirmButtons() {
            return this.orderCopy.policies.isModifiable()
                || this.orderCopy.policies.isConfirmable();
        },

        isCustomizable() {
            if (this.orderCopy.type === 'outgoing') {
                return !this.isConfirmable && !Boolean(this.orderCopy.contractor);
            }

            return this.orderCopy.state !== 'completed';
        },
    },

    methods: {
        selectMember() {
            showEmployeeSelector(this.order.members, (response) => {
                this.$emit('update-members', response.value);
                response.close();
            });
        }
    }
}
</script>

<style lang="scss" scoped>
    .sidebar {
        background: #141414;
        top: 70px;
        width: 320px;
        position: fixed;
        height: calc(100% - 90px);

        /deep/ .v-list {
            background: #141414 !important;
        }
    }

    /deep/ a.v-list__tile {
        text-decoration: none;
        &.v-list__tile--active {
            background: rgba(255,255,255,0.08);
            color: #fff !important;
        }
    }
</style>
