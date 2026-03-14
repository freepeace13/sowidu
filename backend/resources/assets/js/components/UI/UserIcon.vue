<template>
    <div :class="[size, 'user-icon']">
        <v-tooltip bottom color="black">
            <template v-slot:activator="{ on }">
                <v-badge overlap bottom :color="colors[status]">
                    <template v-slot:badge>
                        <span>&nbsp;</span>
                    </template>

                    <v-avatar v-on="on" :size="sizes[size]" class="elevation-1">
                        <v-img :src="url" alt="" class="grey" />
                    </v-avatar>
                </v-badge>
            </template>

            <slot></slot>
        </v-tooltip>
    </div>
</template>

<script>
    export default {
        props: {
            status: {
                type: String,
                default: 'offline',
                validator(v) {
                    return ['offline', 'away', 'online'].indexOf(v) !== -1
                }
            },

            url: {
                type: String,
                required: true
            },

            size: {
                type: String,
                default: 'md',
                validator(v) {
                    return ['sm', 'md', 'lg'].indexOf(v) !== -1
                }
            },
        },

        data: () => ({
            colors: {
                offline: 'grey',
                away: 'warning',
                online: 'green'
            },

            sizes: {
                sm: 30,
                md: 40,
                lg: 50
            }
        })
    }
</script>

<style lang="scss" scoped>
    .user-icon {
        display: inline;

        &.md {
            height: 40px;
            width: 40px;
        }

        &.sm {
            height: 30px;
            width: 30px;
        }

        &.lg {
            height: 50px;
            width: 50px;
        }
        
        /deep/ .v-badge__badge {
            right: 0px;
            bottom: 0px !important;
        }

        &.sm /deep/ .v-badge__badge {
            height: 10px;
            width: 10px;
        }

        &.md /deep/ .v-badge__badge {
            height: 12px;
            width: 12px;
        }

        &.lg /deep/ .v-badge__badge {
            height: 15px;
            width: 15px;
        }
    }
</style>
