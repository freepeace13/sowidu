<template>
    <div :class="[{ 'd-none': !visible, 'app-loader': isFullPage, 'not-full': !isFullPage }]">
        <div class="wrapper">
            <div>
                <v-progress-circular
                    :size="50"
                    color="primary"
                    indeterminate
                ></v-progress-circular>
                
                <h4 class="mt-2 bold">Please wait</h4>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'AppLoader',

    props: {
        visible: {
            type: Boolean,
            default: false
        },

        isFullPage: {
            required: false,
            default: true,
            type: Boolean,
        }
    },
}
</script>

<style scoped>
.app-loader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 999;
    background: rgba(0, 0, 0, .5);
    display: flex;
    justify-content: center;
    align-items: center;
}

.wrapper {
    border-radius: 5px;
    width: 120px;
    height: 110px;
    background: rgba(0, 0, 0, .7);
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
}

.not-full {
    display: flex;
    justify-content: center;
}

.not-full .wrapper {
    background: transparent;
}
</style>