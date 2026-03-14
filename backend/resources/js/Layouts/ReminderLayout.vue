<template>
    <blank-layout title="Sowidu">
        <v-content>
            <v-container
                fluid
                grid-list-lg
                fill-height
            >
                <slot />
            </v-container>
        </v-content>
    </blank-layout>
</template>

<script>
import BlankLayout from './BlankLayout.vue'

export default {
    components: {
        BlankLayout,
    },

    mounted() {
        console.log(this.$page)
    },
}
</script>
