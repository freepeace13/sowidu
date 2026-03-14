<template>
    <v-card tag="label">
        <v-img :src="url" :aspect-ratio="aspectRatio">
            <template #placeholder>
                <v-layout fill-height align-center justify-center ma-0>
                    <v-progress-circular indeterminate color="grey" />
                </v-layout>
            </template>

            <slot />
        </v-img>
    </v-card>
</template>

<script>
export default {
    props: ['url', 'aspectRatio'],
}
</script>