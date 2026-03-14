<template>
    <div class="">
        <FilterBar :filters="filters"
                   :filter-value="filterKey"
                   :search-value="searchKey"
                   @search="search"
                   @filter="fetchResults" />
        <v-layout row wrap class="mt-1">
            <EmptyMessage v-if="!results.length" message="No results found." />
            <v-flex v-else x12 sm6 md4 lg3
                    v-for="(result, key) in results"
                    :key="key"
                    class="pb-3 pr-3">
                <ContactCard :contact="result" :source="result.original_info" />
            </v-flex>
        </v-layout>
    </div>

</template>

<script>

    import axios from 'axios'
    import { mapGetters } from 'vuex'
    import { merge } from 'lodash'
    import FilterBar from '~/components/UI/contact/FilterBar'

    export default {
        components: { FilterBar },

        data: () => ({
            results: [],
            filters: [
                { value: 'all', color: 'white', text: 'All' },
                { value: 'active', color: 'green', text: 'Active' },
                { value: 'private', color: 'blue', text: 'Private' },
                { value: 'company', color: 'teal', text: 'Company' },
                { value: 'employee', color: 'purple', text: 'Employee' }
            ],
            filterKey: 'all',
            searchKey: ''
        }),

        computed: {
            ...mapGetters('auth/user', ['account'])
        },

        methods: {
            async fetchResults(filter = 'all') {
                try {
                    let q = this.$route.query.q
                    let params = { q }

                    if (filter == 'active') params = merge(params, { online: true })
                    else if (filter == 'private') params = merge(params, { type: 'users' })
                    else if (filter == 'company') params = merge(params, { type: 'companies' })
                    else if (filter == 'employee') params = merge(params, { type: 'employees' })

                    this.filterKey = filter
                    this.searchKey = q
                    this.$router.push({ query: { q, filter } }).catch(err => {})

                    const { data } = await axios.get('/api/contacts/search', {
                        params
                    })

                    this.results = data.data
                } catch (e) {}
            },

            search(key) {
                this.$router.push({ query: { q: key, filter:  this.$route.query.filter } }).catch(err => {})
                this.fetchResults(this.$route.query.filter)
            }
        },

        mounted() {
            this.fetchResults()
        }
    }

</script>
