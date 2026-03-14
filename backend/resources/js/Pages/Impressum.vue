<template>
    <v-layout
        align-center
        justify-center
        column
        fill-height
    >
        <h1
            class="display-3 font-weight-bold mb-3"
            v-text="title"
        />

        <v-card max-width="480">
            <v-card-text>
                <v-layout
                    row
                    wrap
                >
                    <v-flex
                        xs12
                        class="tw-grid"
                    >
                        <div class="tw-grid tw-grid-cols-5">
                            <div class="tw-col-span-2">Name</div>
                            <div class="tw-col-span-3 pl-2">
                                GS Goebel Haustechnik GmbH
                            </div>
                        </div>
                    </v-flex>
                    <v-flex xs12>
                        <div class="tw-grid tw-grid-cols-5">
                            <div class="tw-col-span-2">Adresse</div>
                            <div class="tw-col-span-3 pl-2">
                                Karl Theodor Straße 81 80796 München
                            </div>
                        </div>
                    </v-flex>
                    <v-flex xs12>
                        <div class="tw-grid tw-grid-cols-5">
                            <div class="tw-col-span-2">Telefon</div>
                            <div class="tw-col-span-3 pl-2">089 27273018</div>
                        </div>
                    </v-flex>
                    <v-flex xs12>
                        <div class="tw-grid tw-grid-cols-5">
                            <div class="tw-col-span-2">E-Mail-Adresse</div>
                            <div class="tw-col-span-3 pl-2">
                                info@gs-goebel-haustechnik.de
                            </div>
                        </div>
                    </v-flex>
                    <v-flex xs12>
                        <div class="tw-grid tw-grid-cols-5">
                            <div class="tw-col-span-2">
                                Verantwortlich für den Inhalt
                            </div>
                            <div class="tw-col-span-3 pl-2">
                                Sebastian Goebel
                            </div>
                        </div>
                    </v-flex>
                    <v-flex xs12>
                        <div class="tw-grid tw-grid-cols-5">
                            <div class="tw-col-span-2">Umsatzsteuer-ID</div>
                            <div class="tw-col-span-3 pl-2">DE256569809</div>
                        </div>
                    </v-flex>

                    <v-flex xs12>
                        <div class="tw-grid tw-grid-cols-5">
                            <div class="tw-col-span-2">Register</div>
                            <div class="tw-col-span-3 pl-2">HRB 170444</div>
                        </div>
                    </v-flex>

                    <v-flex xs12>
                        <div class="tw-grid tw-grid-cols-5">
                            <div class="tw-col-span-2">
                                Vertretungsberechtigter
                            </div>
                            <div class="tw-col-span-3 pl-2">
                                Sebastian Goebel
                            </div>
                        </div>
                    </v-flex>
                </v-layout>
            </v-card-text>
        </v-card>
        <div class="tw-flex tw-items-center mt-3 tw-w-[480px] tw-justify-end">
            <v-btn
                :href="$route('auth.login')"
                depressed
                color="primary"
            >
                Login Now
            </v-btn>
        </div>
    </v-layout>
</template>

<script>
import GuestLayout from '@/Layouts/GuestLayout.vue'

export default {
    layout: GuestLayout,

    props: {
        title: {
            type: String,
        },
    },
}
</script>
