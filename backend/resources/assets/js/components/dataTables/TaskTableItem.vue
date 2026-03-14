<template>
    <tr>
        <td>{{ item.title }}</td>
        <td>{{ item.description }}</td>
        <td v-if="removable" class="text-xs-right">
            <v-btn icon flat color="red" small @click="$emit('remove', item)">
                <v-icon>delete</v-icon>
            </v-btn>
        </td>
    </tr>
</template>

<script>
import Task from '~/services/models/task'

export default {
    props: {
        item: {
            type: Task,
            required: true
        },

        removable: {
            type: Boolean,
            default: false
        }
    }
}
</script>
