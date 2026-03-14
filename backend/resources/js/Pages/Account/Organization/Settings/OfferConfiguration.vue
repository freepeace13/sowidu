<script>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import { useForm } from '@inertiajs/vue2'
import AccountPageLayout from '@/Pages/Account/AccountPageLayout.vue'

export default {
    layout: [AuthLayout, AccountPageLayout],
}
</script>
<script setup>
import SubmitButton from '@/Components/Forms/SubmitButton.vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'

const props = defineProps({
    configuration: {
        required: true,
        type: Object,
    },
})

const { $t, $route } = useGlobalVariables()

const form = useForm({
    terms_and_conditions: props.configuration.terms_and_conditions,
})

function save() {
    form.patch($route('account.settings.offer-configuration.update'))
}
</script>
<template>
    <div>
        <div class="mb-5">
            <v-layout
                align-center
                justify-space-between
            >
                <v-flex class="headline shrink font-weight-bold">
                    {{ $t('account.labels.offer-configuration') }}
                </v-flex>
            </v-layout>
            <v-divider class="mb-3" />
            <v-layout
                row
                wrap
            >
                <v-flex
                    xs12
                    class="subheading"
                >
                    {{
                        $t('account.offer-configuration.labels.default-values')
                    }}
                    <v-divider />
                </v-flex>
                <v-flex
                    sm12
                    xs12
                >
                    <v-textarea
                        v-model="form.terms_and_conditions"
                        :label="
                            $t(
                                'account.offer-configuration.labels.terms-and-conditions',
                            )
                        "
                        :disabled="form.processing"
                        :loading="form.processing"
                        :error-messages="form.errors.terms_and_conditions"
                        :hide-details="!form.errors.terms_and_conditions"
                        outline
                    />
                </v-flex>
                <v-flex
                    sm6
                    xs12
                />
            </v-layout>
            <v-flex mt-3>
                <v-layout>
                    <v-flex
                        pa-0
                        xs12
                        align-self-center
                        class="tw-flex tw-justify-end"
                    >
                        <SubmitButton
                            :is-processing="form.processing"
                            @click="save"
                        >
                            {{ $t('buttons.save-changes') }}
                        </SubmitButton>
                    </v-flex>
                </v-layout>
            </v-flex>
        </div>
    </div>
</template>
<style scoped lang="scss">
.append-outer {
    .v-input__append-outer {
        @apply tw-mt-0;
    }
}
</style>
