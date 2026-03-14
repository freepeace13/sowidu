<template>
    <component :is="$store.state.ui.layout"></component>
</template>

<script>
import Auth from '@common/layouts/Auth';
import Guest from '@common/layouts/Guest';

export default {
    name: 'Application',

    components: {
        auth: Auth,
        guest: Guest,
    }
}
</script>
