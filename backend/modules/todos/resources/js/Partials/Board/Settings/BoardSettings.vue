<template>
    <div>
        <v-list
            class="py-0"
            two-line
        >
            <v-list-tile
                v-for="tab in tabs"
                :key="tab.component"
                @click="$emit('click:tab', tab)"
            >
                <v-list-tile-action>
                    <v-btn
                        icon
                        flat
                    >
                        <v-icon :color="tab.color">{{ tab.icon }}</v-icon>
                    </v-btn>
                </v-list-tile-action>
                <v-list-tile-content>
                    <v-list-tile-title>{{ tab.title }}</v-list-tile-title>
                    <v-list-tile-sub-title>
                        {{ tab.description }}
                    </v-list-tile-sub-title>
                </v-list-tile-content>
            </v-list-tile>
        </v-list>
        <v-divider />
        <BoardActivities :is-show="isShow" />
    </div>
</template>
<script>
import BoardActivities from './BoardActivities.vue'

export default {
    components: { BoardActivities },

    props: {
        tabs: {
            type: Array,
            required: true,
        },
        isShow: {
            required: false,
            type: Boolean,
            default: false,
        },
    },
}
</script>
