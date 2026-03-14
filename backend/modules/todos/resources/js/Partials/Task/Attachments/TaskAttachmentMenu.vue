<template>
    <v-menu
        v-model="isShow"
        :position-x="x"
        :position-y="y"
        absolute
        :close-on-content-click="true"
    >
        <v-list dense>
            <v-list-tile @click="$emit('click:view', attachment)">
                <v-list-tile-content>
                    <v-list-tile-title class="caption">
                        <v-icon
                            small
                            class="mr-1"
                            >visibility</v-icon
                        >
                        View
                    </v-list-tile-title>
                </v-list-tile-content>
            </v-list-tile>
            <v-list-tile @click="$emit('click:delete', attachment)">
                <v-list-tile-content>
                    <v-list-tile-title class="caption">
                        <v-icon
                            small
                            class="mr-1"
                            >delete</v-icon
                        >
                        Delete
                    </v-list-tile-title>
                </v-list-tile-content>
            </v-list-tile>
        </v-list>
    </v-menu>
</template>
<script>
export default {
    data: () => ({
        isShow: false,
        x: 0,
        y: 0,
        attachment: null,
    }),

    watch: {
        isShow(newVal) {
            if (!newVal) this.attachment = null
        },
    },

    methods: {
        show({ e, attachment }) {
            e.preventDefault()
            this.attachment = attachment
            this.isShow = false
            this.x = e.clientX
            this.y = e.clientY
            this.$nextTick(() => {
                this.isShow = true
            })
        },
    },
}
</script>
