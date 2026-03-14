<template>
    <v-toolbar height="64" card prominent>
        <v-tabs
            height="64"
            slider-color="grey"
            color="transparent"
            active-class="font-weight-bold"
        >
            <v-tab exact :to="{
                name: `orders.show.details`,
                params: $route.params
            }">
                {{ $t('labels.details') }}
            </v-tab>

            <v-tab exact :to="{
                name: `orders.show.media`,
                params: $route.params
            }">
                {{ $t('labels.media') }}
            </v-tab>

            <v-tab exact :to="{
                name: `orders.show.deliveries`,
                params: $route.params
            }">
                {{ $t('labels.deliveries') }}
            </v-tab>

            <v-tab exact :to="{
                name: `orders.show.tasks`,
                params: $route.params
            }">
                {{ $t('labels.tasks') }}
            </v-tab>
        </v-tabs>

        <template v-if="showSaveAndConfirmButtons">
            <v-btn
                fab small
                color="primary"
                title="Save Changes"
                v-if="allowSaveAction"
                @click="handleSavingChanges(order)"
                :loading="isSaving"
                :disabled="isSaving"
            >
                <v-icon>save</v-icon>
            </v-btn>

            <v-btn
                flat small
                color="green"
                v-if="allowConfirmAction"
                @click="handleConfirm(order)"
                :loading="isSaving"
                :disabled="isSaving"
            >
                <v-icon left>check</v-icon> {{ $t('buttons.confirm') }}
            </v-btn>

            <v-btn
                flat small
                color="red"
                v-if="allowConfirmAction"
                @click="handleCancel(order)"
                :loading="isSaving"
                :disabled="isSaving"
            >
                <v-icon left>close</v-icon> {{ $t('buttons.cancel') }}
            </v-btn>
        </template>
    </v-toolbar>
</template>

<script>
import { AuthorizationService } from '@features/auth/api';
import ShowOrderStates from '../mixins/ShowOrderStates';
import ShowOrderHandlers from '../mixins/ShowOrderHandlers';
import * as Enums from '../enums';

export default {
    name: 'ShowOrderToolbar',

    mixins: [
        ShowOrderStates(),
        ShowOrderHandlers()
    ],

    data: () => ({
        allowUpdate: false,
        allowConfirm: false
    }),

    computed: {
        isSaving() {
            return this.$wait.is('order/sketch/update');
        },

        showSaveAndConfirmButtons() {
            return this.orderCopy.policies.isModifiable()
                || this.orderCopy.policies.isConfirmable();
        },

        allowSaveAction() {
            return this.orderCopy.policies.isModifiable() && this.allowUpdate;
        },

        allowConfirmAction() {
            const { policies } = this.orderCopy;

            if (policies.states('state').isTransitionableTo('pending')) {
                return !policies.isType('outgoing')
                    ? this.allowConfirm
                    : true;
            }

            return false;
        }
    },

    created() {
        AuthorizationService
            .can(Enums.PERMISSIONS.CONFIRM_ORDER)
            .then(() => {
                this.allowConfirm = true;
            })
            .catch(() => {
                this.allowConfirm = false;
            });

        AuthorizationService
            .can(Enums.PERMISSIONS.UPDATE_ORDER)
            .then(() => {
                this.allowUpdate = true;
            })
            .catch(() => {
                this.allowUpdate = false;
            });
    }
}
</script>