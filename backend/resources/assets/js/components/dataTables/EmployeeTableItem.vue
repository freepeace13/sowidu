<template>
    <tr :active="highlight" @click="$emit('select', item)">
        <td class="d-inline-flex align-items-center">
            <div>
                <UserIcon
                    :url="item.avatar.url"
                    :status="item.status.authStatus"
                    class="mr-3 algin-self-center"
                />
            </div>
            <div>
                <h4 class="bold">{{ item.name }}</h4>
                {{ item.specialization }} &middot; {{ item.email }}
            </div>
        </td>
        <td v-if="removable" class="text-xs-right">
            <v-btn icon flat color="red" small @click="$emit('remove', item)">
                <v-icon>delete</v-icon>
            </v-btn>
        </td>
    </tr>
</template>

<script>
import Employee from '~/services/models/employee';

export default {
    props: {
        item: {
            type: Employee,
            required: true
        },

        removable: {
            type: Boolean,
            default: false
        },

        highlight: {
            type: Boolean,
            default: false
        }
    }
}
</script>
