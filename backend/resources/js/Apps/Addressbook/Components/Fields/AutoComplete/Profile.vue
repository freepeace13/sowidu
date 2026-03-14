<template>
    <v-autocomplete
        v-bind="$attrs"
        v-on="$listeners"
    >
        <template #selection="data">
            <slot
                name="item"
                v-bind="data"
            >
                <v-chip
                    :selected="data.selected"
                    close
                    @input="$emit('unselect', data.item)"
                >
                    <v-avatar>
                        <img :src="data.item.photo" />
                    </v-avatar>
                    {{ data.item.name }}
                </v-chip>
            </slot>
        </template>

        <template #item="data">
            <slot
                name="item"
                v-bind="data"
            >
                <v-list-tile-avatar>
                    <img :src="data.item.photo" />
                </v-list-tile-avatar>
                <v-list-tile-content>
                    <v-list-tile-title>
                        {{ data.item.name }}
                    </v-list-tile-title>
                </v-list-tile-content>
            </slot>
        </template>
    </v-autocomplete>
</template>

<script>
export default {
    name: 'ProfileAutoComplete',
}
</script>
