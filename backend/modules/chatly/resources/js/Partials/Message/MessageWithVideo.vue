<template>
    <span>
        <v-img
            :src="data.thumbnail_url"
            class="cursor-pointer"
            max-height="200"
            :aspect-ratio="1.4"
            gradient="to bottom, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.2)"
        />
        <v-card-actions class="cursor-pointer">
            <v-btn icon>
                <v-icon color="red">play_circle</v-icon>
            </v-btn>
            <div class="text-truncate">{{ data.original_name }}</div>
        </v-card-actions>
    </span>
</template>
<script>
export default {
    props: {
        data: {
            required: true,
            type: Object,
        },
    },
}
</script>
