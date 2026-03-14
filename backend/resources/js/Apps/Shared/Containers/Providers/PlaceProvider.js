
export default {
    name: 'PlaceProvider',

    data: () => ({
        items: [],
    }),

    render() {
        return this.$scopedSlots.default({
            items: this.items,
        });
    },
}