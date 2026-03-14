<script setup>
import { computed, nextTick, ref, watch } from 'vue'
import RoleForm from './RoleForm.vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { useForm, router, usePage } from '@inertiajs/vue2'
import SubmitButton from '@/Components/Forms/SubmitButton.vue'
import { getPageProps } from '@/Composables/useUtils'

defineExpose({ show })

const { $t, $route } = useGlobalVariables()

const isShow = ref(false)
const isLoading = ref(false)
const rateForm = useForm({
    rate: null,
})

const employee = computed(() => getPageProps('employee'))
const companyCurrency = computed(() => getPageProps('currency'))

watch(employee, (newVal) => {
    const rate = newVal?.rate

    rateForm.rate = rate?.rate
})

function show({ id: employee }) {
    // Check auth user if it has permission to manage rates
    const user = usePage().props.user
    if (!user?.can['can manage employee rates']) {
        alert($t('validation.403'))
        return
    }

    if (!employee) {
        return
    }

    isShow.value = true

    router.reload({
        data: { employee },
        only: ['employee'],
        onBefore: () => {
            isLoading.value = true
        },
        onFinish: () => {
            nextTick(() => {
                isLoading.value = false
            })
        },
    })
}

function close() {
    router.get($route('account.employees.index'), {
        onFinish: () => {
            rateForm.reset()
            isShow.value = false
        },
    })
}

function submit() {
    rateForm.post($route('account.employees.rates.store', employee.value.id), {
        preserveScroll: true,
        preserveState: true,
        only: ['employee'],
        onSuccess: () => {
            close()
        },
    })
}
</script>
<template>
    <v-dialog
        v-model="isShow"
        max-width="650"
    >
        <v-card>
            <v-card-title class="title">
                {{ $t('account.employees.labels.manage-rates') }}
            </v-card-title>

            <v-card-text>
                <v-layout
                    row
                    wrap
                >
                    <v-flex xs12>
                        <v-list-tile
                            px-0
                            class="tile px-0"
                        >
                            <v-list-tile-avatar>
                                <v-avatar>
                                    <v-img
                                        :src="employee?.photo"
                                        :lazy-src="employee?.photo"
                                    />
                                </v-avatar>
                            </v-list-tile-avatar>
                            <v-list-tile-content>
                                <v-list-tile-title class="">
                                    {{ employee?.name }}

                                    <span class="ml-2 tw-text-base">
                                        Role: {{ employee?.roles.join(', ') }}
                                    </span>
                                </v-list-tile-title>
                                <v-list-tile-sub-title class="caption">
                                    {{ employee?.user.email }}
                                </v-list-tile-sub-title>
                            </v-list-tile-content>
                            <v-list-tile-action>
                                <v-layout
                                    row
                                    wrap
                                    justify-end
                                >
                                    <v-flex xs5>
                                        <v-text-field
                                            v-model="rateForm.rate"
                                            outline
                                            full-width
                                            :items="[]"
                                            :loading="rateForm.processing"
                                            :error-messages="
                                                rateForm.errors.rate
                                            "
                                            :hide-details="
                                                !rateForm.errors.rate
                                            "
                                            label="Rate"
                                            solo
                                            class="required-input small"
                                            required
                                        >
                                            <template #prepend-inner>
                                                <div>
                                                    {{
                                                        companyCurrency?.symbol ??
                                                        '--'
                                                    }}
                                                </div>
                                            </template>
                                        </v-text-field>
                                    </v-flex>
                                </v-layout>
                            </v-list-tile-action>
                        </v-list-tile>
                    </v-flex>
                </v-layout>
            </v-card-text>

            <v-card-actions>
                <v-spacer />
                <v-btn @click="close">
                    {{ $t('buttons.close') }}
                </v-btn>
                <SubmitButton
                    :is-processing="rateForm.processing"
                    @click="submit"
                >
                    {{ $t('buttons.save') }}
                </SubmitButton>
            </v-card-actions>
        </v-card>
        <RoleForm ref="form" />
    </v-dialog>
</template>
