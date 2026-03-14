<template>
    <main id="rootview">
        <slot></slot>
    </main>
</template>

<script>
    export default {
        props: {
            title: {
                type: String,
                required: true
            },

            icon: {
                type: String,
                default: 'apps'
            }
        }
    }
</script>