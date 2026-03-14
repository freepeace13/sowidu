<template>
    <v-container
        fill-height
        fluid
        pt-0
    >
        <AddressbookSidebar :pages="pages" />

        <portal-target
            name="toolbar"
            :slot-props="{ pages }"
        />

        <v-container
            fluid
            py-2
            px-0
            grid-list-md
            mt-0
            class="has-navbar-on-top"
        >
            <slot />
        </v-container>
    </v-container>
</template>
<script>
import '@/../css/views/addressbook.css'
import HasOwnTranslations from '@/Mixins/HasOwnTranslations'
import SharedData from '@/Mixins/SharedData'
import AddressbookSidebar from './Partials/AddressbookSidebar.vue'

export default {
    components: { AddressbookSidebar },

    mixins: [SharedData, HasOwnTranslations],

    props: {
        permissions: {
            required: true,
            type: Object,
        },
    },

    computed: {
        pages() {
            return [
                {
                    title: this.$tc('addressbook.labels.person'),
                    icon: 'person',
                    route: 'addressbooks.people.index',
                },
                {
                    title: this.$tc('addressbook.labels.organization', 2),
                    icon: 'business',
                    route: 'addressbooks.organizations.index',
                },
                {
                    title: this.$t('headings.trash'),
                    icon: 'folder_delete',
                    route: 'addressbooks.trashes.index',
                },
            ]
        },
    },
}
</script>
