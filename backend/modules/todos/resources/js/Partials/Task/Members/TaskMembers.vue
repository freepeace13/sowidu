<template>
    <v-flex
        shrink
        mb-3
    >
        <v-layout
            column
            justify-start
        >
            <v-flex class="font-weight-bold title"> Members </v-flex>
            <v-layout
                row
                wrap
                justify-start
                align-center
                mx-1
            >
                <Subscriber
                    v-for="member in members"
                    :key="member.id"
                    :avatar="member.user.photo"
                    :name="member.user.name"
                    :has-tooltip="true"
                    @click.native="
                        (e) => subscriberDetailsMenuRef.show(e, member)
                    "
                />
                <v-flex shrink>
                    <v-btn
                        v-tooltip.top="'Add member'"
                        color="blue-grey darken-2"
                        icon
                        dark
                        class="mx-0"
                        @click="(e) => taskMemberMenuRef.show(e)"
                    >
                        <v-icon dark>person_add</v-icon>
                    </v-btn>
                </v-flex>
            </v-layout>
        </v-layout>
    </v-flex>
</template>
<script>
import Subscriber from '../../Subscriber/Subscriber.vue'
import useParentFinder from '@/Composables/useParentFinder'

export default {
    components: { Subscriber },

    props: {
        members: {
            type: Array,
            required: false,
        },
    },

    computed: {
        taskMemberMenuRef() {
            return useParentFinder(this.$parent, 'taskMembersMenu')
        },

        subscriberDetailsMenuRef() {
            return useParentFinder(this.$parent, 'memberDetailsMenuRef')
        },
    },
}
</script>
