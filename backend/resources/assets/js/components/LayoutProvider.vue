<script>
import Auth from './LayoutAuth';
import Guest from './LayoutGuest';
import { mapState } from 'vuex';

export default {
    computed: mapState({
        layout: (state) => state.ui.layout,
    }),

    render() {
        const layouts = {
            [Auth.name.toLowerCase()]: Auth,
            [Guest.name.toLowerCase()]: Guest
        }

        return this.$scopedSlots.default({
            current: layouts[this.layout]
        });
    }
}
</script>