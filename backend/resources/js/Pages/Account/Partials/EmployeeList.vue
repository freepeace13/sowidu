<script setup>
import { getPageProps } from '@/Composables/useUtils'
import { computed } from 'vue'

defineProps({
    employee: {
        required: true,
        type: Object,
    },
})

defineEmits(['click:manage-roles', 'click:manage-rates'])

const user = computed(() => getPageProps('user'))
</script>
<template>
    <div>
        <v-list-tile
            :key="employee.id"
            avatar
        >
            <v-list-tile-avatar>
                <v-avatar size="35">
                    <v-img :src="employee.photo" />
                </v-avatar>
            </v-list-tile-avatar>

            <v-list-tile-content class="tw-relative">
                <v-list-tile-title class="tw-capitalize">
                    {{ employee.name }}
                    <span class="tw-text-sm tw-italic">
                        ({{ employee.roles.join(', ') }})
                    </span>
                </v-list-tile-title>
                <v-list-tile-sub-title class="tw-text-xs">
                    {{ employee.email }}
                </v-list-tile-sub-title>
                <div class="tw-absolute tw-right-0">
                    <v-btn
                        flat
                        color="success"
                        class="!tw-normal-case"
                        @click="$emit('click:manage-rates', employee)"
                    >
                        <!-- <v-icon class="tw-mr-2">paid</v-icon> -->
                        <div
                            v-show="!employee?.rate"
                            class="tw-text-error tw-italic"
                        >
                            {{ $t('hints.not-set') }}
                        </div>
                        <div
                            v-show="employee?.rate"
                            class="tw-flex tw-font-semibold"
                        >
                            <div>{{ employee.rate?.symbol }}</div>
                            <div>{{ employee.rate?.rate }}</div>
                        </div>
                    </v-btn>
                </div>
            </v-list-tile-content>

            <v-list-tile-action>
                <v-menu
                    offset-y
                    :disabled="!user.can['update settings']"
                >
                    <template #activator="{ on }">
                        <v-btn
                            icon
                            :disabled="!user.can['update settings']"
                            v-on="on"
                        >
                            <v-icon>settings</v-icon>
                        </v-btn>
                    </template>

                    <v-card width="280">
                        <v-list class="py-0">
                            <v-list-tile
                                v-if="!employee.is_owner"
                                @click.stop="
                                    $emit('click:manage-roles', employee)
                                "
                            >
                                <v-list-tile-avatar>
                                    <v-icon>vpn_key</v-icon>
                                </v-list-tile-avatar>
                                <v-list-tile-content>
                                    {{ $t('buttons.manage-roles') }}
                                </v-list-tile-content>
                            </v-list-tile>
                            <v-list-tile
                                @click.stop="
                                    $emit('click:manage-rates', employee)
                                "
                            >
                                <v-list-tile-avatar>
                                    <v-icon>currency_exchange</v-icon>
                                </v-list-tile-avatar>
                                <v-list-tile-content>
                                    {{
                                        $t(
                                            'account.employees.labels.manage-rates',
                                        )
                                    }}
                                </v-list-tile-content>
                            </v-list-tile>
                        </v-list>
                    </v-card>
                </v-menu>
            </v-list-tile-action>
        </v-list-tile>
        <v-divider inset />
    </div>
</template>
