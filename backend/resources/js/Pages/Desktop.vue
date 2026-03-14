<script setup>
import { useGetPageProps } from '@/Composables/useGetPageProps'
import ServiceCard from '../Components/ServiceCard.vue'

const services = useGetPageProps('services')

console.log('🚀 ~ services:', services)
</script>
<template>
    <v-container
        align-center
        fluid
        grid-list-lg
        fill-height
    >
        <v-layout
            align-center
            justify-center
            row
            wrap
            class="tw-content-center"
        >
            <v-flex
                grow
                tw-flex
                tw-justify-center
            >
                <div
                    class="tw-grid tw-grid-cols-2 tw-gap-x-4 tw-gap-y-5 sm:tw-grid-cols-3 sm:tw-gap-x-5 sm:tw-gap-y-5 md:tw-gap-x-5 md:tw-gap-y-6 tw-content-center"
                >
                    <ServiceCard
                        v-for="service in services"
                        :key="service.name"
                        v-bind="service"
                        :icon-size="35"
                        :min-width="$vuetify.breakpoint.mdAndUp ? 220 : auto"
                    />
                </div>
            </v-flex>
        </v-layout>
    </v-container>
</template>
