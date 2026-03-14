<template>
    <Component :is="form"></Component>
</template>

<script>
    import axios from 'axios'
    import PrivateAccountForm from './Forms/PrivateAccountForm'
    import CompanyAccountForm from './Forms/CompanyAccountForm'

    export default {
        components: {
            PrivateAccountForm,
            CompanyAccountForm
        },

        computed: {
            form() {
                return this.$store.getters['auth/isOnPrivate']
                    ? 'PrivateAccountForm'
                    : 'CompanyAccountForm'
            }
        }
    }
</script>
