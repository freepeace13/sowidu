<template>
    <form :action="url" :method="method">
        <input type="hidden" name="_token" :value="$csrftoken" />
        <slot :errors="$errors"></slot>
    </form>
</template>

<script>
import MessageBag from '~/support/wrappers/MessageBag';

export default {
    name: 'WebForm',

    inject: ['errors'],

    props: {
        url: String,

        method: {
            type: String,
            default: 'post'
        },
    },

    created() {
        this.$errors = new MessageBag("Validation Errors", this.errors);
        this.$csrftoken = document.querySelector('meta[name="csrf-token"').content;
    }
}
</script>