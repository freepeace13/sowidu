<template>
    <v-btn
        v-if="links && links.next"
        color="primary"
        :loading="loading"
        @click="loadmore"
        large
    >
        Load More
    </v-btn>
</template>

<script>
    import axios from 'axios'

    export default {
        props: {
            links: {
                required: true,
                validator(v) {
                    return v === null || typeof v === 'object'
                }
            }
        },

        data: () => ({
            loading: false
        }),

        methods: {
            async loadmore() {
                if (!this.links || !this.links.next) return

                try {
                    this.loading = true
                    const { data } = await axios.get(this.links.next)
                    this.$emit('success', data)
                } catch (e) {
                    this.$emit('fail', e)
                } finally {
                    this.loading = false
                }
            }
        }
    }
</script>
