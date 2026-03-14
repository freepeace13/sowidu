<template>
    <v-toolbar
        color="transparent"
        flat
        class="board-toolbar"
        extended
    >
        <v-menu
            :nudge-width="100"
            allow-overflow
            max-height="400"
            offset-y
        >
            <template #activator="{ on }">
                <v-toolbar-title
                    class="tw-flex tw-items-center tw-space-x-2 h-full"
                >
                    <BoardLogo
                        :src="board?.logo"
                        :title="board?.title"
                        :is-icon="board?.is_icon"
                        :color="board?.icon_color"
                        :avatar-size="40"
                        :icon-size="28"
                    />
                    <span class="text-capitalize sm:tw-text-lg xs:tw-text-sm">
                        {{ board?.title }}
                    </span>
                    <v-icon
                        v-on="on"
                        @click="
                            $inertia.reload({
                                only: ['boards'],
                                preserveState: true,
                                preserveScroll: true,
                            })
                        "
                    >
                        arrow_drop_down
                    </v-icon>
                </v-toolbar-title>
            </template>
            <v-list
                dense
                two-line
                style="min-width: 260px"
            >
                <v-subheader>Switch board</v-subheader>
                <v-divider />
                <v-list-tile
                    v-for="item in boards"
                    :key="item.id"
                    avatar
                    @click="
                        $inertia.get(
                            $route('todos.boards.tasks.index', item.id),
                        )
                    "
                >
                    <v-list-tile-avatar>
                        <BoardLogo
                            :src="item.logo"
                            :title="item.title"
                            :is-icon="item.is_icon"
                            :color="item?.icon_color"
                            :avatar-size="56"
                            :icon-size="28"
                        />
                    </v-list-tile-avatar>
                    <v-list-tile-content>
                        <v-list-tile-title class="text-capitalize">
                            {{ item.title }}
                        </v-list-tile-title>
                        <v-list-tile-sub-title>
                            {{ item.description }}
                        </v-list-tile-sub-title>
                    </v-list-tile-content>
                </v-list-tile>
                <v-divider />
                <MenuListItem
                    dense
                    icon="keyboard_return"
                    @click="$inertia.get($route('todos.boards.index'))"
                >
                    Go to board list
                </MenuListItem>
            </v-list>
        </v-menu>
        <v-divider
            :vertical="true"
            :inset="true"
            class="mx-2"
        />

        <v-spacer v-if="$vuetify.breakpoint.smAndDown" />
        <BoardMemberList
            v-if="$vuetify.breakpoint.smAndUp"
            :subscribers="subscribers"
            @click:subscriber="
                ({ e, subscriber }) =>
                    subscriberDetailsMenuRef.show(e, subscriber)
            "
        />
        <v-spacer v-if="$vuetify.breakpoint.smAndUp" />

        <FilterMenu
            :subscribers="subscribers"
            :labels="labels"
            :active-filters="filters"
        />
        <v-btn
            flat
            depressed
            class="px-2"
            :small="$vuetify.breakpoint.xs"
            @click="$emit('click:settings')"
        >
            <v-icon left>settings</v-icon>Settings
        </v-btn>

        <template
            v-if="$vuetify.breakpoint.xs"
            #extension
        >
            <BoardMemberList
                :subscribers="subscribers"
                @click:subscriber="
                    ({ e, subscriber }) =>
                        subscriberDetailsMenuRef.show(e, subscriber)
                "
            />
        </template>
    </v-toolbar>
</template>
<script>
import MenuListItem from '@components/MenuListItem.vue'
import FilterMenu from './Filter/FilterMenu.vue'
import useParentFinder from '@/Composables/useParentFinder'
import BoardLogo from './BoardLogo.vue'
import BoardMemberList from './BoardMemberList.vue'

export default {
    components: {
        MenuListItem,
        FilterMenu,
        BoardLogo,
        BoardMemberList,
    },

    props: {
        board: {
            type: Object,
            default: null,
            required: false,
        },

        subscribers: {
            type: Array,
            required: false,
            default: () => [],
        },

        boards: {
            type: Array,
            required: false,
            default: () => [],
        },

        labels: {
            required: false,
            type: Array,
            default: () => [],
        },

        filters: {
            required: false,
            type: Object,
        },
    },

    computed: {
        subscriberDetailsMenuRef() {
            return useParentFinder(this, 'subscriberDetailsMenuRef')
        },
    },
}
</script>
<style scoped>
.board-member {
    max-width: 40px;
}

.board-toolbar {
    z-index: 10;
}
</style>
