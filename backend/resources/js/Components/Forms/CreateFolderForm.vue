<template>
    <v-dialog max-width="450" persistent :value="show">
        <v-card>
            <v-card-title class="title">
                {{ $t('headings.new-folder') }}
            </v-card-title>

            <v-card-text>
                <input-text-field
                    ref="input"
                    v-model="form.name"
                    single-line autofocus
                    @focus="$event.target.select()"
                />
            </v-card-text>

            <v-card-actions class="grey lighten-4">
                <v-spacer />

                <v-btn flat @click="close">
                    {{ $t('buttons.cancel') }}
                </v-btn>

                <v-btn
                    color="primary"
                    depressed
                    @click="submit"
                >
                    {{ $t('buttons.create') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import InputTextField from '../InputTextField.vue';

export default {
    components: {
        InputTextField,
    },

    data() {
        return {
            show: false,
            parent: null,
            form: this.$inertia.form({
                name: 'Untitled folder',
            }),
        };
    },

    methods: {
        submit() {
            this.form.post(this.$route('media.folders.store', { folder: this.parent }), {
                errorBag: 'createFolder',
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => this.$emit('success'),
            });
        },

        start(parent = null) {
            this.parent = parent;
            this.show = true;

            this.$refs.input.$el.focus();
        },

        close() {
            this.form.name = 'Untitled folder';

            this.parent = null;
            this.show = false;
        },
    },
};
</script>
