<template>
    <label class="text-no-wrap font-weight-medium">
        {{ label }} <span v-if="required !== undefined" class="red--text">*</span>
    </label>
</template>

<script>
    export default {
        props: ['label', 'required']
    }
</script>
