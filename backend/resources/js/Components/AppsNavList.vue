<template>
    <v-container
        fluid
        grid-list-lg
    >
        <v-layout
            col
            wrap
        >
            <v-flex
                v-for="service in services"
                :key="service.name"
                xs6
                md6
                lg6
            >
                <v-hover>
                    <ServiceCard
                        ref="navListItem"
                        slot-scope="{ hover }"
                        v-bind="service"
                        :icon-size="25"
                        :avatar-size="50"
                        :class="{ 'grey lighten-3': hover }"
                        flat
                        small
                        :min-width="118"
                    />
                </v-hover>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
import ServiceCard from '@/Components/ServiceCard.vue'
import AppServiceMixin from '@/Mixins/AppServiceMixin'

export default {
    components: { ServiceCard },

    mixins: [AppServiceMixin],

    computed: {
        setting() {
            const { breakpoint } = this.$vuetify

            return {
                icon: {
                    color: 'primary',
                    size: breakpoint.smAndUp ? 40 : 26,
                    class: ['flex shrink', { 'mr-3': breakpoint.xsOnly }],
                },
                text: {
                    class: ['flex', { 'pt-0': breakpoint.smAndUp }],
                },
            }
        },
    },

    methods: {
        goTo(url) {
            window.location.href = url
        },
    },
}
</script>
