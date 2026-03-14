<template>
    <v-card>
        <div class="d-flex">
            <v-layout row>
                <v-flex xs4 class="pr-0">
                    <v-icon large class="icon">done</v-icon>
                </v-flex>
                <v-flex xs8 class="pl-0">
                    <v-card-text class="pa-3">
                        <div class="d-flex align-items-center">
                            <div class="flex xs9 pa-0 headline" v-html="task.title"></div>
                        </div>
                        <div v-html="task.description"></div>
                    </v-card-text>
                    <v-card-text>
                        <div class="d-flex align-items-center">
                            <div class="flex xs9 pa-0">Members:</div>
                        </div>
                        <UserIcon
                            v-for="member in task.members"
                            class="mr-2"
                            :key="member.id"
                            :url="member.avatar"
                            :status="member.auth_status"
                        >
                            {{ member.fullName }}
                        </UserIcon>
                    </v-card-text>
                </v-flex>
            </v-layout>
        </div>
    </v-card>
</template>

<script>
    export default {
        props: {
            task: {
                type: Object,
                required: true
            }
        },

        methods: {
            onAction() {
                alert('Changing task status are only available in edit form for now.')
            }
        }
    }
</script>

<style lang="scss" scoped>
    .icon {
        height: 200px;
        line-height: 200px;
        text-align: center;
        background: #303030;
        font-size: 10rem !important;
        display: block;
        color: #616161;
    }
</style>
