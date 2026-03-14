<template>
    <v-tabs centered
        color="grey darken-4"
        slider-color="grey"
        grow>
        <v-tab class="text-capitalize"
            v-for="(tab, index) in tabs"
            :key="tab.key"
            :exact="isFilter ? true : false"
            :to="isFilter ? { name: $route.name, query: { filter: tab.key } } : { name: tab.key }">
            <v-icon v-if="tab.color"
                class="body-2 mr-2"
                :color="tab.color">fiber_manual_record</v-icon>
            {{tab.title}}
        </v-tab>
    </v-tabs>
</template>
<script>
/**
 * Tab component
 */
export default {
    props: {
        tabs: {
            required: true,
            type: Array,
        },
        isFilter: {
            required: false,
            default: false,
            type: Boolean
        }
    },

    methods: {
        tabClick() {
            console.log('tabClick');
        }
    },
}
</script>
