<template>
    <div class="avatar-cropper">
        <div ref="cropper" class="slim-cropper">
            <div class="error-balloon" v-if="errors.length > 0">
                <ul>
                    <li v-for="(message, index) in errors" :key="index">
                        {{ message }}
                    </li>
                </ul>
            </div>
            <img :src="initialImage" alt="Initial Image">
            <input id="imgfile" type="file" name="slim" accept="image/*" required/>
        </div>
    </div>
</template>

<script>
    import { Base64ToFile } from '~/utils/filesystem'
    import Slim from '~/libs/slim/slim.module'

    export default {
        props: {
            errors: {
                type: Array,
                default: () => ([])
            },
            value: {
                required: true
            },
            type: {
                type: String,
                default: 'company',
                validator: (prop) => {
                    return ['company', 'user'].indexOf(prop) !== -1
                }
            },
            initialImage: {
                type: String,
                default() {
                    return window.config.avatars[this.type]
                }
            }
        },

        methods: {
            onSave(data) {
                this.$emit('input', Base64ToFile(data.output.image))
            },

            onRemove(data) {
                this.$emit('input', null)
            },

            initialize() {
                const options = {
                    ratio: '1:1',
                    didSave: this.onSave,
                    didRemove: this.onRemove,
                    saveInitialImage: false
                }

                this.Plugin = new Slim(this.$refs.cropper, options)
            }
        },

        mounted() {
            this.initialize()
        },

        destroyed() {
            if (this.Plugin) {
                this.Plugin.destroy()
            }
        }
    }
</script>

<style lang="scss" scoped>
    .avatar-cropper {
        margin-bottom: 20px;
    }
</style>
