import { mapState } from 'vuex';
import { isFunction, isNullOrUndefined, isArray, isObject } from '~/support/helpers';

export default () => ({
    computed: {
        ...mapState({
            order: (state) => state.order.sketch.sheet,
            orderCopy: (state) => state.order.sketch.original,
            errors: (state) => state.order.sketch.errors
        }),

        isReadOnly() {
            if (this.order.contractor && isFunction(this.order.contractor.equals)) {
                return !this.order.contractor.equals(
                    this.$store.getters['auth/profile']()
                );
            }

            if (this.order.customer && isFunction(this.order.customer.billerIs)) {
                return this.order.customer.billerIs(
                    this.$store.getters['auth/profile']()
                );
            }

            return true;
        }
    }
})