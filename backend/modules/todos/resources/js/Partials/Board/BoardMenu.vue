<template>
    <v-menu
        offset-y
        offset-overflow
        close-on-content-click
        min-width="280"
    >
        <template #activator="{ on }">
            <v-btn
                flat
                icon
                small
                color="primary"
                class="more-action"
                v-on="on"
            >
                <v-icon color="grey darken-2">more_horiz</v-icon>
            </v-btn>
        </template>
        <v-list>
            <template v-for="(menu, index) in menus">
                <v-divider
                    v-if="menu.divider"
                    :key="index"
                />
                <MenuListItem
                    v-else
                    :key="index"
                    :icon="menu.icon"
                    :class="{ disabled: menu?.disabled }"
                    @click="$emit(menu.action)"
                >
                    {{ menu.label }}
                </MenuListItem>
            </template>
        </v-list>
    </v-menu>
</template>
<script>
import MenuListItem from '@components/MenuListItem.vue'

export default {
    components: { MenuListItem },

    computed: {
        menus() {
            return [
                { label: 'Edit board', icon: 'edit', action: 'click:edit' },
                {
                    label: 'Change cover photo',
                    icon: 'wallpaper',
                    action: 'click:change-cover-photo',
                    disabled: true,
                },
                { divider: true },
                {
                    label: 'Duplicate',
                    icon: 'content_copy',
                    action: 'click:duplicate',
                },
                { label: 'Delete', icon: 'delete', action: 'click:delete' },
            ]
        },
    },
}
</script>
