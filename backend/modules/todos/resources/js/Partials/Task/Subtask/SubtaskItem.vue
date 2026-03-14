<template>
    <v-card
        tile
        flat
        color="white"
        class="subtask-item tw-cursor-pointer"
    >
        <v-card-text class="tw-flex tw-items-center tw-gap-x-2 tw-p-3">
            <v-icon color="blue">task</v-icon>
            <div
                class="w-full tw-flex tw-items-center"
                @mouseenter="title.showEditIcon = true"
                @mouseleave="title.showEditIcon = false"
            >
                <div
                    v-show="!title.editing"
                    class="tw-font-bold tw-capitalize hover:tw-underline"
                    @click="
                        () => {
                            taskViewerRef.$parent.closeModal()
                            taskViewerRef.$parent.open(subtask.id)
                        }
                    "
                >
                    {{ subtask.title }}
                </div>
                <v-btn
                    v-show="title.showEditIcon && !title.editing"
                    color="info"
                    icon
                    small
                    class="ml-4 x-small"
                    @click="
                        () => {
                            title.editing = true
                            $nextTick(() => {
                                $refs.subtaskTitleInput.focus()
                            })
                        }
                    "
                >
                    <v-icon small>edit</v-icon>
                </v-btn>
                <div v-show="title.editing">
                    <v-text-field
                        ref="subtaskTitleInput"
                        v-model="form.title"
                        :value="form.title"
                        :outline="title.editing"
                        :loading="form.processing"
                        :disabled="form.processing"
                        solo
                        tabindex="0"
                        class="font-weight-bold small"
                        hide-details
                        @focus="title.editing = true"
                        @focusout="submit"
                        @keyup.enter="submit"
                    />
                </div>
            </div>

            <v-spacer />

            <div class="tw-flex tw-items-center">
                <v-btn
                    v-tooltip.top="'Add member'"
                    color="grey"
                    icon
                    dark
                    small
                    class="ma-0 x-small"
                    @click="
                        (e) =>
                            taskMembersMenuRef.show(e, subtask, {
                                reload: ['subtasks'],
                            })
                    "
                >
                    <v-icon
                        dark
                        small
                        >person_add</v-icon
                    >
                </v-btn>
                <div
                    class="tw-flex -tw-space-x-1 tw-overflow-hidden tw-mr-2 tw-items-center tw-h-full px-1"
                >
                    <Subscriber
                        v-for="member in subtask.members"
                        :key="member.id"
                        class="pa-0"
                        :size="8"
                        :avatar="member?.user.photo"
                        :name="member?.user.name"
                    />
                </div>
                <div>
                    <v-btn
                        color="primary"
                        small
                        :loading="form.processing"
                        :disabled="form.processing"
                        @click="(e) => subtaskGroupMenuRef.show(e, subtask)"
                    >
                        {{ subtask.group }}
                        <v-icon>expand_more</v-icon>
                    </v-btn>
                </div>
            </div>
        </v-card-text>
    </v-card>
</template>
<script>
import useParentFinder from '@/Composables/useParentFinder'
import Subscriber from '../../Subscriber/Subscriber.vue'
import { socketIdHeader } from '@/Composables/useSocketId'

export default {
    components: { Subscriber },

    props: {
        subtask: {
            type: Object,
            default: () => ({
                members: [],
            }),
        },
    },

    data: (vm) => ({
        form: vm.$inertia.form({
            title: vm.subtask.title,
        }),
        title: {
            editing: false,
            showEditIcon: false,
        },
    }),

    computed: {
        subtaskGroupMenuRef() {
            return useParentFinder(this, 'subtaskGroupMenu')
        },

        taskViewerRef() {
            return useParentFinder(this.$parent, 'taskViewerModal')
        },

        taskMembersMenuRef() {
            return useParentFinder(this.$parent, 'taskMembersMenu')
        },
    },

    methods: {
        submit() {
            const { board } = this.$page.props
            const task = this.subtask
            this.form.put(
                this.$route('todos.boards.tasks.update', { board, task }),
                {
                    ...socketIdHeader,
                    preserveState: true,
                    preserveScroll: true,
                    errorBag: 'updateBoardTask',
                    only: ['groups', 'errors', 'subtasks'],
                    onSuccess: () => {
                        this.$root.$emit(
                            'flash.success',
                            'Sub task title has been updated.',
                        )
                    },
                    onError: (errors) => {
                        this.$root.$emit('flash.validation', errors)
                    },
                    onFinish: () => {
                        this.title.editing = false
                        this.title.showEditIcon = false
                        this.form.reset()
                    },
                },
            )
        },
    },
}
</script>
