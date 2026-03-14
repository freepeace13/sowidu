<template>
    <v-card color="grey darken-4" v-bind="$attrs">
        <v-container grid-list-xl fluid class="pa-2">
            <v-layout>
                <v-flex xs1 class="text-xs-center" align-self-center>
                    <UserIcon :url="delivery.customer.avatar"></UserIcon>
                </v-flex>

                <template v-if="!hideSchedule">
                    <v-flex lg1 xs3 align-self-center>
                        <span :class="['darken-1 text-xs-center px-2 py-1 d-block', dateScheduleClass]">
                            {{ delivery.deliveryDate }}
                        </span>
                    </v-flex>
                    <v-flex lg1 xs2 align-self-center>
                        <span :class="['darken-1 text-xs-center px-2 py-1 d-block', timeScheduleClass]">
                            {{ delivery.deliveryTime }}
                        </span>
                    </v-flex>
                </template>

                <v-flex lg8 xs6 align-self-center>
                    <h4 class="font-weight-bold">{{ delivery.title }}</h4>
                    <span>
                        {{ delivery.itemCount }} item(s) &middot;
                        {{ delivery.customer.name }} &mdash;
                        {{ delivery.customer.longAddress  }}
                    </span>
                </v-flex>
                <v-flex xs2 align-self-center text-xs-right>
                    <v-layout>
                        <v-flex>
                            <v-btn depressed icon color="warning" small v-if="delivery.isOutgoing()">
                                <v-icon>arrow_forward</v-icon>
                            </v-btn>
                            <v-btn depressed icon color="success" small v-else>
                                <v-icon>arrow_back</v-icon>
                            </v-btn>
                        </v-flex>
                        <v-flex xs4 v-if="removable">
                            <v-btn icon flat color="red" small @click="$emit('remove', delivery)">
                                <v-icon>delete</v-icon>
                            </v-btn>
                        </v-flex>
                    </v-layout>
                </v-flex>
            </v-layout>
        </v-container>
    </v-card>
</template>

<script>
    export default {
        props: {
            delivery: {
                type: Object,
                required: true
            },

            hideSchedule: {
                type: Boolean,
                default: false
            },

            removable: {
                type: Boolean,
                default: false
            }
        },

        computed: {
            biller() {
                return this.delivery.customer.biller
            },

            dateScheduleClass() {
                return this.delivery.deliveryDateOverdue ? 'red' : 'primary'
            },

            timeScheduleClass() {
                return this.delivery.deliveryTimeOverdue ? 'red' : 'green'
            },
        }
    }
</script>
