<template>
    <tr>
        <td v-if="!hideIconType">
            <v-icon :color="iconColor" v-html="icon" />
        </td>
        <td>{{ item.title }}</td>
        <td>{{ item.items.length }} item(s)</td>
        <td>{{ item.deliveryDate }} @ {{ item.deliveryTime }}</td>
        <td v-if="removable" class="text-xs-right">
            <v-btn icon flat color="red" small @click="$emit('remove', item)">
                <v-icon>delete</v-icon>
            </v-btn>
        </td>
    </tr>
</template>

<script>
import Delivery from '~/services/models/delivery'

export default {
    props: {
        item: {
            type: Delivery,
            required: true
        },

        removable: {
            type: Boolean,
            default: false
        },

        hideIconType: {
            type: Boolean,
            default: false
        }
    },

    computed: {
        iconColor() {
            return this.item.type === 'incoming' ? 'success' : 'warning';
        },

        icon() {
            return this.item.type === 'incoming'
                ? 'subdirectory_arrow_left'
                : 'subdirectory_arrow_right'
        }
    }
}
</script>
