<template>
    <v-container
        fluid
        grid-list-lg
        fill-height
    >
        <v-layout
            column
            align-center
            justify-center
            fill-height
        >
            <v-card width="380">
                <v-card-text>
                    <form @submit.prevent="lockScreenStop">
                        <v-layout column>
                            <v-flex align-self-center>
                                <v-avatar size="130">
                                    <v-img :src="photo" />
                                </v-avatar>
                                <h2 class="my-2 text-xs-center primary--text">
                                    {{ name }}
                                </h2>
                            </v-flex>
                            <v-flex>
                                <input-text-field
                                    v-model="form.password"
                                    :label="$t('labels.inputs.password')"
                                    type="password"
                                    :hide-details="!form.errors.password"
                                    :error-messages="form.errors.password"
                                />
                            </v-flex>

                            <v-flex>
                                <v-btn
                                    block
                                    type="submit"
                                    color="primary"
                                    :loading="form.processing"
                                    :disabled="form.processing"
                                    @click="lockScreenStop"
                                >
                                    {{ $t('buttons.unlock') }}
                                    <template #loader>
                                        <span>Unlocking...</span>
                                    </template>
                                </v-btn>
                            </v-flex>
                        </v-layout>
                    </form>
                </v-card-text>
            </v-card>
        </v-layout>
    </v-container>
</template>

<script>
import InputTextField from '@components/InputTextField.vue'

export default {
    components: {
        InputTextField,
    },

    props: {
        name: {
            type: String,
        },
        photo: {
            type: String,
        },
    },

    data: (vm) => ({
        form: vm.$inertia.form({
            password: null,
        }),
    }),

    methods: {
        lockScreenStop() {
            this.form.post(this.$route('lockscreen.deactivate'))
        },
    },
}
</script>
