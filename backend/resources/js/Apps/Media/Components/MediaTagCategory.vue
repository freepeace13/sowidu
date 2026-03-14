<template>
    <v-dialog
        v-model="isShow"
        max-width="450"
        persistent
    >
        <v-card>
            <v-card-title>
                <h3>{{ $t('media.labels.tag-to-category') }}</h3>
            </v-card-title>

            <v-card-text>
                <v-select
                    v-model="form.category"
                    :items="categories"
                    :label="$t('media.labels.select-category')"
                    outline
                    clearable
                    :disabled="form.processing"
                    :loading="form.processing"
                    :hide-details="!form.errors.category"
                    :error-messages="form.errors.category"
                />
            </v-card-text>

            <v-card-actions class="grey lighten-4">
                <v-spacer />

                <v-btn
                    flat
                    @click="close"
                >
                    {{ $t('buttons.cancel') }}
                </v-btn>
                <SubmitButton
                    v-show="isRemovingTag"
                    depressed
                    color="error"
                    :is-processing="form.processing"
                    @click="remove"
                >
                    {{ $t('buttons.remove_tag') }}
                </SubmitButton>

                <SubmitButton
                    v-show="!isRemovingTag"
                    depressed
                    :is-processing="form.processing"
                    @click="submit"
                >
                    {{ $t('buttons.tag') }}
                </SubmitButton>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import SubmitButton from '@components/Forms/SubmitButton.vue'

export default {
    components: { SubmitButton },
    props: {
        categories: {
            required: true,
            type: Array,
        },
    },
    data: (vm) => ({
        isShow: false,
        form: vm.$inertia.form({
            category: null,
        }),
        media: null,
        hasCategoryTag: false,
    }),
    computed: {
        isRemovingTag() {
            return this.hasCategoryTag && !this.form.category
        },
    },
    methods: {
        show({ uuid, category = null }) {
            if (!uuid) return
            this.media = uuid
            this.form.category = category
            if (category) {
                this.hasCategoryTag = true
            }
            this.isShow = true
        },

        remove() {
            this.$confirm.ask({
                title: 'Delete',
                question: 'Do you want to remove category tag on this file?',
                type: 'delete',
                confirm: () => {
                    this.$inertia.delete(
                        this.$route('media.tag_category.destroy', {
                            media: this.media,
                        }),
                        {
                            preserveState: true,
                            preserveScroll: true,
                            onSuccess: () => {
                                this.$root.$emit(
                                    'flash.success',
                                    'Category tag has been removed.',
                                )
                                this.$emit('refresh-media', this.media)
                                this.close()
                            },
                            onError: (errors) =>
                                this.$root.$emit('flash.validation', errors),
                        },
                    )
                },
            })
        },
        submit() {
            this.form.post(
                this.$route('media.tag_category.store', { media: this.media }),
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['errors'],
                    onSuccess: () => {
                        this.$root.$emit(
                            'flash.success',
                            'Category has been tagged this media.',
                        )
                        this.$emit('refresh-media', this.media)
                        this.close()
                    },
                    onError: (errors) =>
                        this.$root.$emit('flash.validation', errors),
                },
            )
        },
        close() {
            this.form.reset()
            this.media = null
            this.hasCategoryTag = false
            this.isShow = false
        },
    },
}
</script>
