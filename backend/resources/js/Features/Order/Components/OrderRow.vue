<template>
    <tr
        :style="{
            backgroundColor: order?.status?.color,
        }"
    >
        <td
            class="tw-flex tw-items-center tw-gap-x-2 hover:tw-underline tw-cursor-pointer"
            @click="
                $inertia.get(
                    $route('orders.show', {
                        order,
                    }),
                )
            "
        >
            <AppAvatar
                :avatar="from?.photo"
                :name="from?.name"
            />
            <div class="tw-flex tw-flex-col">
                <div class="tw-whitespace-nowrap tw-text-base">
                    {{ from?.name | nullSafe }}
                </div>
                <div class="tw-text-xs">
                    {{ from?.email | nullSafe }}
                </div>
            </div>
        </td>
        <td>{{ order.description }}</td>
        <td>
            <div class="tw-flex">
                <div>
                    {{
                        order.planned_start_date
                            | formatDate('DD.MM.YYYY hh:mm A', 'Not Set')
                    }}
                </div>
                <div class="mx-2">-</div>
                <div>
                    {{
                        order.planned_finish_date
                            | formatDate('DD.MM.YYYY hh:mm A', 'Not Set')
                    }}
                </div>
            </div>
        </td>
        <td>
            <v-badge
                color="red"
                overlap
                :value="order?.is_require_response"
            >
                <template #badge>
                    <span>!</span>
                </template>
                <v-chip
                    v-tooltip="`${order?.status?.text}`"
                    color="white"
                >
                    <v-icon :color="order?.status?.icon_color">
                        {{ order?.status?.icon }}
                    </v-icon>
                </v-chip>
            </v-badge>
        </td>
        <td class="tw-text-right">
            <v-icon
                small
                class="mr-2"
                @click="$emit('click:more', $event)"
            >
                more_horiz
            </v-icon>
        </td>
    </tr>
</template>
<script>
import AppAvatar from '@/Components/AppAvatar.vue'
import { nullSafe } from '@/Composables/useFilters'

export default {
    components: { AppAvatar },

    filters: {
        nullSafe: (value) => nullSafe(value),
    },

    props: {
        order: {
            type: Object,
            required: true,
        },
        from: {
            type: Object,
            required: true,
        },
    },
}
</script>
