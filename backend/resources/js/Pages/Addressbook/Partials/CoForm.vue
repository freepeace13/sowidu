<script setup>
import { computed, ref } from 'vue'
import { useForm } from '@inertiajs/vue2'
import useGlobalVariables from '@/Composables/useGlobalVariables'

const props = defineProps({
    title: {
        type: String,
    },
    members: {
        type: Array,
    },
    addressbook: {
        type: Object,
    },
})

const form = useForm({
    cos: props.addressbook.careOfs,
})
const show = ref(false)
const { $route } = useGlobalVariables()
const toggle = () => (show.value = !show.value)

const save = () => {
    form.transform((data) => ({ cos: data.cos.map((co) => co.id) })).put(
        $route('addressbooks.careof.update', {
            addressbook: props.addressbook.id,
        }),
    )
    toggle()
}
defineExpose({
    toggle,
})

const options = computed(() =>
    props.members.map((member) => ({
        name: member.column_values.name,
        id: member.id,
        photo: member.column_values.photo,
    })),
)
</script>
<template>
    <v-dialog
        v-model="show"
        persistent
        max-width="600px"
    >
        <v-card>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>
                    {{ title }}
                </v-toolbar-title>
                <v-spacer />
                <v-btn
                    icon
                    @click="toggle"
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
                        <v-combobox
                            v-model="form.cos"
                            item-value="id"
                            item-text="name"
                            item-avatar="photo"
                            :items="options"
                            value="id"
                            multiple
                            outline
                            required
                            chips
                            solo
                            :hide-details="false"
                            placeholder="Select or search for C/O"
                            color="primary"
                        />
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-divider />
            <v-card-actions class="px-4">
                <v-btn
                    :disabled="form.processing"
                    color="secondary"
                    depressed
                    @click="toggle"
                >
                    {{ $t('buttons.cancel') }}
                </v-btn>
                <v-spacer />
                <v-btn
                    color="primary"
                    depressed
                    :disabled="form.processing"
                    :loading="form.processing"
                    @click="save"
                >
                    {{ $t('buttons.save') }}
                    <template #loader>
                        <span>{{ $t('buttons.saving') }}</span>
                    </template>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
