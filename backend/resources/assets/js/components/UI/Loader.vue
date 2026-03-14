<template>
    <div class="loader-container">
        <v-layout col align-items-center>
            <v-flex
                xs8 offset-xs2
                lg2 offset-lg5
                md4 offset-md4
                class="text-xs-center"
            >
                <v-progress-linear :indeterminate="true"/>
                <p class="loader-message" v-html="message"></p>
            </v-flex>
        </v-layout>
    </div>
</template>

<script>
export default {
    props: {
        message: {
            type: String,
            default: "Please wait..."
        }
    }
}
</script>

<style scoped>
.loader-container {
    height: 100%;
    display: flex;
    align-content: center;
}

.loader-message {
    font-size: 17px;
    font-weight: bold;
}
</style>