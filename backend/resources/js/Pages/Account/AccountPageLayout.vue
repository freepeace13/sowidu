<template>
    <v-container
        fluid
        fill-height
        pa-0
        :py-2="$vuetify.breakpoint.xs"
        class="white"
    >
        <AccountNavigationDrawer
            v-bind="$page.props"
            :user="user"
        />
        <v-container
            fluid
            grid-list-lg
            fill-height
            py-0
        >
            <v-layout align-start>
                <v-flex
                    grow
                    align-self-start
                    py-0
                >
                    <slot />
                </v-flex>
            </v-layout>
        </v-container>
        <CategoryForm ref="categoryForm" />
    </v-container>
</template>

<script>
import AccountNavigationDrawer from '@/Layouts/Partials/AccountNavigationDrawer.vue'
import HasOwnTranslations from '@/Mixins/HasOwnTranslations'
import SharedData from '@/Mixins/SharedData'
import HandlesImpersonations from '../../Mixins/HandlesImpersonations'
import CategoryForm from './Partials/CategoryForm.vue'

export default {
    components: { AccountNavigationDrawer, CategoryForm },

    mixins: [HandlesImpersonations, SharedData, HasOwnTranslations],

    mounted() {
        this.$root.$on('category-form.show', this.showCategoryForm)
    },

    beforeDestroy() {
        this.$root.$off('category-form.show', this.showCategoryForm)
    },

    methods: {
        showCategoryForm(category = null) {
            this.$refs.categoryForm.show(category)
        },
    },
}
</script>
