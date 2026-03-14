<template>
    <v-menu
        v-model="isShow"
        :position-x="x"
        :position-y="y"
        v-bind="$attrs"
    >
        <v-list dense>
            <MenuListItem
                v-for="listItem in items"
                :key="`contact-menu-${
                    listItem?.name ?? listItem?.label ?? listItem?.icon
                }`"
                :icon="listItem.icon"
                @click="$emit(listItem.action, item)"
            >
                {{ listItem?.label ?? $t(`buttons.${listItem.name}`) }}
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

    props: {
        items: {
            type: Array,
            required: true,
        },
    },

    data: () => ({
        isShow: false,
        item: null,
    }),

    watch: {
        isShow(value) {
            if (!value) this.item = null
        },
    },

    methods: {
        show(position, item = null) {
            this.isShow = false

            this.x = position.x
            this.y = position.y

            this.item = item

            this.$nextTick(() => {
                this.isShow = true
            })
        },
    },
}
</script>
