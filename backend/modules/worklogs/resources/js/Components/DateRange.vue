<template>
    <v-menu
        v-model="isShow"
        :close-on-content-click="false"
        :nudge-right="40"
        lazy
        transition="scale-transition"
        offset-y
        full-width
        min-width="290px"
    >
        <template #:activator="{ on }">
            <v-text-field
                v-model="dates"
                prepend-icon="event"
                readonly
                v-on="on"
            />
        </template>
        <v-date-picker
            v-model="date"
            @input="isShow = false"
        />
    </v-menu>
</template>
<script>
export default {
    props: {
        value: {
            required: true,
            type: String,
        },
    },

    data: () => ({
        isShow: false,
    }),
}
</script>
