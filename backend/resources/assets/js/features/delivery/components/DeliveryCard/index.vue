<template>
    <DisplayCard
        :min-height="minHeight"
        max-height="none"
        :title="delivery.title | truncate(25)"
        v-on="$listeners"
        hide-photos
    >
        <template slot="subtitle">
            <span class="grey--text text-lighten-5">
                {{ delivery.deliveryDate }}
                {{ delivery.deliveryTime }}
                &mdash;
                <span :class="typeClass">{{ delivery.type | capitalize }}</span>
            </span>
        </template>

        <template slot="menu" v-if="!hideMenu">
            <slot name="menu" v-if="$scopedSlots.menu"></slot>

            <v-list light v-else>
                <v-list-tile>
                    <v-list-tile-content @click="$emit('clickmenu-edit')">
                        Edit
                    </v-list-tile-content>
                </v-list-tile>
                <v-list-tile>
                    <v-list-tile-content @click="$emit('clickmenu-delete')">
                        Delete
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>
        </template>

        <v-layout column justify-space-between align-space-around fill-height>
            <v-flex>
                <p class="grey--text text-lighten-5 mb-0">
                    {{ delivery.remarks | truncate(90) }}
                </p>
            </v-flex>
            <v-flex class="text-xs-right" v-if="! hideMembers">
                <div class="members">
                    <UserIcon
                        v-for="member in delivery.members"
                        class="mr-2"
                        :key="member.id"
                        :url="member.avatar.url"
                        :status="member.status.authStatus"
                    >
                        {{ member.name }}
                    </UserIcon>
                </div>
            </v-flex>
        </v-layout>
    </DisplayCard>
</template>

<script>
import DisplayCard from '~/components/layouts/DisplayCard';
import { Delivery } from '~/services/models';

export default {
    name: 'DeliveryCard',

    components: {
        DisplayCard
    },

    props: {
        delivery: {
            type: Delivery,
            required: true
        },

        hideMenu: {
            type: Boolean,
            default: false
        },

        hideMembers: {
            type: Boolean,
            defaut: false
        },

        minHeight: {
            type: [String, Number],
            default: '180px'
        }
    },

    computed: {
        typeClass() {
            const classes = ['white--text', 'px-1'];

            return this.delivery.type === 'incoming'
                ? [...classes, 'success']
                : [...classes, 'primary'];
        }
    }
}
</script>