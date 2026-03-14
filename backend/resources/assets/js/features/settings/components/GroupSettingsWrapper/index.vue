<template>
    <div>
        <v-card-text>
            <h3 class="headline">{{ title }}</h3>
            <p class="subtitle">{{ subtitle }}</p>
        </v-card-text>
        <v-card-text>
            <slot></slot>
        </v-card-text>
    </div>
</template>

<script>
export default {
    props: ['title', 'subtitle']
}
</script>