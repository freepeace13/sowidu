<template>
    <v-menu
        offset-y
        offset-overflow
        close-on-content-click
        min-width="280"
    >
        <template #activator="{ on }">
            <v-btn
                icon
                v-on="on"
            >
                <v-icon> more_horiz </v-icon>
            </v-btn>
        </template>
        <v-list>
            <template v-for="(menu, key) in menus">
                <v-divider
                    v-if="menu.divider"
                    :key="key"
                />
                <MenuListItem
                    v-else
                    :key="key"
                    :icon="menu.icon"
                    :class="{ disabled: menu?.disabled }"
                    @click="$emit(`click:${menu.action}`)"
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
                { label: 'Add a task', icon: 'note_add', action: 'add-card' },
                {
                    label: 'Duplicate',
                    icon: 'folder_copy',
                    action: 'duplicate',
                    disabled: true,
                },
                { divider: true },
                {
                    label: 'Sorty by',
                    icon: 'sort',
                    action: 'sort',
                    disabled: true,
                },
                { divider: true },
                {
                    label: 'Move all cards on this list',
                    icon: 'moving',
                    action: 'move-all-card',
                    disabled: true,
                },
                {
                    label: 'Delete all cards on this list',
                    icon: 'layers_clear',
                    action: 'delete-all-card',
                    disabled: true,
                },
                { divider: true },
                {
                    label: 'Edit name',
                    icon: 'edit',
                    action: 'edit',
                },
                { divider: true },
                {
                    label: 'Delete this group',
                    icon: 'delete',
                    action: 'delete',
                },
            ]
        },
    },
}
</script>
