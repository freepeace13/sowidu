<template>
    <v-card flat class="address-pane mb-3">
        <span class="change" @click="$emit('clear')" v-if="clearable">
            <v-icon class="clear-icon">clear</v-icon>
        </span>
        <v-card-text>
            <h4 class="font-weight-bold">{{ contractor && contractor.name }}</h4>
            <div v-html="contractor && contractor.address && contractor.address.label || `Address Unknown`"></div>
        </v-card-text>
    </v-card>
</template>

<script>
import { Company, User, Employee } from '~/services/models';
import { isNullOrUndefined } from '~/support/helpers';

export default {
    props: {
        contractor: {
            required: true,
            validator(v) {
                return isNullOrUndefined(v)
                    || v instanceof User 
                    || v instanceof Company
                    || v instanceof Employee;
            }
        },

        clearable: {
            type: Boolean,
            default: false
        }
    },

    computed: {
        address() {
            if (this.contractor) {
                const { longAddress } = this.contractor

                return typeof(longAddress) === 'function'
                    ? longAddress()
                    : longAddress;
            }

            return null;
        },

        iAmContractor() {
            const account = this.$store.getters['auth/activeAccount']
            return account.id === this.contractor.id
        },

        fullname() {
            return this.contractor ? this.contractor.fullName : null;
        }
    }
}
</script>

<style lang="scss" scoped>
    .address-pane {
        background: #ccc !important;
        position: relative;
        border: 1px solid #777;

        * {
            color: #000;
        }

        .change {
            position: absolute;
            right: 7px;
            top: 7px;
            cursor: pointer;

            .icon {
                color: #EF5350;
            }
        }
    }
</style>
