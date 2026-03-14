<template>
    <v-tabs slider-color="grey" :value="tab" color="transparent">
        <v-tab key="pending">Pending Tasks</v-tab>
        <v-tab key="finished">Finished</v-tab>
        <v-tab key="archived" class="red--text">Archived</v-tab>

        <v-tab-item :key="tab">
            <slot></slot>
        </v-tab-item>
    </v-tabs>
</template>

<script>
export default {
    computed: {
        tab() {
            const groups = ['pending', 'finished', 'archived'];

            return groups.includes(this.$route.query.group)
                ? this.$route.query.group
                : 'pending';
        }
    }
}
</script>