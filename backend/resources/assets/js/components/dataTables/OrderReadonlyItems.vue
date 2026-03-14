<template>
    <tr>
        <td>{{ item.quantity }}</td>
        <td>{{ item.name }}</td>
        <td>{{ item.price }}</td>
        <td>{{ item.subtotal }}</td>
        <td v-if="removable" class="text-xs-right">
            <v-btn icon flat color="red" small @click="$emit('remove', item)">
                <v-icon>delete</v-icon>
            </v-btn>
        </td>
    </tr>
</template>

<script>
import Item from '~/services/models/item'

export default {
    props: {
        item: {
            type: Item,
            required: true
        },

        removable: {
            type: Boolean,
            default: false
        }
    },

    created() {
        console.log(this.item)
    }
}
</script>
