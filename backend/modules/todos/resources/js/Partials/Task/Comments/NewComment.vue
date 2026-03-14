<template>
    <v-flex
        xs12
        mb-2
    >
        <div class="comment">
            <v-avatar color="primary">
                <img
                    :src="user.photo"
                    :alt="user.name"
                />
            </v-avatar>
            <div class="content">
                <v-card flat>
                    <v-card-text class="py-0 pr-0">
                        <CommentForm is-new>
                            <div class="pb-3 tw-text-grey-800">
                                Write a comment...
                            </div>
                        </CommentForm>
                    </v-card-text>
                </v-card>
            </div>
        </div>
    </v-flex>
</template>
<script>
import CommentForm from './CommentForm.vue'

export default {
    components: { CommentForm },

    computed: {
        user() {
            return this.$page.props.user
        },
    },
}
</script>
<style lang="scss" scoped>
.comment {
    display: flex;
    flex-direction: row;
    .content {
        flex: 1 1 0%;
    }
}
</style>
