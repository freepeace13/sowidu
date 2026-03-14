<template>
    <v-dialog
        max-width="450"
        persistent
        :value="show"
    >
        <v-card>
            <v-card-title>
                <h3>{{ $t('headings.rename-media') }}</h3>
            </v-card-title>

            <v-card-text>
                <input-text-field
                    ref="input"
                    v-model="form.name"
                    single-line
                    autofocus
                    @focus="$event.target.select()"
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

                <v-btn
                    color="primary"
                    depressed
                    @click="submit"
                >
                    {{ $t('buttons.rename') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import Http from '@/Modules/Http'
import InputTextField from '../../../Components/InputTextField.vue'

export default {
    components: {
        InputTextField,
    },

    data() {
        return {
            show: false,
            media: null,
            form: this.$inertia.form({
                name: null,
            }),
        }
    },

    watch: {
        show: {
            immediate: true,
            handler() {
                if (!this.show) return

                this.$nextTick(() => {
                    this.$refs.input.$el.querySelector('input').focus()
                })
            },
        },
    },

    methods: {
        start(media) {
            Http.get(this.$route('media.show', { media }))
                .then((response) => {
                    this.media = response.data.file
                    this.form.name = response.data.file.name
                    this.show = true
                })
                .catch(console.error)
        },

        submit() {
            this.form.put(
                this.$route('media.rename', { media: this.media.uuid }),
                {
                    errorBag: 'renameMedia',
                    preserveScroll: true,
                    onSuccess: () => this.$emit('success', this.media.uuid),
                },
            )
        },

        close() {
            this.form.name = null
            this.media = null
            this.show = false
        },
    },
}
</script>
