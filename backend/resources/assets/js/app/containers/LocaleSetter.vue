<template>
    <WebForm url="/change-language" ref="form">
        <input type="hidden" name="locale" :value="locale" />
        <LocaleSelector :value="locale" @change="changeLanguage" />
    </WebForm>
</template>

<script>
import WebForm from '@common/components/WebForm';
import LocaleSelector from '@common/components/LocaleSelector';

export default {
    name: 'LocaleSetter',

    components: {
        LocaleSelector,
        WebForm
    },

    data() {
        return {
            locale: this.shared('app.locale')
        }
    },

    methods: {
        changeLanguage(locale) {
            this.locale = locale;
            setTimeout(() => this.$refs.form.$el.submit(), 200);
        }
    }
}
</script>
