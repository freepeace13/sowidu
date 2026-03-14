<template>
    <router-view></router-view>
</template>

<script>
import DispatchWhenTokenChanges from '@common/mixins/DispatchWhenTokenChanges';

export default {
    name: 'IndexView',

    mixins: [
        DispatchWhenTokenChanges('contact/all')
    ]
}
</script>