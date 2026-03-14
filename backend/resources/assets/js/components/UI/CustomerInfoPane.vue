<template>
    <v-card flat class="address-pane mb-3">
        <span class="change" @click="$emit('clear')" v-if="clearable">
            <v-icon class="clear-icon">clear</v-icon>
        </span>
        <v-card-text>
            <h4 class="font-weight-bold">{{ customer && customer.name }}</h4>
            <div v-html="customer.address && customer.address.label || `Address Unknown`"></div>
        </v-card-text>
    </v-card>
</template>

<script>
import Customer from '~/services/models/customer';

export default {
    props: {
        customer: {
            validator(v) {
                return v === null || v instanceof Customer;
            }
        },

        clearable: {
            type: Boolean,
            default: false
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
