<template>
    <router-view></router-view>
</template>

<script>
import DispatchWhenTokenChanges from '@common/mixins/DispatchWhenTokenChanges';
import IndexSidebarWidget from '../widgets/IndexSidebar.vue';

export default {
    name: 'OrderListView',

    // mixins: [DispatchWhenTokenChanges('order/all')],

    components: {
        IndexSidebarWidget,
    },
}
</script>
