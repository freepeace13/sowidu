import Vue from 'vue';

export default function(fn, callback, immediate = false) {
    new Vue({
        created() {
            this.$watch(fn, callback, { immediate });
        }
    });
}