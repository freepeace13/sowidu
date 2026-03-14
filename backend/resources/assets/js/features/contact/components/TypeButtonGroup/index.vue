<template>
    <v-btn-toggle
        :value="value"
        class="transparent"
        @change="$emit('change', $event)"
    >
        <v-tooltip bottom color="white black--text">
            <template v-slot:activator="{ on }">
                <v-btn value="employees" icon v-on="on">
                    <v-icon>business_center</v-icon>
                </v-btn>
            </template>
            <span>Display Employees</span>
        </v-tooltip>

        <v-tooltip bottom color="white black--text">
            <template v-slot:activator="{ on }">
                <v-btn value="companies" icon v-on="on">
                    <v-icon>apartment</v-icon>
                </v-btn>
            </template>
            <span>Display Companies</span>
        </v-tooltip>

        <v-tooltip bottom color="white black--text">
            <template v-slot:activator="{ on }">
                <v-btn value="users" icon v-on="on">
                    <v-icon>group</v-icon>
                </v-btn>
            </template>
            <span>Display Users</span>
        </v-tooltip>
    </v-btn-toggle>
</template>

<script>
export default {
    name: 'TypeButtonGroup',

    props: {
        value: String
    }
}
</script>