<template>
    <v-menu
        v-model="isShow"
        :position-x="x"
        :position-y="y"
        min-width="200"
        close-on-content-click
        offset-y
        left
        bottom
        nudge-right="16"
    >
        <v-list dense>
            <MenuListItem
                v-for="{ name, action, icon } in items"
                :key="`contact-menu-${name}`"
                :icon="icon"
                @click="$emit(action, contact)"
            >
                {{ $t(`buttons.${name}`) }}
            </MenuListItem>
        </v-list>
    </v-menu>
</template>
<script>
import IsDynamicMenu from '@/Mixins/IsDynamicMenu'
import MenuListItem from '@components/MenuListItem.vue'

export default {
    components: { MenuListItem },
    mixins: [IsDynamicMenu],

    data: () => ({
        isShow: false,
        contact: null,
    }),

    computed: {
        items() {
            return [
                { icon: 'info', name: 'details', action: 'click:details' },
                { icon: 'edit', name: 'update', action: 'click:update' },
                { icon: 'delete', name: 'delete', action: 'click:delete' },
            ]
        },
    },

    watch: {
        isShow(value) {
            if (!value) this.contact = null
        },
    },

    methods: {
        show(e, contact) {
            e.preventDefault()
            this.isShow = false
            this.x = e.clientX
            this.y = e.clientY
            this.contact = contact
            this.$nextTick(() => {
                this.isShow = true
            })
        },
    },
}
</script>
