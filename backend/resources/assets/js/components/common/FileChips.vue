<template>
    <v-container grid-list-lg fluid>
        <p v-if="items.isEmpty()">No attachment(s) added.</p>
        <div v-else>
            <v-chip close v-for="item in items.values()" :key="item.uuid" @input="$emit('remove', item)">
                <v-avatar>
                    <v-icon>insert_drive_file</v-icon>
                </v-avatar>
                {{ item.toString() }}
            </v-chip>
        </div>
    </v-container>
</template>

<script>
import Collection from '~/services/collections/attachables'

export default {
    props: {
        items: {
            type: Collection,
            default: () => new Collection
        }
    }
}
</script>
