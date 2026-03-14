<template>
    <v-flex>
        <div
            v-for="(menu, id) in menus"
            :key="id"
            :class="{ disabled: menu?.disabled }"
        >
            <template v-if="menus.length">
                <div
                    v-if="menu.header"
                    class="grey--text text--darken-1 font-weight-bold"
                    v-text="menu.label"
                />
                <keep-alive v-else-if="!menu.header && menu.component">
                    <component :is="menu.component">
                        <v-btn
                            :class="customButtonClass"
                            block
                            flat
                        >
                            <v-icon
                                class="mr-1"
                                size="19"
                            >
                                {{ menu.icon }}
                            </v-icon>
                            {{ menu.label }}
                        </v-btn>
                    </component>
                </keep-alive>
                <v-btn
                    v-else
                    :class="customButtonClass"
                    block
                    flat
                    @click="(e) => $emit(`click:${menu.action}`, e)"
                >
                    <v-icon
                        size="19"
                        class="mr-1"
                        >{{ menu.icon }}</v-icon
                    >
                    {{ menu.label }}
                </v-btn>
            </template>
        </div>
    </v-flex>
</template>
<script>
import LabelMenu from '../Label/LabelMenu.vue'
import MoveTaskMenu from './Menu/MoveTaskMenu.vue'

export default {
    components: { LabelMenu, MoveTaskMenu },

    props: {
        imSubscribed: {
            type: Boolean,
            default: false,
        },

        isSubtask: {
            type: Boolean,
            default: true,
        },
    },

    computed: {
        menus() {
            return [
                ...this.suggestions,
                { label: 'Add to card', header: true },
                {
                    label: 'Members',
                    icon: 'person_add',
                    action: 'task-members',
                },
                { label: 'Labels', icon: 'label', action: 'label' },
                {
                    label: 'Attachment',
                    icon: 'attachment',
                    action: 'attach',
                },
                {
                    label: 'Log time',
                    icon: 'more_time',
                    action: 'log-time',
                },
                ...this.subtask,
                { label: 'Actions', header: true },
                {
                    label: 'Time Logs',
                    icon: 'history',
                    action: 'time-logs',
                },
                { label: 'Move', icon: 'east', component: 'move-task-menu' },
                {
                    label: 'Duplicate',
                    icon: 'content_copy',
                    action: 'duplicate',
                },
                {
                    label: 'Delete',
                    icon: 'delete',
                    action: 'delete',
                },
            ]
        },

        customButtonClass() {
            return 'grey--text text--darken-2 grey lighten-2 text-capitalize text-md-left task-sidebar-options'
        },

        suggestions() {
            if (this.imSubscribed) return []

            return [
                { label: 'Suggested', header: true },
                { label: 'Join', icon: 'person', action: 'join' },
            ]
        },

        subtask() {
            if (this.isSubtask) return []

            return [
                {
                    label: 'Subtask',
                    icon: 'playlist_add',
                    action: 'subtask',
                },
            ]
        },
    },
}
</script>
