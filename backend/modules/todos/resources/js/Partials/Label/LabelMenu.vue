<template>
    <v-menu
        v-model="isShow"
        min-width="300"
        max-width="300"
        :position-x="x"
        :position-y="y"
        absolute
        :nudge-left="150"
        :close-on-content-click="false"
    >
        <!-- Label List -->
        <v-card>
            <v-toolbar
                dense
                flat
                card
            >
                <v-icon
                    v-show="isShowForm"
                    class="mr-2"
                    @click="$refs.labelForm.close()"
                >
                    navigate_before
                </v-icon>
                <v-toolbar-title class="body-1 ml-0">
                    {{ toolbarTitle }}
                </v-toolbar-title>
                <v-spacer />
                <v-btn
                    icon
                    @click="isShow = false"
                >
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>
            <v-divider />
            <div v-show="!isShowForm">
                <v-container
                    grid-list-xs
                    pt-0
                    pb-2
                >
                    <v-layout
                        row
                        wrap
                    >
                        <v-flex>
                            <v-text-field
                                placeholder="Search label"
                                prepend-inner-icons="search"
                                hide-details
                            />
                        </v-flex>
                    </v-layout>
                </v-container>
                <v-list
                    subheader
                    class="pb-0"
                >
                    <v-subheader class="px-4">
                        {{ $t('hints.click-to-attach-label-to-this-task.') }}
                    </v-subheader>
                </v-list>
                <v-list
                    subheader
                    class="labels-list"
                >
                    <v-list-tile
                        v-for="label in labels"
                        :key="label.id"
                        ripple
                        :class="['pl-2', { disabled: form.processing }]"
                    >
                        <v-list-tile-content @click="toggleLabel(label.id)">
                            <TaskLabel
                                :label="label"
                                :selected="labelIsSelected(label.id)"
                                align="right"
                            />
                        </v-list-tile-content>
                        <v-list-tile-action>
                            <v-btn
                                small
                                icon
                                @click="openLabelForm(label)"
                            >
                                <v-icon small>edit</v-icon>
                            </v-btn>
                        </v-list-tile-action>
                    </v-list-tile>
                </v-list>
                <v-divider class="mt-2" />
                <v-list class="mt-2">
                    <v-list-tile class="px-2 mb-2">
                        <v-list-tile-content>
                            <v-btn
                                color="primary"
                                block
                                small
                                depressed
                                @click="openLabelForm()"
                            >
                                Create New Label
                            </v-btn>
                        </v-list-tile-content>
                    </v-list-tile>
                </v-list>
                <v-divider />
                <v-card-actions>
                    <v-spacer />
                    <v-btn
                        flat
                        @click="isShow = false"
                        >Close</v-btn
                    >
                </v-card-actions>
            </div>
            <!-- Label Form -->
            <LabelForm
                ref="labelForm"
                :available-colors="settings?.labels_available_colors"
                @display="(isShow) => (isShowForm = isShow)"
            />
        </v-card>
    </v-menu>
</template>
<script>
import TaskLabel from './TaskLabel.vue'
import LabelForm from './LabelForm.vue'

export default {
    components: { TaskLabel, LabelForm },

    props: {
        settings: {
            type: Object,
            required: false,
        },
        taskLabels: {
            required: false,
            type: Array,
        },
        labels: {
            required: false,
            type: Array,
        },
    },

    data: (vm) => ({
        isShow: false,
        x: 0,
        y: 0,
        isShowForm: false,
        isCreate: true,
        form: vm.$inertia.form({}),
    }),

    computed: {
        toolbarTitle() {
            const isShow = this.isShowForm
            const isCreate = this.isCreate
            return isShow && isCreate
                ? 'Create Label'
                : isShow && !isCreate
                ? 'Update Label'
                : 'Labels'
        },
    },

    watch: {
        isShow(val) {
            if (val) {
                this.$refs.labelForm.close()
            }
        },
    },

    methods: {
        show(e) {
            e.preventDefault()
            this.isShow = false
            this.x = e.clientX
            this.y = e.clientY

            this.$nextTick(() => {
                this.isShow = true
            })
        },

        toggleLabel(label) {
            const { board, task } = this.$page.props
            const isDeleting = this.labelIsSelected(label)
            const method = isDeleting ? 'delete' : 'post'
            const baseRoute = 'todos.boards.tasks.labels'
            const route = isDeleting
                ? this.$route(`${baseRoute}.destroy`, { board, task, label })
                : this.$route(`${baseRoute}.store`, { board, task })

            this.form.transform((data) => ({
                ...data,
                label,
            }))
            this.form[method](route, {
                preserveScroll: true,
                only: ['taskLabels', 'errors'],
                onSuccess: () => {
                    this.$root.$emit(
                        'flash.success',
                        'Label has been added to this task.',
                    )
                },
                onError: (errors) =>
                    this.$root.$emit('flash.validation', errors),
            })
        },

        labelIsSelected(labelId) {
            return this.taskLabels?.some(
                (taskLabel) => taskLabel.label_id == labelId,
            )
        },

        openLabelForm(label = null) {
            this.$refs.labelForm.show(label)
            this.isCreate = true
            if (label) this.isCreate = false
        },

        closeLabelForm() {
            this.$refs.labelForm.close()
        },
    },
}
</script>
<style scoped>
.labels-list {
    overflow-y: auto;
    max-height: 200px;
}
</style>
