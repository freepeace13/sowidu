<template>
    <v-navigation-drawer
        class="transparent"
        stateless
        permanent
        floating
    >
        <v-list two-line class="transparent">
            <v-subheader class="text-uppercase">
                {{ $t('labels.status') }}
            </v-subheader>

            <div class="px-2 pb-2">
                <ResourceStateSelector
                    :items="selectableStates"
                    :disabled="isSaving || !orderCopy.policies.isModifiable()"
                    :value="order.states.state.current"
                    :loading="isSaving"
                    @change="handleStateUpdate($event)"
                    block
                />
            </div>

            <v-subheader class="text-uppercase position-relative">
                {{ $t('labels.inputs.members') }}

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
        </v-list>
    </v-navigation-drawer>
</template>

<script>
import { ProgressState } from '~/services/models/order/states';
import Order from '~/services/models/order';
import { showEmployeeSelector } from '~/services/events/modal'
import ResourceStateSelector from '~/components/UI/Inputs/Selectors/ResourceStateSelector';
import ShowOrderHandlers from '../mixins/ShowOrderHandlers';
import ShowOrderStates from '../mixins/ShowOrderStates';

export default {
    name: 'ShowSidebar',

    mixins: [
        ShowOrderStates(),
        ShowOrderHandlers(),
    ],

    components: {
        ResourceStateSelector
    },

    computed: {
        isSaving() {
            return this.$wait.is('order/sketch/update');
        },

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
    },

    methods: {
        selectMember() {
            showEmployeeSelector(this.order.members, (response) => {
                this.handleResetMembers(response.value);
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
