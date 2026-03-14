<template>
    <div :class="['back-button-toolbar', classes]">
        <v-btn dark fab small color="grey darken-4" @click="to ? $router.push(to) : $router.go(-1)">
          <v-icon>arrow_left</v-icon>
        </v-btn>
        <slot></slot>
    </div>
</template>

<script>
    export default {
        props: ['to', 'classes']
    }
</script>

<style lang="scss" scoped>
    .back-button-toolbar {
        position: relative;
    }
</style>
