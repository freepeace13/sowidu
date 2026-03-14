<script setup>
import useGlobalVariables from '@/Composables/useGlobalVariables'
import AddressFields from '@components/Fields/AddressFields.vue'
import { useForm } from '@inertiajs/vue2'
import { computed, ref } from 'vue'

defineExpose({ show })

const { $t, $route } = useGlobalVariables()

const form = useForm({
    address: {
        house_number: null,
        street: null,
        city: null,
        state: null,
        country: null,
        zipcode: null,
    },
})
const isShow = ref(false)

const completeAddress = computed(() => {
    const { country, zipcode, house_number, street, city, state } = form.address
    let countryName = country?.name

    return Object.values({
        street,
        house_number,
        zipcode,
        city,
        countryName,
        state,
    })
        .filter(Boolean)
        .join(', ')
})

const isEditing = computed(() => !!form.address?.id)

function show(address) {
    form.reset()

    if (address) {
        form.address = address
    }

    isShow.value = true
}

function close() {
    isShow.value = false
    reset()
}

function reset() {
    form.reset()
    form.clearErrors()
}

async function submit() {
    form.transform((data) => ({
        ...data,
        address: {
            ...data.address,
            country: data.address.country?.code,
        },
    }))

    if (isEditing.value) {
        console.log(
            'route',
            $route('app.settings.addresses.patch', {
                place: form.address.id,
            }),
        )

        form.patch(
            $route('app.settings.addresses.patch', {
                place: form.address.id,
            }),
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    close()
                },
            },
        )

        return
    }

    form.post($route('app.settings.addresses.store'), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            close()
        },
    })
}
</script>
<template>
    <v-dialog
        v-model="isShow"
        persistent
        max-width="600px"
        lazy
    >
        <v-card>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>
                    {{
                        isEditing
                            ? $t('app_settings.address.labels.update-address')
                            : $t('app_settings.address.labels.add_address')
                    }}
                </v-toolbar-title>
                <v-spacer />
                <v-btn
                    icon
                    @click="close"
                >
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>
            <v-divider />
            <v-card-text>
                <v-container
                    grid-list-lg
                    fluid
                    pa-2
                >
                    <v-layout column>
                        <v-flex>
                            <v-text-field
                                :loading="form.processing"
                                readonly
                                :label="
                                    $t(
                                        'app_settings.address.labels.complete-address-preview',
                                    )
                                "
                                outline
                                :hide-details="true"
                                :value="completeAddress"
                            />
                        </v-flex>

                        <v-flex xs12>
                            <AddressFields
                                v-model="form.address"
                                :is-loading="form.processing"
                                :errors="form.errors"
                            />
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-divider />
            <v-card-actions class="px-4">
                <v-btn
                    :disabled="form.processing"
                    color="secondary"
                    depressed
                    @click="close"
                >
                    {{ $t('buttons.cancel') }}
                </v-btn>
                <v-spacer />
                <v-btn
                    color="primary"
                    depressed
                    :loading="form.processing"
                    :disabled="form.processing"
                    @click="submit"
                >
                    {{
                        isEditing ? $t('buttons.update') : $t('buttons.create')
                    }}
                    <template #loader>
                        <span>{{
                            isEditing
                                ? $t('buttons.updating')
                                : $t('buttons.creating')
                        }}</span>
                    </template>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
