<template>
    <v-img
        :src="data.thumbnail_url"
        class="cursor-pointer"
        max-height="200"
        :aspect-ratio="1.4"
        :alt="data.original_name"
        gradient="to bottom, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.2)"
    />
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
