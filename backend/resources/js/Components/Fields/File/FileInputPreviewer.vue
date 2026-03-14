<template>
    <v-badge :value="hasAttachment" right color="red">
        <template #badge>
            <v-icon
                v-tooltip.top="'Remove'"
                size="14"
                color="white"
                class="cursor-pointer"
                @click="$emit('clear')"
            >
                remove_circle
            </v-icon>
        </template>
        <v-avatar v-if="icon?.name" tile :size="width" :color="icon.color">
            <v-icon dark large>
                {{ icon.name }}
            </v-icon>
        </v-avatar>
        <v-img
            v-else
            :width="width"
            :height="height"
            :src="attachment.preview ?? placeholder"
            :class="imgClass"
        />
    </v-badge>
</template>
<script>
export default {
    props: {
        attachment: {
            required: true,
            type: Object,
        },

        placeholder: {
            required: false,
            type: String,
        },

        width: {
            required: false,
            type: [String, Number],
            default: 60,
        },

        height: {
            required: false,
            type: [String, Number],
            default: 60,
        },

        imgClass: {
            required: false,
            type: String,
            default: null,
        },

        icon: {
            required: false,
            type: Object,
            default: () => ({
                name: null,
                color: null,
            }),
        },
    },

    computed: {
        hasAttachment() {
            return this.attachment.preview
        },
    },
}
</script>
