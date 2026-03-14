<template>
    <section id="notifications">
        <v-snackbar
            v-for="(toast, index) in toasts"
            :key="toast.id"
            :id="`notification-${index}`"
            :value="true"
            :right="true"
            :top="true"
            :style="{ top: `${computedTopValue(index)}` }"
            @input="remove(index)"
            multi-line
            :timeout="5000"
        >
            <v-list class="py-0" three-line dense>
                <v-list-tile>
                    <v-list-tile-avatar>
                        <v-img :src="toast.data.avatar" />
                    </v-list-tile-avatar>
                    <v-list-tile-content>
                        <v-list-tile-title>{{ toast.data.title }}</v-list-tile-title>
                        <v-list-tile-sub-title v-if="toast.data.subtitle">
                            {{ toast.data.subtitle }}
                        </v-list-tile-sub-title>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>
        </v-snackbar>
    </section>
</template>

<script>
export default {
    name: 'Notifications',

    data: () => ({
        toasts: []
    }),

    computed: {
        previousNode() {
            return (index) => document.querySelector(`#notification-${index - 1}`);
        },

        computedTopValue() {
            return (index) => {
                const previousNode = this.previousNode(index);

                if (previousNode) {
                    const styles = window.getComputedStyle(previousNode);
                    return `calc(${styles.top} + ${styles.height} + 20px)`;
                }

                return 0;
            }
        }
    },

    methods: {
        remove(index) {
            if (this.toasts[index]) {
                this.toasts.splice(index, 1);
            }
        }
    },

    created() {
        this.$store.subscribe((mutation, state) => {
            if (mutation.type === 'notification/INSERT_NOTIFICATION') {
                this.toasts.push(mutation.payload);
            }
        });
    }
}
</script>

<style lang="scss" scoped>
    #notifications {
        /deep/ .v-snack--multi-line .v-snack__content {
            height: 100px;
        }

        /deep/ .v-snack > .v-snack__wrapper {
            max-width: 450px;
            min-width: 450px;

            & > .v-snack__content {
                padding: 0px;

                & > .v-list {
                    width: 100%;
                    background: transparent;
                }
            }
        }
    }
</style>