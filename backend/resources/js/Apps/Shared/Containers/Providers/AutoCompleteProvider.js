
export default {
    name: 'AutoCompleteProvider',

    service: null,

    data: () => ({
        items: [],
        loading: false,
    }),

    methods: {
        search(text, size) {
            if (!text) return;

            this.items = [];
            this.loading = true;

            if (typeof this.$options.service === 'function') {
                this.$options.service((fn) => {
                    fn(text, size)
                        .then((response) => {
                            this.items = response.data;
                        })
                        .catch(console.error)
                        .finally(() => {
                            this.loading = false;
                        });
                });
            }
        },
    },

    render() {
        return this.$scopedSlots.default({
            items: this.items,
            loading: this.loading,
            search: this.search,
        });
    },
}