<template>
    <v-navigation-drawer
        ref="boardSettingsDrawer"
        v-model="isShow"
        right
        fixed
        temporary
        width="320"
        style="z-index: 10"
        :class="[
            'board-settings-menu',
            {
                'mt-5': $vuetify.breakpoint.smAndDown,
                'mt-6': $vuetify.breakpoint.mdAndUp,
            },
        ]"
        disable-resize-watcher
    >
        <v-toolbar
            flat
            class="board-settings-menu-toolbar"
        >
            <v-btn
                v-show="!onMainTab"
                icon
                class="hidden-xs-only"
                @click="currentTab = null"
            >
                <v-icon>navigate_before</v-icon>
            </v-btn>
            <v-toolbar-title class="body-1 ml-0">
                {{ onMainTab ? 'Board Settings' : `${currentTab.title}` }}
            </v-toolbar-title>
            <v-spacer />
            <v-btn
                icon
                flat
                @click="isShow = false"
            >
                <v-icon>close</v-icon>
            </v-btn>
        </v-toolbar>
        <v-divider />
        <BoardSettings
            v-show="onMainTab"
            :is-show="isShow"
            :tabs="tabs"
            @click:tab="(tab) => (currentTab = tab)"
        />
        <component
            :is="tabComponent"
            v-show="!onMainTab"
            :permissions="settings.permissions.members"
            :policies="policies"
            :details="details"
        />
    </v-navigation-drawer>
</template>
<script>
import BoardSettings from './BoardSettings.vue'
import BoardAbout from './BoardAbout.vue'
import MembersPermissions from './MembersPermissions.vue'
import BoardLogo from './BoardLogo.vue'

export default {
    components: { BoardSettings, BoardAbout, MembersPermissions, BoardLogo },

    props: {
        settings: {
            type: Object,
            default: () => ({}),
        },
        policies: {
            required: false,
            type: Object,
        },
        details: {
            required: false,
            type: Object,
            default: () => ({}),
        },
    },

    data: () => ({
        isShow: false,
        currentTab: null,
    }),

    computed: {
        tabs() {
            return [
                {
                    title: 'About this board',
                    description: 'Add a description to your board.',
                    icon: 'description',
                    color: 'green',
                    component: 'board-about',
                },
                {
                    title: 'Change board logo',
                    description: 'Add or change board logo.',
                    icon: 'wallpaper',
                    color: 'blue',
                    component: 'board-logo',
                },
                {
                    title: 'Members permissions',
                    description: 'Change members permissions.',
                    icon: 'manage_accounts',
                    color: 'orange',
                    component: 'members-permissions',
                },
            ]
        },

        onMainTab() {
            return !this.currentTab
        },

        tabComponent() {
            return this.currentTab?.component
        },
    },

    methods: {
        show() {
            this.$inertia.reload({ only: ['details'] })
            this.currentTab = null
            this.isShow = true
        },
    },
}
</script>
