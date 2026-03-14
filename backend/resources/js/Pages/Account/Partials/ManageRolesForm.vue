<template>
    <v-dialog
        v-model="isShow"
        max-width="650"
    >
        <v-card>
            <v-card-title class="title">
                {{ $t('buttons.manage-roles') }}
                <v-spacer />
                <v-btn
                    color="primary"
                    @click="$refs.form.show()"
                >
                    {{ $t('buttons.create') }} {{ $t('labels.inputs.role') }}
                </v-btn>
            </v-card-title>

            <v-card-text>
                <v-data-table
                    :headers="headers"
                    :items="roles"
                    :hide-actions="true"
                >
                    <template #items="{ item }">
                        <td>{{ item }}</td>
                        <td>
                            <v-icon
                                small
                                class="mr-2"
                                @click="$refs.form.show(item)"
                            >
                                edit
                            </v-icon>
                        </td>
                    </template>
                </v-data-table>
            </v-card-text>

            <v-card-actions>
                <v-spacer />

                <v-btn @click="close">
                    {{ $t('buttons.close') }}
                </v-btn>
            </v-card-actions>
        </v-card>
        <RoleForm ref="form" />
    </v-dialog>
</template>
<script>
import RoleForm from './RoleForm.vue'

export default {
    components: { RoleForm },
    props: {
        roles: {
            required: true,
            type: Array,
        },
    },
    data: () => ({
        isShow: false,
    }),

    computed: {
        headers() {
            return [
                {
                    text: this.$t('labels.inputs.name'),
                    align: 'left',
                    sortable: true,
                    value: 'name',
                },
                {
                    text: this.$t('labels.actions'),
                    value: 'name',
                    sortable: false,
                },
            ]
        },
    },

    methods: {
        show() {
            this.isShow = true
        },

        close() {
            this.isShow = false
        },
    },
}
</script>
