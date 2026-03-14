<script setup></script>
<template>
    <v-card class="grey--text text--darken-3 order-card tw-animate-pulse">
        <v-card-text>
            <v-container
                grid-list-md
                fluid
                pa-0
            >
                <v-layout
                    row
                    class="subheading"
                >
                    <v-flex xs2>
                        <div
                            class="tw-h-2.5 tw-bg-gray-200 tw-rounded-full mr-3"
                        />
                    </v-flex>
                    <v-flex
                        xs3
                        ml-2
                        class="!tw-text-black tw-font-semibold"
                    >
                        <div
                            class="tw-h-2.5 tw-bg-gray-200 tw-rounded-full mr-3"
                        />
                    </v-flex>
                    <v-flex
                        xs5
                        class="!tw-text-black tw-font-semibold"
                    >
                        <div
                            class="tw-h-2.5 tw-bg-gray-200 tw-rounded-full mr-3"
                        />
                    </v-flex>
                    <v-flex
                        xs2
                        class="tw-flex tw-justify-end"
                    >
                        <div class="tw-h-9 tw-bg-gray-200 tw-w-48" />
                    </v-flex>
                </v-layout>
                <v-layout row>
                    <v-flex xs2>
                        <div
                            class="tw-flex tw-items-center tw-justify-center tw-h-28 mb-4 tw-bg-gray-300 tw-rounded"
                        >
                            <svg
                                class="tw-w-10 tw-h-10 tw-text-gray-200"
                                aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="currentColor"
                                viewBox="0 0 16 20"
                            >
                                <path
                                    d="M14.066 0H7v5a2 2 0 0 1-2 2H0v11a1.97 1.97 0 0 0 1.934 2h12.132A1.97 1.97 0 0 0 16 18V2a1.97 1.97 0 0 0-1.934-2ZM10.5 6a1.5 1.5 0 1 1 0 2.999A1.5 1.5 0 0 1 10.5 6Zm2.221 10.515a1 1 0 0 1-.858.485h-8a1 1 0 0 1-.9-1.43L5.6 10.039a.978.978 0 0 1 .936-.57 1 1 0 0 1 .9.632l1.181 2.981.541-1a.945.945 0 0 1 .883-.522 1 1 0 0 1 .879.529l1.832 3.438a1 1 0 0 1-.031.988Z"
                                />
                                <path
                                    d="M5 5V.13a2.96 2.96 0 0 0-1.293.749L.879 3.707A2.98 2.98 0 0 0 .13 5H5Z"
                                />
                            </svg>
                        </div>
                    </v-flex>
                    <v-flex
                        xs3
                        ml-2
                    >
                        <div
                            class="tw-h-5 tw-bg-gray-200 tw-rounded-full tw-w-48 mb-2"
                        />
                        <div
                            class="tw-h-5 tw-bg-gray-200 tw-rounded-full tw-w-48"
                        />
                    </v-flex>
                    <v-flex xs6>
                        <div
                            class="tw-h-3 tw-bg-gray-200 tw-rounded-full tw-w-80 mb-2"
                        />
                        <div
                            class="tw-h-3 tw-bg-gray-200 tw-rounded-full tw-w-96 mb-2"
                        />
                    </v-flex>
                </v-layout>
            </v-container>
        </v-card-text>
        <v-divider />
        <v-card-actions class="tw-bg-primary-lighter">
            <v-layout>
                <v-flex xs2>
                    <!-- Working Time -->
                    <div class="tw-flex tw-gap-x-2 tw-items-center">
                        <v-icon color="primary">work_history</v-icon>
                        <div
                            class="tw-h-3 tw-bg-gray-200 tw-rounded-full tw-w-48"
                        />
                    </div>
                </v-flex>
                <v-flex
                    xs3
                    ml-2
                >
                    <!-- Execution Date -->
                    <div class="tw-flex tw-gap-x-2 tw-items-center">
                        <v-icon color="primary">date_range</v-icon>
                        <div
                            class="tw-h-3 tw-bg-gray-200 tw-rounded-full tw-w-48"
                        />
                    </div>
                </v-flex>
                <v-flex xs6>
                    <!-- Appointment Date -->
                    <div class="tw-flex tw-gap-x-2 tw-items-center">
                        <v-icon color="primary">person_pin_circle</v-icon>
                        <div
                            class="tw-h-3 tw-bg-gray-200 tw-rounded-full tw-w-48"
                        />
                    </div>
                </v-flex>
            </v-layout>
        </v-card-actions>
    </v-card>
</template>
