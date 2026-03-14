<script setup>
import AppAvatar from '@/Components/AppAvatar.vue'
import { computed } from 'vue'

const props = defineProps({
    offer: {
        required: true,
        type: Object,
    },
    editable: {
        required: false,
        type: Boolean,
        default: false,
    },
    deletable: {
        required: false,
        type: Boolean,
        default: false,
    },
})

defineEmits(['click:show-details', 'click:edit', 'click:delete'])

const recipient = computed(() => props.offer.recipient)
</script>
<template>
    <tr class="tw-align-start">
        <td>
            <a
                class="info--text hover:tw-underline tw-font-semibold"
                @click="$emit('click:show-details', offer.id)"
            >
                {{ offer?.internal_id ?? offer?.uuid }}
            </a>
        </td>
        <td>{{ offer.title }}</td>
        <td class="">
            <div class="tw-flex tw-items-center tw-gap-x-2">
                <AppAvatar :avatar="recipient.photo" />
                {{ recipient.name }}
            </div>
        </td>

        <td>{{ offer.type.name }}</td>
        <td>
            {{ offer.grand_total_formatted }}
        </td>
        <td>
            <v-chip
                :color="offer.status_metadata.color"
                label
                small
                :class="{
                    'white--text': !offer.status_metadata.is_draft,
                }"
            >
                {{ offer?.status_metadata.label }}
            </v-chip>
        </td>
        <td class="!tw-text-right !tw-flex tw-justify-end tw-items-center">
            <div class="tw-flex tw-items-center tw-justify-end tw-content-end">
                <v-btn
                    v-if="editable"
                    small
                    color="info"
                    flat
                    class="!tw-my-0 !tw-mx-0"
                    icon
                    @click="$emit('click:edit', offer)"
                >
                    <v-icon small>edit</v-icon>
                </v-btn>
                <v-btn
                    v-if="deletable"
                    small
                    flat
                    class="!tw-my-0 !tw-mx-0"
                    icon
                    color="error"
                    @click="$emit('click:delete', offer)"
                >
                    <v-icon small>delete</v-icon>
                </v-btn>
            </div>
        </td>
    </tr>
</template>
