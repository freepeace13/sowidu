<template>
    <v-layout row class="btn-filter-group">
        <v-flex shrink align-self-center d-flex align-items-center>
            <v-icon class="mr-2">filter_list</v-icon> Type
        </v-flex>
        <v-flex>
            <ItemTypeSelector
                v-bind="$attrs"
                v-on="$listeners"
                hide-details
            />
        </v-flex>
    </v-layout>
</template>

<script>
import ItemTypeSelector from '~/components/UI/Inputs/Selectors/ItemType';

export default {
    components: {
        ItemTypeSelector
    }
}
</script>
