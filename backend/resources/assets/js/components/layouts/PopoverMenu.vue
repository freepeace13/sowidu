<template>
    <v-menu
        v-model="menu"
        v-bind="$attrs"
        offset-x
    >
        <template v-slot:activator="{ on }">
            <slot name="activator" :on="on"></slot>
        </template>

        <v-card>
            <slot></slot>
        </v-card>
    </v-menu>
</template>

<script>
export default {
    name: 'PopoverMenu',

    data: () => ({
        menu: false
    })
}
</script>