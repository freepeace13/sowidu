<template>
    <DisplayCard
        :min-height="minHeight"
        max-height="none"
        :title="task.title | truncate(25)"
        v-on="$listeners"
        hide-photos
    >
        <template slot="subtitle">
            <span class="grey--text text-lighten-5">
                {{ task.formattedDates.dateCreated }}
                &middot;
                {{ task.formattedDates.timeCreated }}
            </span>
        </template>

        <template slot="menu" v-if="!hideMenu">
            <slot name="menu" v-if="$scopedSlots.menu"></slot>
            <v-list light v-else>
                <v-list-tile v-if="detachable">
                    <v-list-tile-content @click="$emit('clickmenu-detach')">
                        Detach
                    </v-list-tile-content>
                </v-list-tile>
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

        <v-layout
            v-if="!hideMembers || Boolean(task.description)"
            column justify-space-between
            align-space-around
            fill-height
        >
            <v-flex>
                <p class="grey--text text-lighten-5 mb-0">
                    {{ task.description | truncate(90) }}
                </p>
            </v-flex>
            <v-flex class="text-xs-right" v-if="!hideMembers">
                <div class="members">
                    <UserIcon
                        v-for="member in task.members"
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
import Task from '~/services/models/task';

export default {
    name: 'TaskCard',

    components: {
        DisplayCard
    },

    props: {
        task: {
            type: Task,
            required: true
        },

        detachable: {
            type: Boolean,
            default: false
        },

        minHeight: {
            type: [String, Number],
            default: '180px'
        },

        hideMenu: {
            type: Boolean,
            default: false
        },

        hideMembers: {
            type: Boolean,
            default: false
        },
    },
}
</script>