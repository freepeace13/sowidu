<template>
    <v-toolbar
        absolute
        top
        flat
        color="white"
    >
        <v-toolbar-title>
            <v-avatar
                :size="$vuetify.breakpoint.mdAndUp ? 30 : 26"
                class="mr-2"
                tile
            >
                <v-img
                    :src="photo"
                    :lazy-src="photo"
                />
                <!-- <v-icon v-else>business</v-icon> -->
            </v-avatar>
            {{ name }}
        </v-toolbar-title>
        <v-spacer />
        <v-btn
            outline
            color="error"
            @click="$emit('click:delete')"
        >
            {{ $t('buttons.delete') }}
        </v-btn>
    </v-toolbar>
</template>
<script>
export default {
    props: {
        photo: {
            required: true,
            type: String,
        },
        name: {
            required: true,
            type: String,
        },
    },

    methods: {
        destroy() {
            this.$confirm.ask({
                title: 'Delete',
                question: 'Do you want to delete this contact?',
                type: 'delete',
                confirm: () => {
                    this.$inertia.delete(
                        this.$route('contacts.organizations.destroy', {
                            organization: this.contact,
                        }),
                        {
                            preserveState: true,
                            onSuccess: () => {
                                this.$inertia.visit(
                                    this.$route('contacts.organizations.index'),
                                )
                                this.$root.$emit(
                                    'flash.success',
                                    'Contact has been deleted.',
                                )
                            },
                        },
                    )
                },
            })
        },
    },
}
</script>
